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


if (!$xtc_acl->isLoggedIn()) {
    die('login required, or session is out of time');
}

$refferer = $_SERVER['HTTP_REFERER'];
$url_ext = stristr($refferer,'xtAdmin/');
$url = str_replace($url_ext,"",$refferer).'xtAdmin/';

define('_SRV_URL', $url);
//require_once(_SRV_WEBROOT._SRV_WEB_FRAMEWORK.'admin/classes/class.grid.js.php');
set_include_path(_SRV_WEBROOT._SRV_WEB_FRAMEWORK.'library/');
//ini_set("include_path", _SRV_WEBROOT._SRV_WEB_FRAMEWORK.'library/');
include_once 'PhpExt/Javascript.php';


include_once 'PhpExt/Ext.php';
include_once 'PhpExt/Data/Store.php';
include_once 'PhpExt/Data/ArrayReader.php';
include_once 'PhpExt/Data/FieldConfigObject.php';
include_once 'PhpExt/Data/SimpleStore.php';
include_once 'PhpExt/Data/GroupingStore.php';
include_once 'PhpExt/Data/SortInfoConfigObject.php';
include_once 'PhpExt/Data/ScriptTagProxy.php';
include_once 'PhpExt/Data/StoreLoadOptions.php';
include_once 'PhpExt/Data/JsonReader.php';
include_once 'PhpExt/Data/HttpProxy.php';
include_once 'PhpExt/Data/JsonStore.php';
include_once 'PhpExt/Data/XmlReader.php';
include_once 'PhpExt/Data/Record.php';


include_once 'PhpExt/Grid/ColumnModel.php';
include_once 'PhpExt/Grid/ColumnConfigObject.php';
include_once 'PhpExt/Grid/GridPanel.php';
include_once 'PhpExt/Grid/RowSelectionModel.php';
include_once 'PhpExt/Grid/CheckboxSelectionModel.php';
include_once 'PhpExt/Grid/EditorGridPanel.php';


include_once 'PhpExt/DataView.php';
include_once 'PhpExt/Panel.php';
include_once 'PhpExt/Listener.php';
include_once 'PhpExt/Config/ConfigObject.php';

include_once 'PhpExt/Form/FormPanel.php';
include_once 'PhpExt/Form/FieldSet.php';
include_once 'PhpExt/Form/TextField.php';
include_once 'PhpExt/Form/TimeField.php';
include_once 'PhpExt/Form/HtmlEditor.php';
include_once 'PhpExt/Form/FroalaEditor.php';
include_once 'PhpExt/Form/DateField.php';
include_once 'PhpExt/Form/Hidden.php';
include_once 'PhpExt/Form/NumberField.php';
include_once 'PhpExt/Form/PasswordField.php';
//include_once 'PhpExt/Form/UploadField.php';
include_once 'PhpExt/Form/Radio.php';
include_once 'PhpExt/Form/ComboBox.php';
include_once 'PhpExt/Form/Checkbox.php';
include_once 'PhpExt/Form/TriggerField.php';
include_once 'PhpExt/Form/TextArea.php';
include_once 'PhpExt/Form/ComboBox.php';

include_once 'PhpExt/QuickTips.php';

include_once 'PhpExt/Layout/ColumnLayout.php';
include_once 'PhpExt/Layout/ColumnLayoutData.php';
include_once 'PhpExt/Layout/FitLayout.php';
include_once 'PhpExt/Layout/FitLayoutData.php';
include_once 'PhpExt/Layout/BorderLayout.php';
include_once 'PhpExt/Layout/BorderLayoutData.php';
include_once 'PhpExt/Layout/CardLayout.php';
include_once "PhpExt/Layout/AccordionLayout.php";
include_once "PhpExt/Layout/AccordionLayoutData.php";


include_once 'PhpExt/Window.php';
include_once 'PhpExt/AutoLoadConfigObject.php';
include_once 'PhpExt/DataView.php';

include_once 'PhpExt/Handler.php';
include_once 'PhpExt/Button.php';
include_once 'PhpExt/TabPanel.php';


include_once 'PhpExt/Layout/FormLayout.php';
include_once 'PhpExt/Layout/FitLayout.php';
include_once 'PhpExt/Layout/AnchorLayoutData.php';
include_once 'PhpExt/Layout/TableLayout.php';
include_once 'PhpExt/Layout/TableLayoutData.php';


include_once 'PhpExt/Toolbar/Toolbar.php';
include_once 'PhpExt/Toolbar/Button.php';
include_once 'PhpExt/Toolbar/PagingToolbar.php';


include_once 'PhpExt/Template.php';
include_once 'PhpExt/XTemplate.php';

include_once 'PhpExtUx/ImageChooser.php';

include_once 'PhpExtUx/Form/LovCombo.php';
include_once 'PhpExtUx/Form/LovCombo2.php';
include_once 'PhpExtUx/Form/RadioGroup.php';
include_once 'PhpExtUx/Form/ItemSelector.php';
include_once 'PhpExtUx/Form/Multiselect.php';

// Amchart objects
include_once 'PhpExtUx/Form/AmchartObjects/AmChartInvokable.php';
include_once 'PhpExtUx/Form/AmchartObjects/AmChartConfigObject.php';
include_once 'PhpExtUx/Form/AmchartObjects/AmchartExecutableObject.php';
include_once 'PhpExtUx/Form/AmchartObjects/AmchartCallable.php';
include_once 'PhpExtUx/Form/AmchartObjects/AmAxisBase.php';
include_once 'PhpExtUx/Form/AmchartObjects/AmChart.php';
include_once 'PhpExtUx/Form/AmchartObjects/AmCoordinateChart.php';
include_once 'PhpExtUx/Form/AmchartObjects/AmRectangularChart.php';
include_once 'PhpExtUx/Form/AmchartObjects/AmSerialChart.php';
include_once 'PhpExtUx/Form/AmchartObjects/AmSlicedChart.php';
include_once 'PhpExtUx/Form/AmchartObjects/AmPieChart.php';
include_once 'PhpExtUx/Form/AmchartObjects/AmCategoryAxis.php';
include_once 'PhpExtUx/Form/AmchartObjects/AmValueAxis.php';
include_once 'PhpExtUx/Form/AmchartObjects/AmGraph.php';
include_once 'PhpExtUx/Form/AmchartObjects/AmChartCursor.php';
include_once 'PhpExtUx/Form/AmchartObjects/AmChartScrollbar.php';
include_once 'PhpExtUx/Form/AmchartObjects/AmChartLegend.php';
include_once 'PhpExtUx/Form/ExtAmchart.php';

include_once 'PhpExt/Grid/GroupingView.php';


include_once 'PhpExt/Tree/TreePanel.php';
include_once 'PhpExt/Tree/TreeNode.php';
include_once 'PhpExt/Tree/AsyncTreeNode.php';
include_once 'PhpExt/Tree/TreeLoader.php';
include_once 'PhpExt/Tree/TreeEditor.php';


// User Extension
include_once 'PhpExtUx/App/SearchField.php';
include_once 'PhpExtUx/App/CheckboxField.php';

include_once 'PhpExtUx/Grid/CheckColumn.php';
include_once 'PhpExtUx/Grid/RowAction.php';


include_once 'PhpExtUx/DataView/DragSelector.php';
include_once 'PhpExtUx/DataView/LabelEditor.php';
include_once 'PhpExtUx/View/ImageDragZone.php';

include_once 'PhpExt/Menu/Menu.php';
include_once 'PhpExt/Menu/TextItem.php';
//include_once 'PhpExt/Menu/MenuBaseItem.php';
//include_once 'PhpExt/Menu/Item.php';


require_once(_SRV_WEBROOT._SRV_WEB_FRAMEWORK.'classes/class.ImageTypes.php');
require_once(_SRV_WEBROOT._SRV_WEB_FRAMEWORK.'admin/classes/class.ExtFunctions.php');
require_once(_SRV_WEBROOT._SRV_WEB_FRAMEWORK.'admin/classes/class.ExtEditForm.php');
require_once(_SRV_WEBROOT._SRV_WEB_FRAMEWORK.'admin/classes/class.ExtGrid.php');
require_once(_SRV_WEBROOT._SRV_WEB_FRAMEWORK.'classes/class.MediaFileTypes.php');
require_once(_SRV_WEBROOT._SRV_WEB_FRAMEWORK.'classes/class.MediaData.php');
require_once(_SRV_WEBROOT._SRV_WEB_FRAMEWORK.'classes/class.MediaImages.php');
require_once(_SRV_WEBROOT._SRV_WEB_FRAMEWORK.'classes/class.MediaFiles.php');
require_once(_SRV_WEBROOT._SRV_WEB_FRAMEWORK.'classes/class.MediaGallery.php');
require_once(_SRV_WEBROOT._SRV_WEB_FRAMEWORK.'admin/classes/class.ExtAdminHandler.php');
require_once(_SRV_WEBROOT._SRV_WEB_FRAMEWORK.'classes/class.adminTask.php');
require_once(_SRV_WEBROOT._SRV_WEB_FRAMEWORK.'admin/classes/class.FunctionHandler.php');



?>