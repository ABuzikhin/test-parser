<?php

declare(strict_types=1);

namespace App\Service;

use HeadlessChromium\Browser\ProcessAwareBrowser;
use HeadlessChromium\BrowserFactory;
use HeadlessChromium\Exception\NavigationExpired;
use HeadlessChromium\Exception\OperationTimedOut;
use HeadlessChromium\Page;

class Browser
{
    private const MAX_TRIES_COUNT = 3;

    private ?ProcessAwareBrowser $browser = null;

    public function getBrowser(): ProcessAwareBrowser
    {
        if (null === $this->browser) {
            $this->init();
        }

        return $this->browser;
    }

    public function getPageContent(string $url): mixed
    {
        $page = $this->getBrowser()->createPage();
        $try  = 1;

        while ($try <= self::MAX_TRIES_COUNT) {
            try {
                $navigation = $page->navigate($url);
                $navigation->waitForNavigation(Page::LOAD, 30_000);

                break;
            } catch (OperationTimedOut|NavigationExpired $exception) {
                ++$try;

                if ($try > self::MAX_TRIES_COUNT) {
                    return null;
                }
            }
        }

        $evaluation = $page->evaluate('document.documentElement.innerHTML');

        return $evaluation->getReturnValue(30_000);

    }

    private function init(): void
    {
        $browserFactory = new BrowserFactory('chromium');

        $this->browser = $browserFactory
            ->createBrowser(
                [
                    'noSandbox'     => true,
                    'enableImages'  => true,
                    'headless'      => true,
                    'noProxyServer' => true,
                    'userAgent'     => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/115.0.0.0 Safari/537.36',
                ]
            )
        ;
    }
}