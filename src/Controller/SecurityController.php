<?php
namespace App\Controller;

use App\Common\Traits\Comm\MailerTrait;
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

/**
 * Class LoginController
 * @package App\Controller
 *
 * Prefix all routes
 * @see https://symfony.com/blog/new-in-symfony-3-4-prefix-all-controller-route-names
 * @Route("{_locale}/user", name="security_")
 */
class SecurityController extends Controller
{

    use MailerTrait;

    /**
     * SecurityController constructor.
     * @param \Swift_Mailer $mailer
     */
    public function __construct(\Swift_Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * User login
     *
     * @Route(
     *     "/login.html",
     *      name="login"
     * )
     * @param AuthenticationUtils $authenticationUtils
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function connexion(AuthenticationUtils $authenticationUtils)
    {
        # Check if user is logged in
        if ($this->container->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirectToRoute('index_logged');
        }

        # Get error message if exist
        $error = $authenticationUtils->getLastAuthenticationError();

        # Get last user email set by user
        $lastEmail = $authenticationUtils->getLastUsername();

        # Display form
        return $this->render('security/login.html.twig', array(
            'last_email' => $lastEmail,
            'error' => $error
        ));
    }

    /**
     * User register
     *
     * @Route (
     *     "/register/request.html",
     *      name="register",
     *     methods={"GET","POST"}
     *     )
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return Response
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        # New user registration
        $user = new User();

        # Symfony automatically serialize it
        $user->setRoles('ROLE_MEMBER');

        # create the user form
        $form = $this->createForm(UserType::class, $user);

        # post data manager
        $form->handleRequest($request);

        # check form datas
        if ($form->isSubmitted() && $form->isValid()) {
            # password encryption
            $password = $passwordEncoder->encodePassword($user, $user->getPassword());
            $user
                ->setPassword($password)
                ->setInactive()
                ->setToken();

            # insert into database
            $eManager = $this->getDoctrine()->getManager();
            $eManager->persist($user);
            $eManager->flush();

            # send the mail
            $this->send(SiteConfig::SECURITYMAIL, $user->getEmail(), 'mail/registration.html.twig', SiteConfig::SITENAME . ' - Validation mail', $user);

            # redirect user
            return $this->redirectToRoute('security_login', ['register' => 'success']);
        }

        # Display form view
        return $this->render('security/register.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * User registration validation
     *
     * @Route(
     *     "/register/validation.html",
     *      name="register_validation"
     * )
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function registerValidation(Request $request)
    {
        # get request infos
        $email = $request->query->get('email');
        $token = $request->query->get('token');

        # getUserFromEmail
        $reposirotyAuthor = $this->getDoctrine()->getRepository(User::class);
        $user = $reposirotyAuthor->findOneBy(
            [
                'email' => $email,
                'token' => $token
            ]
        );

        # author not found
        if (!$user) {
            return $this->redirectToRoute('security_login', ['register' => 'notfound']);
        }

        # account already registered
        if ($user->isEnabled()) {
            return $this->redirectToRoute('security_login', ['register' => 'actived']);
        }

        # checkif author is banned
        if ($user->isBanned()) {
            return $this->redirectToRoute('security_login', ['register' => 'banned']);
        }

        # checkif author is banned
        if ($user->isClosed()) {
            return $this->redirectToRoute('security_login', ['register' => 'closed']);
        }

        # remove token and enable account
        $user->removeToken()
            ->setActive();

        # update database
        $eManager = $this->getDoctrine()->getManager();
        $eManager->persist($user);
        $eManager->flush();

        # redirect user
        return $this->redirectToRoute('security_login', ['register' => 'validated']);
    }


    /**
     * User password recover form
     *
     * @Route(
     *     "/password/request.html",
     *      name="recover"
     * )
     *
     * @param Request $request
     * @return Response
     */
    public function recoverRequest(Request $request)
    {
        # New user registration
        $user = new User();

        # create the user form
        $form = $this->createForm(UserRecoverType::class, $user);

        # post data manager
        $form->handleRequest($request);

        # check form datas
        if ($form->isSubmitted()) {
            # get repo author
            $repositoryArticle = $this->getDoctrine()->getRepository(User::class);

            #get posted email
            $email = $form->getData()->getEmail();

            #get author from email
            $user = $repositoryArticle->findOneBy(['email' => $email]);

            # author not found
            if (!$user) {
                # redirect user
                return $this->redirectToRoute('security_recover', ['register' => 'notfound', 'last_email' => $email]);
            }

            # create a token
            $user->setToken();

            # insert into database
            $eManager = $this->getDoctrine()->getManager();
            $eManager->persist($user);
            $eManager->flush();

            # send the mail
            $this->send(SiteConfig::SECURITYMAIL, $user->getEmail(), 'mail/recover.html.twig', SiteConfig::SITENAME . ' - Password recovery', $user);

            # redirect user
            return $this->redirectToRoute('security_login', ['register' => 'recovered']);
        }

        # Display form view
        return $this->render('security/recover.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * User password recover
     *
     * @Route(
     *     "/password/validation.html",
     *      name="recover_validation"
     * )
     *
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return Response
     */
    public function recoverValidation(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        # get request infos
        $email = $request->query->get('email');
        $token = $request->query->get('token');

        # getUserFromEmail
        $repositoryArticle = $this->getDoctrine()->getRepository(User::class);
        $user = $repositoryArticle->findOneBy(
            [
                'email' => $email,
                'token' => $token
            ]
        );

        # author not found
        if (!$user) {
            return $this->redirectToRoute('security_login', ['register' => 'notfound']);
        }

        # checkif token has expired
        if ($user->isTokenExpired()) {
            return $this->redirectToRoute('security_login', ['register' => 'expired']);
        }

        # checkif author is banned
        if ($user->isBanned()) {
            return $this->redirectToRoute('security_login', ['register' => 'banned']);
        }

        # checkif author is banned
        if ($user->isClosed()) {
            return $this->redirectToRoute('security_login', ['register' => 'closed']);
        }


        # create the user form
        $form = $this->createForm(UserPasswordType::class, $user);

        # post data manager
        $form->handleRequest($request);

        # check form datas
        if ($form->isSubmitted()) {
            # password encryption
            $password = $passwordEncoder->encodePassword($user, $user->getPassword());

            # change password into database
            $user
                ->setPassword($password)
                ->removeToken()
                ->setActive();

            # update database
            $eManager = $this->getDoctrine()->getManager();
            $eManager->persist($user);
            $eManager->flush();

            # redirect user
            return $this->redirectToRoute('security_login', ['register' => 'passechanged']);
        }

        # Display form view
        return $this->render('security/changepassword.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
