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
 * apt-get install php7.2-xml (Extension DOM is required.)
 *
 * extension=php_fileinfo.dll for guessExtension()
 * extension=mysqli for Doctrine
 * extension=pdo_mysql for Doctrine
 * extension=intl for swift mailer
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
use App\Service\LocaleManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * TODO : add functionnality : force to login/password when edit content
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
    public function default(LocaleManager $localeService)
    {
        return $localeService->changeDefaultLocale();
    }

    /**
     * Main page route
     *
     * @Route(
     *     {
     *     "fr": "/accueil.html",
     *     "en": "/home.html"
     *     },
     *     name="index"
     * )
     * @return Response
     */
    public function index(): Response
    {
        //displayed logged home if logged
        if ($this->isGranted('IS_AUTHENTICATED_FULLY')) {
             return $this->redirect($this->generateUrl('stock_home'), Response::HTTP_MOVED_PERMANENTLY);
        }

        // display page from twig template
        return $this->render('index.html.twig');
    }

    /**
     * Change locale
     *
     * @Route(
     *     {
     *     "fr": "/langue.html",
     *     "en": "/locale.html"
     *     },
     *     name="change_locale",
     *     methods={"GET"}
     * )
     * @param LocaleManager $localeService
     *
     * @return Response
     */
    public function changeLocale(LocaleManager $localeService)
    {
        // Return current route changed to other lang
        return $localeService->changeSelectedLocale();
    }

    /**
     * Test to access denied pages
     *
     * @Route(
     *     "/test/test.html",
     *     name="index_logged",
     *     methods={"GET"}
     * )
     * @return Response
     */
    public function Temp(): Response
    {
        //Security !
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // display page from twig template
        //return $this->render('TEMP.html.twig', []);

        //test access denied
        return $this->render('security/access_denied.html.twig');
    }
}
