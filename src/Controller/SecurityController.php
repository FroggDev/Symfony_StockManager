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

use App\Common\Traits\Client\UserTrait;
use App\Common\Traits\Controller\FormCheckTrait;
use App\Entity\User;
use App\Form\Security\UserPasswordType;
use App\Form\Security\UserRecoverType;
use App\Form\Security\UserType;
use App\Service\UserManager;
use App\SiteConfig;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * @author Frogg <admin@frogg.fr>
 *
 * Prefix all routes
 * @see https://symfony.com/blog/new-in-symfony-3-4-prefix-all-controller-route-names
 *
 * Inline configuration
 * @see https://symfony.com/blog/new-in-symfony-4-1-inlined-routing-configuration
 *
 * Internationalized Routing
 * @see https://symfony.com/blog/new-in-symfony-4-1-internationalized-routing
 *
 * Manual crsf login form
 * @see https://symfony.com/doc/master/security/form_login_setup.html
 * @see https://symfony.com/doc/current/security/csrf.html
 *
 * Remember me on login screen
 * @see https://symfony.com/doc/current/security/remember_me.html
 *
 *
 * @Route(
 *     {
 *     "fr": "/compte",
 *     "en": "/account"
 *     },
 *     name="security_"
 * )
 */
class SecurityController extends Controller
{

    use FormCheckTrait;

    use UserTrait;

    /**
     * User login
     *
     * @Route(
     *     {
     *     "fr": "/connexion.html",
     *     "en": "/login.html"
     *     },
     *     name="connexion",
     *     methods={"GET","POST"}
     * )
     * @param AuthenticationUtils $authenticationUtils
     * @param TranslatorInterface $translator
     *
     * @return Response
     */
    public function connexion(AuthenticationUtils $authenticationUtils, TranslatorInterface $translator)
    {
        // Check if user is logged in
        if ($this->container->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirectToRoute('index_logged');
        }

        // Get error message if exist
        $error = $authenticationUtils->getLastAuthenticationError();

        // create a flash bag if an authentication error occured
        if ($error) {
            $this->addFlash('error', $translator->trans($error->getMessageKey(), [], "security"));
        }

        // Display form & Get last user email set by user
        $response = $this->render(
            'security/connexion.html.twig',
            ['last_email' => $authenticationUtils->getLastUsername()]
        );

        return $response;
    }

    /**
     * User register
     *
     * @Route (
     *     {
     *     "fr": "/creation.html",
     *     "en": "/register.html"
     *     },
     *     name="register",
     *     methods={"GET","POST"}
     *     )
     *
     * @param UserManager $userManager
     * @param Request     $request
     *
     * @return Response
     */
    public function register(UserManager $userManager, Request $request)
    {
        // New user registration
        $user = new User();

        // create the user form
        $form = $this->createForm(UserType::class, $user);

        // post data manager
        $form->handleRequest($request);

        // check form data
        if ($form->isSubmitted() && $this->isOk($form->getErrors()) && $form->isValid() && $userManager->register($user)) {
            // redirect user
            return $this->setCookieResponse($user->getEmail());
        }

        // Display form view
        return $this->render(
            'security/register.html.twig',
            ['form' => $form->createView()]
        );
    }

    /**
     * User registration validation
     *
     * @Route(
     *     {
     *     "fr": "/confirmation.html",
     *     "en": "/validation.html"
     *     },
     *     name="register_validation",
     *     methods={"GET"}
     * )
     *
     * @param UserManager $userManager
     *
     * @return Response
     */
    public function registerValidation(UserManager $userManager)
    {
        //validate the registration
        $userManager->registerValidation();

        // redirect user
        return $this->redirectToRoute('security_connexion');
    }


    /**
     * User password recover form
     *
     * @Route(
     *     {
     *     "fr": "/mot-de-passe/requete.html",
     *     "en": "/password/request.html"
     *     },
     *     name="recover",
     *     methods={"GET","POST"}
     * )
     *
     * @param AuthenticationUtils $authenticationUtils
     * @param Request             $request
     * @param UserManager         $userManager
     *
     * @return Response
     */
    public function recoverRequest(AuthenticationUtils $authenticationUtils, Request $request, UserManager $userManager)
    {
        $email = $request->cookies->get(SiteConfig::COOKIEUSERNAME) ?? $authenticationUtils->getLastUsername();

        // create the user form
        $form = $this->createForm(UserRecoverType::class, null, ['last_email' => $email]);

        // post data manager
        $form->handleRequest($request);

        // check form data
        if ($form->isSubmitted() && $this->isOk($form->getErrors()) && $form->isValid() && $userManager->recover($form->get('email')->getData())) {
            // redirect user
            return $this->setCookieResponse($form->get('email')->getData());
        }

        // Display form view
        return $this->render('security/recover.html.twig', [
            'form' => $form->createView(),
            'last_email' => $email,
        ]);
    }

    /**
     * User password recover
     *
     * @Route(
     *     {
     *     "fr": "/mot-de-passe/confirmation.html",
     *     "en": "/password/validation.html"
     *     },
     *     name="recover_validation",
     *     methods={"GET","POST"}
     * )
     *
     * @param UserManager $userManager
     * @param Request     $request
     *
     * @return Response
     */
    public function recoverValidation(UserManager $userManager, Request $request)
    {
        // check user token
        $user = $userManager->checkValidation();

        //redirect if user not valid
        if (!$user) {
            return $this->redirectToRoute('security_connexion');
        }

        // create the user form
        $form = $this->createForm(UserPasswordType::class, $user);

        // post data manager
        $form->handleRequest($request);

        // check form data
        if ($form->isSubmitted() && $this->isOk($form->getErrors()) && $form->isValid() && $userManager->recoverValidation($user)) {
            // redirect user
            return $this->setCookieResponse($user->getEmail());
        }

        // Display form view
        return $this->render('security/password.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /*########
     # Utils #
     ########*/

    /**
     * @param string $email
     *
     * @return Response
     */
    private function setCookieResponse(string $email) : response
    {
        $response = $this->redirectToRoute('security_connexion');
        $response->headers->setCookie($this->getUserCookie($email));

        return $response;
    }
}
