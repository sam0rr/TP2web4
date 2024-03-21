<?php namespace Models\Core;

use RuntimeException;
use stdClass;
use Zephyrus\Application\Bootstrap;
use Zephyrus\Application\Configuration;
use Zephyrus\Application\Flash;
use Zephyrus\Application\Localization;
use Zephyrus\Application\Views\PugEngine;
use Zephyrus\Core\Session;
use Zephyrus\Database\Core\Database;
use Zephyrus\Exceptions\LocalizationException;
use Zephyrus\Exceptions\Session\SessionDatabaseStructureException;
use Zephyrus\Exceptions\Session\SessionDatabaseTableException;
use Zephyrus\Exceptions\Session\SessionDisabledException;
use Zephyrus\Exceptions\Session\SessionFingerprintException;
use Zephyrus\Exceptions\Session\SessionHttpOnlyCookieException;
use Zephyrus\Exceptions\Session\SessionLifetimeException;
use Zephyrus\Exceptions\Session\SessionPathNotExistException;
use Zephyrus\Exceptions\Session\SessionPathNotWritableException;
use Zephyrus\Exceptions\Session\SessionRefreshRateException;
use Zephyrus\Exceptions\Session\SessionRefreshRateProbabilityException;
use Zephyrus\Exceptions\Session\SessionStorageModeException;
use Zephyrus\Exceptions\Session\SessionSupportedRefreshModeException;
use Zephyrus\Exceptions\Session\SessionUseOnlyCookiesException;
use Zephyrus\Network\Request;
use Zephyrus\Network\Response;
use Zephyrus\Network\Router;
use Zephyrus\Network\Router\RouteRepository;
use Zephyrus\Utilities\FileSystem\Directory;
use Locale;

final class Application
{
    private static ?Application $instance = null;

    private Request $request;
    private ?PugEngine $pugEngine = null;
    private ?Session $session = null;

    private array $supportedLanguages = [];

    public static function initiate(Request $request): Router
    {
        self::$instance = new self();
        self::$instance->request = $request;
        self::$instance->initializeNativeHelpers();
        self::$instance->initializeSession();
        DatabaseSession::getInstance()->start();
        self::$instance->initializeLocalization();
        self::$instance->initializeErrorHandling();
        return self::$instance->initializeRouter();
    }

    public static function getInstance(): self
    {
        if (is_null(self::$instance)) {
            throw new RuntimeException("Application instance must first be initialized with [Application::initiate()].");
        }
        return self::$instance;
    }

    /**
     * Method used when an error is detected during argument overrides. This will log a security event and display an
     * error message for the user. If no redirect url is given, it will return to the referer.
     *
     * @param string|null $redirectUrl
     * @return Response
     */
    public function trapRouteArgumentError(?string $redirectUrl = null): Response
    {
        if (is_null($redirectUrl)) {
            $redirectUrl = !empty($this->request->getReferer()) ? $this->request->getReferer() : "/";
        }
        Flash::error(localize("errors.wrong_argument_access"));
        return Response::builder()->redirect($redirectUrl);
    }

    /**
     * Retrieves the loaded PugEngine (used for rendering Pug files). Proceeds with an initialization if the engine was
     * not yet initiated (useful to avoid unnecessary class instanciations).
     *
     * @return PugEngine
     */
    public function getPugEngine(): PugEngine
    {
        if (is_null($this->pugEngine)) {
            $this->initializePugEngine();
        }
        return $this->pugEngine;
    }

    public function getRequest(): Request
    {
        return $this->request;
    }

    /**
     * This method retrieves the supported language object for the application (which contains the properties locale,
     * lang_code, country_code, flag_emoji, country and lang).
     *
     * @return array
     */
    public function getSupportedLanguages(): array
    {
        if (empty($this->supportedLanguages)) {
            $languages = [];
            $supportedLocales = Configuration::getApplication('locales', ['fr_CA']);
            $installedLanguages = Localization::getInstance()->getInstalledLanguages();
            foreach ($supportedLocales as $locale) {
                if (key_exists($locale, $installedLanguages)) {
                    $languages[] = $installedLanguages[$locale];
                }
            }
            $this->supportedLanguages = $languages;
        }
        return $this->supportedLanguages;
    }

    public function getCurrentLanguage(): stdClass
    {
        return Localization::getInstance()->getLoadedLanguage();
    }

    /**
     * Initiate the session only if needed.
     *
     * @return Database
     */
    public function getDatabase(): Database
    {
        return DatabaseSession::getInstance()->getDatabase();
    }

    public function getSession(): Session
    {
        return $this->session;
    }

    /**
     * @throws SessionDatabaseStructureException
     * @throws SessionDatabaseTableException
     * @throws SessionStorageModeException
     * @throws SessionRefreshRateProbabilityException
     * @throws SessionLifetimeException
     * @throws SessionDisabledException
     * @throws SessionRefreshRateException
     * @throws SessionPathNotExistException
     * @throws SessionUseOnlyCookiesException
     * @throws SessionPathNotWritableException
     * @throws SessionSupportedRefreshModeException
     * @throws SessionFingerprintException
     * @throws SessionHttpOnlyCookieException
     */
    private function initializeSession(): void
    {
        $configurations = Configuration::getSession();
        //$configurations['save_path'] = ROOT_DIR . '/temp/sessions';
        $this->session = new Session($configurations);
        $this->session->setRequest($this->request);
        $this->session->start();
    }

    private function initializeLocalization(): void
    {
        try {
            $locale = $_COOKIE['lang'] // Seek browser preference first
                ?? 'fr_CA'; // Default app language
            Locale::setDefault($locale);
            Localization::getInstance()->start($locale);
        } catch (LocalizationException $e) {

            // If engine cannot properly start an exception will be thrown and must be corrected to use this
            // feature. Common errors are syntax error in json files. The exception messages should be explicit
            // enough.
            die($e->getMessage());
        }
    }

    private function initializeRouter(): Router
    {
        $rootControllerPath = ROOT_DIR . '/app/Controllers';
        $routeRepository = new RouteRepository();
        if (!Directory::exists($rootControllerPath)) {
            return new Router($routeRepository);
        }

        $lastUpdate = (new Directory($rootControllerPath))->getLastModifiedTime();
        if ($routeRepository->isCacheOutdated($lastUpdate)) {
            Bootstrap::initializeControllerRoutes($routeRepository);
            $routeRepository->cache();
        } else {
            $routeRepository->initializeFromCache();
        }
        return new Router($routeRepository);
    }

    private function initializeNativeHelpers(): void
    {
        require_once(Bootstrap::getHelperFunctionsPath());
        require_once(ROOT_DIR . '/app/formats.php');
        require_once(ROOT_DIR . '/app/functions.php');
    }

    private function initializePugEngine(): void
    {
        $this->pugEngine = new PugEngine();
        $this->pugEngine->share("pug_flash", function () {
            return Flash::readAll();
        });
    }

    private function initializeErrorHandling(): void
    {
        CustomErrorHandler::initializeFormExceptions();
    }
}
