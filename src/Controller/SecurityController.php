<?php
/*
 * This file is part of the StockManager.
 *
 * (c) Frogg <admin@frogg.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Controller;

use App\Common\Traits\Comm\MailerTrait;
use App\Common\Traits\Controller\DatabaseTrait;
use App\Entity\User;
use App\Form\Security\UserPasswordType;
use App\Form\Security\UserRecoverType;
use App\Form\Security\UserType;
use App\SiteConfig;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * @author Frogg <admin@frogg.fr>
 *
 * Prefix all routes
 * @see https://symfony.com/blog/new-in-symfony-3-4-prefix-all-controller-route-names
 *
 * @Route(
 *     "{_locale}/user",
 *      name="security_",
 *     requirements={"_locale"="fr|en"}
 * )
 */
class SecurityController extends Controller
{

    use MailerTrait;

    use DatabaseTrait;

    /** @var \Swift_Mailer */
    private $mailer;
    /** @var TranslatorInterface */
    private $translator;

    /** @var Request $request */
    private $request;

    /**
     * SecurityController constructor.
     * @param \Swift_Mailer       $mailer
     * @param TranslatorInterface $translator
     */
    public function __construct(\Swift_Mailer $mailer, TranslatorInterface $translator)
    {
        $this->mailer = $mailer;
        $this->translator = $translator;
    }

    /**
     * User login
     *
     * @Route(
     *     "/connexion.html",
     *     name="connexion",
     *     methods={"GET","POST"}
     * )
     * @param AuthenticationUtils $authenticationUtils
     *
     * @return Response
     */
    public function connexion(AuthenticationUtils $authenticationUtils)
    {
        // Check if user is logged in
        if ($this->container->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirectToRoute('index_logged');
        }

        // Get error message if exist
        $error = $authenticationUtils->getLastAuthenticationError();

        // create a flash bag if an authentication error occured
        if ($error) {
            $this->addFlash('error', $error->getMessage());
        }

        // Display form & Get last user email set by user
        return $this->render(
            'security/connexion.html.twig',
            ['last_email' => $authenticationUtils->getLastUsername()]
        );
    }

    /**
     * User register
     *
     * @Route (
     *     "/register/request.html",
     *     name="register",
     *     methods={"GET","POST"}
     *     )
     * @param Request                      $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     *
     * @return Response
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        // New user registration
        $user = new User();

        // create the user form
        $form = $this->createForm(UserType::class, $user);

        // post data manager
        $form->handleRequest($request);

        // check form datas
        if ($form->isSubmitted() && $form->isValid()) {
            // Symfony automatically serialize it
            $user->setRoles('ROLE_MEMBER');

            // password encryption
            $password = $passwordEncoder->encodePassword($user, $user->getPassword());
            $user
                ->setPassword($password)
                ->setInactive()
                ->setToken();

            // insert into database
            $this->save($user);

            // send the mail
            $this->send(
                SiteConfig::SECURITYMAIL,
                $user->getEmail(),
                'mail/security/register.html.twig',
                SiteConfig::SITENAME.' - '.$this->translator->trans('email account validation subject', [], 'security'),
                $user
            );

            //Add success message
            $this->addFlash('check', $this->translator->trans('validation register sent confirmation', [], 'security'));

            /**
             * TEST PURPOSE
             */
            //exit("/fr/user/register/validation.html?email=" . $user->getEmail() . "&token=" . $user->getToken());

            // redirect user
            return $this->redirectToRoute('security_connexion');
        }

        // Display form view
        return $this->render(
            'security/register.html.twig',
            [
                'form' => $form->createView(),
                'last_email' => $user->getEmail(),
            ]
        );
    }

    /**
     * User registration validation
     *
     * @Route(
     *     "/register/validation.html",
     *     name="register_validation",
     *     methods={"GET"}
     * )
     *
     * @param Request $request
     *
     * @return Response
     */
    public function registerValidation(Request $request)
    {
        // recover user from request
        $user = $this->getUserFromRequest($request);

        $this->checkAccountStatus($user, ['checkAlreadyValidated']);

        // remove token and enable account
        $user->removeToken()
            ->setActive();

        // insert into database
        $this->save($user);

        // set register validation ok message
        $this->addFlash('check', $this->translator->trans('validation register confirmation', [], 'security'));

        // redirect user
        return $this->redirectToRoute('security_connexion');
    }


    /**
     * User password recover form
     *
     * @Route(
     *     "/password/request.html",
     *     name="recover",
     *     methods={"GET","POST"}
     * )
     *
     * @param Request             $request
     * @param AuthenticationUtils $authenticationUtils
     *
     * @return Response
     */
    public function recoverRequest(Request $request, AuthenticationUtils $authenticationUtils)
    {
        // New user registration
        $user = new User();

        // create the user form
        $form = $this->createForm(UserRecoverType::class, $user);

        // post data manager
        $form->handleRequest($request);

        // check form data
        if ($form->isSubmitted()) {
            // get repo author
            $repositoryArticle = $this->getDoctrine()->getRepository(User::class);

            // get posted email
            $email = $form->getData()->getEmail();

            // get author from email
            $user = $repositoryArticle->findOneBy(['email' => $email]);

            // author not found
            if (!$user) {
                //set error message
                $this->addFlash('error', $this->translator->trans('account is unfindable', [], 'security'));
                // redirect user
                return $this->redirectToRoute('security_recover', ['last_email' => $email]);
            }

            // create a token
            $user->setToken();

            // insert into database
            $this->save($user);

            // send the mail
            $this->send(
                SiteConfig::SECURITYMAIL,
                $email,
                'mail/security/recover.html.twig',
                SiteConfig::SITENAME.' - '.$this->translator->trans('email password recovery subject', [], 'security'),
                $user
            );

            // set register validation ok message
            $this->addFlash('check', $this->translator->trans('validation recover sent confirmation', [], 'security'));

            /**
             * TEST PURPOSE
             */
            //exit("/fr/user/password/validation.html?email=" . $user->getEmail() . "&token=" . $user->getToken());

            // redirect user
            return $this->redirectToRoute('security_connexion');
        }

        // Display form view
        return $this->render('security/recover.html.twig', [
            'form' => $form->createView(),
            'last_email' => $authenticationUtils->getLastUsername(),
        ]);
    }

    /**
     * User password recover
     *
     * @Route(
     *     "/password/validation.html",
     *     name="recover_validation",
     *     methods={"GET","POST"}
     * )
     *
     * @param Request                      $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     *
     * @return Response
     */
    public function recoverValidation(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        //init request for test
        $this->request = $request;

        // recover user from request
        $user = $this->getUserFromRequest();

        if (!$this->checkAccountStatus($user, ['checkIfTokenExpired'])) {
            return $this->redirectToRoute('security_connexion');
        }

        // create the user form
        $form = $this->createForm(UserPasswordType::class, $user);

        // post data manager
        $form->handleRequest($request);

        // check form datas
        if ($form->isSubmitted()) {
            // password encryption
            $password = $passwordEncoder->encodePassword($user, $user->getPassword());

            // change password into database
            $user
                ->setPassword($password)
                ->removeToken()
                ->setActive();

            // insert into database
            $this->save($user);

            // set register validation ok message
            $this->addFlash('check', $this->translator->trans('validation password changed', [], 'security'));


            // redirect user
            return $this->redirectToRoute('security_connexion');
        }

        // Display form view
        return $this->render('security/password.html.twig', [
            'form' => $form->createView(),
        ]);
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
            $this->addFlash('error', $this->translator->trans('account is unfindable', [], 'security'));

            return false;
        }

        // checkif user is banned
        if ($user->isBanned()) {
            $this->addFlash('error', $this->translator->trans('account is unfindable', [], 'security'));

            return false;
        }

        // checkif user is closed
        if ($user->isClosed()) {
            $this->addFlash('error', $this->translator->trans('account is closed', [], 'security'));

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
            $this->addFlash('warning', $this->translator->trans('account is already activated', [], 'security'));

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
            $this->addFlash('warning', $this->translator->trans('account is expired token', [], 'security'));

            return false;
        }

        // else check token
        return $this->checkToken($user);
    }

    /**
     * @param Request $request
     * @param User    $user
     *
     * @return bool
     */
    private function checkToken(User $user): bool
    {

        $isValid = $user->getToken() === $this->request->query->get('token');

        // check if is valid token
        if (!$isValid) {
            $this->addFlash('error', $this->translator->trans('account token is not valid', [], 'security'));
        }

        return $isValid;
    }

    /**
     * @param Request $request
     *
     * @return null|User
     */
    private function getUserFromRequest(): ?User
    {
        // get request info
        $email = $this->request->query->get('email');

        // getUserFromEmail
        $reposirotyUser = $this->getDoctrine()->getRepository(User::class);

        /** @var User $user */
        $user = $reposirotyUser->findOneByEmail($email);

        return $user;
    }
}
