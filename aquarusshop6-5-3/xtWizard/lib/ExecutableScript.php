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

class ExecutableScript implements IExecutableScript {
	
	/**
	 * Pages available for this script
	 * @var array
	 */
	protected $_pages = array();
	
	/**
	 * Update commands
	 * @var array
	 */
	protected $_contollerCommands = array();
	
	/**
	 * Next offset for async action
	 * @var unknown
	 */
	protected $_nextOffset = 0;
	
	/**
	 * Next page url
	 * @var string
	 */
	protected $_nextPageUrl = '';
	
	/**
	 * (non-PHPdoc)
	 * @see IExecutableScript::getPages()
	 */
	public function getPages() {
		if (empty($this->_pages)) {
			$xmlObject = $this->getXmlObject();
			
			if (count($xmlObject->Pages->Page) == 0) {
				throw new WizardException(sprintf('No pages defined for %s', get_class($this)));
			}
			
			foreach ($xmlObject->Pages->Page as $page) {
				$this->_pages[] = new WizardPage((string)$page->PageID, $this, (string)$page->PageMethod, (string)$page->PageCondition);
			}
		}
		
		return $this->_pages;
	}
	
	/**
	 * Set next page url
	 * @param string $url
	 * @return ExecutableScript
	 */
	public function setNextPageUrl($url) {
		$this->_nextPageUrl = $url;
		return $this;
	}
	
	/**
	 * Get next page url
	 * @return string
	 */
	public function getNextPageUrl() {
		return $this->_nextPageUrl;
	}
	
	/**
	 * Setnext offset for async action
	 * @param unknown $offset
	 * @return ExecutableScript
	 */
	public function setNextOffset($offset) {
		$this->_nextOffset = $offset;
		return $this;
	}
	
	/**
	 * Get next offset for async action
	 * @return unknown
	 */
	public function getNextOffset() {
		return $this->_nextOffset;
	}
	
	/**
	 * Adds a controller command
	 * @param ControllerCommand $command
	 * @return ExecutableScript
	 */
	public function addControllerCommand(ControllerCommand $command) {
		$command->setExecutableScript($this);
		$this->_contollerCommands[] = $command;
		return $this;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see IExecutableScript::getScriptTitle()
	 */
	public function getScriptTitle() {
		$xmlObject = $this->getXmlObject();
		return (string)$xmlObject->Title;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see IExecutableScript::hasNextPage()
	 */
	public function hasNextPage($currentPage) {
		$pages = $this->getPages();
		
		$currentPageFound = false;
		
		foreach ($pages as $page) {
			if ($currentPage == $page->getPageId()) {
				$currentPageFound = true;
				continue;
			}
			
			if ($currentPageFound) {
				if ($page->isAvailable()) {
					return true;
				}
			}
		}
		return false;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see IExecutableScript::getNextPage()
	 */
	public function getNextPage($currentPage) {
		$pages = $this->getPages();
		
		$currentPageFound = false;
		
		foreach ($pages as $page) {
			if ($currentPage == $page->getPageId()) {
				$currentPageFound = true;
				continue;
			}
				
			if ($currentPageFound) {
				if ($page->isAvailable()) {
					return $page;
				}
			}
		}
		throw new WizardException('Not implemented');
	}
	
	/**
	 * (non-PHPdoc)
	 * @see IExecutableScript::getPage()
	 */
	public function getPage($currentPage) {
		$pages = $this->getPages();
		
		foreach ($pages as $page) {
			if ($currentPage == $page->getPageId()) {
				return $page;
			}
		}
		throw new WizardException('Not implemented');
	}
	
	/**
	 * (non-PHPdoc)
	 * @see IExecutableScript::getWizardType()
	 */
	public function getWizardType() {
		throw new WizardException('Not implemented');
	}
	
	/**
	 * (non-PHPdoc)
	 * @see IExecutableScript::getAppliableShopVersion()
	 */
	public function getAppliableShopVersion() {
		throw new WizardException('Not implemented');
	}

    public function getTargetShopVersion() {
        throw new WizardException('Not implemented');
    }

    public function getSkippableShopVersion(){
        throw new WizardException('Not implemented');
    }
	
	/**
	 * (non-PHPdoc)
	 * @see IExecutableScript::getUniqueId()
	 */
	public function getUniqueId() {
		return get_class($this);
	}
	
	/**
	 * Get pages 
	 * @return string
	 */
	protected function getPagesXml() {
		return '';
	}
	
	/**
	 * Get batch size. This is the limit of ControllerCommand's that will be executed in one call.
	 * @return number
	 */
	protected function getBatchSize() {
		return 1;
	}
	
	/**
	 * Performs asynchronius database update
	 * @param WizardPage $page
	 */
	public function execAsyncAction(WizardPage $page) {
		if (!getParamDefault('ajaxLoad', false)) {
			$page->assignTemplateVar('processing',TEXT_PROCESSING, false);
			
			$page->setTemplate('templates/async_action.html');
			return;
		}
		
		$response = array(
			'HasNext' => true,
			'Offset' => 0,
			'CurrentMessage' => '',
			'LogMessages' => '',
			'Progress' => getParamDefault('Progress', 0),
			'ExtraParams' => array(),
			'NextUrl' => '',
		);
		
		$offset = getParamDefault('offset', 0);
		$batchSize = $this->getBatchSize();
		$response['Offset'] = $offset + $batchSize;
		$this
		->setNextOffset($response['Offset'])
		->setNextPageUrl(Wizard::getInstance()->buildUrl(array(Wizard::PARAM_NEXT => 1, Wizard::PARAM_PAGE => Wizard::getInstance()->getCurrentPage())));
		$controllerCommands = $this->_contollerCommands;
		
		if (!isset($controllerCommands[$offset])) {
			$response['HasNext'] = false;
			if (Wizard::getInstance()->getWizardType() == Wizard::WIZARD_TYPE_UPDATE){
			    $pageMsg = $page->getSuccessMessage();
			    if(!empty($pageMsg))
                {
                    $response['CurrentMessage'] = $pageMsg;
                }
                else
				$response['CurrentMessage'] = $response['LogMessages'] = TEXT_FINISHED_UPDATE;
			}
			else{
				 $response['CurrentMessage'] = $response['LogMessages'] = TEXT_FINISHED;
			}
			$response['Progress'] = 100;
			echo json_encode($response);
			exit;
		}
		
		for ($i = $offset; $i < $response['Offset']; $i++) {
			if (!isset($controllerCommands[$i])) {
				$response['HasNext'] = false;
				$messages = ControllerCommand::getAllMessages(true);
				$response['LogMessages'] = join('<br/>', $messages);
				$response['CurrentMessage'] = array_pop($messages);
				$response['Progress'] = 100;
				echo json_encode($response);
				exit;
			}
			$command = $controllerCommands[$i];
			try {
				$command->executeCommand();
				$response['ExtraParams'] = array_merge($response['ExtraParams'], $command->getCommandParams());
			} catch (WizardException $e) {
				if (Wizard::getInstance()->isSystemInstalled()) {
					Wizard::getInstance()->getDatabaseObject()->Execute('ROLLBACK;');
				}
				$response['HasNext'] = false;
				$messages = ControllerCommand::getAllMessages(true);
				$messages[] = $e->getMessage();
				$response['LogMessages'] = join('<br/>', $messages);
				$response['CurrentMessage'] = array_pop($messages);
				echo json_encode($response);
				exit;
			}
		}
		
		if (Wizard::getInstance()->isSystemInstalled()) {
			Wizard::getInstance()->getDatabaseObject()->Execute('COMMIT;');
		}
		$messages = ControllerCommand::getAllMessages(true);
		//$response['HasNext'] = ($response['Offset'] < count($controllerCommands)) ? true : false;
		WizardLogger::getInstance()->log(join("\n", $messages));
		$response['HasNext'] = isset($controllerCommands[$this->getNextOffset()]) ? true : false;
		if (!$response['HasNext']) {
			if (Wizard::getInstance()->getWizardType() == Wizard::WIZARD_TYPE_UPDATE){
                $pageMsg = $page->getSuccessMessage();
                if(!empty($pageMsg))
                {
                    $messages[] = $pageMsg;
                }
                else
				$messages[] = TEXT_FINISHED_UPDATE;
			}
			else{
				$messages[] = TEXT_FINISHED;
			}
			$response['Progress'] = 100;
		}
		$response['LogMessages'] = join('<br/>' . "\n", $messages) . '<br/>' . "\n";
		$response['CurrentMessage'] = array_pop($messages);
		$response['Progress'] = round(($this->getNextOffset()/count($controllerCommands)*100),2);
		$response['Offset'] = $this->getNextOffset();
		$response['NextUrl'] = $this->getNextPageUrl();
		echo json_encode($response);
		exit;
	}
	
	/**
	 * Get xml object
	 * @throws WizardException
	 * @return SimpleXMLElement
	 */
	protected function getXmlObject() {
		$xmlString = $this->getPagesXml();
		if (empty($xmlString)) {
			$msg = sprintf('Can not read pages/xml for class %s', get_class($this));
			throw new WizardException($msg);
		}
		$xmlObject = simplexml_load_string($xmlString);
		
		return $xmlObject;
	}
	
	/**
	 * Get database object
	 * @return object
	 */
	protected function db() {
		return Wizard::getInstance()->getDatabaseObject();
	}



    public function getHighestTargetVersion()
    {
        $highestTargetVersion = '0';
        $loadedScripts = Wizard::getInstance()->getLoadedScripts();
        foreach ($loadedScripts as $k => $script)
        {
            if (!Wizard::getInstance()->isSystemInstalled() && $script->getWizardType() == null)
                $targetVersion = $script->getTargetShopVersion();
            else if (Wizard::getInstance()->isSystemInstalled() && $script->getWizardType() == Wizard::WIZARD_TYPE_UPDATE)
            {
                $targetVersion = $script->getTargetShopVersion();
            }
            if(version_compare($targetVersion, $highestTargetVersion, '>'))
                $highestTargetVersion = $targetVersion;
        }
        return $highestTargetVersion;
    }

    public function updateVersionInfoPhp(WizardPage $page) {
        global $store_handler;

        //$page->setSuccessMessage('ok, /versioninfo.php wurde aktualisiert');

        $targetVersion = $page->getScript()->getTargetShopVersion();

        $msg = 'Update versioninfo.php > '.$targetVersion;

        $this->addControllerCommand(ControllerCommand::factory()
            ->setAction(function (ControllerCommand $command) use ($targetVersion, $msg)
            {
                $storeVersion = Wizard::getInstance()->getStoreVersion();

                array_map('unlink', glob(_SRV_WEBROOT.'versioninfo._xtWizard_'.$storeVersion.'_'.$targetVersion.'_*.php'));

                $content =  "<?php".PHP_EOL;
                $content .= "define('_SYSTEM_VERSION','{$targetVersion}');".PHP_EOL;
                $content .= "define('IS_UPDATE_VERSIONINFO', true);".PHP_EOL;

                $file = _SRV_WEBROOT.'versioninfo.php';
                if(file_exists($file))
                {
                    $old_file_s = trim(file_get_contents($file));

                    if($old_file_s === trim($content))
                    {
                        return true;
                    }

                    rename($file, _SRV_WEBROOT.'versioninfo._xtWizard_'.$storeVersion.'_'.$targetVersion.'_'.time().'.php');
                }

                $bytes_written = file_put_contents($file, $content);

                $new_file_s = trim(file_get_contents($file));
                if($new_file_s !== trim($content))
                {
                    $err_msg = TEXT_ERROR_VERSIONINFO_NOT_UPDATED.'<br/>'.TEXT_VERSION_INFO_CREATE_MANUALLY.'<br/><pre>'.htmlentities($content).'<br/></pre>';
                    throw new WizardException($err_msg);
                }

                return true;
            })
            ->setDescription($msg)
            ->setAbortOnError(true)
        );

        $page->assignTemplateVar('processing',$msg,true);
        $this->execAsyncAction($page);
    }

    /**
     * Make http response
     * @param string $url
     * @return mixed
     */
    protected function makeRequest($url, $params = array()) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT , "XT:Commerce Platform");
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);

        if (!empty($params)) {
            curl_setopt($ch, CURLOPT_POST , 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS , $params);
        }

        $fp = fopen(_SRV_WEBROOT . _SRV_WEB_LOG . 'curl_log_xtWizard.txt', 'w+');
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_STDERR, $fp);

        $response = curl_exec($ch);

        if (!$response) {
            $msg = 'xtWizard curl empty response. url: '.$url.  '  curl_error: '. curl_error($ch) . '  curl_errno: ' . curl_errno($ch);
            error_log($msg);
            throw new Exception($msg);
        }

        if(curl_errno($ch))
        {
            // 28 -timeout
            $msg = 'xtWizard curl error. url: '.$url.  '  curl_error: '. curl_error($ch) . '  curl_errno: ' . curl_errno($ch);
            error_log($msg);
            throw new Exception($msg);
        }
        curl_close($ch);

        return $response;
    }
}
