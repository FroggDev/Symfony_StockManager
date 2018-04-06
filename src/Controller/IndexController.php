<?php
/**
 * Main page : IndexController
 * Date: 02/03/2018
 * Time: 22:21
 *
 * PHP Version 7.2
 *
 * @category Educational
 * @package  FroggDev/Symfony_TestProject
 * @author   Frogg <admin@frogg.fr>
 * @license  proprietary WebForce3
 * @link     https://github.com/FroggDev/Symfony_TestProject
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

use App\Service\LocaleService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


/**
 * Class IndexController
 * @package App\Controller
 */
class IndexController extends Controller
{
    /**
     * Redirect to main route with locale
     *
     * @Route(
     *     "/",
     *     name="default"
     * )
     * @return Response
     */
    public function index(): Response
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
     *     )
     * @return Response
     */
    public function indexLocale(): Response
    {
        # display page from twig template
        return $this->render('index.html.twig', []);
    }

    /**
     * Change locale
     *
     * @Route(
     *     "/{_locale}/locale.html",
     *     name="change_locale"
     * )
     * @return Response
     */
    public function changeLocale(Request $request)
    {
        $localService = new LocaleService($request, null);
        # Return current route changed to other lang
        return $this->redirect($localService->changeSelectedLocale(),Response::HTTP_MOVED_PERMANENTLY);
    }
}