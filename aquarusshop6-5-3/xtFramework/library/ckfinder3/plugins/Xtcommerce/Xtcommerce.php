<?php

namespace CKSource\CKFinder\Plugin\xtcommerce;

use CKSource\CKFinder\CKFinder;
use CKSource\CKFinder\Error;
use CKSource\CKFinder\Event\CKFinderEvent;
use CKSource\CKFinder\Event\BeforeCommandEvent;
use CKSource\CKFinder\Filesystem\File\UploadedFile;
use CKSource\CKFinder\Filesystem\Path;
use CKSource\CKFinder\Plugin\PluginInterface;
use CKSource\CKFinder\Utils;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class Xtcommerce implements PluginInterface, EventSubscriberInterface {
	
	protected $app;
	
	public function setContainer(CKFinder $app)
	{
		$this->app = $app;
	}
	
	public function getDefaultConfig() {
		return [];
	}
	
	
	public function processAfterFileUpload(CKFinderEvent $event)
	{
	    global $xtPlugin;

		$response = $event->getResponse();
		
		$first = strpos($response,'{');		
		$response = substr($response,$first,strlen($response));
		$response = json_decode($response);		
		$filename = $response->fileName;
		
		/**
		 * Another option to check/rename files
		 * this time after upload
		 * 1st option is editing ckfinder's File class:
		 *  function secureName
		 *  function autorename
		 */

		$fh = new \FileHandler();
		$new_filename = $fh->cleanFileName($filename);
		if($new_filename != $filename)
		{
            // Ticket
            // quick fix KCQ-659-40976 mammut
            // wenn shop in unterordner läuft, zb meinshop/ werden für rename falsche pfade erzeugt:
            // meinshop/meinshop/  also dopplung des _SRV_WEB (ohne xtAdmin, der hier im srv_web mit steckt)
            $root_dir = dirname(__FILE__);
            $root_dir = str_replace('xtFramework/library/ckfinder3/plugins/Xtcommerce','',$root_dir);

            if(_SRV_WEB != '/xtAdmin/')
            {
                $srv_web = str_replace('/xtAdmin', '', _SRV_WEB); // /meinshop/xtAdmin > /meinshop
                $response_url = str_replace($srv_web,'', $response->currentFolder->url);
            }
            else $response_url = $response->currentFolder->url;
            $basePath = $root_dir.$response_url;

			$new_filename = $this->buildNewName($new_filename, $basePath);
			rename($basePath.$filename, $basePath.$new_filename);
			$filename = $new_filename;
		}

        error_reporting(E_ERROR | E_WARNING | E_PARSE);

        // lower extension
        $basePath = _SRV_WEBROOT._SRV_WEB_IMAGES.'org/';
        $extension = strtolower(strrchr($filename,"."));
        $new_filename = substr($filename,0,strlen($filename)-strlen($extension)).$extension;
        rename($basePath.$filename, $basePath.$new_filename);
        $filename = $new_filename;


        ($plugin_code = $xtPlugin->PluginCode('ckfinder.plugin.xtcommerce:processAfterFileUpload')) ? eval($plugin_code) : false;
        if(isset($plugin_return_value))
            return $plugin_return_value;

		$md = new \MediaData();

		if(!isset($_GET['currentType'])) {
		    $_GET["currentType"] = $_REQUEST["currentType"] = $_GET["type"];
        } // ckfinder from within ckeditor

		$md->setClass($_GET["currentType"]);
		$md->url_data = $_REQUEST;
		$class = "Media".ucfirst($md->_getFileTypesByExtension($filename));

		if (class_exists($class)) {

			$md = new $class;
			$md->url_data = $_REQUEST;
            $md->setClass($_GET["currentType"]);
			$obj = $md->processOnly($filename);
		}
	}
		
	private function buildNewName($filename, $path, $counter = 1)
	{
		if(file_exists($path.'/'.$filename))
		{
            $fh = new \FileHandler();
            $file_name = $fh->getFileNameNoExtension($filename);
            $file_ext = $fh->getFileExt($filename);

            preg_match("/_[0-9]+$/", $file_name, $output_array);
            if(count($output_array))
            {
                $s = str_replace('_','', $output_array[0]);
                $file_name = str_replace($output_array[0],'', $file_name);
                $counter = (int) $s;
                $counter++;
            }

            $filename = $file_name.'_'.$counter.'.'.$file_ext;

            if(file_exists($path.'/'.$filename))
                return $this->buildNewName($filename, $path, $counter);
		}

		return $filename;
	}
		
	
	public static function getSubscribedEvents()
	{
		return [
			CKFinderEvent::AFTER_COMMAND_FILE_UPLOAD => 'processAfterFileUpload',
            CKFinderEvent::BEFORE_COMMAND_GET_FILES => 'beforeGetFiles',
		];
	}

	public function beforeGetFiles($eventObject, $eventName, $eventDispatcher)
    {
        $config = $this->app['config'];
        if($config->get('dontListFiles') === true)
        {
            require _SRV_WEBROOT . _SRV_WEB_FRAMEWORK . 'library/ckfinder3/plugins/Xtcommerce/GetFilesEmpty.php';
            $cmd = new GetFilesEmpty($this->app);
            $eventObject->setCommandObject($cmd);
        }
    }
	
}