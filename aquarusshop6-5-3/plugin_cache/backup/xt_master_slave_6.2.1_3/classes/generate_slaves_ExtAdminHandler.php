<?php
/*
 #########################################################################
 #                       xt:Commerce Shopsoftware
 # ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 #
 # Copyright 2007-2018 xt:Commerce International Ltd. All Rights Reserved.
 # This file may not be redistributed in whole or significant part.
 # Content of this file is Protected By International Copyright Laws.
 #
 # ~~~~~~ xt:Commerce Shopsoftware IS NOT FREE SOFTWARE ~~~~~~~
 #
 # http://www.xt-commerce.com
 #
 # ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 #
 # @copyright xt:Commerce International Ltd., www.xt-commerce.com
 #
 # ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 #
 # xt:Commerce International Ltd., Kafkasou 9, Aglantzia, CY-2112 Nicosia
 #
 # office@xt-commerce.com
 #
 #########################################################################
 */

defined('_VALID_CALL') or die('Direct Access is not allowed.');

class generate_slaves_ExtAdminHandler  extends  ExtAdminHandler
{
    function __construct($extAdminHandler)
    {
        foreach ($extAdminHandler as $key => $value)
        {
            $this->$key = $value;
        }
    }
    

    function multiselectStm ($var = 'record_ids', $addModifiedRecordsData = true) {
        $js = "
             var records = new Array();
             records = ".$this->code."ds.getModifiedRecords();
             ".$this->_getGridModifiedRecordsData()."
		 	 var ".$var." = '';
		 	 for (var i = 0; i < records.length; i++) {
		 	     if (records[i].get('selectedItem'))
		 	      ".$var." += records[i].get('".$this->getMasterKey()."') + ',';
		 	 }


		 	 ";
        return $js;
    }

   function UploadImage($id='')
   {
		if ($id)
			$u_js = "var edit_id = ".$id.";";
		else $u_js = "var edit_id = record.data.products_id;";

		$mg = new MediaGallery();
		$code = $mg->_getParentClass(2);
	
		$mediaWindow = $this->getMediaWindow3(true, true, true, 'images', "&mgID=2&link_id='+edit_id+'",$code);
		$u_js .= $mediaWindow->getJavascript(false, "new_window") . "new_window.show();";
		return $u_js;
   }

    function getMediaWindow3($show_grid = true, $show_ck_upload = true, $show_simple_upload = true, $type = 'images', $params = '',$force_code='')
    {
        $code = $force_code;

        $tab = array();
        $add_to_url = (isset($_SESSION['admin_user']['admin_key']))? "&sec=".$_SESSION['admin_user']['admin_key']."": '';
        // tab 1 grid
        if ($show_grid)
        {
            $tab[] = array(
                'url' => 'adminHandler.php',
                'url_short' => true,
                'params' => "mgId=2&link_id='+edit_id+'&load_section=MediaImageList&pg=overview&currentType=".$code.$add_to_url,
                'title' => 'TEXT_IMAGES'
            );
        }

        // tab 2 search
        if ($show_grid)
        {
            $tab[] = array(
                'url' => 'adminHandler.php',
                'url_short' => true,
                'params' => 'load_section=MediaImageSearch&pg=overview&currentType='.$code.$add_to_url.$params,
                'title' => 'TEXT_SEARCH_IMAGES'
            );
        }


        // tab 4 simple upload
        if ($show_simple_upload)
        {
            $tab[] = array(
                'url' => 'upload.php',
                'url_short' => true,
                'params' => "mgId=2&link_id='+edit_id+'&load_section=foo&currentType=".$code.$add_to_url,
                'title' => 'TEXT_SIMPLE_UPLOAD'
            );
        }


        // tab 5 ckfinder upload
        if ($show_ck_upload)
        {
            $tab[] = array(
                'url' => 'uploadCKFinder.php',
                'url_short' => true,
                'params' => "mgId=2&link_id='+edit_id+'uploadtype=single&load_section=foo&currentType=".$code.$add_to_url,
                'title' => 'TEXT_CKFINDER_UPLOAD'
            );
        }

        return $this->_TabRemoteWindow('TEXT_MEDIA_MANAGER', $tab);
    }

	function multiselectStm2 ($var = 'record_ids') {
        $js = "
             var records = new Array();
             var p = '';
             records = ".$this->code."ds.getModifiedRecords();
             ".$this->_getGridModifiedRecordsData()."
		 	 var ".$var." = '';
		 	 var not_saved_data='';
		 	 for (var i = 0; i < records.length; i++) {
		 	     if (records[i].modified.products_name || records[i].modified.products_quantity || 
                     records[i].modified.products_weight || records[i].modified.products_price || records[i].modified.products_model){
                    not_saved_data += records[i].get('".$this->getMasterKey()."') + ',';
                 }
		 	     if (records[i].get('selectedItem')){
		 	        ".$var." += records[i].get('".$this->getMasterKey()."') + ',';
                 }
				 p += records[i].get('".$this->getMasterKey()."') + ',';
		 	 }
		 	 
		 	if (not_saved_data!='')
            {
                   Ext.Msg.show({
                   title:'".TEXT_MASTER_SLAVE."',
                   msg: '".TEXT_MASTER_SLAVE_UNSAVED_DATA."',
                   buttons: Ext.Msg.YESNO,
                   animEl: 'elId',
                   fn: function(btn){runUNsavedDataCheck(btn);},
                   icon: Ext.MessageBox.QUESTION
                });
                
                function runUNsavedDataCheck(btn){
                    if (btn == 'yes') {
                        contentTabs.remove(contentTabs.getActiveTab());
                        var gh=Ext.getCmp('generated_slavesgridForm'); if (gh) contentTabs.remove('node_generated_slaves'); 
                        addTab('adminHandler.php?type=generate_slaves&plugin=xt_master_slave&load_section=generated_slaves&pg=overview&products_id='+edit_id+'&record_ids='+p+'&parentNode=node_generated_slaves','". TEXT_GENERATE_SLAVES_STEP_3 ."');
                    }else {
                        addTab('adminHandler.php?type=generate_slaves&plugin=xt_master_slave&load_section=generate_slaves&pg=overview&products_id='+edit_id+'&parentNode=node_generate_slaves','". TEXT_GENERATE_SLAVES_STEP_2 ."');
                    }
        
                };
                
                return true;
            }
			if (".$var."=='')
			{
    			   Ext.Msg.show({
    			   title:'".TEXT_MASTER_SLAVE."',
    			   msg: '".TEXT_MASTER_SLAVE_NO_ITEMS_SELECTED."',
    			   buttons: Ext.Msg.YESNO,
    			   animEl: 'elId',
    			   fn: function(btn){runSelectedItemsChecked(btn);},
    			   icon: Ext.MessageBox.QUESTION
    			});
				
                
                
				function runSelectedItemsChecked(btn){
			  		if (btn == 'yes') {
			  			contentTabs.remove(contentTabs.getActiveTab());
						var gh=Ext.getCmp('generated_slavesgridForm'); if (gh) contentTabs.remove('node_generated_slaves'); 
						addTab('adminHandler.php?type=generate_slaves&plugin=xt_master_slave&load_section=generated_slaves&pg=overview&products_id='+edit_id+'&record_ids='+p+'&parentNode=node_generated_slaves','". TEXT_GENERATE_SLAVES_STEP_3 ."');
					}else {
						addTab('adminHandler.php?type=generate_slaves&plugin=xt_master_slave&load_section=generate_slaves&pg=overview&products_id='+edit_id+'&parentNode=node_generate_slaves','". TEXT_GENERATE_SLAVES_STEP_2 ."');
					}
		
				};
			}
			else
			{
				contentTabs.remove(contentTabs.getActiveTab());
				var gh=Ext.getCmp('generated_slavesgridForm'); if (gh) contentTabs.remove('node_generated_slaves'); 
				addTab('adminHandler.php?type=generate_slaves&plugin=xt_master_slave&load_section=generated_slaves&pg=overview&products_id='+edit_id+'&record_ids='+p+'&parentNode=node_generated_slaves','". TEXT_GENERATE_SLAVES_STEP_3 ."');
					
			}";
			
        return $js;
    }
}
