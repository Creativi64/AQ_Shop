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

defined('_VALID_CALL') or die('Direct Access is not allowed.');

class MediaImages extends MediaData {
	public $response='';
    public mixed $UploadExt;
    public string $iconFileNotExists;
    public string $type;
    public string $urlPath;
    public $imageTypes;

    function __construct() {
        parent::__construct();
        
		$this->type 		= 'images';
		$this->path 		= _SRV_WEB_IMAGES.'';
        $this->urlPath      = _SYSTEM_BASE_HTTP. (defined('_SRV_WEB_UPLOAD') ? constant('_SRV_WEB_UPLOAD'):'NODEF__SRV_WEB_UPLOAD');

        $types = $this->getFileExt($this->type);
		$this->FileTypes 	= $types['FileTypes'];
		$this->UploadExt    = $types['UploadTypesArray'];
		$this->iconFileNotExists = 'filetypes/file_error.gif';
    }

    function setClass($val){
    	$this->class = $val;
    }
    
    function getClass(){
    	return $this->class;
    }
    
    function setUrlData($data=''){
    	if(is_array($data))
    	$this->url_data = $data;
    }
    
	function check_folder($folder,$dir){
		if(!is_dir($dir.$folder)){
			mkdir($dir.$folder);
			chmod($dir.$folder, 0777);
		}
	}    
    
	function checkProcessImage($data) {
          if (count($data) == 0) return false;
          foreach ($data as $key => $val) {
              $this->processImage($val['file']);
          }
	}	
	
	function getImageType() {
		return $this->imageTypes;
	}

	function getImageTypes ($class='') {
	    global $db;
	    // get image types by class
	    
	    if(!$class)
	    $class = $this->class;
	    
	    $query = "SELECT * FROM ".TABLE_IMAGE_TYPE." where class = ? ";
		$record = $db->Execute($query, array($class));

		if ($record->RecordCount() == 0) {
		    // get default image types
    	    $query = "SELECT * FROM ".TABLE_IMAGE_TYPE." where class = 'default' ";
    		$record = $db->Execute($query);
		}
		if ($record->RecordCount() > 0) {
			while(!$record->EOF){
    		    $types[] = $record->fields;
                $record->MoveNext();
			}
			$record->Close();
		}
		return $types;
	}	
		
	function readDir($custom_path='',$type='') {
		if ($custom_path!='') $path = $custom_path;
		else $path = $this->getPath().'org/';
		if ($type=='files')$types = $this->getFileExt($type);
		$dir = _SRV_WEBROOT.$path;
		$images = array();

		$d = dir($dir);
		while($name = $d->read()){
		    if ($type=='files')  
			{
				if(!preg_match('/\.('.$types["FileTypes"].')$/', $name)) continue;
			}
			else 
			{
				if(!preg_match('/\.('.$this->getFileTypes().')$/', $name)) continue;
			}
		    $size = filesize($dir.$name);
		    $lastmod = filemtime($dir.$name);
		    $images[] = array(
				'name'=>$name,
				'size'=>$size,
				'lastmod'=>$lastmod,
				'url'=> $this->urlPath.$path.$name,
				'url_full'=> $this->urlPath.$this->getPath().'org/'.$path.$name
			);
		}
		$d->close();
		return array('images'=>$images);
	}

	function setAutoReadFolderData($mgID='') {
		global $db;

	    $data = $this->readDir();
	    if (is_array($data['images'])) {
	        foreach ($data['images'] as $key => $imageData) {
	         	$record = $db->Execute(
					"SELECT id FROM ".$this->_table_media." where file=?", array($imageData['name'])
			 	);
    			if ($record->RecordCount() == 0) {
                	$this->setMediaData(array(
						'file' => $imageData['name'],
						'type' => $this->type,
						'class' => $this->url_data['currentType'],
						'mgID'=>$this->url_data['mgID']
					));
    			}	        	
	        }
	        return true;
	    }
	    return false;
	}	

    function setMediaToCurrentType($filename, $m_id='') {
  		global $db,$filter;
  		
    	if($this->url_data['link_id'] && !is_int($this->url_data['link_id'])){
			$tmp_link_id_array = explode('_', $this->url_data['link_id']);
			$tmp_link_id_array = array_reverse($tmp_link_id_array);
			$this->url_data['link_id'] = $tmp_link_id_array[0];
		}

        if($this->url_data['currentType']=='default'){
            return true;
        }

		$current = $this->getCurrentIds();
		$count = count($current->currentIds);

		if(!$m_id)
		$m_id = $this->_getMediaID($filename);
		
		if ($this->url_data['currentType'] == 'logo' || $this->url_data['currentType'] == 'slides') {
			$this->setMainFile($m_id, $this->url_data['link_id'], $this->url_data['currentType']);
			return true;
		}

		if (!$m_id || (is_array($current->currentIds) && in_array($m_id, $current->currentIds))) return false;
		
		if ($count == 0) {
			$this->setMainFile($m_id, $this->url_data['link_id'], $this->url_data['currentType']);
			return true;
		}
		
		if ($count > 0) {
			$sortData = array(
				'sort_order' => $count,
				'm_id' => $m_id,
				'link_id' => $this->url_data['link_id'],
				'class' => $this->url_data['currentType']
			);
			if ($m_id) {
	        	$db->Execute(
					"DELETE FROM ". $this->_table_media_link ." WHERE m_id = ? and link_id = ? and class = ? and type = ?",
					array($m_id, (int)$this->url_data['link_id'], $this->url_data['currentType'], $this->type)
				);
				$db->AutoExecute($this->_table_media_link, $sortData, 'INSERT');
			}
			return true;
		} 
	}	
	
	function getCurrentIds () {
		global $xtPlugin, $db, $language;
		if(!$this->class)
            $this->setClass($this->url_data['currentType']);
		
		$currentIds = array();
		if ($mainFile = $this->getMainFile($this->url_data['link_id'], $this->url_data['currentType'])) {
			$mainId = $this->_getMediaID($mainFile, $this->class);

			if(is_data($mainId)){
				$currentIds[] = $mainId;
				$sortedIds[$mainId] = 0;
			}
		}

		$qry = " type = ? and class = ?";
        $record = $db->Execute(
			"SELECT * FROM ".$this->_table_media_link." ml WHERE link_id = ? and ".$qry." order by sort_order ",
			array($this->url_data['link_id'], $this->type, $this->class)
		);
    	if ($record->RecordCount() > 0) {
    		while(!$record->EOF){
    			$currentIds[] = $record->fields['m_id'];    			
    			if (!$record->fields['sort_order'] > 0 || count($currentIds)-1 != $record->fields['sort_order']) {
    				$sortData = $record->fields;
    				$sortData['sort_order'] = count($currentIds)-1;
    				$this->_setSortOrder($sortData);
    				$sortCount = $sortData['sort_order'];
    			} else {
    				$sortCount = $record->fields['sort_order'];
    			}
    			
    			$sortedIds[$record->fields['m_id']] = $sortCount;
    			
    			$record->MoveNext();
    		} $record->Close();
    	}	
    	$obj = new stdClass();
    	$obj->sortedIds = $sortedIds;
    	$obj->currentIds = $currentIds;
	    return $obj;				
	}	
	
	function isMainFile($id, $class, $file) {
		global $db;
		$m = new $class;
		$image_key = $m->_image_key;
		$table = $m->_table;
		$master_key = $m->_master_key;	
		if (!($image_key && $master_key && $table)) return false;	

		$record = $db->Execute("SELECT distinct ".$image_key." FROM ".$table." WHERE ".$master_key." = ?", array($id) );
		if ($record->RecordCount() == 0) return false;
		
		if($record->fields[$image_key] == $file){
			return true;
		}else{
			return false;
		}
	}	

	function getMainFile($id, $class) {
		global $db;
		$m = new $class;
		$image_key = $m->_image_key;
		$table = $m->_table;
		$master_key = $m->_master_key;	
		if (!($image_key && $master_key && $table)) return false;	

		$record = $db->Execute("SELECT distinct ".$image_key." FROM ".$table." WHERE ".$master_key." = ?", array($id) );
		if ($record->RecordCount() == 0) return false;
		
		return $record->fields[$image_key];
	}
	
	function setMainFile($mId, $link_id, $class) {
		global $db;
		$m = new $class;
		$image_key = $m->_image_key;
		$table = $m->_table;
		$master_key = $m->_master_key;	
		$uploaded = $this->_getMediaFileName($mId);
		$data = array($image_key => $uploaded );
		
		if ($class=='logo') {
			$m->_setImage($uploaded);
		} else {
			$db->AutoExecute($table, $data, 'UPDATE', $master_key . " = " . $db->Quote($link_id) . "");
		}
		
	}	
	
	function unsetMainFile($mId, $link_id, $class) {
		global $db;
		$m = new $class;
		$image_key = $m->_image_key;
		$table = $m->_table;
		$master_key = $m->_master_key;

		$data = array($image_key => '');

        //check if last image
        $sql="SELECT m.file, ml.m_id FROM ".TABLE_MEDIA_LINK." ml, ".TABLE_MEDIA." m WHERE ml.link_id = ? AND  ml.TYPE = 'images' AND ml.class = ? AND m.id=ml.m_id ORDER BY ml.sort_order LIMIT 1";
        $record=$db->Execute($sql, array($link_id, $class));
        if($record->RecordCount()>0){
            $data[$image_key]=$record->fields['file'];
            $this->unsetMediaLink($link_id,$record->fields['m_id'],'images');
        }
        $record->Close();

		$db->AutoExecute($table, $data, 'UPDATE', $master_key." = ".$db->Quote($link_id)."");
	}	
	
	function remove($data){
		
		$types = $this->getImageTypes($data['class']);
		if(!is_array($types))
		$types = $this->getImageTypes('default');
		
		foreach ($types as $key => $val){
			unlink(_SRV_WEBROOT._SRV_WEB_IMAGES.$val['folder'].'/'.$data['file']);	
		}
		
		unlink(_SRV_WEBROOT._SRV_WEB_IMAGES.'org/'.$data['file']);
		$this->unsetMediaData($data['id']);
	}
	
	function updateMediaLink_mId($mId, $new_mId, $link_id, $class) {
		global $db;

		$data = array('m_id' => $new_mId);
		$db->AutoExecute(TABLE_MEDIA_LINK, $data, 'UPDATE', ' link_id = '. (int)$link_id . ' and class = '.$db->Quote($class).' and m_id = '.(int)$mId);
	}	
	
	function get_media_images($id, $class){
		global $xtPlugin;
		
		if(USER_POSITION=='admin') return false;
		
		if ($data['tmp_images'] = $this->_getMediaFiles($id, $class, 'images', 'free')) { 
			
			foreach ($data['tmp_images'] as $key => $val){
				$data['images'][$key]['file'] = $class.':'.$val['file'];
				$data['images'][$key]['data'] = $val;
			}
			
			($plugin_code = $xtPlugin->PluginCode(__CLASS__.':get_media_images')) ? eval($plugin_code) : false;
			
			return $data;
		} else {
			return false;
		}
	}

	/**
	 * process uploaded file
	 * @param unknown $filename
	 */
	function processOnly($filename) {
		
		$obj = new stdClass;
		
		$filename = $this->setExtensiontolower($filename);
		
		$m_id = $this->setMediaData(array(
				'file' => $filename,
				'type' => $this->type,
				'class' => $this->url_data['currentType'],
				'mgID'=>$this->url_data['mgID']
		));
		$this->setMediaToCurrentType($filename, $m_id);
		$this->processImage($filename);
		$obj->success = true;
		
		return $obj;
	}

	function Upload ($filename) {
        $upload_good = false;
        $filename = $this->setExtensiontolower($filename);
        if(!$this->class)
        $this->setClass($this->url_data['currentType']);
        
        $obj = new stdClass;

    	$upload = new uploader();
    	
     	$upload->file='Filedata';
     	$upload->upload_dir = _SRV_WEBROOT._SRV_WEB_IMAGES.'org/';

     	$fh = new FileHandler();
     	$filename = $fh->cleanFileName($filename);
     	
        $filename = $this->buildNewName($filename);
     	
     	$upload->new_name = $filename;
     	$upload->extensions = $this->UploadExt;
     	
     	$response = $upload->uploadFile();

    	if (!$response) {
    	    $obj->error = true;
    		$obj->waitMsg = 'Error:'.$upload->error;
    	} else {
            $m_id = $this->setMediaData(array(
				'file' => $filename,
				'type' => $this->type,
				'class' => $this->url_data['currentType'],
				'mgID'=>$this->url_data['mgID']
			));
            $this->setMediaToCurrentType($filename, $m_id);
    		$this->processImage($filename);
    		$obj->success = true;
    	}
    	return $obj;
	}    

    private function buildNewName($new_filename)
    {
        if(file_exists(_SRV_WEBROOT._SRV_WEB_IMAGES.'org/'.$new_filename))
        {
            $nameParts = explode('.', $new_filename);
            $new_filename = $nameParts[0].'_1.';
            unset($nameParts[0]);
            $new_filename .= implode('.', $nameParts);
        }
        if(file_exists(_SRV_WEBROOT._SRV_WEB_IMAGES.'org/'.$new_filename))
            return $this->buildNewName($new_filename);
        return $new_filename;
    }
    
	function processImage($image_name, $overwrite = false, $orgPath = '')
    {
	    global  $xtPlugin;

		$path = $this->getPath();
		$dir = _SRV_WEBROOT.$path;
		$original =  !empty($orgPath) ? ($dir . $orgPath) : ($dir . 'org/');
		$types = $this->getImageTypes();

		foreach ($types as $key => $typ ) {

			$this->check_folder($typ['folder'],$dir);
 
		    if ($typ['process'] != 'false') {

                $image = new image();

                $image->max_height= $typ['height'];
                $image->max_width = $typ['width'];
                $image->resource = $original.$image_name;
                $image->target_dir = $dir.'/'.$typ['folder'].'/';
                $image->target_name = $image_name;
                if (isset($typ['crop_status']))
                	$image->crop = $typ['crop_status'];

                ($plugin_code = $xtPlugin->PluginCode(__CLASS__.':processImage_before_createThumbnail')) ? eval($plugin_code) : false;

                if ($overwrite || !is_file($image->target_dir))  {
                    $this->response = $image->createThumbnail();
                } 

		    }else{
				copy($original.$image_name, $dir.$typ['folder'].'/' . $image_name);
		    }
		}
	}

    function setExtensiontolower($filename) {
        $extension = strtolower(strrchr($filename,"."));
        $new_name = substr($filename,0,strlen($filename)-strlen($extension)).$extension;
        return $new_name;
    }
}