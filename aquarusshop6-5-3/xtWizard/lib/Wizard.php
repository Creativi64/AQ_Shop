<?php
/*
 #########################################################################
 #                       xt:Commerce Shopsoftware
 # ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 #
 # Copyright 2021 xt:Commerce GmbH All Rights Reserved.
 # This file may not be redistributed in whole or significant part.
 # Content of this file is Protected By International Copyright Laws.
 #
 # ~~~~~~ xt:Commerce Shopsoftware IS NOT FREE SOFTWARE ~~~~~~~
 #
 # https://www.xt-commerce.com
 #
 # ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 #
 # @copyright xt:Commerce GmbH, www.xt-commerce.com
 #
 # ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 #
 # xt:Commerce GmbH, Maximilianstrasse 9, 6020 Innsbruck
 #
 # office@xt-commerce.com
 #
 #########################################################################
 */

class Wizard extends Singleton {
	
	// Define available wizard types
	const WIZARD_TYPE_INSTALL = 1;
	const WIZARD_TYPE_UPDATE = 2;
	
	// Defined some more needed constants
	const SCRIPTS_DIR = 'scripts';
	
	// Nav cosntants
	const PARAM_PAGE = 'wpage';
	const PARAM_SCRIPT = 'script';
	const PARAM_LANG = 'install_lang';
	const PARAM_NEXT = 'next';
	const PARAM_WIZARD_TYPE = 'WizardType';
	const PARAM_TITLE = 'title';
	const PARAM_MSG = 'msg';
	
	const SESSION_KEY_WIZARD_LANG = 'WizardLang';
	
	const WIZARD_DEFAULT_LANGUAGE = 'de';
	
	const UPDATE_FILE_NAME = 'update.php';
	const UPDATE_FILE_PAGES = 'pages.xml';

	const MIN_VERSION_SUPPORTED = '4.2.00';
	
	/**
	 * Store version
	 * @var number
	 */
	protected $_storeVersion = 0;
	/**
	 * Available wizard languages
	 * @var array
	 */
	protected $_availableLanguages = array('en', 'de');
	
	/**
	 * Current language
	 * @var string
	 */
	protected $_currentLanguage = null;
	
	/**
	 * Array with objects, created from script files in SCRIPTS_DIR
	 * @var array <string, ExecutableScript>
	 */
	protected $_loadedScripts = array();
	
	/**
	 * Current wizard page
	 * @var WizardPage
	 */
	protected $_currentPage = null;
	
	/**
	 * Current script being executed
	 * @var IExecutableScript
	 */
	protected $_currentScript = null;
	
	/**
	 * Params that will be persisted when Wizard::buildUrl() is invoked
	 * @var array
	 */
	protected $_persistParams = array(
		self::PARAM_LANG,
		self::PARAM_PAGE,
		self::PARAM_SCRIPT,
		self::PARAM_WIZARD_TYPE,
	);
	
	/**
	 * Template parser
	 * @var Smarty
	 */
	protected $_templateParser = null;
	
	/**
	 * Wizard params
	 * @var array
	 */
	protected $_wizardParams = array();
	
	/**
	 * Determine if the system has been already installed
	 * @var boolean
	 */
	protected $_systemInstalled = true;
	
	/**
	 * Database object
	 * @var ADOConnection
	 */
	protected $_db = null;
	
	/**
	 * Class constructor
	 */
	protected function __construct() {
		WizardErrorHandler::getInstance();
		parent::__construct();
	}
	
	/**
	 * Loads the scripts from Wizard::SCRIPTS_DIR dir
	 * @return Wizard
	 */
	public function loadScripts() {
		$scriptsPath = ROOT_DIR_PATH . self::SCRIPTS_DIR;
		// Register the startpage script
		$this->registerScript(new StartPageScript());
		$directories = glob($scriptsPath . DS . '*');
		
		foreach ($directories as $dir) {
			if (!is_dir($dir)) {
				continue;
			}
			$updateFile = $dir . DS . self::UPDATE_FILE_NAME;
			$pagesFile = $dir . DS . self::UPDATE_FILE_PAGES;
			
			if (!file_exists($updateFile) || !file_exists($pagesFile)) {
				WizardLogger::getInstance()->log(sprintf('File %s or %s doesn\'t exists', $updateFile, $pagesFile));
				continue;
			}
			includeScript($updateFile);
		}
		return $this;
	}
	
	/**
	 * Add param name that will be persisted when building an url.
	 * @param string $paramName
	 * @return Wizard
	 */
	public function addPersistParam($paramName) {
		$this->_persistParams[] = $paramName;
		return $this;
	}
	
	/**
	 * Builds a url from given params
	 * @param array $params
	 * @return string
	 */
	public function buildUrl($params) {
		foreach ($this->_persistParams as $param) {
			if (($paramValue = getParamDefault($param, false)) && !isset($params[$param])) {
				$params[$param] = $paramValue;
			}
		}
		$url = _SRV_WEB . 'xtWizard/index.php?' . http_build_query($params);
		return $url;
	}
	
	/**
	 * Get current wizard language
	 * @return string
	 */
	public function getCurrentLanguage() {
		return $this->_currentLanguage;
	}
	
	/**
	 * Sets current page
	 * @param int $page
	 * @throws WizardException
	 * @return Wizard
	 */
	protected function setCurrentPage($page) {
		if (null !== $this->_currentPage) {
			throw new WizardException('Page has been set already', WizardException::ERROR_LEVEL_WARNING);
		}
		$this->_currentPage = $page;
		return $this;
	}

    /**
     * Returns current page
     * @return WizardPage
     */
	public function getCurrentPage() {
		return $this->_currentPage;
	}
	
	/**
	 * Check if system is installed
	 * @return boolean
	 */
	public function isSystemInstalled() {
		return $this->_systemInstalled;
	}
	
	/**
	 * Sets current script that is being executed
	 * @param ExecutableScript|null $script
	 * @throws WizardException
	 * @return Wizard
	 */
	protected function setCurrentScript($script) {
		if (null !== $this->_currentScript) {
			throw new WizardException('Current script has been already set', WizardException::ERROR_LEVEL_WARNING);
		}
		
		if (!isset($this->_loadedScripts[$script])) {
			throw new WizardException("Script $script not found", WizardException::ERROR_LEVEL_FATAL);
		}
		$this->_currentScript = $this->_loadedScripts[$script];
		return $this;
	}
	
	/**
	 * Get current script
	 * @return IExecutableScript
	 */
	public function getCurrentScript() {
		return $this->_currentScript;
	}
	
	/**
	 * Register new script
	 * @param ExecutableScript $script
	 * @return Wizard
	 */
	public function registerScript(ExecutableScript $script) {
		$this->_loadedScripts[$script->getUniqueId()] = $script;
		return $this;
	}
	
	/**
	 * Get all loaded scripts
	 * @return array <string, ExecutableScript>
	 */
	public function getLoadedScripts() {
		return $this->_loadedScripts;
	}
	
	/**
	 * Set wizard type
	 * @param int $type
	 * @return Wizard
	 */
	public function setWizardType($type) {
		$_SESSION[self::PARAM_WIZARD_TYPE] = $type;
		return $this;
	}
	
	public function getWizardType() {
		if (($type = getParamDefault(self::PARAM_WIZARD_TYPE, false))) {
			return $type;
		}
		return isset($_SESSION[self::PARAM_WIZARD_TYPE]) ? $_SESSION[self::PARAM_WIZARD_TYPE] : null;
	}

    /**
     * Main function
     * @return Wizard
     * @throws WizardException
     */
	public function run() {
		try {
			$this->initWizard();
			// Load scripts
			$this->loadScripts();
			// Set current page
			$this->setCurrentPage(getParamDefault(self::PARAM_PAGE, 'index'));
			$this->setCurrentScript(getParamDefault(self::PARAM_SCRIPT, 'StartPageScript'));
			$this->dispatch();
			// If no error occured commit the changes
			if (!(null === $this->_db)) {
				$this->getDatabaseObject()->Execute('COMMIT;');
			}
		} catch (WizardException $e) {
			// Rollback all changes
			if (!(null === $this->_db)) {
				$this->getDatabaseObject()->Execute('ROLLBACK;');
			}
			// Build the url for info page
			$url = $this->buildUrl(array(
				self::PARAM_SCRIPT => 'StartPageScript',
				self::PARAM_PAGE => 'info',
				self::PARAM_TITLE => 'Error',
				self::PARAM_MSG => $e->getMessage()
			));
			// Redirect to info page
			$this->redirect($url);
		} catch (Exception $e) {
			if (!(null === $this->_db)) {
				$this->getDatabaseObject()->Execute('ROLLBACK;');
			}
			var_dump($e);
			die;
		}
		return $this;	
	}

    /**
     * @throws WizardException
     */
	protected function dispatch() {
		
		if (getParamDefault(self::PARAM_NEXT, false)) {
			if (!$this->_currentScript->hasNextPage($this->_currentPage)) {
				// @TODO Fix
				throw new WizardException('End', WizardException::ERROR_LEVEL_FATAL);
			}
			/** @var WizardPage $page */
			$page = $this->_currentScript->getNextPage($this->_currentPage);
			$this->_currentPage = $page->getPageId();
		} else {
			$page = $this->_currentScript->getPage($this->_currentPage);
		}
		
		$page->process();
	}

    /**
     * Init wizard dependencies
     * @return Wizard
     * @throws WizardException
     */
	protected function initWizard() {
		@session_start();

        $this->initLanguage();
        $this->initTemplateParser();
        $this->initDatabaseConnection();

        if(!defined('_SYSTEM_VERSION'))
        {
            // sollte nicht vorkommen, aber
            // der 4.2>5 updater mit dem 4er code liest nicht die versioninfo
            $this->_storeVersion = 0;
            if(file_exists(_SRV_WEBROOT.'versioninfo.php'))
            {
                include _SRV_WEBROOT.'versioninfo.php';
                $this->_storeVersion = defined("_SYSTEM_VERSION") ? _SYSTEM_VERSION : self::MIN_VERSION_SUPPORTED;
            }
        }
		else if (version_compare(5,_SYSTEM_VERSION) == 1) // xt4
		{
		    if(file_exists(_SRV_WEBROOT.'versioninfo.php'))
            {
                include _SRV_WEBROOT.'versioninfo.php';
                $this->_storeVersion = defined("_SYSTEM_VERSION") ? _SYSTEM_VERSION : self::MIN_VERSION_SUPPORTED;
            }
            else {
		        $storeVersion = $this->getDatabaseObject()->GetOne('SELECT config_value FROM '.TABLE_CONFIGURATION.' WHERE config_key = ?', array('_SYSTEM_VERSION'));
                $this->_storeVersion = $storeVersion ? $storeVersion : self::MIN_VERSION_SUPPORTED;
            }


		}
		else { // xt5
            $this->_storeVersion = defined("_SYSTEM_VERSION") ? _SYSTEM_VERSION : self::MIN_VERSION_SUPPORTED;
		    $this->checkLicense();
		}


		return $this;
	}
	
	public function checkLicense() {
    // lic check
    $_lic = _SRV_WEBROOT.'lic/license.txt';
    if ( ! file_exists($_lic)) die ('- xt:Commerce License File missing (license.txt) - lic41');
    $val_line = '';
    $_file_content = file($_lic);
    foreach ($_file_content as $bline)
    {
        if (strpos($bline, 'updatesuntil:') !== FALSE)
        {
            $val_line = $bline;
            break;
        }
    }
    
    $val_line = explode(' ',$val_line);
    $_updates_until = '';
    $_updates_until = trim($val_line[1]);
    unset($val_line, $_lic);
    // end license check

    // lic check  end
	}
	
	/**
	 * Init database connection
	 * @return Wizard
	 */
	protected function initDatabaseConnection() {
		global $db;
		if (null === $db) {
			$this->_systemInstalled = false;
            return $this;
		}
		$this->_db = $db;
		$this->_db->Execute('START TRANSACTION;');
		return $this;
	}
	
	/**
	 * Get database object
	 * @throws WizardException
	 * @return ADOConnection
	 */
	public function getDatabaseObject() {
		if (null === $this->_db) {
			throw new WizardException('Datenbankverbindung konnte nicht instanziiert werden.<br />Bitte  öffnen Sie die Datei conf/config.php und prüfen Sie, ob die Werte für die Datenbankverbindung erzeugt und vollständig geschrieben wurden.<br />Laden Sie dann diese Seite neu um den Schritt neu zu starten.<br /><br />Database was not yet instantiated, please reload page', WizardException::ERROR_LEVEL_FATAL);
		}
		return $this->_db;
	}
	
	/**
	 * Get store version
	 * @return number
	 */
	public function getStoreVersion() {
		return $this->_storeVersion;
	}
	
	/**
	 * Includes needed classes for smarty
     * @return Wizard
	 */
	protected function initTemplateParser() {
		global $_SYSTEM_INSTALL_SUCCESS;
		
		return $this;
	}

    /**
     * Init wizard language
     * @return Wizard
     * @throws WizardException
     */
	protected function initLanguage() {
		if (!isset($_SESSION[self::SESSION_KEY_WIZARD_LANG])) {
			
			$lang = isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2) : '';
			if (in_array($lang, $this->_availableLanguages)) {
				$_SESSION[self::SESSION_KEY_WIZARD_LANG] = $lang;
			} else {
				$_SESSION[self::SESSION_KEY_WIZARD_LANG] = self::WIZARD_DEFAULT_LANGUAGE;
			}
		}
		
		$lang = getParamDefault(self::PARAM_LANG, $_SESSION[self::SESSION_KEY_WIZARD_LANG]);
		
		if (in_array($lang, $this->_availableLanguages)) {
			$this->_currentLanguage = $_SESSION[self::SESSION_KEY_WIZARD_LANG] = $lang;
		}
		
		$langFile = ROOT_DIR_PATH . 'lang' . DS . $this->getCurrentLanguage() . '.yml';
		if (!file_exists($langFile)) {
			throw new WizardException("Language file for language '{$this->_currentLanguage}' was not found", WizardException::ERROR_LEVEL_FATAL);
		}
		
		$contents = file_get_contents($langFile);
		$languageParts = explode("\n", $contents);
		
		foreach ($languageParts as $part) {
			$delimiterPos = strpos($part, "=", 0);
			
			if ($delimiterPos === false) {
				continue;
			}
			$systemParts = substr($part, 0, $delimiterPos);
			$value = substr($part, $delimiterPos+1);
			
			list($plugin, $type, $key) = explode(".", $systemParts);
			if (!defined($key)) {
				$value = str_replace(array("\r", "\n"), '', $value);
				define($key, $value);
			}
		}

        /*
        $lc = new language_content();
        $lc->environment_language = $this->getCurrentLanguage();
        $lc->_getLanguageContent('admin');
        $lc->_getLanguageContent('store');
        */
		
		return $this;
	}
	
	/**
	 * Check if current request is POST
	 * @return boolean
	 */
	public function isPost() {
		return (strtoupper($_SERVER['REQUEST_METHOD']) === 'POST');
	}
	
	public function stop() {
		// Rollback
		// Display error messages and stats
	}
	
	public function redirect($url) {
		header("Location: $url");
		die;
	}
	
	/**
	 * @return Wizard
	 */
	public static function getInstance() {
		return parent::getInstance();
	}

    /**
     * recursiv rmdir
     * @param $dir
     * @return bool
     */
    public static function rrmdir($dir) {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (filetype($dir."/".$object) == "dir")
                        self::rrmdir($dir."/".$object);
                    else unlink   ($dir."/".$object);
                }
            }
            reset($objects);
            return rmdir($dir);
        }
    }
}

/**
 * This function is used to encapsulate the script 
 * so it won't break the wizard
 * @param string $scriptPath
 */
function includeScript($scriptPath) {
	require_once $scriptPath;
}