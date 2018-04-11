<?php
/*
 * This file is part of the StockManager.
 *
 * (c) Frogg <admin@frogg.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Security;

use App\Entity\User;
use App\Service\MailerManager;
use App\SiteConfig;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\AccountStatusException;
use Symfony\Component\Translation\TranslatorInterface;
use Twig\Environment;

/**
 * @author Frogg <admin@frogg.fr>
 */
class UserManager
{
    /** @var UserPasswordEncoderInterface */
    private $passwordEncoder;
    /** @var TranslatorInterface */
    private $translator;
    /** @var MailerManager */
    private $mailer;
    /** @var Environment */
    private $twig;
    /** @var EntityManagerInterface */
    private $entityManager;
    /** @var FlashBagInterface */
    private $flash;
    /** @var Request */
    private $request;
    /** @var UserChecker */
    private $userChecker;


    /**
     * UserManager constructor.
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param EntityManagerInterface $entityManager
     * @param TranslatorInterface $translator
     * @param Environment $twig
     * @param RequestStack $requestStack
     * @param MailerManager $mailer
     * @param SessionInterface $session
     * @param UserChecker $userChecker
     */
    public function __construct(UserPasswordEncoderInterface $passwordEncoder, EntityManagerInterface $entityManager, TranslatorInterface $translator, Environment $twig, RequestStack $requestStack, MailerManager $mailer, FlashBagInterface $flashbag, UserChecker $userChecker)
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->translator = $translator;
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->entityManager = $entityManager;
        $this->flash = $flashbag;
        $this->request = $requestStack->getMasterRequest();
        $this->userChecker = $userChecker;
    }

    /*##############
    # Main Methods #
    ###############*/

    /**
     * Create a new user then send mail + add flashbag message
     * @param User $user
     *
     * @return bool
     */
    public function register(User $user): bool
    {
        try {
            // set user data
            $user
                // password encryption
                ->setPassword($this->passwordEncoder->encodePassword($user, $user->getPassword()));


            // insert into database
            $this->entityManager->persist($user);
            $this->entityManager->flush();

            // send the mail
            $this->mailer->send(
                SiteConfig::SECURITYMAIL,
                $user->getEmail(),
                $this->twig->render('mail/security/register.html.twig', array('data' => $user)),
                $this->twig->render('mail/security/register.txt.twig', array('data' => $user)),
                SiteConfig::SITENAME . ' - ' . $this->translator->trans('email account validation subject', [], 'security_mail')
            );

            // set confirm message
            $this->flash->add('check', 'validation register sent confirmation');

        } catch (\Exception $exception) {
            //error occured
            $this->flash->add(
                'error',
                method_exists($exception, 'getMessageKey') ?
                    $exception->getMessageKey() : $exception->getMessage()
            );

            return false;
        }

        return true;
    }

    /**
     * Check if request is valid then validate the user account + add flash bag message
     *
     * @return bool
     */
    public function registerValidation(): bool
    {
        try {
            // recover user from request
            $user = $this->getUserFromRequest();

            // check before validation
            $this->userChecker->checkRegisterValidation($user, $this->request->query->get('token'));

            // remove token and enable account
            $user->setEnabled();

            // insert into database
            $this->entityManager->persist($user);
            $this->entityManager->flush();

            // set register validation ok message
            $this->flash->add('check', 'validation register confirmation');

        } catch (\Exception $exception) {
            //error occured
            $this->flash->add(
                'error',
                method_exists($exception, 'getMessageKey') ?
                    $exception->getMessageKey() : $exception->getMessage()
            );

            return false;
        }

        return true;
    }

    /**
     * Check if email exist then send a mail with link to recover password + add flah bag message
     * @param string $email
     *
     * @return bool
     */
    public function recover(string $email): bool
    {
        try {
            /** @var User $user get author from email */
            $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);

            // check before validation
            $this->userChecker->basicTest($user);

            // create a token
            $user->setToken();

            // insert into database
            $this->entityManager->persist($user);
            $this->entityManager->flush();

            // send the mail
            $this->mailer->send(
                SiteConfig::SECURITYMAIL,
                $email,
                $this->twig->render('mail/security/recover.html.twig', array('data' => $user)),
                $this->twig->render('mail/security/recover.txt.twig', array('data' => $user)),
                SiteConfig::SITENAME . ' - ' . $this->translator->trans('email password recovery subject', [], 'security_mail')
            );

            // set register validation ok message
            $this->flash->add('check', 'validation recover sent confirmation');
        } catch (\Exception $exception) {
            //error occured
            $this->flash->add(
                'error',
                method_exists($exception, 'getMessageKey') ?
                    $exception->getMessageKey() : $exception->getMessage()
            );

            return false;
        }

        return true;
    }


    /**
     * change user password + add flash bag message
     * @param User $user
     *
     * @return bool
     */
    public function recoverValidation(user $user): bool
    {
        try {
            // check before validation
            $this->userChecker->checkRecoverValidation($user, $this->request->query->get('token'));

            // password encryption
            $password = $this->passwordEncoder->encodePassword($user, $user->getPassword());

            // change password into database & remove token + set enbaled if just registered
            $user
                ->setPassword($password)
                ->setEnabled();

            // insert into database
            $this->entityManager->persist($user);
            $this->entityManager->flush();

            // set register validation ok message
            $this->flash->add('check', $this->translator->trans('validation password changed', [], 'security'));

        } catch (\Exception $exception) {
            //error occured
            $this->flash->add(
                'error',
                method_exists($exception, 'getMessageKey') ?
                    $exception->getMessageKey() : $exception->getMessage()
            );

            return false;
        }

        return true;
    }

    /*##############
    # Util Methods #
    ###############*/

    /**
     * @return null|User
     */
    public function getUserFromRequest(): ?User
    {
        // get request info
        $email = $this->request->query->get('email');

        // getUserFromEmail
        $reposirotyUser = $this->entityManager->getRepository(User::class);

        /** @var User $user */
        $user = $reposirotyUser->findOneByEmail($email);

        return $user;
    }
}
