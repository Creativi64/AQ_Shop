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

// Include some of the base needed files
require_once 'Singleton.php';
// Base errors
require_once 'errors/WizardException.php';
require_once 'errors/LoaderException.php';
// Base interfaces
require_once 'interfaces/ILoader.php';

/**
 * Class reponsible for all autoloding during the execution
 * @author radoslav
 */
class Loader extends Singleton {
	
	/**
	 * Array of registered loaders. All of the loaders are set by Loader::registerLoader
	 * and must implement ILoader
	 * @var array
	 */
	protected $_loaders = array();
	
	/**
	 * Class constructor. Resiters this class as autoloader.
	 */
	protected function __construct() {
		spl_autoload_register(array($this, 'load'));
		parent::__construct();
	}
	
	/**
	 * Registers an autoloader class
	 * @param ILoader $loader
	 * @return Loader
	 */
	public function registerLoader(ILoader $loader) {
		$this->_loaders[] = $loader;
		return $this;
	}
	
	protected  function load($class) {
		foreach ($this->_loaders as $loader) {
			if ($loader->load($class)) {
				return true;
			}
		}
		
		//throw new LoaderException("Autoload failed. Can not find class: {$class}");
	}
	
	/**
	 * @return Loader
	 */
	public static function getInstance() {
		return parent::getInstance();
	}
	
}

class BaseAutoloader implements ILoader {
	
	protected $_classMap = array(
		// Classes
		'ControllerCommand' => 'lib/ControllerCommand.php',
		'ExecutableScript' => 'lib/ExecutableScript.php',
		'Page' => 'lib/WizardPage.php',
		'StartPageScript' => 'lib/StartPageScript.php',
		'Wizard' => 'lib/Wizard.php',
		'WizardLogger' => 'lib/WizardLogger.php',
		'WizardPage' => 'lib/WizardPage.php',
			
		// Interfaces
		'IExecutableScript' => 'interfaces/IExecutableScript.php',
		'IPage' => 'interfaces/IPage.php',
			
		// Errors
		'WizardErrorHandler' => 'errors/WizardErrorHandler.php'
	);
	
	public function load($class) {
		if (!isset($this->_classMap[$class])) {
			return false;
		}
		
		$file = ROOT_DIR_PATH . $this->_classMap[$class];
		
		if (!file_exists($file)) {
			return false;
		}
		
		require_once $file;
		
		return true;
	}
	
}

/**
 * Helper function
 * @param string $paramName
 * @param mixed $default
 * @return mixed
 */
function getParamDefault($paramName, $default) {
	if (isset($_POST[$paramName])) {
		return $_POST[$paramName];
	}

	if (isset($_GET[$paramName])) {
		return $_GET[$paramName];
	}

	return $default;
}

Loader::getInstance()->registerLoader(new BaseAutoloader());