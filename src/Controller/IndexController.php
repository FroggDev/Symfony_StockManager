<?php
/**
 * Main page : IndexController
 * Date: 06/04/2018
 * Time: 09:30
 *
 * PHP Version 7.2
 *
 * @category Food
 *
 * @package  FroggDev/Symfony_StockManager
 *
 * @author   Frogg <admin@frogg.fr>
 *
 * @license  MIT
 *
 * @link     https://github.com/FroggDev/Symfony_StockManager
 *
 * @Requirement for PHP
 * extension=php_fileinfo.dll for guessExtension()
 * extension=mysqli for Doctrine
 * extension=pdo_mysql for Doctrine
 */

/**
 * @see https://mailtrap.io
 * mailtrap@frogg.fr
 * testtest
 *
 * swiftmailer:
 * spool:     { type: memory }
 * transport: smtp
 *   host:      smtp.mailtrap.io
 *   username:  e5e05820e45013
 *   password:  e4ecbcfef4fb67
 *   auth_mode: cram-md5
 *   port: 2525
 */

namespace App\Controller;

use App\Common\Traits\Client\ResponseTrait;
use App\Service\LocaleService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * TODO :
 * https://symfony.com/doc/current/security/remember_me.html
 * add functionnality : force to login/password when edit content
 *
 * TODO bug security yaml secret remember me ?
 * TODO how to login with csr ? https://symfony.com/doc/master/security/form_login_setup.html
 * (connexion.html.twig)
 */

/**
 * @author Frogg <admin@frogg.fr>
 */
class IndexController extends Controller
{

    use ResponseTrait;

    /**
     * Redirect to main route with locale
     *
     * @Route(
     *     "/",
     *     name="default",
     *     methods={"GET"}
     * )
     * @return Response
     */
    public function default(): Response
    {
        return $this->redirect($this->generateUrl('index'), Response::HTTP_MOVED_PERMANENTLY);
    }

    /**
     * Main page route
     *
     * @Route(
     *     "/{_locale}",
     *     name="index",
     *     requirements={"_locale"="fr|en"}
     * )
     * @return Response
     */
    public function index(): Response
    {
        // display page from twig template
        return $this->render('index.html.twig', []);
    }

    /**
     * Change locale
     *
     * @Route(
     *     "/{_locale}/locale.html",
     *     name="change_locale",
     *     requirements={"_locale"="fr|en"},
     *     methods={"GET"}
     * )
     * @param Request $request
     *
     * @return Response
     */
    public function changeLocale(Request $request)
    {
        $localService = new LocaleService($request, null);
        // Return current route changed to other lang
        return $this->removeCacheFromResponse(
            $this->redirect(
                $localService->changeSelectedLocale(),
                Response::HTTP_MOVED_PERMANENTLY
            )
        );
    }

    /**
     * Main page route
     *
     * @Route(
     *     "/{_locale}/TEMP.html",
     *     name="index_logged",
     *     requirements={"_locale"="fr|en"},
     *     methods={"GET"}
     * )
     * @return Response
     */
    public function TEMP(): Response
    {
        // display page from twig template
        return $this->render('index.html.twig', []);
    }
}
