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

require_once(_SRV_WEBROOT._SRV_WEB_FRAMEWORK.'classes/class.recursive.php');
class product_to_attributes extends product {

	public $_table = TABLE_PRODUCTS_TO_ATTRIBUTES;
	public $_table_attributes = TABLE_PRODUCTS_ATTRIBUTES;
	public $_table_lang = TABLE_PRODUCTS_ATTRIBUTES_DESCRIPTION;
	protected $_table_seo = null;
	public $_master_key = 'attributes_id';
    protected $_icons_path = "images/icons/";

    function __construct() {
       parent::__construct();
       $this->indexID = time().'-Prod2Attrib';

       $add_to_url = (isset($_SESSION['admin_user']['admin_key']))? '&sec='.$_SESSION['admin_user']['admin_key']: '';

       $this->getTreeUrl = 'adminHandler.php?plugin=xt_master_slave&load_section=product_to_attributes'.$add_to_url.'&pg=getNode&';
       $this->getSaveUrl = 'adminHandler.php?plugin=xt_master_slave&load_section=product_to_attributes'.$add_to_url.'&pg=setData&';
    }

	function setPosition ($position) {
		$this->position = $position;
	}

	function setProductsId ($id) {
	    $this->pID = $id;
	}
	function getProductsId () {
        return $this->pID;
	}

	function setData() {
        global $db;


        if ($this->url_data['attIds'] && $this->url_data['products_id']) {
            $db->CacheExecute("DELETE FROM " . $this->_table . " WHERE products_id = ?",array((int)$this->url_data['products_id']));

            $this->url_data['attIds'] = str_replace(array('[',']','"','\\'), '', $this->url_data['attIds']);
	        $att_ids = explode(',', $this->url_data['attIds']);

	        for ($i = 0; $i < count($att_ids); $i++) {

	            if ($att_ids[$i]) {

		        $record = $db->CacheExecute("select attributes_parent from " . TABLE_PRODUCTS_ATTRIBUTES . " where attributes_id = ? ",array((int)$att_ids[$i]));
				if($record->RecordCount() > 0){
					$parent = $record->fields['attributes_parent'];
				}

	            $data = array($this->_master_key => (int)$att_ids[$i], 'attributes_parent_id'=>$parent, 'products_id' => (int)$this->url_data['products_id']);
        	        $o = new adminDB_DataSave($this->_table, $data, false, __CLASS__);
        		    $obj = $o->saveDataSet();

	            }
	        }
	    }
        header('Content-Type: application/json; charset='._SYSTEM_CHARSET);
        echo json_encode($obj);
        die;
//        return
	}

	function getTreePanel()
    {
		if ($this->url_data['products_id'])
		    $this->setProductsId($this->url_data['products_id']);

	    $root = new PhpExt_Tree_AsyncTreeNode();
        $root->setText(__define('TEXT_PRODUCTS_TO_ATTRIBUTES'))
            ->setId('root')
            ->setId('root')
            ->setText(__define('TEXT_PRODUCTS_TO_ATTRIBUTES'))
            ->setDisabled(true)
            ->expandChildNodes();

        $tl = new PhpExt_Tree_TreeLoader();
        $tl->setDataUrl($this->getTreeUrl)
            ->attachListener('load',  new PhpExt_Listener(PhpExt_Javascript::functionDef('ms_tree_loader_load', "
                try {
                    //console.log(self, node, response);
                    node.cascade( function(child){
                        child.on('checkchange', function(self, checked) {
                            //console.log(checked, child.parentNode);
                            child.suspendEvents();
                            child.parentNode.cascade( function(child){
                                console.log(child.getUI().checked);
                                child.getUI().toggleCheck(false);
                                console.log(child.getUI().checked);
                            });
                            child.getUI().toggleCheck(true);
                            child.resumeEvents();
                        });
                    });
                } catch(e)
                {
                    console.error(e);
                }
            ", [ 'self', 'node', 'response' ])));
        if ($this->getProductsId())
            $tl->setBaseParams(array('products_id' => $this->getProductsId()));

        $tp = new PhpExt_Tree_TreePanel();
        $tp->setTitle(__define('TEXT_PRODUCTS_TO_ATTRIBUTES'))->setBaseCssClass('xt-filter-panel')
            ->setCssClass('ms_attr_treepanel')
            ->setid('ms_attr_treepanel')
            ->setRoot($root)
            ->setLoader($tl)
            ->setAutoScroll(true)
            ->setAutoWidth(false)
            ->attachListener('render',  new PhpExt_Listener(PhpExt_Javascript::functionDef('ms_variants_render', "
                try {
                    this.root.expand();
                } catch(e)
                {
                    console.error(e);
                }
            ")));

        $btn_next = new PhpExt_Handler(PhpExt_Javascript::stm("
                 var checked = Ext.encode(tree.getChecked('id'));
                 var conn = new Ext.data.Connection();
                 conn.request({
                 url: '".$this->getSaveUrl."',
                 method:'POST',
                 params: {'products_id': ".$this->getProductsId().", attIds: checked},
                 error: function(responseObject) {
                            Ext.Msg.alert('".__define('TEXT_ALERT')."', '".__define('TEXT_NO_SUCCESS')."');
                          },
                 waitMsg: 'SAVED..',
                 success: function(responseObject) {
                            Ext.Msg.alert('".__define('TEXT_ALERT')."','".__define('TEXT_SUCCESS')."');
                          }
         });"));
        $cb_hide_unused = PhpExt_Form_Checkbox::createCheckbox('cb_hide_unsed', 'nicht verwendete ausblenden', 'cb_hide_unsed', 1)
            ->setBoxLabel('nicht verwendete ausblenden')
            ->setChecked(false)
            ->attachListener('check', new PhpExt_Listener(PhpExt_Javascript::functionDef( 'ms_checkchanged',"
                 if(root.loaded)
                 {
                     var checked = self.checked;
                     //console.log(checked, root, root.ownerTree.loader.dataUrl);
                     if(checked)
                     {
                        tree.loader.dataUrl = tree.loader.dataUrl + '&hide_unused=1';
                     }
                     else {
                        tree.loader.dataUrl = tree.loader.dataUrl.replace('&hide_unused=1', '');
                     }
                     //console.log(root.ownerTree.loader.dataUrl);
                     root.reload();
                 }     
         ", ['self'])));

        $tb = $tp->getTopToolbar();
        $tb->addButton(2,__define('TEXT_MS_NEXT'), $this->_icons_path.'arrow_right.png', $btn_next);
        $tb->addSeparator(3);
        $tb->addItem(4, $cb_hide_unused);

        $tp->setRenderTo(PhpExt_Javascript::variable("Ext.get('".$this->indexID."')"));

        $js = PhpExt_Ext::OnReady(
            PhpExt_Javascript::stm(PhpExt_QuickTips::init()),
            $root->getJavascript(false, "root"),
        	$tp->getJavascript(false, "tree")
        );


        return '<script type="text/javascript">'. $js . '

            let width = window.getComputedStyle(document.getElementById("ms_attr_treepanel")).width;
            let els2change = document.querySelectorAll(".ms_attr_treepanel > .xt-filter-panel-header, .ms_attr_treepanel > .xt-filter-panel-bwrap > .xt-filter-panel-tbar");
            for(el of els2change)
            {
                el.style.width = width;
            }
            
            </script>
            <style type="text/css">
            .force-no-background-image {background-image: none !important; width:6px !important;}
            .ms_attr_treepanel .x-tree-node-cb { margin-left: 4px; }
            .ms_attr_treepanel > .xt-filter-panel-header{ position:fixed; z-index:20; background-color: white; width: 10%; padding-top: 10px }
            .ms_attr_treepanel > .xt-filter-panel-bwrap > .xt-filter-panel-tbar { position:fixed; z-index:19; background-color: white; width: 10% ; padding-top: 35px; padding-bottom: 5px;}
            .ms_attr_treepanel > .xt-filter-panel-bwrap > .xt-filter-panel-body { padding-top: 80px }
            </style> 
        <div id="'.$this->indexID.'"></div>';

	}

	function getNode() {
		if ($this->url_data['products_id'])
		    $this->setProductsId($this->url_data['products_id']);

		$table_data = new adminDB_DataRead($this->_table, null, null, $this->_master_key, 'products_id='.$this->getProductsId());
		//$table_data = new adminDB_DataRead($this->_table, null, null, $this->_master_key, 'products_id='.$this->getProductsId(), 50000, '', '', ' ORDER BY attributes_model ASC');
        //$table_data->setJoinCondtion('LEFT JOIN '.$this->_table_attributes. ' pa ON pa.attributes_id = '.$this->_table.".attributes_id " );

		//__debug($table_data);

		$d = new recursive(TABLE_PRODUCTS_ATTRIBUTES, $this->_master_key, 'attributes_parent');

		$attributesData = $table_data->getData();

	//	__debug($attributesData);

		$expand = array();
		$attr_parents = $att_ids = [];
		if(is_array($attributesData)){
			foreach ($attributesData as $adata) {
			    $path = $d->getPath($adata[$this->_master_key]);


			    $expand = array_merge($expand, $path);
			    $att_ids[] = $adata[$this->_master_key];

			    $attr_parents[] = $adata['attributes_parent_id'];
			}
		}

		//__debug($expand);

        $d->setLangTable(TABLE_PRODUCTS_ATTRIBUTES_DESCRIPTION);
        $d->setDisplayKey('attributes_name');
        $d->setDisplayLang(true);
        $data = $d->_getLevelItems($this->url_data['node']);
        $nodes = $new_atts = [];
        if(is_array($data)){
	        foreach ($data as $att_data) {
	            if($this->url_data["hide_unused"] == '1' && $att_data['attributes_parent'] == 0
                    && !in_array($att_data['attributes_id'], $attr_parents))
	                continue;
	            $checked = false;
	            if(is_array($att_data)&&is_array($att_ids)){
		            if (in_array($att_data[$this->_master_key], $att_ids)) {
		                $checked = true;
		            }
	            }
	            $expanded = false;
	            if (in_array($att_data[$this->_master_key], $expand)) {
	                $expanded = true;
	            }

				if($att_data['attributes_parent']!=0)
	                $new_atts[] = array(
	                    'id' => $att_data[$this->_master_key],
                        'name' => $att_data[$d->getDisplayKey()],
                        'model' => $att_data['attributes_model'],
                        'sort_order' => $att_data['sort_order'],
                        'text' => $att_data[$d->getDisplayKey()] . " (" . $att_data['attributes_model'] . ")",
                        'checked' => $checked,
                        'leaf' => true,
                        'iconCls' => 'force-no-background-image',
                        'expandable' => false);
	            else
                {
                    $parent_attrs = ['id' => $att_data[$this->_master_key], 'text' => $att_data[$d->getDisplayKey()] . " (" . $att_data['attributes_model'] . ")", 'expanded' => $expanded];
                    if($this->url_data['tmp'] == '1')
                        $parent_attrs['checked'] = $checked;
                    $new_atts[] = $parent_attrs;
                }

	        }
        }

        $sort = defined('BACKEND_ATTRIBUTES_SORT_1') ? BACKEND_ATTRIBUTES_SORT_1 : 'name';
        usort($new_atts, function ($a, $b) use ($sort) {
            return strcmp($a[$sort], $b[$sort]);
        });


        header('Content-Type: application/json; charset='._SYSTEM_CHARSET);
        return json_encode($new_atts);
	}
}
