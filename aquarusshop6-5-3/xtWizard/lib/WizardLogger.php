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

class WizardLogger extends Singleton {
	
	/**
	 * Log dir
	 * @var string
	 */
	protected $_logDir = null;
	
	/**
	 * Log file
	 * @var string
	 */
	protected $_logFile = 'xtWizard.log';
	
	/**
	 * Logs a message
	 * @param string $msg
	 * @return WizardLogger
	 */
	public function log($msg) {
		$this->checkLogDir();
		if (!is_dir(ROOT_DIR_PATH . $this->_logDir)) {
			mkdir(ROOT_DIR_PATH . $this->_logDir, 0755);
		}
		file_put_contents(ROOT_DIR_PATH . $this->_logDir . DS . $this->_logFile, strip_tags($msg) . "\n", FILE_APPEND);
		return $this;	
	}
	
	/**
	 * Cleats the log file
	 * @return WizardLogger
	 */
	public function clearLog() {
		$this->checkLogDir();
		$logFile = ROOT_DIR_PATH . $this->_logDir . DS . $this->_logFile;
		if (file_exists($logFile)) {
			unlink($logFile);
		}
		return $this;
	}
	
	/**
	 * @return WizardLogger
	 */
	public static function getInstance() {
		return parent::getInstance();
	}

	protected function checkLogDir()
	{
		if (empty($this->_logDir)) $this->_logDir = '..'. DS .'xtLogs';
	}
}