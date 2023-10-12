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

class WizardPage implements IPage {
	
	/**
	 * Page ID
	 * @var string
	 */
	protected $_pageId = null;
	
	/**
	 * Script that called this page
	 * @var ExecutableScript
	 */
	protected $_script = null;
	
	/**
	 * Action that will be trigered
	 * @var string
	 */
	protected $_action = null;
	
	/**
	 * Condition if the page is available
	 * @var function
	 */
	protected $_pageCondition = null;
	
	/**
	 * Template variables that will be passed to the template
	 * @var array
	 */
	protected $_templateVars = array();
	
	/**
	 * Template file to render for that page
	 * @var string
	 */
	protected $_template = null;

	
    protected $_success_message = '';
	
	/**
	 * Class constructor
	 * @param string $pageId
	 * @param ExecutableScript $script
	 * @param string $action
	 * @param string $condition
	 */
	public function __construct($pageId, ExecutableScript $script, $action, $condition = null) {
		$this->_pageId = $pageId;
		$this->_script = $script;
		$this->_action = $action;
		$this->_pageCondition = $condition;
	}

	public function getScript()
    {
        return $this->_script;
    }
	
	/**
	 * Set template file to be rendered upon WizardPage::display() is called
	 * @param string $template
	 * @throws WizardException
	 * @return WizardPage
	 */
	public function setTemplate($template) {
		$templatePath = ROOT_DIR_PATH . $template;
		
		if (!file_exists($templatePath)) {
			throw new WizardException('Template file not found', WizardException::ERROR_LEVEL_FATAL);
		}
		
		$this->_template = $templatePath;
		return $this;
	}
	
	/**
	 * Get template vars
	 * @return array
	 */
	public function getTemplateVars() {
		$systemVariables = array(
			'show_language' => false,
			'next_page' => Wizard::getInstance()->buildUrl(array(Wizard::PARAM_NEXT => 1, Wizard::PARAM_PAGE => Wizard::getInstance()->getCurrentPage())),
			'current_page' => Wizard::getInstance()->buildUrl(array(Wizard::PARAM_PAGE => Wizard::getInstance()->getCurrentPage())),
			'system_installed' => Wizard::getInstance()->isSystemInstalled(),
			'current_language' => Wizard::getInstance()->getCurrentLanguage(),
		);
		return array_merge($systemVariables, $this->_templateVars);
	}
	
	/**
	 * Assign a template var
	 * @param string $name
	 * @param mixed $value
	 * @param boolean $overwrite
	 * @return WizardPage
	 */
	public function assignTemplateVar($name, $value, $overwrite = true) {
		if (!isset($this->_templateVars[$name]) || $overwrite) {
			$this->_templateVars[$name] = $value;
		}
		return $this;
	}
	
	/**
	 * Assign array of template vars
	 * @param array $vars
	 * @return WizardPage
	 */
	public function assignTemplateVars($vars) {
		$this->_templateVars = array_merge($this->_templateVars, $vars);
		return $this;
	}
	
	/**
	 * Get page ID
	 * @return string
	 */
	public function getPageId() {
		return $this->_pageId;
	}
	
	/**
	 * Check if page is avalable
	 * @return boolean
	 */
	public function isAvailable() {
		if (empty($this->_pageCondition)) {
			return true;
		}
		
		return eval($this->_pageCondition);
	}
	
	public function process() {
		
		if (!$this->isAvailable()) {
			$this->goToNextPage();
		}
		
		$this->_script->{$this->_action}($this);
		if (!getParamDefault('ajaxLoad', false)) {
			$this->display();
		}
	}
	
	public function display() {
		$parser = new Smarty();
		
		$parser->template_dir = _SRV_WEBROOT.'xtWizard/templates';
		$parser->compile_dir = _SRV_WEBROOT.'templates_c';

		if(method_exists($parser, 'addPluginsDir'))
        {
            $parser->addPluginsDir(array(
                    _SRV_WEBROOT.'xtFramework/library/smarty/xt_plugins')
            );
        }
        else
        {
		if(method_exists($parser, 'setPluginsDir'))
        {
		    $parser->setPluginsDir(array(
					//_SRV_WEBROOT.'xtFramework/library/smarty/'.$this->_smarty_version.'/libs/plugins',
					_SRV_WEBROOT.'xtFramework/library/smarty/xt_plugins'));
            }
        }

		$tempalteVars = $this->getTemplateVars();
		
		foreach ($tempalteVars as $name => $value) {
			$parser->assign($name, $value);
		}
		
		echo $parser->fetch(ROOT_DIR_PATH . 'templates/header.html');
		echo $parser->fetch($this->_template);
		echo $parser->fetch(ROOT_DIR_PATH . 'templates/footer.html');
	}
	
	/**
	 * Redirects to next page if available
	 * @throws WizardException
	 */
	protected function goToNextPage() {
		$pageId = $this->_pageId;
		while ($this->_script->hasNextPage($pageId)) {
			$nextPage = $this->_script->getNextPage($pageId);
			if ($nextPage->isAvailable()) {
				$nextPage->goToThisPage();
				return;
			}
			$pageId = $nextPage->getPageId();
		}
		// @TODO what if it is not avalable and no next pages?
		throw new WizardException('End.', WizardException::ERROR_LEVEL_FATAL);
	}
	
	/**
	 * Get page url
	 * @return string
	 */
	public function getPageUrl() {
		$params = array(
				Wizard::PARAM_SCRIPT => $this->_script->getUniqueId(),
				Wizard::PARAM_PAGE => $this->_pageId,
		);
		
		return Wizard::getInstance()->buildUrl($params);
	}
	
	/**
	 * Redirects to this page
	 */
	public function goToThisPage() {
		$url = $this->getPageUrl();
		Wizard::getInstance()->redirect($url);
	}

    /**
     * @return string
     */
    public function getSuccessMessage()
    {
        return $this->_success_message;
    }

    /**
     * @param string $success_message
     */
    public function setSuccessMessage($success_message)
    {
        $this->_success_message = $success_message;
        return $this;
    }
}