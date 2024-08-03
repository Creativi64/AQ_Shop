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

class email_to_media extends product {

	public $_table = TABLE_MEDIA_LINK;
	public $_table_lang = null;
	protected $_table_seo = null;
	public $_master_key = 'ml_id';
    protected $_icons_path = "images/icons/";
    public string $indexID;
    public string $getTreeUrl;
    private string $getSaveUrl;
    public mixed $pED;

    function __construct() {
       $this->indexID = time().'-Mail2Med';

       if(USER_POSITION == 'admin')
       {
           $this->getTreeUrl = 'adminHandler.php?load_section=email_to_media&sec=' . $_SESSION['admin_user']['admin_key'] . '&pg=getNode&';
           $this->getSaveUrl = 'adminHandler.php?load_section=email_to_media&sec=' . $_SESSION['admin_user']['admin_key'] . '&pg=setData&';
       }
    }

	function setEmailId ($id) {
	    $this->pED = $id;
	}
	function getEmailId () {
        return $this->pED;
	}	

	function getTreePanel() {
		if ($this->url_data['tpl_id'])
		$this->setEmailId($this->url_data['tpl_id']);
	    $root = new PhpExt_Tree_AsyncTreeNode();
        $root->setText("Media")
              ->setId('root');

        $tl = new PhpExt_Tree_TreeLoader();
        $tl->setDataUrl($this->getTreeUrl);
        if ($this->getEmailId())
        $tl->setBaseParams(array('tpl_id' => $this->getEmailId()));



        $tp = new PhpExt_Tree_TreePanel();
        $tp->setTitle(__text('TEXT_PRODUCTS_TO_MEDIA'))
          ->setRoot($root)
          ->setLoader($tl)
 //         ->setRootVisible(false)
          ->setAutoScroll(true)
//          ->setCollapsible(true)
          ->setAutoWidth(true);

         $tb = $tp->getBottomToolbar();

                $tb->addButton(1,__text('TEXT_SAVE'), $this->_icons_path.'disk.png',new PhpExt_Handler(PhpExt_Javascript::stm("
                 var checked = Ext.encode(tree.getChecked('id'));
                 var conn = new Ext.data.Connection();
                 conn.request({
                 url: '".$this->getSaveUrl."',
                 method:'POST',
                 params: {'tpl_id': ".$this->getEmailId().", medIds: checked},
                 error: function(responseObject) {
                            Ext.Msg.alert('".__text('TEXT_ALERT')."', '".__text('TEXT_NO_SUCCESS')."');
                          },
                 waitMsg: 'SAVED..',
                 success: function(responseObject) {
                            Ext.Msg.alert('".__text('TEXT_ALERT')."','".__text('TEXT_SUCCESS')."');
                          }
                 });")));
       $tp->setRenderTo(PhpExt_Javascript::variable("Ext.get('".$this->indexID."')"));

        $js = PhpExt_Ext::OnReady(
            PhpExt_Javascript::stm(PhpExt_QuickTips::init()),

            $root->getJavascript(false, "root"),
        	$tp->getJavascript(false, "tree")

        );


        return '<script type="text/javascript">'. $js .'</script><div id="'.$this->indexID.'"></div>';

	}	
	
	function getNode() {
		if ($this->url_data['tpl_id'])
		$this->setEmailId($this->url_data['tpl_id']);
		
			$table_data = new adminDB_DataRead($this->_table, null, null, $this->_master_key, 'link_id='. (int)$this->getEmailId().' and class="email" and type="media"');
			$FileData = $table_data->getData();
			
			if(is_array($FileData)){
				foreach ($FileData as $fdata) {
				    $f_ids[] = $fdata['m_id'];
				}
			}			
			
	   		$data = $this->getMedia($this->url_data['node']);
	   	
		        if(is_array($data)){
			        foreach ($data as $m_data) {
			            $checked = false;
			            
			        	if(is_array($m_data)&&is_array($f_ids)){
				            if (in_array($m_data['id'], $f_ids)) {
				                $checked = true;
				            }
	            		}			            
			            
			            if($m_data['id']=='free' || $m_data['id'] =='order'){
			            $new_media[] = array('id' => $m_data['id'], 'text' => $m_data['file'], 'expanded' => false);
			            }else{
			            $new_media[] = array('id' => $m_data['id'], 'text' => $m_data['file'], 'checked' => $checked, 'expanded'=>false);
			            }
			        }
	        	}	   		
	   		        
        header('Content-Type: application/json; charset='._SYSTEM_CHARSET);
        return json_encode($new_media);
	}	
	
	function getMedia($download_status){

		if($download_status=='root'){
			$data[0] = array('id'=>'free', 'file'=>__text('TEXT_FREE_DOWNLOAD'));
			return $data;			
		}else{
            $table_data = new adminDB_DataRead(TABLE_MEDIA, null, null, 'id', 'download_status="'.$download_status.'" and type="files"');
		    return $table_data->getData();
		}
	}	

	function setData() {
        global $db;

        if ($this->url_data['medIds'] && $this->url_data['tpl_id']) {
        	
        	$m = new MediaData();
        	$m->unsetAllMediaLink($this->url_data['tpl_id'], 'email', 'media');
 
            $this->url_data['medIds'] = str_replace(array('[',']','"','\\'), '', $this->url_data['medIds']);
	        $med_ids = preg_split('/,/', $this->url_data['medIds']);

	        for ($i = 0; $i < count($med_ids); $i++) {
	            if ($med_ids[$i]) {
	            $data = array(
                    'm_id' => (int)$med_ids[$i],
                    'link_id' => $this->url_data['tpl_id'],
                    'class' => 'email',
                    'type' => 'media'
                );
                    $o = new adminDB_DataSave($this->_table, $data, false, __CLASS__);
        		    $obj = $o->saveDataSet();
	            }
	        }
	    }
        header('Content-Type: application/json; charset='._SYSTEM_CHARSET);
        echo json_encode($obj);
        die;
	}

    function getAttachments($tpl_id) {
        global $db;

        $sql = "SELECT * FROM " . TABLE_MEDIA_LINK . " ml, ".TABLE_MEDIA." m WHERE ml.link_id=? and ml.class='email' and ml.m_id=m.id";

        $rs = $db->Execute($sql, array($tpl_id));
        if ($rs->RecordCount()==0) {
            return false;
        } else {
            $files = array ();
            $path = _SRV_WEBROOT . 'media/files/';
            while (!$rs->EOF) {
                $files[] = $path . $rs->fields['file'];
                $rs->MoveNext();
            }
            return $files;
        }
    }
}