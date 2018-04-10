<?php
/*
 * This file is part of the StockManager.
 *
 * (c) Frogg <admin@frogg.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Service;

use App\Entity\User;
use App\SiteConfig;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
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


    /**
     * UserManager constructor.
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param EntityManagerInterface       $entityManager
     * @param TranslatorInterface          $translator
     * @param Environment                  $twig
     * @param RequestStack                 $requestStack
     * @param MailerManager                $mailer
     * @param SessionInterface             $session
     */
    public function __construct(UserPasswordEncoderInterface $passwordEncoder, EntityManagerInterface $entityManager, TranslatorInterface $translator, Environment $twig, RequestStack $requestStack, MailerManager $mailer, SessionInterface $session)
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->translator = $translator;
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->entityManager = $entityManager;
        $this->flash = $session->getFlashBag();
        $this->request = $requestStack->getMasterRequest();
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
        $user
            // password encryption
            ->setPassword($this->passwordEncoder->encodePassword($user, $user->getPassword()))
            ->setInactive()
            ->setToken();

        try {
            // insert into database
            $this->entityManager->persist($user);
            $this->entityManager->flush();

            // send the mail
            $this->mailer->send(
                SiteConfig::SECURITYMAIL,
                $user->getEmail(),
                $this->twig->render('mail/security/register.html.twig', array('data' => $user)),
                $this->twig->render('mail/security/register.txt.twig', array('data' => $user)),
                SiteConfig::SITENAME.' - '.$this->translator->trans('email account validation subject', [], 'security_mail')
            );

            // set confirm message
            $this->flash->add('check', 'validation register sent confirmation');
        } catch (\Exception $exception) {
            //error occured
            $this->flash->add('error', $exception->getMessage());

            return false;
        }

        return true;
    }

    /**
     * Check if request is valid then validate the user account + add flah bag message
     *
     * @return bool
     */
    public function registerValidation(): bool
    {
        try {
            // recover user from request
            $user = $this->getUserFromRequest();

            $this->checkAccountStatus($user, ['checkAlreadyValidated']);

            // remove token and enable account
            $user
                ->removeToken()
                ->setActive();

            // insert into database
            $this->entityManager->persist($user);
            $this->entityManager->flush();

            // set register validation ok message
            $this->flash->add('check', 'validation register confirmation');
        } catch (\Exception $exception) {
            //error occured
            $this->flash->add('error', $exception->getMessage());

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
            // get author from email
            $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);

            // author not found
            if (!$user) {
                // set error message
                // $this->addFlash('error', 'account is unfindable');

                // Fake message to prevent test if email exist
                // set register validation ok message
                $this->flash->add('check', 'validation recover sent confirmation');

                // redirect user
                return true;
            }

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
                SiteConfig::SITENAME.' - '.$this->translator->trans('email password recovery subject', [], 'security_mail')
            );

            // set register validation ok message
            $this->flash->add('check', 'validation recover sent confirmation');
        } catch (\Exception $exception) {
            //error occured
            $this->flash->add('error', $exception->getMessage());

            return false;
        }

        return true;
    }


    /**
     * change user password + add flah bag message
     *
     * @param User $user
     *
     * @return bool
     */
    public function recoverValidation(user $user): bool
    {
        try {
            // password encryption
            $password = $this->passwordEncoder->encodePassword($user, $user->getPassword());

            // change password into database
            $user
                ->setPassword($password)
                ->removeToken()
                ->setActive();

            // insert into database
            $this->entityManager->persist($user);
            $this->entityManager->flush();

            // set register validation ok message
            $this->flash->add('check', $this->translator->trans('validation password changed', [], 'security'));
        } catch (\Exception $exception) {
            //error occured
            $this->flash->add('error', $exception->getMessage());

            return false;
        }

        return true;
    }

    /*##############
    # Util Methods #
    ###############*/

    /**
     * Check if token is valid
     *
     * @return null|User
     */
    public function checkValidation(): ?User
    {
        // recover user from request
        $user = $this->getUserFromRequest();

        //check if token has expired
        if (!$this->checkAccountStatus($user, ['checkIfTokenExpired'])) {
            return null;
        }

        return $user;
    }

    /**
     * @return null|User
     */
    private function getUserFromRequest(): ?User
    {
        // get request info
        $email = $this->request->query->get('email');

        // getUserFromEmail
        $reposirotyUser = $this->entityManager->getRepository(User::class);

        /** @var User $user */
        $user = $reposirotyUser->findOneByEmail($email);

        return $user;
    }


    /**
     * @param null|User $user
     * @param array     $extraCheck
     *
     * @return bool
     */
    private function checkAccountStatus(?User $user, array $extraCheck = []): bool
    {
        // user not found
        if (!$user) {
            $this->flash->add('error', $this->translator->trans('account is unfindable', [], 'security'));

            return false;
        }

        // checkif user is banned
        if ($user->isBanned()) {
            $this->flash->add('error', $this->translator->trans('account is unfindable', [], 'security'));

            return false;
        }

        // checkif user is closed
        if ($user->isClosed()) {
            $this->flash->add('error', $this->translator->trans('account is closed', [], 'security'));

            return false;
        }

        // Extra custom check
        foreach ($extraCheck as $check) {
            if (!$this->$check($user)) {
                return false;
            }
        }

        // else it is ok
        return true;
    }

    /**
     * @param User $user
     *
     * @return bool
     */
    private function checkAlreadyValidated(User $user): bool
    {
        // check if account already registered
        if ($user->isEnabled()) {
            $this->flash->add('warning', $this->translator->trans('account is already activated', [], 'security'));

            return false;
        }

        // else check token
        return $this->checkToken($user);
    }

    /**
     * @param User $user
     *
     * @return bool
     */
    private function checkIfTokenExpired(User $user): bool
    {
        // check if account already registered
        if ($user->isTokenExpired()) {
            $this->flash->add('warning', $this->translator->trans('account is expired token', [], 'security'));

            return false;
        }

        // else check token
        return $this->checkToken($user);
    }

    /**
     * @param User $user
     *
     * @return bool
     */
    private function checkToken(User $user): bool
    {

        $isValid = $user->getToken() === $this->request->query->get('token');

        // check if is valid token
        if (!$isValid) {
            $this->flash->add('error', $this->translator->trans('account token is not valid', [], 'security'));
        }

        return $isValid;
    }
}
