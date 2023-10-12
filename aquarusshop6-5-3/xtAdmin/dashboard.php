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

include '../xtFramework/admin/main.php';

/**
 * Check if admin is logged in
 */
if (!$xtc_acl->isLoggedIn()) {
	die('login required');
}

$rand = time().rand(0, getrandmax());
$gridView = "gridView_".$rand;
$chartView = "chartView_".$rand;

include (_SRV_WEBROOT_ADMIN.'page_includes.php');

include _SRV_WEBROOT._SRV_WEB_FRAMEWORK.'charts/grid.orders.php';
include _SRV_WEBROOT._SRV_WEB_FRAMEWORK.'charts/grid.sales.php';
include _SRV_WEBROOT._SRV_WEB_FRAMEWORK.'charts/grid.keywords.php';
include _SRV_WEBROOT._SRV_WEB_FRAMEWORK.'charts/chart.layout.php';

include '../xtFramework/admin/charts_generator.php';

if(_SYSTEM_SSL == true && checkHTTPS()==true){
	$link_base = _SYSTEM_BASE_HTTPS._SRV_WEB;
}else{
	$link_base = _SYSTEM_BASE_HTTP._SRV_WEB;
}


// Layout 1 gridView

$gridOrders = new grid_orders();
$myData = $gridOrders->_getTotalGrid();

// Store
$store = new PhpExt_Data_Store();
$reader = new PhpExt_Data_ArrayReader();
$reader->addField(new PhpExt_Data_FieldConfigObject("shops"));
$reader->addField(new PhpExt_Data_FieldConfigObject("customers",null,"float"));
$reader->addField(new PhpExt_Data_FieldConfigObject("products",null,"float"));
$reader->addField(new PhpExt_Data_FieldConfigObject("today",null,"float"));
$reader->addField(new PhpExt_Data_FieldConfigObject("yesterday",null,"float"));
$reader->addField(new PhpExt_Data_FieldConfigObject("week",null,"float"));
$reader->addField(new PhpExt_Data_FieldConfigObject("month",null,"float"));
$reader->addField(new PhpExt_Data_FieldConfigObject("year",null,"float"));
$store->setReader($reader)
      ->setData(PhpExt_Javascript::variable("data"));


// ColumnModel
$colModel = new PhpExt_Grid_ColumnModel();
$colModel->addColumn(PhpExt_Grid_ColumnConfigObject::createColumn(TEXT_STORES,"shops","Shops",200, null, null, true, false))
         ->addColumn(PhpExt_Grid_ColumnConfigObject::createColumn(TEXT_CUSTOMERS,"customers",null,80,'right',null, true, false))
       //  ->addColumn(PhpExt_Grid_ColumnConfigObject::createColumn(TEXT_PRODUCTS,"products",null,80,'right',null, true, false))
         ->addColumn(PhpExt_Grid_ColumnConfigObject::createColumn(TEXT_TODAY,"today",null,80,'right',null, true, false))
         ->addColumn(PhpExt_Grid_ColumnConfigObject::createColumn(TEXT_YESTERDAY,"yesterday",null,80,'right',null, true, false))
         ->addColumn(PhpExt_Grid_ColumnConfigObject::createColumn(TEXT_WEEK,"week",null,80,'right',null, true, false))
         ->addColumn(PhpExt_Grid_ColumnConfigObject::createColumn(TEXT_MONTH,"month",null,80,'right',null, true, false))
         ->addColumn(PhpExt_Grid_ColumnConfigObject::createColumn(TEXT_YEAR,"year",null,80,'right',null, true, false));
        
// Setup Grid
$selModel = new PhpExt_Grid_RowSelectionModel();
$selModel->setSingleSelect(true);

$grid = new PhpExt_Grid_GridPanel();
$grid->setStore($store)
     ->setColumnModel($colModel)
     ->setSelectionModel($selModel)
     ->setAutoExpandColumn("Shops")
     ->setAutoHeight(false)
     ->setMaxHeight(200)
     ->setAutoScroll(true)
     ->setBorder(false)
     ->setBodyStyle("padding: 5px;")
     ->attachListener("render",
		new PhpExt_Listener(PhpExt_Javascript::functionDef(null,
			"g.getSelectionModel().selectRow(0);", array("g")),
			null, 10)
		);

$gridPanel = new PhpExt_Panel();
$gridPanel->setTitle(TEXT_STATS_TOTAL_ORDERS);
$gridPanel->setLayout(new PhpExt_Layout_FitLayout());
$gridPanel->addItem($grid);

$totalPanel = new PhpExt_TabPanel();
$totalPanel->setActiveTab(0)
           ->setDeferredRender(false);
$totalPanel->addItem($gridPanel);

global $store_handler;
$layout1 = new PhpExt_Panel("total-form");
$layout1->setLayout(new PhpExt_Layout_BorderLayout())
        ->setAutoWidth(true)
        ->setHeight(44 + 10 + 42 + (42 * (int)$store_handler->store_count))->setBodyStyle("max-height: 305px;");
$layout1->addItem($totalPanel, PhpExt_Layout_BorderLayoutData::createCenterRegion());
$layout1->setRenderTo(PhpExt_Javascript::variable("Ext.get('".$gridView."')"));

// Layout 2

// news page
$news_page = new PhpExt_Panel();
$news_page->setTitle(TEXT_NEWS_XT_WORLD)
        ->setAutoScroll(true)
        ->setAutoWidth(true)
		->setAutoHeight(false)
		->setCssStyle('border:none;')
        ->setLayout(new PhpExt_Layout_FitLayout());

$news_page->setHtml('<iframe width="100%" frameborder="0" src="'.$link_base.'rssfeed.php" onload="resizeIframe(this);"></iframe>');

$chartGenerator = new ChartsGenerator();
// Customers chart

// Create the panel
$stat_customers = new PhpExt_Panel();
$stat_customers->setTitle(TEXT_CUSTOMERS)
->setAutoScroll(true)
->setAutoWidth(true);
$chartGenerator->getCustomersChart($stat_customers);
// END customers

// Orders chart
$stat_orders = new PhpExt_Panel();
$stat_orders->setTitle(TEXT_ORDERS)
->setAutoScroll(false)
->setAutoWidth(true)
->setAutoHeight(true)
->setCssStyle('border:none;');

$chartGenerator->getOrdersChart($stat_orders);
// End orders chart

// Shopping cart chart
$stat_shopping_cart = new PhpExt_Panel();
$stat_shopping_cart->setTitle(TEXT_SHOPPING_CART)
->setAutoScroll(true)
->setAutoWidth(true);

$chartGenerator->getShoppingCartsChart($stat_shopping_cart);
// End shopping cart chart

//top sales
$gridSales = new grid_sales();

// _getTotalSales("quantity, amount","today, yesterday, week, month, year, all", count)
$salesData_today     = $gridSales->_getTotalSales('amount', 'today', 20);
$salesData_yesterday = $gridSales->_getTotalSales('amount', 'yesterday', 20);
$salesData_week      = $gridSales->_getTotalSales('amount', 'week', 20);
$salesData_month     = $gridSales->_getTotalSales('amount', 'month', 20);
$salesData_year      = $gridSales->_getTotalSales('amount', 'year', 20);
$salesData_all       = $gridSales->_getTotalSales('amount', 'all', 20);

// salesReader
$salesReader = new PhpExt_Data_ArrayReader();
$salesReader->addField(new PhpExt_Data_FieldConfigObject("no",null,"float"));
$salesReader->addField(new PhpExt_Data_FieldConfigObject("shopname"));
$salesReader->addField(new PhpExt_Data_FieldConfigObject("name"));
$salesReader->addField(new PhpExt_Data_FieldConfigObject("model"));
$salesReader->addField(new PhpExt_Data_FieldConfigObject("price",null,"float"));
$salesReader->addField(new PhpExt_Data_FieldConfigObject("quantity",null,"float"));
$salesReader->addField(new PhpExt_Data_FieldConfigObject("amount",null,"float"));

// ColumnModel
$salesColModel = new PhpExt_Grid_ColumnModel();
$salesColModel->addColumn(PhpExt_Grid_ColumnConfigObject::createColumn(TEXT_STORES, "shopname", null, 120, null, null, true, false))
          ->addColumn(PhpExt_Grid_ColumnConfigObject::createColumn(TEXT_PRODUCTS, "name", null, 120, null, null, true, false))
          ->addColumn(PhpExt_Grid_ColumnConfigObject::createColumn(TEXT_PRODUCTS_MODEL, "model", null, 120, null, null, true, false))
          ->addColumn(PhpExt_Grid_ColumnConfigObject::createColumn(TEXT_PRICE, "price", null, 80, 'right', false, true, false))
          ->addColumn(PhpExt_Grid_ColumnConfigObject::createColumn(TEXT_QUANTITY_ORDERED, "quantity", null, 80, 'right', null, true, false))
          ->addColumn(PhpExt_Grid_ColumnConfigObject::createColumn(TEXT_AMOUNT_TOTAL, "amount", null, 80, 'right', false, true, false));

//week
$sales_week = new PhpExt_Data_Store();
$sales_week->setReader($salesReader)
                ->setData(PhpExt_Javascript::variable("dweek"));

$grid_week = new PhpExt_Grid_GridPanel();
$grid_week->setStore($sales_week)
     ->setColumnModel($salesColModel)
     ->setAutoExpandColumn('2')
     ->setAutoHeight(true)
     ->setMaxHeight(200)
     ->setAutoScroll(true)
     ->setBorder(false)
     ->setBodyStyle("padding: 5px;")
     ->attachListener("render",
		new PhpExt_Listener(PhpExt_Javascript::functionDef(null,
			"g.getSelectionModel().selectRow(0);", array("g")),
			null, 10)
		);

$stat_week = new PhpExt_Panel();
$stat_week->setTitle(TEXT_WEEK)
		->setAutoScroll(true)
		->setAutoWidth(true)
        ->setLayout(new PhpExt_Layout_FitLayout())
		->addItem($grid_week);

//month
$sales_month = new PhpExt_Data_Store();
$sales_month->setReader($salesReader)
                ->setData(PhpExt_Javascript::variable("dmonth"));

$grid_month = new PhpExt_Grid_GridPanel();
$grid_month->setStore($sales_month)
     ->setColumnModel($salesColModel)
     ->setAutoExpandColumn('2')
     ->setAutoHeight(true)
     ->setMaxHeight(200)
     ->setAutoScroll(true)
     ->setBorder(false)
     ->setBodyStyle("padding: 5px;")
     ->attachListener("render",
		new PhpExt_Listener(PhpExt_Javascript::functionDef(null,
			"g.getSelectionModel().selectRow(0);", array("g")),
			null, 10)
		);

$stat_month = new PhpExt_Panel();
$stat_month->setTitle(TEXT_MONTH)
		->setAutoScroll(true)
		->setAutoWidth(true)
        ->setLayout(new PhpExt_Layout_FitLayout())
		->addItem($grid_month);

//year
$sales_year = new PhpExt_Data_Store();
$sales_year->setReader($salesReader)
                ->setData(PhpExt_Javascript::variable("dyear"));

$grid_year = new PhpExt_Grid_GridPanel();
$grid_year->setStore($sales_year)
     ->setColumnModel($salesColModel)
     ->setAutoExpandColumn('2')
     ->setAutoHeight(true)
     ->setMaxHeight(200)
     ->setAutoScroll(true)
     ->setBorder(false)
     ->setBodyStyle("padding: 5px;")
     ->attachListener("render",
		new PhpExt_Listener(PhpExt_Javascript::functionDef(null,
			"g.getSelectionModel().selectRow(0);", array("g")),
			null, 10)
		);

$stat_year = new PhpExt_Panel();
$stat_year->setTitle(TEXT_YEAR)
		->setAutoScroll(true)
		->setAutoWidth(true)
        ->setLayout(new PhpExt_Layout_FitLayout())
		->addItem($grid_year);

// sales TabPanel
$salesTabPanel = new PhpExt_TabPanel();
$salesTabPanel->setActiveTab(0)
->setDeferredRender(false);

$salesTabPanel->addItem($stat_week);
$salesTabPanel->addItem($stat_month);
$salesTabPanel->addItem($stat_year);

$salesPanel = new PhpExt_Panel();
$salesPanel ->setLayout(new PhpExt_Layout_BorderLayout())
      ->setTitle(TEXT_STATS_SALES)
		->setAutoWidth(true)
		->setHeight(600)
		->addItem($salesTabPanel, PhpExt_Layout_BorderLayoutData::createCenterRegion());

// dkeywords
$gridKeywords = new grid_keywords();

// _getKeywordsStat
$keywords_stat     = $gridKeywords->getKeywordsStat();

// _getKeywordsNoResultStat
$keywords_no_res_stat     = $gridKeywords->getKeywordsNoResultStat();

// searchReader
$searchReader = new PhpExt_Data_ArrayReader();
$searchReader->addField(new PhpExt_Data_FieldConfigObject("no",null,"float"));
$searchReader->addField(new PhpExt_Data_FieldConfigObject("shopname"));
$searchReader->addField(new PhpExt_Data_FieldConfigObject("keyword"));
$searchReader->addField(new PhpExt_Data_FieldConfigObject("result_count", null, "float"));
$searchReader->addField(new PhpExt_Data_FieldConfigObject("request_count",null,"float"));
$searchReader->addField(new PhpExt_Data_FieldConfigObject("last_date"));

// ColumnModel
$keywordsColModel = new PhpExt_Grid_ColumnModel();
$keywordsColModel->addColumn(PhpExt_Grid_ColumnConfigObject::createColumn(TEXT_STORES, "shopname", null, 120, null, null, true, false))
				->addColumn(PhpExt_Grid_ColumnConfigObject::createColumn(TEXT_SEARCH_KEYWORD, "keyword", null, 120, null, null, true, false))
				->addColumn(PhpExt_Grid_ColumnConfigObject::createColumn(TEXT_SEARCH_RESULT_COUNT, "result_count", null, 120, null, null, true, false))
				->addColumn(PhpExt_Grid_ColumnConfigObject::createColumn(TEXT_SEARCH_REQUEST_COUNT, "request_count", null, 120, null, null, true, false))
				->addColumn(PhpExt_Grid_ColumnConfigObject::createColumn(TEXT_SEARCH_LAST_DATE, "last_date", null, 120, null, null, true, false));

$keywords = new PhpExt_Data_Store();
$keywords->setReader($searchReader)
		->setData(PhpExt_Javascript::variable("dkeywords"));

$keywords_no_res = new PhpExt_Data_Store();
$keywords_no_res->setReader($searchReader)
		->setData(PhpExt_Javascript::variable("dkeywords_no_res"));

$grid_keywords = new PhpExt_Grid_GridPanel();
$grid_keywords->setStore($keywords)
				->setColumnModel($keywordsColModel)
				->setAutoExpandColumn('1')
				->setAutoHeight(true)
				->setMaxHeight(200)
				->setAutoScroll(true)
				->setBorder(false)
				->setBodyStyle("padding: 5px;");

$grid_keywords_no_res = new PhpExt_Grid_GridPanel();
$grid_keywords_no_res->setStore($keywords_no_res)
				->setColumnModel($keywordsColModel)
				->setAutoExpandColumn('1')
				->setAutoHeight(true)
				->setMaxHeight(200)
				->setAutoScroll(true)
				->setBorder(false)
				->setBodyStyle("padding: 5px;");

// First Panel
$stat_keywords = new PhpExt_Panel();
$stat_keywords->setTitle(TEXT_DASHBOARD_SEARCH_SUBTAB1)
				->setAutoScroll(true)
				->setAutoWidth(true)
				->setLayout(new PhpExt_Layout_FitLayout())
				->addItem($grid_keywords);

// Second Panel
$stat_keywords2 = new PhpExt_Panel();
$stat_keywords2->setTitle(TEXT_DASHBOARD_SEARCH_SUBTAB2)
				->setAutoScroll(true)
				->setAutoWidth(true)
				->setLayout(new PhpExt_Layout_FitLayout())
				->addItem($grid_keywords_no_res);

// search_keywords TabPanel
$searchTabPanel = new PhpExt_TabPanel();
$searchTabPanel->setActiveTab(0)
				->setDeferredRender(false);

$searchTabPanel->addItem($stat_keywords);
$searchTabPanel->addItem($stat_keywords2);
//$searchTabPanel->addItem($stat_month);

$searchPanel = new PhpExt_Panel();
$searchPanel ->setLayout(new PhpExt_Layout_BorderLayout())
			->setTitle(TEXT_DASHBOARD_SEARCH_TAB)
			->setAutoWidth(true)
			->setHeight(600)
			->addItem($searchTabPanel, PhpExt_Layout_BorderLayoutData::createCenterRegion());

$chartPanel = new PhpExt_TabPanel();
/**
 * Quick fix for amcharts component ... by default orders chart is in the
 * second tab and initially is hidden. This causes amcharts to not properly render on page load.
 * So we have to set some properties.....
 */
$chartPanel
->setActiveTab(0)
->setDeferredRender(true)
->setHideMode(PhpExt_Component::HIDE_MODE_OFFSETS);
$chartPanel->setLayoutOnTabChange(true);
/**
 * Voala now amcharts should properly render on tab open!
 */

$chartPanel->addItem($news_page); 
$chartPanel->addItem($stat_orders);
$chartPanel->addItem($stat_customers);
$chartPanel->addItem($stat_shopping_cart);
$chartPanel->addItem($salesPanel);
$chartPanel->addItem($searchPanel);

($plugin_code = $xtPlugin->PluginCode('dashboard.php:chart_panel_init')) ? eval($plugin_code) : false;

$layout2 = new PhpExt_Panel();
$layout2 ->setLayout(new PhpExt_Layout_BorderLayout())
		->setAutoWidth(true)
		->setHeight(1200)
		->addItem($chartPanel, PhpExt_Layout_BorderLayoutData::createCenterRegion())		
		->setRenderTo(PhpExt_Javascript::variable("Ext.get('".$chartView."')"));
		
?>
<script>
<?php
echo PhpExt_Ext::OnReady(
	PhpExt_Javascript::assign("data",PhpExt_Javascript::valueToJavascript($myData)),
//    PhpExt_Javascript::assign("dtoday",PhpExt_Javascript::valueToJavascript($salesData_today)),
//    PhpExt_Javascript::assign("dyesterday",PhpExt_Javascript::valueToJavascript($salesData_yesterday)),
    PhpExt_Javascript::assign("dweek",PhpExt_Javascript::valueToJavascript($salesData_week)),
    PhpExt_Javascript::assign("dmonth",PhpExt_Javascript::valueToJavascript($salesData_month)),
    PhpExt_Javascript::assign("dyear",PhpExt_Javascript::valueToJavascript($salesData_year)),
	PhpExt_Javascript::assign("dkeywords",PhpExt_Javascript::valueToJavascript($keywords_stat)),
	PhpExt_Javascript::assign("dkeywords_no_res",PhpExt_Javascript::valueToJavascript($keywords_no_res_stat)),
    
//    PhpExt_Javascript::assign("dall",PhpExt_Javascript::valueToJavascript($salesData_all)),
	$layout1->getJavascript(false, "p1"),
    $layout2->getJavascript(false, "p2")
);
?>
</script>
<div id="<?php echo $gridView; ?>"></div>
<div id="<?php echo $chartView; ?>"></div>