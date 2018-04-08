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

use App\Common\Traits\Client\BrowserTrait;
use App\SiteConfig;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;

/**
 * @author Frogg <admin@frogg.fr>
 */
class LocaleService
{
    use BrowserTrait;

    /** @var null|Request */
    private $request;
    /** @var RouterInterface */
    private $router;

    /** @var string */
    private $newLocale;
    /** @var string */
    private $currentLocale;
    /** @var string */
    private $uri;

    /**
     * LocaleService constructor.
     * @param RouterInterface $router
     * @param RequestStack    $requestStack
     */
    public function __construct(RouterInterface $router, RequestStack $requestStack)
    {
        $this->request = $requestStack->getMasterRequest();
        $this->router = $router;
    }

    /**
     * Change locale and redirect by user choice
     *
     * @return Response
     */
    public function changeSelectedLocale(): Response
    {
        var_dump("changeSelectedLocale");
        //exit();

        return $this
            ->setRequestedLocale()
            ->setLocale()
            ->doRedirectResponse($this->request->headers->get('referer'));
    }

    /**
     * Restore default locale to user when coming back on the website
     */
    public function changeDefaultLocale(): void
    {
        var_dump("changeDefaultLocale");
        //exit();

        $uri = $this->request->getRequestUri();

        // do stuff only for the master request & not a /_ request (symfony debug stuff)
        if (substr($uri, 0, 2) !== "/_") {
            $this
                ->setUserLocale()
                ->setLocale();

            var_dump("TEST !".$this->currentLocale." VS ".$this->newLocale);
            //exit();

            // Check locale
            if ($this->currentLocale !== $this->newLocale) {
                $this->doRedirectResponse($uri);
            }
        }
    }

    /**
     * redirect response
     *
     * @return Response
     */
    private function setResponse(): Response
    {
        var_dump("SET RESPONSE TO ");
        var_dump($this->uri);

        return new RedirectResponse($this->uri, Response::HTTP_MOVED_PERMANENTLY);
    }

    /**
     * do the redirect based on the uri
     * @param string $uri
     *
     * @return Response
     */
    private function doRedirectResponse(string $uri): Response
    {
        return $this
            ->setCookie()
            ->setSystemLocale()
            ->setRedirectUri($uri)
            ->setResponse();
    }

    /**
     * Set Uri from referer
     * @param string $originalUri
     *
     * @return LocaleService
     */
    private function setRedirectUri(string $originalUri): LocaleService
    {

        var_dump("ORIGINAL URI");
        var_dump($originalUri);

        //get route from uri
        $route = $this->router->match(
            preg_replace(
                '/(http[s]?:\/\/[^\/]*)/',
                '',
                $originalUri
            )
        )['_route'];


        var_dump("BEFORE GENERATION");
        var_dump($route);
        var_dump($this->newLocale);

        //set uri from route (as lang has been set the new route will be the same but translated
        $this->uri = $this->router->generate($route, ['_locale' => $this->newLocale]);

        var_dump("AFTER GENERATION");
        var_dump($this->uri);

        var_dump("LOCALE FROM REQUEST");
        var_dump($this->request->getLocale());

        /* BIDOUILLE !!! */
        var_dump("SEARCH "."/".$this->currentLocale."/");
        var_dump("REPLACE "."/".$this->newLocale."/");
        var_dump("IN ".$this->uri);
        $this->uri = str_replace("/".$this->currentLocale."/", "/".$this->newLocale."/", $this->uri);
        var_dump("RESULT ".$this->uri);

        // fluent
        return $this;
    }

    /**
     * set current locale
     *
     * @return LocaleService
     */
    private function setLocale(): LocaleService
    {
        // get current local
        $this->currentLocale = $this->request->getLocale();

        var_dump("Current locale");
        var_dump($this->currentLocale);


        // fluent
        return $this;
    }

    /**
     * get user locale from cookie or browser or default
     *
     * @return LocaleService
     */
    private function setUserLocale(): LocaleService
    {
        // check if lang are set as arguments, then check in cookies
        $this->newLocale = $_COOKIE[SiteConfig::COOKIELOCALENAME] ?? $this->getUserBrowserLangs() ?? $this->request->getDefaultLocale();

        // fluent
        return $this;
    }

    /**
     * set user locale
     *
     * @return LocaleService
     */
    private function setRequestedLocale(): LocaleService
    {
        // get selected locale from user choice
        $this->newLocale = $this->request->get(SiteConfig::COOKIELOCALENAME);

        var_dump("newLocale");
        var_dump($this->newLocale);

        // fluent
        return $this;
    }

    /**
     * set user cookie with locale
     *
     * @return LocaleService
     */
    private function setCookie(): LocaleService
    {
        // Update cookiez
        setcookie(SiteConfig::COOKIELOCALENAME, $this->newLocale, time() + (SiteConfig::COOKIELOCALEVALIDITY * 24 * 60 * 60), "/"); // 24 * 60 * 60 = 86400 = 1 day

        // fluent
        return $this;
    }

    /**
     * set system locale
     *
     * @return LocaleService
     */
    private function setSystemLocale(): LocaleService
    {
        // some logic to determine the $locale
        $this->request->setLocale($this->newLocale);

        // Update session
        $this->request->getSession()->set('_locale', $this->newLocale);

        // Update default locale
        $this->request->setDefaultLocale($this->newLocale);

        // fluent
        return $this;
    }
}
