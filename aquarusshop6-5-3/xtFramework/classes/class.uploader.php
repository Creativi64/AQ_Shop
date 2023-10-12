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

class uploader {

	var $upload_dir;
	var $file;
	var $new_name;
	var $permissions;
	var $error;

	var $extensions;

	var $size;
	var $type;
	var $name;
	var $tmp_name;

	var $overwrite;

    function __construct() {
        $this->extensions = array();
        $this->overwrite = true;
        
        $this->extension_whitelist = array();
        $this->extension_whitelist = explode(',',_SYSTEM_EXTENSION_WHITELIST);
    }


	function uploadFile() {

		if ($this->file=='') {
			$this->error = __text('ERROR_FILENAME_EMPTY');
			return false;
		}


		if (!is_writeable($this->upload_dir)) {
			$this->error = __text('ERROR_DIR_NOT_WRITEABLE').' '.$this->upload_dir;
			return false;
		}
		$this->size = $_FILES[$this->file]['size'];
		$this->type = $_FILES[$this->file]['type'];
		$this->name = $_FILES[$this->file]['name'];
		$this->tmp_name = $_FILES[$this->file]['tmp_name'];
		$this->extension = $this->_getExtension($this->name);
		$this->errorNr = $_FILES[$this->file]["error"];

		if (!$this->overwrite && is_file($this->upload_dir.$this->name)) {
		    $this->error = __text('ERROR_FILE_EXISTS').' '.$this->upload_dir.$this->name;
		    return false;
		}


		if ($this->errorNr) {
		  $this->error = __text('ERROR_FILE_ERROR_NUMBER').' '.$this->errorNr;
		}

		if (!$this->_isAllowedExtension()) {
		    $this->error = __text('ERROR_FILE_NOT_ALLOWED').' '.$this->extension;
		    return false;
		}

		if (!$this->_checkMimeType()) {
		    //$this->error = $this->type.'-'.$this->errorNr.'-'.__text('ERROR_FILE_NOT_MIME_TYPES');
		    //return false;
		}

		// rename file ?
		if ($this->new_name!='') {
			$this->name = $this->_getNewName();
		}


		// upload file
		if (move_uploaded_file($this->tmp_name, $this->upload_dir.$this->name)) {
				chmod($this->upload_dir.$this->name , 0666);
			return true;
		} else {
			$this->error = __text('ERROR_MOVE_UPLOAD').' '.$this->tmp_name.' > '.$this->upload_dir.$this->name;
			return false;

		}


	}

    /**
     * Check if extension is in whitelist
     *
     * @return boolean
     */
    function _isWhitelisted() {
        if (count($this->extensions)>0) {
            if (in_array($this->extension,$this->extensions)) {
                // TODO MIME CHECK
                return true;
            } else {
                $this->error = __text('ERROR_EXTENSION_NOTALLOWED');
                return false;
            }
        }
    }
    
	/**
	 * Check if extension is allowed
	 *
	 * @return boolean
	 */
	function _isAllowedExtension() {
		if (count($this->extension_whitelist)>0) {
			if (in_array(str_replace('.','', $this->extension),$this->extension_whitelist)) {
				// TODO MIME CHECK
				return true;
			} else {
				$this->error = __text('ERROR_EXTENSION_NOTALLOWED');
				return false;
			}
		}
	}

	/**
	 * return new filename, with extension
	 *
	 * @return string new filename
	 */
	function _getNewName() {
		return $this->new_name;
//		return $this->new_name.$this->extension;
	}

	/**
	 * Extract extension from filename
	 *
	 * @param string $filename
	 * @return string extension
	 */
	function _getExtension($filename) {
		$extension = strtolower(strrchr($filename,"."));
		return $extension;
	}

	/**
	 * Check if file exists on server
	 *
	 * @param string $filename
	 * @return boolean
	 */
	function _checkFileExists($filename) {
		if (file_exists($this->upload_dir.$filename)) {
			return false;
		} else {
			return true;
		}

	}

	function _checkMimeType() {

		$mime = $this->type;
		$extension = $this->extension;
		$extension = str_replace('.','',$extension);
		switch ($extension) {

			case 'bmp':
			case 'gif':
			case 'png':
				if ($mime!='image/'.$extension) return false;
				return true;
				break;
			case 'jpeg':
			case 'jpg':
			case 'jpe':
				if ($mime!='image/jpeg') return false;
				return true;
				break;

			default:
				return true;
		}



	}


}

