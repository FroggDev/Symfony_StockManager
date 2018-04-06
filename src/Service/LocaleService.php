<?php
namespace App\Service;

use App\Common\Traits\Client\BrowserTrait;
use App\SiteConfig;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;


/**
 * Class LocaleService
 * @package App\Service
 */
class LocaleService
{
    use BrowserTrait;

    private $request;
    private $event;

    private $locale;
    private $currentLocale;
    private $uri;

    /**
     * LocaleService constructor.
     * @param null|Request $request
     * @param null|GetResponseEvent $event
     */
    public function __construct(Request $request, ?GetResponseEvent $event)
    {
        $this->request = $request;
        $this->event = $event;
    }

    /**
     * Change locale and redirect
     * @return string
     */
    public function changeSelectedLocale(): string
    {
        return $this
            ->setRequestedLocale()
            ->setLocale()
            ->setCookie()
            ->setSystemLocale()
            ->setUriFromReferer()
            ->getUri();
    }

    /**
     * Restore default locale to user when coming back on the website
     */
    public function changeDefaultLocale(): void
    {
        $this->getUriFromRequest();

        # do stuff only for the master request & not a /_ request (symfony debug stuff)
        if ($this->event->isMasterRequest() && substr($this->uri, 0, 2) !== "/_") {

            $this
                ->getUserLocale()
                ->setLocale();

            # Check locale
            if ($this->currentLocale != $this->locale) {
                $this
                    ->setCookie()
                    ->setSystemLocale()
                    ->setUri()
                    ->setResponse();
            }
        }
    }

    /**
     * redirect response
     */
    private function setResponse() : void
    {
        $this->event->setResponse(new RedirectResponse($this->uri, Response::HTTP_MOVED_PERMANENTLY));
    }

    /**
     * Set URI from Referer URI
     * @return LocaleService
     */
    private function setUriFromReferer()
    {
        # get the uri to redirect
        preg_match("/^http[s]?:\/\/[^\/]+\/(.*)/", $this->request->headers->get('referer'), $matches);
        $this->uri = "/".$matches[1];

        # fluent
        return  $this->setUri();
    }

    /**
     * Set modified with locale Uri
     * @return LocaleService
     */
    private function setUri()
    {
        # if start with /en for example then replace $uri for new $locale
        if (substr($this->uri, 0, 3) === "/$this->currentLocale") {
            $this->uri = preg_replace("/^\/$this->currentLocale/", "/$this->locale", $this->uri);
        } else {
            #else add $newlocale to url
            $this->uri = preg_replace("/^\//", "/$this->locale", $this->uri);
        }

        # fluent
        return $this;
    }

    /**
     * Get URI from Request
     * @return $this
     */
    private function getUriFromRequest()
    {
        # get current uri
        $this->uri = $this->request->getRequestUri();

        # fluent
        return $this;
    }

    /**
     * set current locale
     * @return LocaleService
     */
    private function setLocale(): LocaleService
    {
        # get current local
        $this->currentLocale = $this->request->getLocale();

        # fluent
        return $this;
    }

    /**
     * get user locale from cookie or browser or default
     * @return LocaleService
     */
    private function getUserLocale(): LocaleService
    {
        # check if lang are set as arguments, then check in cookies
        $this->locale = $_COOKIE[SiteConfig::COOKIELOCALENAME] ?? $this->getUserBrowserLangs() ?? $this->request->getDefaultLocale();

        # fluent
        return $this;
    }

    /**
     * set user locale
     * @return LocaleService
     */
    private function setRequestedLocale(): LocaleService
    {
        # get selected locale from user choice
        $this->locale = $this->request->get(SiteConfig::COOKIELOCALENAME);

        # fluent
        return $this;
    }

    /**
     * set user cookie with locale
     * @return LocaleService
     */
    private function setCookie(): LocaleService
    {
        # Update cookiez
        setcookie(SiteConfig::COOKIELOCALENAME, $this->locale, time() + (SiteConfig::COOKIELOCALEVALIDITY * 24 * 60 * 60), "/"); // 24 * 60 * 60 = 86400 = 1 day

        # fluent
        return $this;
    }

    /**
     * set system locale
     * @return LocaleService
     */
    private function setSystemLocale(): LocaleService
    {
        # some logic to determine the $locale
        $this->request->setLocale($this->locale);

        # fluent
        return $this;
    }

    /**
     * get the URI
     * @return string
     */
    private function getUri() : string
    {
        return $this->uri;
    }
}