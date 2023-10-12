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

//require_once(_SRV_WEBROOT._SRV_WEB_FRAMEWORK.'admin/classes/class.navigation.php');


class admin_handler_base extends extJS
{
    protected $blank_image_url;
    protected $is_demo;

	function __construct ()
    {
        parent::__construct();

		$this->blank_image_url = _SRV_WEB.'images/s.gif';
		$this->defineVars();
		$this->is_demo = false;
	}
	function defineVars () {
		$string = $this->setVar('contentTabs');
		$string .= $this->setVar('tree');
		$string .= $this->setVar('trees', 'new Array()');
		$string .= $this->setVarValue('Ext.BLANK_IMAGE_URL', '"'.$this->blank_image_url.'"');
		$this->_setJS_String($string);
	}
	function clickHandler2 () {
		ob_start(); ?>
		if (e.attributes) {
        if (e.attributes.url_e) {
            var newwin = window.open(e.attributes.url_e, '_blank'); newwin.focus();
        }
		if (e.attributes.url_i) {
		addITab(e.attributes.url_i, e.text, e.attributes.id);
		}
		if (e.attributes.url_d) {
		e.attributes.url_d = e.attributes.url_d+'&parentNode='+e.attributes.id;
		addTab(e.attributes.url_d, e.text, e.attributes.id);
		}
		}
		if (e.url_i) {
		addITab( e.url_i, e.text, e.itemId);
		}
		if (e.url_d) {
		e.url_d = e.url_d+'&parentNode='+e.itemId;
		addTab(e.url_d, e.text, e.itemId);
		}
		<?php
		$string = ob_get_contents();
		ob_end_clean();
		$string = $this->jsFunction('clickHandler2', $string, 'e, obj, op');
		$this->_setJS_String($string);
	}


	function clickHandler2_ssl () {
		ob_start(); ?>

		if (e.url_i) {
		window.open(e.url_i);
		}

		<?php
		$string = ob_get_contents();
		ob_end_clean();
		$string = $this->jsFunction('clickHandler2_ssl', $string, 'e, obj, op');
		$this->_setJS_String($string);
	}

	function clickHandler3 () {

		$string = '
	  		Ext.Ajax.request({
	url : \'versioncheck.php\' ,
	params : { module : \'core\' },
	method: \'GET\',
	success: function ( result, request ) {
		Ext.Msg.alert(\'Updatecheck\', result.responseText);
	},
	failure: function ( result, request) {
		Ext.Msg.alert(\'Updatecheck\', result.responseText);
	}
});

	  	';
		$string = $this->jsFunction('clickHandler3', $string, 'e, obj, op');
		$this->_setJS_String($string);
	}

    function clickHandlerBlank()
    {
        ob_start(); ?>

        if (e.url_i) {
        window.open(e.url_i,'_blank');
        }

        <?php
        $string = ob_get_contents();
        ob_end_clean();
        $string = $this->jsFunction('clickHandlerBlank', $string, 'e, obj, op');
        $this->_setJS_String($string);
    }


	function logOutUser () {
		$string = $this->jsFunction('logOutUser', $this->WindowLocation("login.php?logout=1"));
		$this->_setJS_String($string);
	}

	function pinger() {
		$add_to_url = (isset($_SESSION['admin_user']['admin_key']))? '?sec='.$_SESSION['admin_user']['admin_key']: '';
		$string =" var ses = '".$add_to_url."'; \n";

		$string .= "
        var pingtask = {
            run: function(){
                Ext.Ajax.request({
	  				url: 'adminHandler.php'+ses,
	  				method: 'GET',
	  				params: { keepalive:true},
	  				success: function(response) {
	  				    var obj = Ext.decode(response.responseText);
	  				    if (obj.success == false) {
	  				        var msg = obj.message ? obj.message : '';
	  				        msg += obj.error_message ? '<br />' + obj.error_message : '';
	  				        if(msg.length == 0)
	  				            msg = 'Unbekannter Fehler<br/>Unknown error';
	  				        Ext.Msg.alert('".__define('TEXT_ALERT')."', msg); 
	  				    }
			 			
			          }
	  			});
            },
            interval: 600000 // 10 min
        }

        setTimeout(function(){
        var pinger = new Ext.util.TaskRunner();
        pinger.start(pingtask);
        }, 600000);"; // 10 min
		$this->_setJS_String($string);
	}
	
	function crontrigger() {
			
		$string = "
        var crontask = {
            run: function(){
                Ext.Ajax.request({
	  				url: '../cronjob.php',
	  				method: 'GET',
	  				params: { keepalive:true}
	  			});
            },
            interval: 30000 //30 seconds
        }
	
	    setTimeout(function(){
        var cront = new Ext.util.TaskRunner();
        cront.start(crontask);
        }, 60000);";
		$this->_setJS_String($string);
	}


	function addTab () {
		$options = "{ xtype : 'panel', title: title, closable:true, enableTabScroll:true, id:panelId, layout: 'fit' }";
		//$wrapperOptions = "{ xtype: 'panel', html: 'Loading <br/><br/>', layout: 'fit', id:panelId+'Wrapper'}";
		$url_options = "{url : url, params: { parentNode: panelId }, scripts : true}";

		$this->_call_Method_JS_Function('contentTabs');
		$this->_add_Method_JS_Funktion('add', $options);
		$this->_add_Method_JS_Funktion('show');

		//$this->_call_Method_JS_Function('; Ext.getCmp( panelId )');
		//$this->_add_Method_JS_Funktion('add', $wrapperOptions);
		$this->_add_Method_JS_Funktion('load', $url_options);
		//		$string = "alert(contentTabs.find.apply(this, {title: title}));";
		$js_str = $this->_getTMP_JS_String();

		$add_to_url = (isset($_SESSION['admin_user']['admin_key']))? '&sec='.$_SESSION['admin_user']['admin_key']: '';
		$string =" var ses = '".$add_to_url."'; \n";

		$string .= <<<END
    if( typeof(panelId) != 'string' || !panelId.length )
    {
        var edit_id = '';
		var ind = url.indexOf("parentNode=");
		if (ind>0)
			var pattern = new Array("parentNode=", "edit_id=", "new=", "c_oID=");
        else var pattern = new Array("load_section=", "edit_id=", "new=", "gridHandle=", "c_oID=");
        var my_arr = url.split("&");
        for (y = 0; y < my_arr.length; y++) {
            for (pattern_key = 0; pattern_key < pattern.length; pattern_key++) 
			{
                var match = my_arr[y].split(pattern[pattern_key]);
                if (match.length > 1) edit_id = edit_id + ((pattern[pattern_key] == "load_section=") ? 'node_' : '') + match[1].toString() + ((pattern_key == (pattern.length + 1)) ? '' : '_');
            }
        }
	  panelId = edit_id.substring(0, edit_id.length - 1);
	  if (panelId=='') panelId = '_pnl'+new Date().getTime()+'_'+Math.floor(Math.random()*10000);
END;

		$string .= <<<END
    }
	url = url +ses;
	
	var ind = url.indexOf("load_section=configuration") 
	    + url.indexOf("store_id=");
	if(ind >= 0)
	{
        var expl = url.split("store_id=");
        if (expl.length > 1) {
            var expl2 = expl[1].split("&");
            title = title + ' Shop ' + expl2[0];
        }
    }      
	else {
	    var ind = url.indexOf("parentNode=system_status_order_status")
	        + url.indexOf("load_section=configuration") ;
        if(ind < -1)
        {
	var expl = url.split("edit_id=");
    if (expl.length > 1) {
        var expl2 = expl[1].split("&");
        title = title + ' ' + expl2[0];
    }
        }
	}
	

	
    if( !Ext.getCmp( panelId ) ) 
    {
      {$js_str}
    }
    else
    {
      Ext.getCmp( panelId ).show();
    }
	var ind = url.indexOf("parentNode=node_product");
	if (ind>0){
	   var expl = url.split("edit_id=");
	   if (expl.length>1){
		   var expl2 = expl[1].split("&");
		   CallOnLoad(expl2[0],url);
	   }
	}
END;
		$string = $this->jsFunction('addTab', $string, 'url, title, panelId');



		$this->_setJS_String($string);

	}

	function addITab () {
		$options = "{title: title,i_d: 'webde',defaultSrc : url,closable:true, enableTabScroll:true, id:panelId, layout: 'fit'}";
		$this->_call_Method_JS_Function('contentTabs');
		$this->_add_Method_JS_Funktion('add', $options);
		$this->_add_Method_JS_Funktion('show');
		$string = $this->_getTMP_JS_String();
		$string = "if( !Ext.getCmp( panelId ) ) {".$string."} else { Ext.getCmp( panelId ).show(); }";
		$string = $this->jsFunction('addITab', $string, 'url, title, panelId');
		$this->_setJS_String($string);
	}

	function TreePanelOptions ($data) {

	}

	function  Tree () {
		global $xtc_acl,$db, $xtPlugin;

		$cat_view = $xtc_acl->checkPermission('category', 'view');

		$cat_new = $xtc_acl->checkPermission('category', 'new');
		$cat_edit = $xtc_acl->checkPermission('category', 'edit');
		$custom_link_edit = $xtc_acl->checkPermission('category', 'edit');
		$custom_link_del = $xtc_acl->checkPermission('category', 'delete');
		$cat_del = $xtc_acl->checkPermission('category', 'delete');

		$media_view = $xtc_acl->checkPermission('MediaGallery', 'view');
		$media_new = $xtc_acl->checkPermission('MediaGallery', 'new');
		$media_edit = $xtc_acl->checkPermission('MediaGallery', 'edit');
		$media_del = $xtc_acl->checkPermission('MediaGallery', 'delete');

		$shop_view = $xtc_acl->checkPermission('multistore', 'view');
		$shop_new = $xtc_acl->checkPermission('multistore', 'new');
		$shop_edit = $xtc_acl->checkPermission('multistore', 'edit');
		$shop_del = $xtc_acl->checkPermission('multistore', 'delete');

		$dashboard_view = $xtc_acl->checkPermission('dashboard', 'view');

		$product_new = $xtc_acl->checkPermission('product', 'new');
		$media_image_new = $xtc_acl->checkPermission('MediaImage', 'new');
		$media_file_new = $xtc_acl->checkPermission('MediaFiles', 'new');
		$arrMenu = array();

		($plugin_code = $xtPlugin->PluginCode('class.admin_handler:tree_perm')) ? eval($plugin_code) : false;

		$function_content = $this->setVar('Tree', 'Ext.tree');
		$navigation = new navigation();


        $arrMenu[] = Array('text' => __define("BUTTON_LOGOFF") .' : '.$xtc_acl->getUsername(),
			'handler' => 'logOutUser',
			'xtype' => 'tbbutton',
			'icon' => 'images/icons/door_out.png',
			'cls' => 'x-btn-text-icon');

		$default_manual = 'https://xtcommerce.atlassian.net/wiki/display/MANUAL/Home';
		if (isset($_SESSION['selected_language'])) {
			if ($_SESSION['selected_language']=='es')   $default_manual = 'https://xtcommerce.atlassian.net/wiki/pages/viewpage.action?pageId=16056449';
			if ($_SESSION['selected_language']=='en')   $default_manual = 'https://xtcommerce.atlassian.net/wiki/display/XME/xt%3ACommerce+Manual+%28EN%29+Home';

		}
		
		// XTC4-248
		$admin_ssl = '';

        $arrMenu[] = Array('text' => __define("TEXT_DOCUMENTATION"),
            'handler' => 'clickHandlerBlank',
			'xtype' => 'tbbutton',
			'itemId' => 'documentation',
			'url_i' => $default_manual,
			'cls' => 'x-btn-text-icon');

        $arrMenu[] = Array('text' => __define("TEXT_SUPPORTCENTER"),
            'handler' => 'clickHandlerBlank' . $admin_ssl,
			'xtype' => 'tbbutton',
			'itemId' => 'supportcenter',
			'url_i' => 'https://helpdesk.xt-commerce.com',
				
			'cls' => 'x-btn-text-icon');

        $arrMenu[] = Array('text' => __define("TEXT_ADMIN_NEWS"),
            'handler' => 'clickHandlerBlank' . $admin_ssl,
			'xtype' => 'tbbutton',
			'itemId' => 'newscenter',
			'url_i' => 'https://www.xt-commerce.com/blog',
			'cls' => 'x-btn-text-icon');

		$arrMenu[] = Array('text' => 'Marketplace',
            'handler' => 'clickHandlerBlank' . $admin_ssl,
			'xtype' => 'tbbutton',
			'itemId' => 'marketplace',
			'url_i' => 'https://addons.xt-commerce.com',
			'cls' => 'x-btn-text-icon');

        $arrMenu[] = Array('text' => __define("TEXT_CHECKFORUPDATES").' ('._SYSTEM_VERSION.')',
			'handler' => 'clickHandler3',
			'xtype' => 'tbbutton',
			'itemId' => 'updatecheck',
			'url_i' => 'https://updatecheck.xt-commerce.com',
			'icon' => 'images/icons/arrow_refresh.png',
			'cls' => 'x-btn-text-icon');

		$tmp_arrMenu = 	$navigation->readNorthNavLevel('clickHandler2');

		if(is_data($tmp_arrMenu))
			$arrMenu = array_merge($arrMenu, $tmp_arrMenu);

		/*
		 $arrMenu = Array_merge(Array(Array('text' => 'Logout : '.$xtc_acl->getUsername(),
		 'handler' => 'logOutUser',
		 'xtype' => 'tbbutton',
		 'icon' => 'images/icons/door_out.png',
		 'cls' => 'x-btn-text-icon')),
		 $navigation->readNorthNavLevel('clickHandler2'));
		 */

		$TopNav = $navigation->encodeMenuArray($arrMenu);

		$arrMenu = $navigation->readWestNavLevel();

		// NEW RIGHTS
		$navMenu = $arrMenu;
		unset($arrMenu);
		foreach ($navMenu as $key=>$val){
			$check_read = $xtc_acl->checkPermission($val['title'], 'read');

			if($check_read==true)
				$arrMenu[] = $val;
		}

		$arrMenu[] = Array(
			'text' => 'Partner',
			'url_i' => '',
			'url_d' => '',
			'tabtext' => 'Partner',
			'draggable' => 'false',
			'itemId' => '13',
			'pid' => 'veyton_partner',
			'icon' => 'images/icons/user_add.png',
			'iconCls' => 'fa fa-handshake',
			'type' => 'G',
			'id' => 'node_veyton_partner',
			'leaf' => '',
			'title' => 'veyton_partner');


		$i = 0;
		$rootNodeIds = array();

		if(is_array($arrMenu)){
			foreach($arrMenu as $item) {

				if ($item['type'] == 'G') {

					$rootNodeIds[$item['pid']] = "root_".$i;

					$function_content .="\n var tree_$i = new Tree.TreePanel({";
                    $function_content .="\n		        autoScroll:true,";
                    $function_content .="\n		        animate:false,";
                    $function_content .="\n          title:'".$item['text']."',		        ";
                    $function_content .="\n		        enableDD:true,";


                    $function_content .="\n		        containerScroll: true,";
                    $function_content .="\n			    			singleExpand: false, ";
                    if ($item['icon'] != '')  $function_content .="\n						    icon : '".$item['icon']."', ";
                    $function_content .="\n						    rootVisible : false";
                    if ($item['iconCls'] != '')   $function_content .=",\n						    iconCls:'".$item['iconCls']."'";
                    $function_content .="});";
                    $function_content .="\n		        
	  		
	  		
                        setTimeout(function() {
                        
                            tree_$i.loader = new Tree.TreeLoader({
                        
                                    createNode: function(attr) { 
                                    try
                                    {
                                        console.log('create node');
                                        attr.uiProvider = Tree.HelpTreeNodeUI;
                                        return Ext.tree.TreeLoader.prototype.createNode.call(this, attr);
                                    }
                                    catch(e)
                                    {
                                        console.log(e);
                                    }
                            } ,
                        ";

                    $function_content .="\n		            dataUrl:'get-nodes.php'";
                    $function_content .="\n		        });
                                        //console.log(tree_$i,tree_$i.loader);
                                        tree_$i.getLoader().load(tree_$i.root); ";
                    $function_content .="\n		    }, $i*300 );";

                    $function_content .="\n		    // set the root node";
                    $function_content .="\n		    var root_$i = new Tree.AsyncTreeNode({";
                    $function_content .="\n		        text: '".$item['text']."',";
                    $function_content .="\n		        draggable:false,";
                    $function_content .="\n		        id:'node_".$item['pid']."'";
                    $function_content .="\n		    });";
                    $function_content .="\n		    tree_$i.setRootNode(root_$i);";
                    $function_content .="\n		    ";
                    $function_content .="		    tree_$i.on('click', clickHandler2);";


                    // contextmenu
                    $function_content .="

                    function canBeMoved(old_parent_id, new_parent_id) {
                        var old_parts = old_parent_id.split('_catst_'),
                            old_store_id = null,
                            new_parts = new_parent_id.split('_catst_'),
                            new_store_id = null;
            
                        if (new_parent_id.indexOf('unasigned') != -1) {
                            return false;
                        }
            
                        if (old_parts.length > 1) {
                            old_store_id = old_parts[1];
                        } else {
                            old_store_id = old_parts[0];
                            old_store_id = old_store_id.replace('node_cat_store', '');
                        }
            
                        if (new_parts.length > 1) {
                            new_store_id = new_parts[1];
                        } else {
                            new_store_id = new_parts[0];
                            new_store_id = new_store_id.replace('node_cat_store', '');
                        }
            
                        return (new_store_id == old_store_id);
                    }
            
                    tree_$i.on('contextmenu', xtcontextMenu);
            
                    tree_$i.on('click',function(node,index) {
                        var nodeId = node.id;
                    });
            
            
                    tree_$i.on('beforemovenode',function(tree,node,oldParent,newParent,index){
                        var targetNodeId = newParent.id;
                        var sourceNodeId = oldParent.id;
            
                        if (!canBeMoved(sourceNodeId, targetNodeId)) {
                            Ext.Msg.alert('Not Allowed','Moving this Node is not allowed');
                            return false;
                        }
                    });
            
            
                    tree_$i.on('nodedrop', function (e){
                        var dropId;
                        var targetId;
                        var point;
                        dropId = e.dropNode.id;
                        targetId = e.target.id;
                        point = e.point;
                        if (canBeMoved(dropId, targetId)) {
                            Ext.Ajax.request({
                                url: 'move-nodes.php',
                                params: { targetId: targetId, dropId:dropId, position:point},
                                success: reloadCatTree()
                            });
                        return true;
                        } else {
                        return false;
                        }
                    });
            
                    ";

                    $function_content .="\n";
                    $function_content .="\n   trees[$i] = tree_$i;";
                    ++$i;
	  	        }

				if (($item['type'] == 'I') && ($item['url_i'] != '')) {
					$function_content .="\n  var ifr_$i = new Ext.ux.ManagedIframePanel({";
                    $function_content .="\n          title:'".$item['text']."'";
                    if ($item['icon'] != '')  $function_content .="\n						    ,icon : '".$item['icon']."' ";
                    if ($item['iconCls'] != '')   $function_content .="\n						    ,iconCls:'".$item['iconCls']."'";
                    $function_content .="\n        ,defaultSrc : '".$item['url_i']."'";
                    $function_content .="\n });";
                    $function_content .="\n trees[$i] = ifr_$i;";
                    ++$i;
                }

				if (($item['type'] == 'I') && ($item['url_d'] != '')) {
					$function_content .="\n  var ifr_$i = new Ext.Panel({";
                    $function_content .="\n          id:'".$item['id']."'";
                    $function_content .="\n          title:'".$item['text']."'";
                    if ($item['icon'] != '')  $function_content .="\n						    ,icon : '".$item['icon']."' ";
                    if ($item['iconCls'] != '')   $function_content .="\n						    ,iconCls:'".$item['iconCls']."'";
                    $function_content .="\n        ,autoLoad : '".$item['url_d']."'";
                    $function_content .="\n });";
                    $function_content .="\n trees[$i] = ifr_$i;";
                    ++$i;
                }
			}

			if($cat_new==true){
                $top_cat_menu_new = "menu.add({text: '".__define("TEXT_NEW_MAIN_CATEGORY")."',iconCls:'folder_add', handler: function(){addCategory(node_id);}});";
                //$top_cat_menu_new .= "menu.add({text: '".__define("TEXT_PROCESS_SEO")."',iconCls:'reload', handler: function(){doCatSeo();}});";
                $top_cat_menu_new .= "menu.add({text: '".__define("TEXT_ADD_CUSTOM_LINK")."',iconCls:'brick_link',
				menu:{
			        	items:[
			                {
			                    text: '".__define("TEXT_CATEGORY_CUSTOM_LINK_PRODUCT")."',
			                    iconCls:'product_add',
			                    handler: function(){addCustomLink(node_id,'product');}
			                },
			            	{
			                    text: '".__define("TEXT_CATEGORY_CUSTOM_LINK_CATEGORY")."',
			                    iconCls:'folder_add',
			                    handler: function(){addCustomLink(node_id,'category');}
			                },
			            	{
			                    text: '".__define("TEXT_CATEGORY_CUSTOM_LINK_CONTENT")."',
			                    iconCls:'layout',
			                    handler: function(){addCustomLink(node_id,'content');}
			                },
			            	{
			                    text: '".__define("TEXT_CATEGORY_CUSTOM_LINK_CUSTOM_LINK")."',
			                    iconCls:'link_go',
			                    handler: function(){addCustomLink(node_id,'custom');}
			                },
			            	{
			                    text: '".__define("TEXT_CATEGORY_CUSTOM_LINK_PLUGIN_PAGE")."',
			                    iconCls:'install_plugin',
			                    handler: function(){addCustomLink(node_id,'plugin');}
			                }
			            ]
			        }
			    });";

                $cat_menu = "menu.add({text: '".__define("TEXT_NEW_SUB_CATEGORY")."',iconCls:'folder_add', handler: function(){addCategory(node_id);}});";
                $cat_menu .= "menu.add({text: '".__define("TEXT_PROCESS_SEO")."',iconCls:'reload', handler: function(){doCatSeo(node_id);}});";


				($plugin_code = $xtPlugin->PluginCode('class.admin_handler:cat_new')) ? eval($plugin_code) : false;

			}

			if($cat_edit==true){
                $cat_menu .= "menu.add({text: '".__define("TEXT_EDIT")."', iconCls:'folder_edit',id:'edit_category', handler: function(){editCategory(node_id);}});";

				($plugin_code = $xtPlugin->PluginCode('class.admin_handler:cat_edit')) ? eval($plugin_code) : false;
			}

			if($cat_del==true){
                $cat_menu .= "menu.add({text: '".__define("TEXT_DELETE_CATEGORY")."', iconCls:'folder_delete', handler: function(){deleteCategory(node_id);}});";

				($plugin_code = $xtPlugin->PluginCode('class.admin_handler:cat_del')) ? eval($plugin_code) : false;
			}
			if($cat_new==true)
			{
                $cat_menu .= "menu.add({text: '".__define("TEXT_ADD_CUSTOM_LINK")."',iconCls:'brick_link',
			    menu:{
			        	items:[
			                {
			                    text: '".__define("TEXT_CATEGORY_CUSTOM_LINK_PRODUCT")."',
			                    iconCls:'product_add',
			                    handler: function(){addCustomLink(node_id,'product');}
			                },
			            	{
			                    text: '".__define("TEXT_CATEGORY_CUSTOM_LINK_CATEGORY")."',
			                    iconCls:'folder_add',
			                    handler: function(){addCustomLink(node_id,'category');}
			                },
			            	{
			                    text: '".__define("TEXT_CATEGORY_CUSTOM_LINK_CONTENT")."',
			                    iconCls:'layout',
			                    handler: function(){addCustomLink(node_id,'content');}
			                },
			            	{
			                    text: '".__define("TEXT_CATEGORY_CUSTOM_LINK_CUSTOM_LINK")."',
			                    iconCls:'link_go',
			                    handler: function(){addCustomLink(node_id,'custom');}
			                },
			            	{
			                    text: '".__define("TEXT_CATEGORY_CUSTOM_LINK_PLUGIN_PAGE")."',
			                    iconCls:'install_plugin',
			                    handler: function(){addCustomLink(node_id,'plugin');}
			                }
			            ]
			        }
		        },'-');";

				($plugin_code = $xtPlugin->PluginCode('class.admin_handler:custom_link_add')) ? eval($plugin_code) : false;
			}



			if($product_new==true)
			{
                $cat_menu .= "menu.add({text: '".__define("TEXT_NEW_PRODUCT")."',iconCls:'product_add', handler: function(){addProduct(node_id);}});";
			}

			$media_cat_menu = '';
			if($media_new==true){
                $media_cat_menu .= "menu.add({text: '".__define("TEXT_NEW_SUB_CATEGORY")."',iconCls:'folder_add', handler: function(){addMediaCategory(node_id);}});";
			}

			if($media_edit==true){
                $media_cat_menu .= "menu.add({text: '".__define("TEXT_EDIT_CATEGORY")."', iconCls:'folder_edit',id:'edit_media_category', handler: function(){editMediaCategory(node_id);}});";
			}

			if($media_del==true){
                $media_cat_menu .= "menu.add({text: '".__define("TEXT_DELETE_CATEGORY")."', iconCls:'folder_delete', handler: function(){deleteMediaCategory(node_id);}},'-');";
			}

			$shop_menu_new = '';
			if($shop_new==true){
                $shop_menu_new = $this->getMenu_newShop();
			}

			$shop_menu_edit = '';
			if($shop_edit==true){
                $shop_menu_edit .= "menu.add({text: '".__define("TEXT_EDIT_SHOP")."', iconCls:'folder_edit',id:'edit_shop', handler: function(){editShop(node_id);}},'-');";

				// store: activate/deactivate categories
                $shop_menu_edit .= PHP_EOL."menu.add({text: '".__define("TEXT_DISABLE_ALL_CATEGORIES")."', iconCls:'disable',id:'disable_all_categories_shop', handler: function(){disableAllCategoriesForShop(node_id);}});";
                $shop_menu_edit .= PHP_EOL."menu.add({text: '".__define("TEXT_ENABLE_ALL_CATEGORIES")."', iconCls:'enable',id:'enable_all_categories_shop', handler: function(){enableAllCategoriesForShop(node_id);}});";
				// store: activate/deactivate products
                $shop_menu_edit .= PHP_EOL."menu.add({text: '".__define("TEXT_DISABLE_ALL_PRODUCTS")."', iconCls:'disable',id:'disable_all_products_shop', handler: function(){disableAllProductsForShop(node_id);}});";
                $shop_menu_edit .= PHP_EOL."menu.add({text: '".__define("TEXT_ENABLE_ALL_PRODUCTS")."', iconCls:'enable',id:'enable_all_products_shop', handler: function(){enableAllProductsForShop(node_id);}});";
				// store: activate/deactivate manufactures
                $shop_menu_edit .= PHP_EOL."menu.add({text: '".__define("TEXT_DISABLE_ALL_MANUFACTURERS")."', iconCls:'disable',id:'disable_all_manufacturers_shop', handler: function(){disableAllManufacturersForShop(node_id);}});";
                $shop_menu_edit .= PHP_EOL."menu.add({text: '".__define("TEXT_ENABLE_ALL_MANUFACTURERS")."', iconCls:'enable',id:'enable_all_manufacturers_shop', handler: function(){enableAllManufacturersForShop(node_id);}},'-');";
			}

			$shop_menu_del = '';
			if($shop_del==true){
                $shop_menu_del .= "menu.add({text: '".__define("TEXT_DELETE_SHOP")."', iconCls:'folder_delete', handler: function(){deleteShop(node_id);}},'-');";
			}

            $top_media_menu_new = "";
			($plugin_code = $xtPlugin->PluginCode('class.admin_handler:menu')) ? eval($plugin_code) : false;

			$function_content .="

	  	function xtcontextMenu(node, e) {
	  		var node_id = node.id;
	  		
	    	if (node_id == 'node_category') {  // main Category
	    		var menu = new Ext.menu.Menu({id: 'cat-menu'});
	    		".$top_cat_menu_new ."
                    menu.add({text: '".__define("TEXT_RELOAD")."',iconCls:'reload', handler: function(){reloadCatTree();}});
	    		menu.showAt(e.getXY());
	  		}

			if (node_id.indexOf('node_unasigned_cats')==0) {  // main Category
	    		var menu = new Ext.menu.Menu({id: 'cat-menu'});
	    		".$top_cat_menu_new ."
                    menu.add({text: '".__define("TEXT_RELOAD")."',iconCls:'reload', handler: function(){reloadCatTree();}});
	    		menu.showAt(e.getXY());
	  		}
			
			if (node_id.indexOf('node_cat_store')==0) {  // main Category
	    		var menu = new Ext.menu.Menu({id: 'cat-menu'});
	    		".$top_cat_menu_new ."
                    menu.add({text: '".__define("TEXT_RELOAD")."',iconCls:'reload', handler: function(){reloadCatTree();}});
	    		menu.showAt(e.getXY());
	  		}
	  		
	  		if (node_id.indexOf('subcat_')==0) {
	  			var menu = new Ext.menu.Menu({id: 'cat-menu'});
	  			".$cat_menu."
                    menu.add({text: '".__define("TEXT_RELOAD")."',iconCls:'reload', handler: function(){reloadCatTree();}});
	  			menu.showAt(e.getXY());
		  	}

	    	if (node_id == 'node_MediaGallery') {  // main Category
	    		var menu = new Ext.menu.Menu({id: 'media-cat-menu'});
	    		".$top_media_menu_new ."
                    menu.add({text: '".__define("TEXT_RELOAD")."',iconCls:'reload', handler: function(){reloadMediaCatTree();}});
	    		menu.showAt(e.getXY());
	  		}

	  		if (node_id.indexOf('media_subcat_')==0) {
	  			var menu = new Ext.menu.Menu({id: 'media-cat-menu'});
	  			".$media_cat_menu."
                    menu.add({text: '".__define("TEXT_RELOAD")."',iconCls:'reload', handler: function(){reloadMediaCatTree();}});
	  			menu.showAt(e.getXY());
		  	}		  	
		  	
	    	if (node_id == 'node_stores') {  // main Category
	    		var menu = new Ext.menu.Menu({id: 'shop-menu'});
	    		".$shop_menu_new."
                    menu.add({text: '".__define("TEXT_RELOAD")."',iconCls:'reload', handler: function(){reloadShopTree();}});
	    		menu.showAt(e.getXY());
	  		}


		  	if (node_id.indexOf('store_')==0) {
	  			var menu = new Ext.menu.Menu({id: 'shop-menu'});
	  			".$shop_menu_edit."
	  			".$shop_menu_new."
	  			if (node_id!='store_1') {
	  				".$shop_menu_del."
	  			}
                    menu.add({text: '".__define("TEXT_RELOAD")."',iconCls:'reload', handler: function(){reloadShopTree();}});
	  			menu.showAt(e.getXY());
		  	}

		";

			($plugin_code = $xtPlugin->PluginCode('class.admin_handler:content_nodes')) ? eval($plugin_code) : false;

            $fixed_media_galleries_ids = [];
            $fixed_media_galleries_nodes = [];
            global $db;
            $mg_rows = $db->GetArray('SELECT mg_id FROM '. TABLE_MEDIA_GALLERY. " WHERE class in ('default','product','category','manufacturer','files_free','files_order','logo')");
            foreach($mg_rows as $row)
            {
                $fixed_media_galleries_ids[] = $row['mg_id'];
            }
            if(count($fixed_media_galleries_ids))
            {
                foreach($fixed_media_galleries_ids as $id)
                {
                    $fixed_media_galleries_nodes[] = 'media_subcat_'.$id;
                }
                $fixed_media_galleries_nodes = '"'.implode('","', $fixed_media_galleries_nodes).'"';
            }
            else $fixed_media_galleries_nodes = '"0"';


			$function_content .="
		  		  
	  	}

	  	";

			($plugin_code = $xtPlugin->PluginCode('class.admin_handler:content_functions')) ? eval($plugin_code) : false;

			$add_to_url = (isset($_SESSION['admin_user']['admin_key']))? ",sec:'".$_SESSION['admin_user']['admin_key']."'": "";

			$function_content .="

		function disableAllCategoriesForShop(node_id)
        {
            Ext.MessageBox.confirm('Message', '".__define("TEXT_ASK_DISABLE_CATEGORIES_SHOP")."',function(btn){disableAllCategoriesForShopConfirm(node_id,btn);});
        }
        function disableAllCategoriesForShopConfirm(node_id,btn)
        {
            var node_id = node_id;
            if (btn == 'yes') {
                Ext.Ajax.request({
                    url: 'adminHandler.php',
                    method: 'GET',
                    params: { load_section:'multistore_options', pg:'disableAllCategoriesForShop' ,shop_id:node_id".$add_to_url."},
                    success: function(){
                        Ext.MessageBox.alert('Message', '".__define("TEXT_SUCCESS")."');
                    },
                    failure: function(){
                        Ext.MessageBox.alert('Message', '".__define("TEXT_FAILURE")."');
                    }

                });
            }
        }

        function enableAllCategoriesForShop(node_id)
        {
            Ext.MessageBox.confirm('Message', '".__define("TEXT_ASK_ENABLE_CATEGORIES_SHOP")."',function(btn){enableAllCategoriesForShopConfirm(node_id,btn);});
        }
        function enableAllCategoriesForShopConfirm(node_id,btn)
        {
            var node_id = node_id;
            if (btn == 'yes') {
                Ext.Ajax.request({
                    url: 'adminHandler.php',
                    method: 'GET',
                    params: { load_section:'multistore_options', pg:'enableAllCategoriesForShop' ,shop_id:node_id".$add_to_url."},
                    success: function(){
                        Ext.MessageBox.alert('Message', '".__define("TEXT_SUCCESS")."');
                    },
                    failure: function(){
                        Ext.MessageBox.alert('Message', '".__define("TEXT_FAILURE")."');
                    }

                });
            }
        }

        function disableAllProductsForShop(node_id)
        {
            Ext.MessageBox.confirm('Message', '".__define("TEXT_ASK_DISABLE_PRODUCTS_SHOP")."',function(btn){disableAllProductsForShopConfirm(node_id,btn);});
        }
        function disableAllProductsForShopConfirm(node_id,btn)
        {
            var node_id = node_id;
            if (btn == 'yes') {
                Ext.Ajax.request({
                    url: 'adminHandler.php',
                    method: 'GET',
                    params: { load_section:'multistore_options', pg:'disableAllProductsForShop' ,shop_id:node_id".$add_to_url."},
                    success: function(){
                        Ext.MessageBox.alert('Message', '".__define("TEXT_SUCCESS")."');
                    },
                    failure: function(){
                        Ext.MessageBox.alert('Message', '".__define("TEXT_FAILURE")."');
                    }

                });
            }
        }

        function enableAllProductsForShop(node_id)
        {
            Ext.MessageBox.confirm('Message', '".__define("TEXT_ASK_ENABLE_PRODUCTS_SHOP")."',function(btn){enableAllProductsForShopConfirm(node_id,btn);});
        }
        function enableAllProductsForShopConfirm(node_id,btn)
        {
            var node_id = node_id;
            if (btn == 'yes') {
                Ext.Ajax.request({
                    url: 'adminHandler.php',
                    method: 'GET',
                    params: { load_section:'multistore_options', pg:'enableAllProductsForShop' ,shop_id:node_id".$add_to_url."},
                    success: function(){
                        Ext.MessageBox.alert('Message', '".__define("TEXT_SUCCESS")."');
                    },
                    failure: function(){
                        Ext.MessageBox.alert('Message', '".__define("TEXT_FAILURE")."');
                    }

                });
            }
        }


        function disableAllManufacturersForShop(node_id)
        {
            Ext.MessageBox.confirm('Message', '".__define("TEXT_ASK_DISABLE_MANUFACTURERS_SHOP")."',function(btn){disableAllManufacturersForShopConfirm(node_id,btn);});
        }
        function disableAllManufacturersForShopConfirm(node_id,btn)
        {
            var node_id = node_id;
            if (btn == 'yes') {
                Ext.Ajax.request({
                    url: 'adminHandler.php',
                    method: 'GET',
                    params: { load_section:'multistore_options', pg:'disableAllManufacturersForShop' ,shop_id:node_id".$add_to_url."},
                    success: function(){
                        Ext.MessageBox.alert('Message', '".__define("TEXT_SUCCESS")."');
                    },
                    failure: function(){
                        Ext.MessageBox.alert('Message', '".__define("TEXT_FAILURE")."');
                    }

                });
            }
        }

        function enableAllManufacturersForShop(node_id)
        {
            Ext.MessageBox.confirm('Message', '".__define("TEXT_ASK_ENABLE_MANUFACTURERS_SHOP")."',function(btn){enableAllManufacturersForShopConfirm(node_id,btn);});
        }
        function enableAllManufacturersForShopConfirm(node_id,btn)
        {
            var node_id = node_id;
            if (btn == 'yes') {
                Ext.Ajax.request({
                    url: 'adminHandler.php',
                    method: 'GET',
                    params: { load_section:'multistore_options', pg:'enableAllManufacturersForShop' ,shop_id:node_id".$add_to_url."},
                    success: function(){
                        Ext.MessageBox.alert('Message', '".__define("TEXT_SUCCESS")."');
                    },
                    failure: function(){
                        Ext.MessageBox.alert('Message', '".__define("TEXT_FAILURE")."');
                    }

                });
            }
        }
	  	
	  	function editCategory(node_id){
	  		var node_id = node_id;
	  		addTab('adminHandler.php?load_section=category&edit_id='+node_id,'".__define("TEXT_EDIT_CATEGORY")."');
	  	};
		
		function editCustomLink(node_id){
	  		var node_id = node_id;
	  		addTab('adminHandler.php?load_section=custom_link&edit_id='+node_id,'".__define("TEXT_EDIT_CUSTOM_LINK")."');
	  	};
		
		function deleteCustomLink(node_id){
	  		var node_id = node_id;
	  		Ext.MessageBox.confirm('Message', '".__define("TEXT_ASK_DELETE_CUSTOM_LINK")."',function(btn){CheckCustomLink(node_id,btn);});
	  	};
		
		
		
	  	function deleteCategory(node_id){
	  		var node_id = node_id;
	  		Ext.MessageBox.confirm('Message', '".__define("TEXT_ASK_DELETE_CATEGORY")."',function(btn){CheckCategory(node_id,btn);});
	  	};

	  	function editMediaCategory(node_id){
	  		var node_id = node_id;
	  		addTab('adminHandler.php?load_section=MediaGallery&edit_id='+node_id,'".__define("TEXT_EDIT_CATEGORY")."');
	  	};

	  	function deleteMediaCategory(node_id){
	  	    var dont_allowed = [".$fixed_media_galleries_nodes."];
	  	    if(dont_allowed.indexOf(node_id)!= -1)
	  	    {
	  	        Ext.MessageBox.alert('Message','".__define("TEXT_ERROR_CANT_DELETE_SYSTEM_GALERY")."');
	  	    }
	  	    else {
                var node_id = node_id;
                Ext.MessageBox.confirm('Message', '".__define("TEXT_ASK_DELETE_MEDIA_CATEGORY")."',function(btn){deleteMediaCategoryConfirm(node_id,btn);});
	  		}
	  	};	  	
	  	
	  	function showMaxShopMessage(node_id) {
	  		var node_id = node_id;
	  		Ext.MessageBox.alert('Message','".__define("TEXT_ERROR_MAX_STORES")."');
	  	}
		
		function CheckCategory(node_id,btn)
		{
			 var conn = new Ext.data.Connection();
		
			 conn.request({
			 url: 'adminHandler.php',
			 method:'GET',
			 params: { load_section:'category',pg:'CheckItem',edit_id:node_id".$add_to_url."},
			 waitMsg: '".__define("TEXT_LOADING")."',
			 success: function(responseObject) {
			 			if (responseObject.responseText=='failure') deleteCustomLinkConfirm(node_id,btn);
			            else deleteCategoryConfirm(node_id,btn);
			          }
			 });
		}

	  	function deleteCategoryConfirm(node_id,btn) {
	  		var node_id = node_id;
	  		if (btn == 'yes') {
	  		Ext.Ajax.request({
	  			url: 'adminHandler.php',
	  			method: 'GET',
	  			params: { multiFlag_unset: 'true', load_section:'category',edit_id:node_id".$add_to_url."}
	  		});
	  		Ext.MessageBox.alert('Message', '".__define("TEXT_SUCCESS_DELETE_CATEGORY")."');
	  		}
	  	};
		
		
		function CheckCustomLink(node_id,btn)
		{
			 var conn = new Ext.data.Connection();
		
			 conn.request({
			 url: 'adminHandler.php',
			 method:'GET',
			 params: { load_section:'custom_link',pg:'CheckItem',edit_id:node_id".$add_to_url."},
			 waitMsg: '".__define("TEXT_LOADING")."',
			 success: function(responseObject) {
			 			if (responseObject.responseText=='failure') Ext.Msg.alert('".__define('TEXT_ALERT')."', '".__define('TEXT_ALERT_NOT_A_CUSTOM_LINK')."');
			            else deleteCustomLinkConfirm(node_id,btn);
			          }
			 });
		}
		
		function deleteCustomLinkConfirm(node_id,btn) {
	  		var node_id = node_id;
	  		if (btn == 'yes') {
	  		Ext.Ajax.request({
	  			url: 'adminHandler.php',
	  			method: 'GET',
	  			params: { multiFlag_unset: 'true', load_section:'custom_link',edit_id:node_id".$add_to_url."}
	  		});
	  		Ext.MessageBox.alert('Message', '".__define("TEXT_SUCCESS_DELETE_CUSTOM_LINK")."');
	  		}
	  	};
		
		

	  	function deleteMediaCategoryConfirm(node_id,btn) {
	  		var node_id = node_id;
	  		if (btn == 'yes') {
	  		Ext.Ajax.request({
	  			url: 'adminHandler.php',
	  			method: 'GET',
	  			params: { multiFlag_unset: 'true', load_section:'MediaGallery',edit_id:node_id".$add_to_url."}
	  		});
	  		Ext.MessageBox.alert('Message', '".__define("TEXT_SUCCESS_DELETE_CATEGORY")."');
	  		}
	  	};	  	
	  	
	  	function addCategory(node_id){
	  		var node_id = node_id;
	  		addTab('adminHandler.php?load_section=category&new=true&master_node='+node_id,'".__define("TEXT_NEW_CATEGORY")."');
	  	};
		
		function addCustomLink(node_id,type){
	  		var node_id = node_id;
	  		addTab('adminHandler.php?load_section=custom_link&new=true&master_node='+node_id+'&link_type='+type,'".__define("TEXT_NEW_CUSTOM_LINK")."');
	  	};
		
	  	function addMediaCategory(node_id){
	  		var node_id = node_id;
	  		addTab('adminHandler.php?load_section=MediaGallery&new=true&master_node='+node_id,'".__define("TEXT_NEW_CATEGORY")."');
	  	};	  	
	  	
	  	function addProduct(node_id){
	  		var node_id = node_id;
  			addTab('adminHandler.php?load_section=product&new=true&catID='+node_id,'".__define("TEXT_NEW_PRODUCT")."');
		};

	  	function editShop(node_id){
	  		var node_id = node_id;
	  		addTab('adminHandler.php?load_section=multistore&edit_id='+node_id,'".__define("TEXT_SHOP_EDIT")."');
	  	};

	  	function deleteShop(node_id){
	  		var node_id = node_id;
	  		Ext.MessageBox.confirm('Message', '".__define("TEXT_ASK_DELETE_SHOP")."',function(btn){deleteShopConfirm(node_id,btn);});
	  	};

	  	

	  	function addShop(node_id){
	  		var node_id = node_id;
	  		addTab('adminHandler.php?load_section=multistore&new=true&master_node='+node_id,'".__define("TEXT_SHOP_ADD")."');
	  	};

	  	function doInstall(edit_id){
	  		var edit_id = edit_id;
			addTab('plugin_install.php?plugin_id='+edit_id,'".__define("TEXT_INSTALL_PLUGIN")."');
		};
		";

			if($rootNodeIds["shop"]){
				$function_content .="
	
			function reloadCatTree() {
				//console.log('reloadCatTree');
				".$rootNodeIds["shop"].".ownerTree.getLoader().load(".$rootNodeIds["shop"].".ownerTree.root);
			};
			";
			};

			if($rootNodeIds["contentroot"]){
				$function_content .="
			function reloadMediaCatTree() {
			    ".$rootNodeIds["contentroot"].".ownerTree.getLoader().load(".$rootNodeIds["contentroot"].".ownerTree.root);
			};		
			";
			}
			if($rootNodeIds["config_store"]){
				$function_content .="
			function reloadShopTree() {
			    ".$rootNodeIds["config_store"].".ownerTree.getLoader().load(".$rootNodeIds["config_store"].".ownerTree.root);
			};
                
                function deleteShopConfirm(node_id,btn) {
                var node_id = node_id;
                if (btn == 'yes') {
                Ext.Ajax.request({
                    url: 'adminHandler.php',
                    method: 'GET',
                    params: { multiFlag_unset: 'true', load_section:'multistore',edit_id:node_id".$add_to_url."}
                });
                Ext.MessageBox.alert('Message', '".__define("TEXT_SUCCESS_DELETE_SHOP")."');
                ".$rootNodeIds["config_store"].".reload();
                }
            };
			
			";
			};

			$function_content .="
	  	function doCatSeo(node_id){
	  		var node_id = node_id;
	  		Ext.MessageBox.confirm('Message', '".__define("BUTTON_START_SEO")."',function(btn){doCatSeoConfirm(node_id,btn);});
	  	};		
		
	  	function doCatSeoConfirm(node_id,btn) {
	  		var node_id = node_id;
	  		if (btn == 'yes') {
	  		Ext.Ajax.request({
	  			url: 'adminHandler.php',
	  			method: 'GET',
	  			params: { multiFlag_rebuildSeo: 'true', load_section:'category',edit_id:node_id".$add_to_url."}
	  		});
	  		Ext.MessageBox.alert('Message', '".__define("TEXT_SUCCESS")."');
	  		}
	  	};		

	  	";

		}
		$tabpanel_options = "{
	                    region:'center',
	                    deferredRender:false,
	                    activeTab:0,
						enableTabScroll:true,
	       				defaults: {autoScroll:true},
						plugins: new Ext.ux.TabCloseMenu(),
	                    id:'sites',
	  					margins: '10 10 10 10',
	                    activeItem:0,
	                    defaultType: 'iframepanel',";
		if($dashboard_view==true){
            $tabpanel_options .= "items: [ {xtype: 'panel', title: '".__define("TEXT_DASHBOARD")."', autoLoad : {url : 'dashboard.php', params: { parentNode: 'dashboard' }, scripts : true}, id: 'dashboard', closable:false }],";
		}
		$tabpanel_options .= "defaults:{
	                    closable:true,
	                    autoScroll:true,
				        loadMask:{msg:'Loading ...'},
	                    //required so nonIE (of all things) wont refresh the iframe object when hidden
	                    style:{position:(Ext.isIE?'relative':'absolute')},
	                    hideMode:(Ext.isIE?'display':'visibility')
                      }
				 }";
		$viewport_options = "{  renderTo: 'sites',
			            layout:'border',
			            items:[
			                new Ext.BoxComponent({ // raw
			                    region:'north',
			                    el: 'north',
			                    height:50,
			                    layout:'accordion'
			                })
							,{
								region: 'north',
								tbar : tb,
								margins: '0 240 0 230',
  								id: 'top-navigation'
							},{
			                    region:'west',
			                    el: 'west',
			                    id:'west-panel',
			                    title:'Version ". _SYSTEM_VERSION ."',
			                    split:true,
			                    width: 230,
			                    minSize: 200,
			                    maxSize: 430,
			                    collapsible: false,
	  							overflowY: 'auto',
			                    margins:'0 0 0 0',
			                    layout:'accordion',
			                    layoutConfig:{
			                        animate:true
			                    },
			                    items: trees
								},
			                contentTabs
			             ]
			        }
";
		$this->_call_Method_JS_Function('contentTabs =	new Ext');
 		$this->_add_Method_JS_Funktion('TabPanel',$tabpanel_options);

		$function_content .= $this->_getTMP_JS_String();
	//	$function_content .= $this->setVar('tb_items','Ext.util.JSON.decode("'.$TopNav.'")');
	//	$function_content .= $this->setVar('tb','new Ext.Toolbar({items: tb_items})');
		$function_content .= $this->setVarValue('String.prototype.trim','function() {
				return this.replace(/^\s+|\s+$/g,"");
			}');
		$function_content .= $this->_getTMP_JS_String();
		$function_content .= $this->setVar('tb_items','Ext.util.JSON.decode("'.$TopNav.'")');


		$items ='tb_items'; // Sprachbox der Toolbar hinzuf?gen

		($plugin_code = $xtPlugin->PluginCode('class.admin_handler:toolbar_items')) ? eval($plugin_code) : false;

		$function_content .= $this->setVar('tb','new Ext.Toolbar({items: ['.$items.'],cls:\'toolbar-top\',id:\'toolbar-top\'})');


		$function_content .= $this->setVar('viewport','new Ext.Viewport('.$viewport_options.')');

		$function = $this->jsFunction('',$function_content);


		$this->_call_Method_JS_Function('Ext');
		$this->_add_Method_JS_Funktion('onReady', $function);

		$string = $this->_getTMP_JS_String();
		$this->_setJS_String($string);
	}

	protected function getMenu_newShop()
    {
        return '';
    }


}
