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

class ControllerCommand {
	
	const SESSION_KEY_CONTROLLER_COMMAND_RESULTS = 'ControllerCommandResults';
	const STATUS_SUCCESS = 1;
	const STATUS_ERROR = 2;
	const STATUS_SKIPPED = 3;
	
	/**
	 * Action that have to be executed
	 * @var Callable|Closure
	 */
	protected $_action = null;
	
	/**
	 * Condtion, that determines if the action should be xecuted
	 * @var Callable|Closure
	 */
	protected $_condition = null;
	
	/**
	 * Abort on error
	 * @var boolean
	 */
	protected $_abortOnError = true;
	
	/**
	 * Description of the command that will be shown in the progress bar and the log
	 * @var string
	 */
	protected $_commandDescription = '';
	
	/**
	 * Error message
	 * @var string
	 */
	protected $_errorMessage = '';
	
	/**
	 * Script that this command belongs to
	 * @var ExecutableScript
	 */
	protected $_script = null;
	
	/**
	 * Command params
	 * @var array
	 */
	protected $_commandParams = array();
	
	/**
	 * Creates new controller command
	 * @return ControllerCommand
	 */
	public static function factory() {
		return new ControllerCommand();
	}
	
	protected function __construct() {
		
	}
	
	/**
	 * Set script
	 * @param ExecutableScript $script
	 * @return ControllerCommand
	 */
	public function setExecutableScript(ExecutableScript $script) {
		$this->_script = $script;
		return $this;
	}
	
	/**
	 * Get executable script
	 * @return ExecutableScript
	 */
	public function getExecutableScript() {
		return $this->_script;
	}
	
	/**
	 * Add a command param
	 * @param unknown $paramName
	 * @param unknown $paramValue
	 * @return ControllerCommand
	 */
	public function addCommandParam($paramName, $paramValue) {
		$this->_commandParams[$paramName] = $paramValue;
		return $this;
	}
	
	/**
	 * Get command params
	 * @return multitype:
	 */
	public function getCommandParams() {
		return $this->_commandParams;
	}
	
	/**
	 * Sets action for this controller command
	 * @param Callable|Closure $action
	 * @throws WizardException
	 * @return ControllerCommand
	 */
	public function setAction($action) {
		if (!is_callable($action)) {
			throw new WizardException(sprintf('Class ControllerCommand::setAction expects function for a parameter, %s given', gettype($action)));
		}
		$this->_action = $action;
		return $this;
	}
	
	/**
	 * Sets condition for this command. The condtion must be a function that returns true or false.
	 * It is executed before $_action and determines if the action should be executed.
	 * @param Callable|Closure $condition
	 * @throws WizardException
	 * @return ControllerCommand
	 */
	public function setCondition($condition) {
		if (!is_callable($condition)) {
			throw new WizardException(sprintf('Class ControllerCommand::setCondition expects function for a parameter, %s given', gettype($condition)));
		}
		$this->_condition = $condition;
		return $this;
	}
	
	/**
	 * Sets abort on error
	 * @param boolean $bool
	 * @return ControllerCommand
	 */
	public function setAbortOnError($bool) {
		$this->_abortOnError = (bool)$bool;
		return $this;
	}
	
	/**
	 * Description of the command
	 * @param string $text
	 * @return ControllerCommand
	 */
	public function setDescription($text) {
		$this->_commandDescription = $text;
		return $this;
	}
	
	/**
	 * Error message
	 * @param string $msg
	 * @return ControllerCommand
	 */
	public function setErrorMessage($msg) {
		$this->_errorMessage = $msg;
		return $this;
	}
	
	/**
	 * Execute the command
	 * @throws WizardException
	 */
	public function executeCommand() {
		try {
			$condition = $this->_condition;
			if (null !== $condition && !$condition($this)) {
				$this->logCommandResult(self::STATUS_SKIPPED);
				return;
			}
			$action = $this->_action;
			$actionResult = $action($this);
			
			if (($actionResult === false) && $this->_abortOnError) {
				throw new WizardException(sprintf('%s %s <br/>%s', TEXT_QUERY_FAILED, $this->_commandDescription, $this->_errorMessage));
			}
			$this->logCommandResult(self::STATUS_SUCCESS);
			
		} catch (WizardException $e) {
			$this->setErrorMessage($e->getMessage());
			//$this->logCommandResult(self::STATUS_ERROR);
			if ($this->_abortOnError) {
				throw $e;
			}
		}
	}
	
	/**
	 * Log controller command result
	 * @param unknown $status
	 */
	protected function logCommandResult($status) {
		$statusMapping = array(
			self::STATUS_ERROR => 'ERROR',
			self::STATUS_SKIPPED => 'SKIPPED',
			self::STATUS_SUCCESS => 'SUCCESS',
		);
		$time = date('H:i:s');
		$msg = '[' . $time . '][<span class="msg-' . strtolower($statusMapping[$status]) . '">' . $statusMapping[$status] . '</span>] ' . $this->_commandDescription . ' ' . $this->_errorMessage.'<br />';
		self::addMessage($msg);
	}
	
	/**
	 * Adds a message
	 * @param string $msg
	 */
	public static function addMessage($msg) {
		$_SESSION[self::SESSION_KEY_CONTROLLER_COMMAND_RESULTS][] = $msg;
	}
	
	/**
	 * Gets all messages from session
	 * @param boolean $delete
	 * @return unknown
	 */
	public static function getAllMessages($delete = false) {
		$messages = $_SESSION[self::SESSION_KEY_CONTROLLER_COMMAND_RESULTS];
		if ($delete) {
			$_SESSION[self::SESSION_KEY_CONTROLLER_COMMAND_RESULTS] = array();
		}
		
		return $messages;
	}
}