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
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;

/**
 * @author Frogg <admin@frogg.fr>
 */
class LocaleManager
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
    /** @var bool */
    private $addCookie = false;

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
        //As requested by user, set cookie for it
        $this->addCookie = true;

        return $this
            ->setRequestedLocale()
            ->setCurrentLocale()
            ->setSystemLocale()
            ->setRedirectUri($this->request->headers->get('referer'))
            ->setResponse();
    }

    /**
     * Restore default locale to user when coming back on the website
     *
     * @return Response
     */
    public function changeDefaultLocale()
    {
        //init locales
        $this->setUserLocale();

        $this->uri = $this->router->generate('index', ['_locale' => $this->newLocale]);

        return $this->setResponse();
    }

    /**
     * redirect response
     *
     * @return Response
     */
    private function setResponse(): Response
    {
        $response = new RedirectResponse($this->uri, Response::HTTP_MOVED_PERMANENTLY);

        if ($this->addCookie) {
            $response->headers->setCookie(
                new Cookie(
                    SiteConfig::COOKIELOCALENAME,
                    $this->newLocale,
                    // 24 * 60 * 60 = 86400 = 1 day
                    time() + (SiteConfig::COOKIELOCALEVALIDITY * 24 * 60 * 60)
                )
            );
        }

        return $response;
    }

    /**
     * Set Uri from referer
     * @param string $originalUri
     *
     * @return LocaleManager
     */
    private function setRedirectUri(string $originalUri): LocaleManager
    {

        //get route from uri
        $route = $this->router->match(
            preg_replace(
                '/(http[s]?:\/\/[^\/]*)/',
                '',
                $originalUri
            )
        )['_route'];


        //set uri from route (as lang has been set the new route will be the same but translated
        $this->uri = $this->router->generate($route, ['_locale' => $this->newLocale]);

        // fluent
        return $this;
    }

    /**
     * set current locale
     *
     * @return LocaleManager
     */
    private function setCurrentLocale(): LocaleManager
    {
        // get current local
        $this->currentLocale = $this->request->getLocale();

        // fluent
        return $this;
    }

    /**
     * get user locale from cookie or browser or default
     *
     * @return LocaleManager
     */
    private function setUserLocale(): LocaleManager
    {
        // check if lang are set as arguments, then check in cookies
        $this->newLocale = $this->request->cookies->get(SiteConfig::COOKIELOCALENAME) ?? $this->getUserBrowserLangs() ?? $this->request->getDefaultLocale();

        // fluent
        return $this;
    }

    /**
     * set user locale
     *
     * @return LocaleManager
     */
    private function setRequestedLocale(): LocaleManager
    {
        // get selected locale from user choice
        $this->newLocale = $this->request->get(SiteConfig::COOKIELOCALENAME);

        // fluent
        return $this;
    }

    /**
     * set system locale
     *
     * @return LocaleManager
     */
    private function setSystemLocale(): LocaleManager
    {
        if ($this->newLocale) {
            // some logic to determine the $locale
            $this->request->setLocale($this->newLocale);
        }

        // Update session
        //$this->request->getSession()->set('_locale', $this->newLocale);

        // Update default locale
        //$this->request->setDefaultLocale($this->newLocale);

        // fluent
        return $this;
    }
}
