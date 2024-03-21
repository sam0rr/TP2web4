<?php namespace Controllers;

use Zephyrus\Application\Controller as BaseController;
use Models\Core\Application;
use Zephyrus\Application\Configuration;
use Zephyrus\Application\Localization;
use Zephyrus\Network\Response;
use Zephyrus\Security\ContentSecurityPolicy;
use Zephyrus\Security\SecureHeader;

abstract class Controller extends BaseController
{
    public function before(): ?Response
    {
        return parent::before();
    }

    public function render(string $page, array $args = []): Response
    {
        $projectName = Configuration::getApplication('project');
        $arguments = array_merge($args, [

            /**
             * Previous page the user accessed.
             */
            "referer" => $this->request->getReferer(),

            /**
             * Keep the defined controller Root attribute (for easier navigation).
             */
            "route_root" => $this->request->getRouteDefinition()->getRouteRoot(),

            /**
             * String representation of the currently loaded language (e.g. français (Canada)).
             */
            "loaded_locale" => Localization::getInstance()->getLoadedLocale(),

            /**
             * List of all installed and available languages.
             */
            "installed_languages" => Application::getInstance()->getSupportedLanguages(),

            /**
             * Name of the application that should be used within every page as browser title.
             */
            "project_name" => $projectName
        ]);

        $this->setRenderEngine(Application::getInstance()->getPugEngine());
        return parent::render($page, $arguments);
    }

    protected function setupSecurityHeaders(SecureHeader $secureHeader): void
    {
        $csp = new ContentSecurityPolicy();
        $csp->setDefaultSources(["'self'"]);
        $csp->setFontSources(["'self'", 'https://fonts.googleapis.com', 'https://fonts.gstatic.com']);
        $csp->setStyleSources(["'self'", 'https://fonts.googleapis.com', ContentSecurityPolicy::UNSAFE_INLINE]);
        $csp->setScriptSources(["'self'", 'https://ajax.googleapis.com', 'https://maps.googleapis.com',
            'https://www.google-analytics.com', 'https://cdn.jsdelivr.net', ContentSecurityPolicy::UNSAFE_EVAL]); // heatjs requires eval ...
        $csp->setChildSources(["'self'"]);
        $csp->setWorkerSources(["blob:"]);
        $csp->setConnectSources(["'self'", 'https://api.mapbox.com', 'https://events.mapbox.com']);

        // Allow Google authenticator image generation
        $csp->setImageSources(["'self'", 'blob:', 'data:', 'https://chart.googleapis.com', 'https://api.qrserver.com']);
        $csp->setBaseUri([$this->request->getUrl()->getBaseUrl()]);

        // Add custom CSP
        $secureHeader->setContentSecurityPolicy($csp);
    }
}
