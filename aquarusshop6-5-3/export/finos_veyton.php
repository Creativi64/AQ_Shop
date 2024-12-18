<?php
error_reporting(E_ALL);
// ini_set("display_errors", 1); // DEBUG
/* ----------------------------------------------------------------------------------------

 AbamSoft Finos 15.0
 Script f�r xt:Commerce 6.x
 
 * htmlspecialchars($descriptions['products_name'], ENT_COMPAT,'ISO-8859-1', true)
 ----------------------------------------------------------------------------------------- */

require_once 'connection_data.php';
require('database_tables.php');

define('xml_encoding', '0');
//define('CHARSET','iso-8859-1');  
define('CHARSET', 'UTF-8');

define('LANG_ID', 1);
define('LANG_CODE', 'de');

define('CATEGORIES_ROOT', '0');

define('DIR_IMAGES_ORG', '/media/images/org/');          // /media/images/ORG/         http://www.meinshop.de/media/images/org/aviator.jpg 
define('DIR_IMAGES_INFO', '/media/images/info/');        // /media/images/info/
define('DIR_IMAGES_THUMB', '/media/images/thumb/');      // /media/images/thumb/
define('DIR_IMAGES_ICON', '/media/images/icon/');        // /media/images/icon/

define('DATE_FORMAT_LONG', '%A, %d. %B %Y');            // this is used for strftime()

$version_nummer = '15.0';
$version_datum = '2020.12.15';

//echo urlencode(mb_convert_encoding("���", "ISO-8859-1", "UTF-8"));
//echo "\n";
//echo mb_convert_encoding(urldecode("%E5%E4%F6"),"UTF-8","ISO-8859-1");

// echo "<hr/><pre>\n";
// print_r($_POST)."\n";//DEBUG
// echo "</pre>\n";   

// echo isset($_POST['categories_name']) ? $_POST['categories_name']:"";  

foreach ($_POST as $key => $val) {

  $_POST[$key] = mb_convert_encoding(urldecode($val), "UTF-8", "ISO-8859-1");
}

// echo "<hr/><pre>\n";
// print_r($_POST)."\n";//DEBUG
// echo "</pre>\n";   
// echo "<hr/><p>Request</p><pre>\n";
// print_r($_SERVER )."\n";//DEBUG
// echo "</pre>\n"; 
// echo isset($_POST['categories_name']) ? $_POST['categories_name']:"";
// echo "\n";
// echo $_POST["categories_name"];

// echo "<hr/><pre>\n";
// print_r(filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING))."\n";//DEBUG
// echo "</pre>\n";   



//pr�fen, ob eine Aktion, E-Mail-Adresse sowie Passwort eines Administrators beim Aufruf �bergeben wurden
$action = isset($_POST['action']) ? $_POST['action'] : $_GET['action'];
$user = isset($_POST['user']) ? $_POST['user'] : $_GET['user'];
$password = isset($_POST['password']) ? $_POST['password'] : $_GET['password'];

if (!isset($action)) {
  http_response_code(404);
  die();
}

if (!password_verify($password, "\$2y\$10\$exXTd9T/eMIeSFSXaFEamOwQ5Uhw/IK9j44harju.XQPCwSKjlx6W")) {
  http_response_code(404);
  die();
}

/**
 * @var mysqli $db
 */

// COMMENT IN AFTER TESTING


$db = mysqli_connect($MySQL_Host, $MySQL_User, $MySQL_Passw, $MySQL_DB);
//$db= mysqli_connect('127.0.0.1','root','', 'dbs11250703',3306);
/*
    echo "$MySQL_Host";
    echo "$MySQL_DB";
    echo "$action";
    */


// Aktion wählen und ausführen ---------------------------------------------------------

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

  switch ($action) {

      // Bestellungen ---------------
    case 'set_order_update':
      update_order();
      exit;


      // Produkte ---------------
    case 'set_products':
      save_products();
      exit;
    case 'set_products_descriptions':
      save_products_descriptions();
      exit;
    case 'set_products_defaultimage':
      save_products_defaultimage();
      exit;
    case 'set_products_personal_offers':
      save_products_personal_offers();
      exit;
    case 'set_products_categories':
      save_products_categories();
      exit;
    case 'set_products_cross_selling':
      save_products_cross_selling();
      exit;
    case 'set_products_images':
      save_products_images();
      exit;
    case 'set_products_stockings':
      save_products_stockings();
      exit;
    case 'set_products_specials':
      save_products_specials();
      exit;

      // Hersteller ---------------
    case 'set_manufacturers':
      save_manufacturers();
      exit;

      // Sprachen ---------------
    case 'set_languages':
      save_languages();
      exit;


      // Kategorien ---------------
    case 'set_categories':
      save_categories();
      exit;
    case 'set_categories_descriptions':
      save_categories_descriptions();
      exit;
    case 'set_categorie_image':
      save_categorie_image();
      exit;


      // Allgemein ---------------
    case 'set_seo_url':
      save_seo_url();
      exit;
    case 'set_image_options':
      save_image_options();
      exit;
    case 'set_shop_configuration':
      save_shop_configuration();
      exit;
  }
} else {

  if ($_SERVER['REQUEST_METHOD'] == 'GET') {

    switch ($action) {


        // Kategorien ---------------
      case 'get_categories':
        read_categories();
        exit;
      case 'del_categories':
        delete_categories();
        exit;
      case 'del_categories_all':
        delete_categories_all();
        exit;


        // Produkte ---------------
      case 'get_products':
        read_products();
        exit;
      case 'get_products_specials':
        read_products_specials();
        exit;
      case 'get_products_stockings':
        read_products_stockings();
        exit;
      case 'get_products_list':
        read_products_list();
        exit;
      case 'del_products_categories':
        delete_products_categories();
        exit;
      case 'del_products_categories_all':
        delete_products_categories_all();
        exit;
      case 'del_products_cross_selling':
        delete_products_cross_selling();
        exit;
      case 'del_products_cross_selling_all':
        delete_products_cross_selling_all();
        exit;
      case 'del_products':
        delete_products();
        exit;
      case 'get_products_exists':
        products_exists();
        exit;
      case 'del_products_specials':
        delete_products_specials();
        exit;
      case 'del_products_specials_all':
        delete_products_specials_all();
        exit;


        // Hersteller ---------------
      case 'get_manufacturers':
        read_manufacturers();
        exit;
      case 'del_manufacturers':
        delete_manufacturers();
        exit;
      case 'del_manufacturers_all':
        delete_manufacturers_all();
        exit;


        // Bestellungen ---------------
      case 'get_orders':
        read_orders();
        exit;
      case 'get_orders_status':
        read_orders_status();
        exit;


        // Kunden ---------------
      case 'get_customers':
        read_customers();
        exit;
      case 'get_customers_status':
        read_customers_status();
        exit;


        // Allgemein ---------------

      case 'get_shipping_status':
        read_shipping_status();
        exit;
      case 'get_base_price':
        read_base_price();
        exit;
      case 'check_admin_account':
        administration_account_check();
        exit;
      case 'get_shop_configuration':
        read_shop_configuration();
        exit;
      case 'get_currencies':
        read_currencies();
        exit;
      case 'get_taxes':
        read_taxes();
        exit;
      case 'get_stock_rules':
        read_stock_rules();
        exit;
      case 'get_image_options':
        read_image_options();
        exit;
    }
  }

  mysqli_close($db);
}

/* **************************************** SPEICHERN *****************************************
                              XML f�r die R�ckgabe an Finos generieren
********************************************************************************************* */

define("API_USER", "finosScript");
define("API_PASSWORD", "vx#Ne/x5u!Q5YS225H$"); 

function GetValues($fielnName, $existing, $default = null)
{
  $postValue = isset($_POST[$fielnName]) ? $_POST[$fielnName] : null;

  $dbValue = isset($existing->{$fielnName}) ? $existing->{$fielnName} : null;

  if ($postValue == null) {
    if ($dbValue == null) {
      return $default;
    } else {
      return $dbValue;
    }
  } else {
    return $postValue;
  }

  # return ($postValue!=null) ? $postValue : (($dbValue!=null) ? $dbValue : $default);
}

function GetLocalizedValue($fielnName, $existing, $alpha2Code, $default = null)
{
  $postValue = isset($_POST[$fielnName]) ? $_POST[$fielnName] : null;
  $dbValue = isset($existing->{$fielnName}->{$alpha2Code}) ? $existing->{$fielnName}->{$alpha2Code} : null;

  return ($postValue != null) ? $postValue : (($dbValue != null) ? $dbValue : $default);
}

function GetExistingArticle($productsId)
{
  $requestExisitng = json_encode(array(
    "function" => "getArticle",
    "paras" => array(
      "user" => API_USER,
      "pass" => API_PASSWORD,
      "products_id" => $productsId,
      "external_id" => "",
      "indivFieldList" => []
    )
  ));

  $responseExisting = json_decode(CallAPI('POST', 'https://shop.aquarus.net/index.php?page=xt_api', $requestExisitng));

  $existing = null;
  if (isset($responseExisting->{"productsItemExport"})) {
    $existing = $responseExisting->{"productsItemExport"};
  }

  return $existing;
}

function UpdateArticleFromPost($products_id)
{
  $existing = GetExistingArticle($products_id);

  $data = json_encode(array(
    "function" => "setArticle",
    "paras" => array(
      "user" => API_USER,
      "pass" => API_PASSWORD,
      "productItem" => array(
        "products_id" => GetValues("products_id", $existing, $products_id),
        "external_id" =>  GetValues("external_id", $existing),
        "permission_id" => GetValues("permission_id", $existing),
        "products_owner" => 1,
        "products_ean" => GetValues("products_ean", $existing),
        "products_quantity" => GetValues("products_quantity", $existing),
        "products_average_quantity" => GetValues("products_average_quantity", $existing),
        "products_shippingtime" => GetValues("products_shippingtime", $existing),
        "products_model" => GetValues("products_model", $existing),
        "price_flag_graduated_all" => GetValues("price_flag_graduated_all", $existing),
        "price_flag_graduated_1" => GetValues("price_flag_graduated_1", $existing),
        "price_flag_graduated_2" => GetValues("price_flag_graduated_2", $existing),
        "price_flag_graduated_3" => GetValues("price_flag_graduated_3", $existing),
        "products_sort" => GetValues("products_sort", $existing),
        "products_option_master_price" => GetValues("products_option_master_price", $existing),
        "ekomi_allow" => GetValues("ekomi_allow", $existing),
        "products_image" => GetValues("products_image", $existing),
        "products_price" => GetValues("products_price", $existing),
        "date_added" => GetValues("date_added", $existing, date("Y-m-d H:i:s")),
        "last_modified" => date("Y-m-d H:i:s"),
        "date_available" => GetValues("date_available", $existing),
        "products_weight" => GetValues("products_weight", $existing),
        "products_status" => GetValues("products_status", $existing),
        "products_tax_class_id" => GetValues("products_tax_class_id", $existing),
        "product_template" => null,
        "product_list_template" => null,
        "manufacturers_id" => GetValues("manufacturers_id", $existing),
        "products_ordered" =>  GetValues("products_ordered", $existing),
        "products_fsk18" => GetValues("products_fsk18", $existing),
        "products_vpe" => GetValues("products_vpe", $existing),
        "products_vpe_status" => GetValues("products_vpe_status", $existing),
        "products_vpe_value" => GetValues("products_vpe_value", $existing),
        "products_startpage" => GetValues("products_startpage", $existing),
        "products_startpage_sort" => GetValues("products_startpage_sort", $existing),
        "products_average_rating" => GetValues("products_average_rating", $existing),
        "products_rating_count" => GetValues("products_rating_count", $existing),
        "products_digital" => GetValues("products_digital", $existing),
        "flag_has_specials" => GetValues("flag_has_specials", $existing),
        "products_serials" => GetValues("products_serials", $existing),
        "products_master_flag" => GetValues("products_master_flag", $existing),
        "products_master_model" => GetValues("products_master_model", $existing),
        "products_keywords" => array(
          "de" => GetLocalizedValue("products_keywords", $existing, "de"),
          "en" => GetLocalizedValue("products_keywords", $existing, "en")
        ),
        "products_description" => array(
          "de" => GetLocalizedValue("products_description", $existing, "de"),
          "en" => GetLocalizedValue("products_description", $existing, "en")
        ),
        "products_short_description" => array(
          "de" => GetLocalizedValue("products_short_description", $existing, "de"),
          "en" => GetLocalizedValue("products_short_description", $existing, "en")
        ),
        "meta_description" => array(
          "de" => GetLocalizedValue("meta_description", $existing, "de"),
          "en" => GetLocalizedValue("meta_description", $existing, "en")
        ),
        "meta_title" => array(
          "de" => GetLocalizedValue("meta_title", $existing, "de"),
          "en" => GetLocalizedValue("meta_title", $existing, "en")
        ),
        "meta_keywords" => array(
          "de" => GetLocalizedValue("meta_keywords", $existing, "de"),
          "en" => GetLocalizedValue("meta_keywords", $existing, "en")
        ),
        "seo_url" => array(
          "de" => GetLocalizedValue("seo_url", $existing, "de"),
          "en" => GetLocalizedValue("seo_url", $existing, "en")
        ),
        "url" => array(
          "de" => GetLocalizedValue("products_url", $existing, "de"),
          "en" => GetLocalizedValue("products_url", $existing, "en")
        ),
        "products_name" => array(
          "de" => GetLocalizedValue("products_name", $existing, "de"),
          "en" => GetLocalizedValue("products_name", $existing, "en")
        ),
        "products_special_prices" => [],
        "categories" => [GetValues("categories_id", $existing)],
        "image_name" => "",
        "image" => "",
        "products_prices" => [],
        "products_cross_sell" => [],
        "products_images" => [],
        "products_categories" => [],
        "products_attributes" => [],
        "permissionList" => [],
        "indivFieldsList" => [],
      )
    )
  ), JSON_HEX_QUOT | JSON_HEX_APOS);

  $response = json_decode(CallAPI('POST', 'https://shop.aquarus.net/index.php?page=xt_api', $data));

  return $response;
}

function GetExistingManufacturers($manufacturersId)
{
  $responseExisting = json_decode(GetManufacturerById($manufacturersId));
 
  if (isset($responseExisting)) {
    return $responseExisting;
  }
  return  null;
 
}

function GetManufacturerById($manufacturerId) { 
  $start = 0;
  $size = 10; // Number of records to fetch per request

  do {
      // Prepare the request payload
      $requestPayload = [
          'function' => 'getManufacturers',
          'paras' => [
              "user" => API_USER,
              "pass" => API_PASSWORD,
              'start' => $start,
              'size' => $size,
              'extNumberRange' => 0
          ]
      ];

      // Convert payload to JSON
      $jsonPayload = json_encode($requestPayload);

      // Make the API call
      $response = CallAPI('POST', 'https://shop.aquarus.net/index.php?page=xt_api', $jsonPayload);

      // Decode the JSON response
      $responseData = json_decode($response, true);

      // Check for API errors
      if (isset($responseData['message']) && !empty($responseData['message'])) {
          throw new Exception('API Error: ' . $responseData['message']);
      }

      // Iterate through the results to find the manufacturer with the specified ID
      foreach ($responseData['result'] as $manufacturer) {
          if ($manufacturer['manufacturers_id'] == $manufacturerId) {
              return $manufacturer;
          }
      }

      // Increment the start index for the next set of records
      $start += $size;

      // Continue fetching until no more records are returned
  } while (!empty($responseData['result']));

  // If the manufacturer is not found, return null or handle accordingly
  return null;
}

function UpdateManufacturerFromPost($manufacturersId)
{
  $existing = GetExistingManufacturers($manufacturersId);

  $data = json_encode(array(
    "function" => "setManufacturer",
    "paras" => array(
      "user" => API_USER,
      "pass" => API_PASSWORD,
      "Item" => array(
        "manufacturers_id" => GetValues("manufacturers_id", $existing, $manufacturersId),
        "external_id" => GetValues("external_id", $existing),
        "manufacturers_name" => GetValues("manufacturers_name", $existing),
        "manufacturers_image" => GetValues("manufacturers_image", $existing, null),
        "manufacturers_status" => GetValues("manufacturers_status", $existing, 1),
        "manufacturers_sort" => GetValues("manufacturers_sort", $existing, 0),
        "products_sorting2" => GetValues("products_sorting2",$existing, "ASC") ,
        "date_added" =>  GetValues("date_added", $existing, date("Y-m-d H:i:s")),
        "last_modified" => date("Y-m-d H:i:s"),
        "manufacturers_description" => array(
          "de" => GetLocalizedValue("manufacturers_description", $existing, "de"),
          "en" => GetLocalizedValue("manufacturers_description", $existing, "en")
        ),
        "meta_description" => array(
          "de" => GetLocalizedValue("meta_description", $existing, "de"),
          "en" => GetLocalizedValue("meta_description", $existing, "en")
        ),
        "meta_title" => array(
          "de" => GetLocalizedValue("meta_title", $existing, "de"),
          "en" => GetLocalizedValue("meta_title", $existing, "en")
        ),
        "meta_keywords" => array(
          "de" => GetLocalizedValue("meta_keywords", $existing, "de"),
          "en" => GetLocalizedValue("meta_keywords", $existing, "en")
        ),
        "manufacturers_url" => array(
          "de" => GetLocalizedValue("manufacturers_url", $existing, "de"),
          "en" => GetLocalizedValue("manufacturers_url", $existing, "en")
        ),
        "permissionList" => [],
      )
    )
  ), JSON_HEX_QUOT | JSON_HEX_APOS);

  $response = json_decode(CallAPI('POST', 'https://shop.aquarus.net/index.php?page=xt_api', $data));

  return $response;
}

// function GetExistingCategory($categorieId){
//   $requestExisitng = json_encode(array(
//     "function" => "getCategory",
//     "paras" => array(
//       "user" => API_USER,
//       "pass" => API_PASSWORD,
//       "start" => $categorieId-2,
//       "blocksize" => 1 ,
//       "extNumberRange"=>0
//     )
//   ));

//   $responseExisting = json_decode(CallAPI('POST','https://shop.aquarus.net/index.php?page=xt_api', $requestExisitng));

//   $existing = null;
//   if(isset($responseExisting->{"result"})) {
//     $existing = $responseExisting->{"result"};
//   }

//   if($existing->{"categories_id"} == $categorieId) {
//     return $existing;  
//   }else {
//     return null;
//   }

// }

// function UpdateCategoryFromPost($categorieId){
//   $existing = GetExistingCategory($categorieId);

//   $data = json_encode(array(
//     "function" => "setCategory",
//     "paras" => array(
//       "user" => API_USER,
//       "pass" => API_PASSWORD,
//       )
//     ));

//   $response = json_decode(CallAPI('POST','https://shop.aquarus.net/index.php?page=xt_api',$data));

//   return $response;

// }


function save_products()
{
  /**
   * @var mysqli $db
   */
  global $db;

  $header = '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
    '<STATUS>' . "\n";

  $products_id = isset($_POST['products_id']) ? $_POST['products_id'] : "";

  $response = UpdateArticleFromPost($products_id);

  $schema = '<STATUS_DATA>' . "\n" .
    '<EXISTS>' . 1 . '</EXISTS>' . "\n" .
    '<PRODUCTS_ID>' . ($response->{"products_id"}) . '</PRODUCTS_ID>' . "\n" .
    '<ERROR>' .  (isset($response) ? "0" : "1") . '</ERROR>' . "\n" .
    '<MESSAGE>' . 'Keine Meldung vorhanden' . '</MESSAGE>' . "\n" .
    '</STATUS_DATA>' . "\n";

  $footer = '</STATUS>' . "\n";

  //Ergebnis als XML ausgeben
  create_xml($header . $schema . $footer);
}

function save_products_descriptions()
{
  /**
   * @var mysqli $db
   */
  global $db;

  $header = '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
    '<STATUS>' . "\n";


  $products_id = isset($_POST['products_id']) ? $_POST['products_id'] : "";
  $language_code = isset($_POST['language_code']) ? $_POST['language_code'] : "";


  //Beschreibungstexte laden
  $sql = "select * from " . TABLE_PRODUCTS_DESCRIPTION . " where products_id = '" . $products_id . "' and language_code = '" . $language_code . "'";

  $count_query = mysqli_query($db, $sql);
  if ($decriptions = mysqli_fetch_array($count_query, MYSQLI_BOTH)) {
    $exists = 1;
  } else {
    $exists = 0;
  }


  // Variablen nur �berschreiben, wenn sie als Parameter �bergeben worden sind
  $products_id                  = isset($_POST['products_id']) ? $_POST['products_id'] : "";
  $language_code                = isset($_POST['language_code']) ? $_POST['language_code'] : "";
  $products_name                = isset($_POST['products_name']) ? $_POST['products_name'] : "";
  $products_description         = isset($_POST['products_description']) ? $_POST['products_description'] : "";
  $products_short_description   = isset($_POST['products_short_description']) ? $_POST['products_short_description'] : "";
  $products_keywords            = isset($_POST['products_keywords']) ? $_POST['products_keywords'] : "";
  $products_url                 = isset($_POST['products_url']) ? $_POST['products_url'] : "";
  $products_store_id            = isset($_POST['products_store_id']) ? $_POST['products_store_id'] : "";



  $sql_data_array = array(
    'products_id'                 => $products_id,
    'language_code'               => $language_code,
    'products_name'               => $products_name,
    'products_description'        => $products_description,
    'products_short_description'  => $products_short_description,
    'products_keywords'           => $products_keywords,
    'products_url'                => $products_url,
    'products_store_id'           => $products_store_id
  );


  if ($exists == 0)        // Neuanlage
  {

    //$insert_sql_data = array('products_id' => $products_id);
    //$sql_data_array = array_merge($sql_data_array, $insert_sql_data);
    database_insert(TABLE_PRODUCTS_DESCRIPTION, $sql_data_array);
  } elseif ($exists == 1)    //Aktualisieren
  {
    database_insert(TABLE_PRODUCTS_DESCRIPTION, $sql_data_array, 'update', 'products_id = \'' . $products_id . '\' and language_code = \'' . $language_code . '\'');
  }



  $schema = '<STATUS_DATA>' . "\n" .
    '<EXISTS>' . $exists . '</EXISTS>' . "\n" .
    '<MESSAGE>' . 'Die Beschreibungstexte wurden synchronisiert' . '</MESSAGE>' . "\n" .
    '</STATUS_DATA>' . "\n";


  $footer = '</STATUS>' . "\n";

  //Ergebnis als XML ausgeben
  create_xml($header . $schema . $footer);
}



function save_products_defaultimage()
{
  /**
   * @var mysqli $db
   */
  global $db;

  $header = '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
    '<STATUS>' . "\n";

  $products_id = isset($_POST['products_id']) ? $_POST['products_id'] : "";
  $media_id = isset($_POST['media_id']) ? $_POST['media_id'] : "";


  $sql = "select products_image from " . TABLE_PRODUCTS . " where products_id = '" . $products_id . "'";

  $query_count = mysqli_query($db, $sql);
  if ($product = mysqli_fetch_array($query_count, MYSQLI_BOTH)) {
    $exists = 1;
  } else {
    $exists = 0;
  }


  //Variablen nur �berschreiben, wenn diese als Parameter �bergeben worden sind
  $products_image        = isset($_POST['products_image']) ? $_POST['products_image'] : "";


  $sql_data_array = array('products_image' => $products_image);


  if ($exists == 0)        // Neuanlage
  {
    //Produkt kann nicht �berarbeitet werden
    $Meldung = "Das Standard-Bild kann nicht gespeichert werden, da der Artikel nicht vorhanden ist";
  } elseif ($exists == 1)    //Aktualisieren
  {
    database_insert(TABLE_PRODUCTS, $sql_data_array, 'update', 'products_id = \'' . $products_id . '\'');
    $Meldung = "Das Standard-Bild wurde erfolgreich aktualisiert";


    // Bild in MEDIA anlegen bzw. editieren

    $media_query = mysqli_query($db, "select * from " . TABLE_MEDIA . " where id = '" . $media_id . "'");

    if ($media = mysqli_fetch_array($media_query, MYSQLI_BOTH)) {
      $exists = 1;
    } else {
      $exists = 0;
    }

    // uebergebene Daten einsetzen
    $file                    = isset($_POST['file']) ? $_POST['file'] : "";
    $type                    = isset($_POST['type']) ? $_POST['type'] : "";
    $class                   = isset($_POST['class']) ? $_POST['class'] : "";
    $download_status         = isset($_POST['download_status']) ? $_POST['download_status'] : "";
    $status                  = isset($_POST['status']) ? $_POST['status'] : "";
    $owner                   = isset($_POST['owner']) ? $_POST['owner'] : "";
    $date_added              = isset($_POST['date_added']) ? $_POST['date_added'] : "";
    $last_modified           = isset($_POST['last_modified']) ? $_POST['last_modified'] : "";
    $max_dl_count            = isset($_POST['max_dl_count']) ? $_POST['max_dl_count'] : "";
    $max_dl_days             = isset($_POST['max_dl_days']) ? $_POST['max_dl_days'] : "";
    $total_downloads         = isset($_POST['total_downloads']) ? $_POST['total_downloads'] : "";


    $sql_data_array = array(
      'file'                 => $file,
      'type'                 => $type,
      'class'                => $class,
      'download_status'      => $download_status,
      'status'               => $status,
      'owner'                => $owner,
      'last_modified'        => 'now()',
      'max_dl_count'         => $max_dl_count,
      'max_dl_days'          => $max_dl_days,
      'total_downloads'      => $total_downloads
    );


    if ($exists == 0)        // Neuanlage
    {

      $insert_sql_data = array(
        'id'                 => $media_id,
        'date_added'         => 'now()'
      );

      $sql_data_array = array_merge($sql_data_array, $insert_sql_data);
      database_insert(TABLE_MEDIA, $sql_data_array);
      $media_id = mysqli_insert_id($db);
    } elseif ($exists == 1)    //Aktualisieren
    {
      database_insert(TABLE_MEDIA, $sql_data_array, 'update', 'id = \'' . $media_id . '\'');
    }



    // Bild in MEDIA-Gallery anlegen bzw. editieren

    $media_gallery_query = mysqli_query($db, "select * from " . TABLE_MEDIA_TO_MEDIA_GALLERY . " where m_id = '" . $media_id . "'");

    if ($media_gallery = mysqli_fetch_array($media_gallery_query, MYSQLI_BOTH)) {
      $exists = 1;
    } else {
      $exists = 0;
    }

    // uebergebene Daten einsetzen
    $ml_id                    = isset($_POST['ml_id']) ? $_POST['ml_id'] : "";
    $mg_id                    = isset($_POST['mg_id']) ? $_POST['mg_id'] : "";


    $sql_data_array = array(/* 'ml_id'                => $ml_id,  CHRASHT MIT DEM AUTO INCREMENT*/
      'mg_id'                => $mg_id
    );



    if ($exists == 0)        // Neuanlage
    {

      $insert_sql_data = array('m_id'               => $media_id);

      $sql_data_array = array_merge($sql_data_array, $insert_sql_data);
      database_insert(TABLE_MEDIA_TO_MEDIA_GALLERY, $sql_data_array);
      $media_gallery_id = mysqli_insert_id($db);
    } elseif ($exists == 1)    //Aktualisieren
    {
      database_insert(TABLE_MEDIA_TO_MEDIA_GALLERY, $sql_data_array, 'update', 'm_id = \'' . $media_id . '\'');
    }
  }


  $schema = '<STATUS_DATA>' . "\n" .
    '<EXISTS>' . $exists . '</EXISTS>' . "\n" .
    '<PRODUCTS_ID>' . $products_id . '</PRODUCTS_ID>' . "\n" .
    '<MEDIA_ID>' . $media_id . '</MEDIA_ID>' . "\n" .
    '<MEDIA_GALLERY_ID>' . $media_gallery_id . '</MEDIA_GALLERY_ID>' . "\n" .
    '<ERROR>' . '0' . '</ERROR>' . "\n" .
    '<MESSAGE>' . $Meldung . '</MESSAGE>' . "\n" .
    '</STATUS_DATA>' . "\n";


  $footer = '</STATUS>' . "\n";

  //Ergebnis als XML ausgeben
  create_xml($header . $schema . $footer);
}



function save_products_personal_offers()
{
  /**
   * @var mysqli $db
   */
  global $db;

  $header = '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
    '<STATUS>' . "\n";


  $customers_status_id = isset($_POST['customers_status_id']) ? $_POST['customers_status_id'] : ""; //0, 1, 2, all
  $id = isset($_POST['id']) ? $_POST['id'] : "";


  $sql = "select id, products_id, discount_quantity, price " .
    "from " . TABLE_PRODUCTS_PRICE_GROUP . $customers_status_id . " where id = '" . $id . "'";

  $count_query = mysqli_query($db, $sql);
  if ($poffers = mysqli_fetch_array($count_query, MYSQLI_BOTH)) {
    $exists = 1;
    //mysqli_query($db,"delete from " . TABLE_PRODUCTS_PRICE_GROUP . $customers_status_id . " where id = '" . $id . "'");
  } else {
    $exists = 0;
  }


  // Variablen nur �berschreiben, wenn sie als Parameter �bergeben worden sind
  $id                       = isset($_POST['id']) ? $_POST['id'] : "";
  $products_id              = isset($_POST['products_id']) ? $_POST['products_id'] : "";
  $discount_quantity        = isset($_POST['discount_quantity']) ? $_POST['discount_quantity'] : "";
  $price                    = isset($_POST['price']) ? $_POST['price'] : "";


  $sql_data_array = array(
    'products_id'           => $products_id,
    'discount_quantity'     => $discount_quantity,
    'price'                 => $price
  );


  if ($exists == 0)        //Neuanlage
  {


    $insert_sql_data = array('id'               => $id);

    $sql_data_array = array_merge($sql_data_array, $insert_sql_data);
    database_insert(TABLE_PRODUCTS_PRICE_GROUP . $customers_status_id, $sql_data_array);

    if (isset($_POST['id']) && ($id != '')) {
      $id = isset($_POST['id']) ? $_POST['id'] : "";
    } else {
      $id = mysqli_insert_id($db);
    }
  } elseif ($exists == 1)    //Aktualisieren

  {
    database_insert(TABLE_PRODUCTS_PRICE_GROUP . $customers_status_id, $sql_data_array, 'update', 'id = \'' . $id . '\'');
  }


  $schema = '<STATUS_DATA>'  . "\n" .
    '<EXISTS>' . $exists . '</EXISTS>' . "\n" .
    '<PRICE_ID>' . $id . '</PRICE_ID>' . "\n" .
    '<MESSAGE>' . 'Keine Meldung vorhanden' . '</MESSAGE>' . "\n" .
    '</STATUS_DATA>' . "\n";


  $footer = '</STATUS>' . "\n";

  //Ergebnis als XML ausgeben
  create_xml($header . $schema . $footer);
}



function save_products_categories()
{
  /**
   * @var mysqli $db
   */
  global $db;

  $header = '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
    '<STATUS>' . "\n";

  $products_id = isset($_POST['products_id']) ? $_POST['products_id'] : "";
  $categories_id = isset($_POST['categories_id']) ? $_POST['categories_id'] : "";


  //Kategorien zum Artikel laden
  $sql = "select * from " . TABLE_PRODUCTS_TO_CATEGORIES . " where products_id = '" . $products_id . "' and categories_id = '" . $categories_id . "'";

  $count_query = mysqli_query($db, $sql);
  if ($products_categories = mysqli_fetch_array($count_query, MYSQLI_BOTH)) {
    $exists = 1;
  } else {
    $exists = 0;
  }


  // Variablen nur �berschreiben, wenn sie als Parameter �bergeben worden sind
  $categories_id       = isset($_POST['categories_id']) ? $_POST['categories_id'] : "";
  $master_link         = isset($_POST['master_link']) ? $_POST['master_link'] : "";
  $store_id            = isset($_POST['store_id']) ? $_POST['store_id'] : "";


  $sql_data_array = array(
    'categories_id'     => $categories_id,
    'master_link'       => $master_link,
    'store_id'          => $store_id
  );


  if ($exists == 0)        // Neuanlage
  {

    $insert_sql_data = array('products_id'    => $products_id);

    $sql_data_array = array_merge($sql_data_array, $insert_sql_data);
    database_insert(TABLE_PRODUCTS_TO_CATEGORIES, $sql_data_array);
  } elseif ($exists == 1)    //Aktualisieren
  {
    database_insert(TABLE_PRODUCTS_TO_CATEGORIES, $sql_data_array, 'update', 'products_id = \'' . $products_id . '\' and categories_id = \'' . $categories_id . '\'');
  }


  $schema = '<STATUS_DATA>' . "\n" .
    '<EXISTS>' . $exists . '</EXISTS>' . "\n" .
    '<PRODUCTS_ID>' . $products_id . '</PRODUCTS_ID>' . "\n" .
    '<CATEGORIES_ID>' . $categories_id . '</CATEGORIES_ID>' . "\n" .
    '<MESSAGE>' . 'Keine Meldung vorhanden' . '</MESSAGE>' . "\n" .
    '</STATUS_DATA>' . "\n";


  $footer = '</STATUS>' . "\n";

  //Ergebnis als XML ausgeben
  create_xml($header . $schema . $footer);
}



function save_products_cross_selling()
{
  /**
   * @var mysqli $db
   */
  global $db;

  $header = '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
    '<STATUS>' . "\n";


  $products_id = isset($_POST['products_id']) ? $_POST['products_id'] : "";


  //Cross-Selling zum Artikel laden
  $sql = "select * from " . TABLE_PRODUCTS_CROSS_SELL . " where products_id = '" . $products_id . "'";

  $selling_query = mysqli_query($db, $sql);
  if ($products_cross_selling = mysqli_fetch_array($selling_query, MYSQLI_BOTH)) {
    $exists = 1;
  } else {
    $exists = 0;
  }


  // Variablen nur �berschreiben, wenn sie als Parameter �bergeben worden sind
  $products_id_cross_sell       = isset($_POST['products_id_cross_sell']) ? $_POST['products_id_cross_sell'] : "";

  $sql_data_array = array('products_id_cross_sell'     => $products_id_cross_sell);


  if ($exists == 0)        // Neuanlage
  {

    $insert_sql_data = array('products_id'    => $products_id);

    $sql_data_array = array_merge($sql_data_array, $insert_sql_data);
    database_insert(TABLE_PRODUCTS_CROSS_SELL, $sql_data_array);
  } elseif ($exists == 1)    //Aktualisieren
  {
    database_insert(TABLE_PRODUCTS_CROSS_SELL, $sql_data_array, 'update', 'products_id = \'' . $products_id . '\'');
  }


  $schema = '<STATUS_DATA>' . "\n" .
    '<EXISTS>' . $exists . '</EXISTS>' . "\n" .
    '<PRODUCTS_ID>' . $products_id . '</PRODUCTS_ID>' . "\n" .
    '<PRODUCTS_ID_CROSS_SELL>' . $products_id_cross_sell . '</PRODUCTS_ID_CROSS_SELL>' . "\n" .
    '<MESSAGE>' . 'Keine Meldung vorhanden' . '</MESSAGE>' . "\n" .
    '</STATUS_DATA>' . "\n";


  $footer = '</STATUS>' . "\n";

  //Ergebnis als XML ausgeben
  create_xml($header . $schema . $footer);
}




function save_products_images()
{
  /**
   * @var mysqli $db
   */
  $Meldung = "Keine Meldung vorhanden";
  global $db;

  $header = '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
    '<STATUS>' . "\n";

  $media_id = isset($_POST['media_id']) ? $_POST['media_id'] : "";             //Webshop_ID aus zus�tzlichen Bildern
  $media_link_id = isset($_POST['media_link_id']) ? $_POST['media_link_id'] : "";

  $products_id = isset($_POST['link_id']) ? $_POST['link_id'] : "";
  // Bild in MEDIA anlegen bzw. editieren

  $media_query = mysqli_query($db, "select * from " . TABLE_MEDIA . " where id = '" . $media_id . "'");

  if ($media = mysqli_fetch_array($media_query, MYSQLI_BOTH)) {
    $exists = 1;
  } else {
    $exists = 0;
  }

  // uebergebene Daten einsetzen
  $file                    = isset($_POST['file']) ? $_POST['file'] : "";
  $type                    = isset($_POST['type']) ? $_POST['type'] : "";
  $class                   = isset($_POST['class']) ? $_POST['class'] : "";
  $download_status         = isset($_POST['download_status']) ? $_POST['download_status'] : "";
  $status                  = isset($_POST['status']) ? $_POST['status'] : "";
  $owner                   = isset($_POST['owner']) ? $_POST['owner'] : "";
  $date_added              = isset($_POST['date_added']) ? $_POST['date_added'] : "";
  $last_modified           = isset($_POST['last_modified']) ? $_POST['last_modified'] : "";
  $max_dl_count            = isset($_POST['max_dl_count']) ? $_POST['max_dl_count'] : "";
  $max_dl_days             = isset($_POST['max_dl_days']) ? $_POST['max_dl_days'] : "";
  $total_downloads         = isset($_POST['total_downloads']) ? $_POST['total_downloads'] : "";


  $sql_data_array = array(
    'file'                 => $file,
    'type'                 => $type,
    'class'                => $class,
    'download_status'      => $download_status,
    'status'               => $status,
    'owner'                => $owner,
    'last_modified'        => 'now()',
    'max_dl_count'         => $max_dl_count,
    'max_dl_days'          => $max_dl_days,
    'total_downloads'      => $total_downloads
  );


  if ($exists == 0)        // Neuanlage
  {

    $insert_sql_data = array(
      'id'                 => $media_id,
      'date_added'         => 'now()'
    );

    $sql_data_array = array_merge($sql_data_array, $insert_sql_data);
    database_insert(TABLE_MEDIA, $sql_data_array);
    $media_id = mysqli_insert_id($db);
  } elseif ($exists == 1)    //Aktualisieren
  {
    database_insert(TABLE_MEDIA, $sql_data_array, 'update', 'id = \'' . $media_id . '\'');
  }



  // Bild in MEDIA-Link anlegen bzw. editieren

  $media_link_query = mysqli_query($db, "select * from " . TABLE_MEDIA_LINK . " where ml_id = '" . $media_link_id . "'");

  if ($media_link = mysqli_fetch_array($media_link_query, MYSQLI_BOTH)) {
    $exists = 1;
  } else {
    $exists = 0;
  }

  // uebergebene Daten einsetzen
  $m_id                    = isset($_POST['media_id']) ? $_POST['media_id'] : "";
  $link_id                 = isset($_POST['link_id']) ? $_POST['link_id'] : "";
  $class                   = isset($_POST['class']) ? $_POST['class'] : "";
  $type                    = isset($_POST['type']) ? $_POST['type'] : "";
  $sort_order              = isset($_POST['sort_order']) ? $_POST['sort_order'] : "";


  $sql_data_array = array(
    'm_id'                => $m_id,
    'link_id'             => $link_id,
    'class'               => $class,
    'type'                => $type,
    'sort_order'          => $sort_order
  );


  if ($exists == 0)        // Neuanlage
  {

    $insert_sql_data = array(/* 'ml_id'               => $media_link_id, */
      'm_id'                => $media_id
    );

    $sql_data_array = array_merge($sql_data_array, $insert_sql_data);
    database_insert(TABLE_MEDIA_LINK, $sql_data_array);
    $media_link_id = mysqli_insert_id($db);
  } elseif ($exists == 1)    //Aktualisieren
  {
    database_insert(TABLE_MEDIA_LINK, $sql_data_array, 'update', 'ml_id = \'' . $media_link_id . '\'');
  }



  // Bild in MEDIA-Gallery anlegen bzw. editieren

  $media_gallery_query = mysqli_query($db, "select * from " . TABLE_MEDIA_TO_MEDIA_GALLERY . " where m_id = '" . $media_id . "'");

  if ($media_gallery = mysqli_fetch_array($media_gallery_query, MYSQLI_BOTH)) {
    $exists = 1;
  } else {
    $exists = 0;
  }

  // uebergebene Daten einsetzen
  $ml_id                    = isset($_POST['ml_id']) ? $_POST['ml_id'] : "";
  $mg_id                    = isset($_POST['mg_id']) ? $_POST['mg_id'] : "";


  $sql_data_array = array(/* 'ml_id'                => $ml_id,  CHRASHT MIT DEM AUTO INCREMENT*/
    'mg_id'                => $mg_id
  );



  if ($exists == 0)        // Neuanlage
  {

    $insert_sql_data = array('m_id'               => $media_id);

    $sql_data_array = array_merge($sql_data_array, $insert_sql_data);
    database_insert(TABLE_MEDIA_TO_MEDIA_GALLERY, $sql_data_array);
    $media_gallery_id = mysqli_insert_id($db);
  } elseif ($exists == 1)    //Aktualisieren
  {
    database_insert(TABLE_MEDIA_TO_MEDIA_GALLERY, $sql_data_array, 'update', 'm_id = \'' . $media_id . '\'');
    $media_gallery_id = mysqli_insert_id($db);
  }


  $schema = '<STATUS_DATA>' . "\n" .
    '<EXISTS>' . $exists . '</EXISTS>' . "\n" .
    '<PRODUCTS_ID>' . $products_id . '</PRODUCTS_ID>' . "\n" .
    '<MEDIA_ID>' . $media_id . '</MEDIA_ID>' . "\n" .
    '<MEDIA_LINK_ID>' . $media_link_id . '</MEDIA_LINK_ID>' . "\n" .
    '<MEDIA_GALLERY_ID>' . $media_gallery_id . '</MEDIA_GALLERY_ID>' . "\n" .
    '<ERROR>' . '0' . '</ERROR>' . "\n" .
    '<MESSAGE>' . $Meldung . '</MESSAGE>' . "\n" .
    '</STATUS_DATA>' . "\n";


  $footer = '</STATUS>' . "\n";

  //Ergebnis als XML ausgeben
  create_xml($header . $schema . $footer);
}


function save_products_stockings()
{
  /**
   * @var mysqli $db
   */
  global $db;

  $header = '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
    '<STATUS>' . "\n";

  $products_id = isset($_POST['products_id']) ? $_POST['products_id'] : "";


  //Produkt laden
  $cmd = "select * from " . TABLE_PRODUCTS . " where products_id = '" . $products_id . "'";

  $products_query = mysqli_query($db, $cmd);
  if ($product = mysqli_fetch_array($products_query, MYSQLI_BOTH)) {
    $exists = 1;
  } else {
    $exists = 0;
  }


  //Variablen nur �berschreiben, wenn diese als Parameter �bergeben worden sind
  $products_quantity         = isset($_POST['products_quantity']) ? $_POST['products_quantity'] : "";
  $products_status           = isset($_POST['products_status']) ? $_POST['products_status'] : "";
  $products_shippingtime     = isset($_POST['products_shippingtime']) ? $_POST['products_shippingtime'] : "";


  $sql_data_array = array(
    'products_quantity'         => $products_quantity,
    //'products_status  '         => $products_status,
    'products_shippingtime'     => $products_shippingtime
  );

  if ($exists == 0)        // Neuanlage
  {
    //Produkt kann nicht �berarbeitet werden
    $Meldung = "Bestandswerte k�nnen nicht aktualisiert werden, da der Artikel nicht vorhanden ist";
  } elseif ($exists == 1)    //Aktualisieren
  {
    database_insert(TABLE_PRODUCTS, $sql_data_array, 'update', 'products_id = \'' . $products_id . '\'');
    $Meldung = "Bestandswerte wurden erfolgreich aktualisiert";
  }


  $schema = '<STATUS_DATA>' . "\n" .
    '<EXISTS>' . $exists . '</EXISTS>' . "\n" .
    '<PRODUCTS_ID>' . $products_id . '</PRODUCTS_ID>' . "\n" .
    '<ERROR>' . '0' . '</ERROR>' . "\n" .
    '<MESSAGE>' . $Meldung . '</MESSAGE>' . "\n" .
    '</STATUS_DATA>' . "\n";


  $footer = '</STATUS>' . "\n";


  //Ergebnis als XML ausgeben
  create_xml($header . $schema . $footer);
}


function save_products_specials()
{
  /**
   * @var mysqli $db
   */
  global $db;

  $header = '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
    '<STATUS>' . "\n";


  $specials_id = isset($_POST['specials_id']) ? $_POST['specials_id'] : "";

  $sql = "select * from " . TABLE_PRODUCTS_PRICE_SPECIAL . " where id = '" . $specials_id . "'";

  $count_query = mysqli_query($db, $sql);
  if ($specials = mysqli_fetch_array($count_query, MYSQLI_BOTH)) {
    $exists = 1;
  } else {
    $exists = 0;
  }


  // Variablen nur �berschreiben, wenn sie als Parameter �bergeben worden sind
  $products_id                  = isset($_POST['products_id']) ? $_POST['products_id'] : "";
  $specials_price               = isset($_POST['specials_price']) ? $_POST['specials_price'] : "";
  $date_available               = isset($_POST['date_available']) ? $_POST['date_available'] : "";
  $date_expired                 = isset($_POST['date_expired']) ? $_POST['date_expired'] : "";
  $status                       = isset($_POST['status']) ? $_POST['status'] : "";

  $sql_data_array = array(
    'products_id'                 => $products_id,
    'specials_price'              => $specials_price,
    'date_available'              => $date_available,
    'date_expired'                => $date_expired,
    'status'                      => $status
  );


  if ($exists == 0)        // Neuanlage
  {

    $insert_sql_data = array(
      'id'                        => $specials_id,
      'group_permission_all'      => '1'
    );

    $sql_data_array = array_merge($sql_data_array, $insert_sql_data);
    database_insert(TABLE_PRODUCTS_PRICE_SPECIAL, $sql_data_array);

    if (isset($_POST['specials_id']) && ($specials_id != '')) {
      $specials_id = isset($_POST['specials_id']) ? $_POST['specials_id'] : "";
    } else {
      $specials_id = mysqli_insert_id($db);
    }
  } elseif ($exists == 1)    //Aktualisieren
  {
    database_insert(TABLE_PRODUCTS_PRICE_SPECIAL, $sql_data_array, 'update', 'id = \'' . $specials_id . '\'');
  }


  $schema = '<STATUS_DATA>' . "\n" .
    '<EXISTS>' . $exists . '</EXISTS>' . "\n" .
    '<SPECIALS_ID>' . $specials_id . '</SPECIALS_ID>' . "\n" .
    '<MESSAGE>' . 'Keine Meldung vorhanden' . '</MESSAGE>' . "\n" .
    '</STATUS_DATA>' . "\n";


  $footer = '</STATUS>' . "\n";

  //Ergebnis als XML ausgeben
  create_xml($header . $schema . $footer);
}


function save_manufacturers()
{
  /**
   * @var mysqli $db
   */
  global $db;

  $header = '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" . '<STATUS>' . "\n";
 
  $manufacturers_id = isset($_POST['manufacturers_id']) ? $_POST['manufacturers_id'] : "";
    
  $response = UpdateManufacturerFromPost($manufacturers_id);
  
  $schema = '<STATUS_DATA>' . "\n" .
    '<EXISTS>' . 1 . '</EXISTS>' . "\n" .
    '<MANUFACTURERS_ID>' . ($response->{"manufacturers_id"}). '</MANUFACTURERS_ID>' . "\n" .
    '<MESSAGE>' . 'Keine Meldung vorhanden' . '</MESSAGE>' . "\n" .
    '</STATUS_DATA>' . "\n";
 
  $footer = '</STATUS>' . "\n";

  //Ergebnis als XML ausgeben
  create_xml($header . $schema . $footer);
}



function save_image_options()
{
  /**
   * @var mysqli $db
   */
  global $db;

  $header = '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
    '<STATUS>' . "\n";

  $id = isset($_POST['id']) ? $_POST['id'] : "";


  //Bild-Optionen laden
  $cmd = "select * from " . TABLE_IMAGE_TYPE . " where id = '" . $id . "'";

  $imageopt_query = mysqli_query($db, $cmd);
  if ($imageopt = mysqli_fetch_array($imageopt_query, MYSQLI_BOTH)) {
    $exists = 1;
  } else {
    $exists = 0;
  }


  //Variablen nur �berschreiben, wenn diese als Parameter �bergeben worden sind
  $width         = isset($_POST['width']) ? $_POST['width'] : "";
  $height        = isset($_POST['height']) ? $_POST['height'] : "";


  $sql_data_array = array(
    'width'         => $width,
    'height'        => $height
  );

  if ($exists == 0)        // Neuanlage
  {

    //Bild-Option kann nicht �berarbeitet werden
    $Meldung = "Bild-Optionen k�nnen nicht aktualisiert werden, da diese nicht vorhanden sind.";
  } elseif ($exists == 1)    //Aktualisieren
  {
    database_insert(TABLE_IMAGE_TYPE, $sql_data_array, 'update', 'id = \'' . $id . '\'');
    $Meldung = "Die Bild-Optionen wurden erfolgreich aktualisiert.";
  }


  $schema = '<STATUS_DATA>' . "\n" .
    '<EXISTS>' . $exists . '</EXISTS>' . "\n" .
    '<ID>' . $id . '</ID>' . "\n" .
    '<ERROR>' . '0' . '</ERROR>' . "\n" .
    '<MESSAGE>' . $Meldung . '</MESSAGE>' . "\n" .
    '</STATUS_DATA>' . "\n";


  $footer = '</STATUS>' . "\n";


  //Ergebnis als XML ausgeben
  create_xml($header . $schema . $footer);
}



function save_shop_configuration()
{
  /**
   * @var mysqli $db
   */
  global $db;

  $header = '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
    '<STATUS>' . "\n";

  $configuration_id = isset($_POST['configuration_id']) ? $_POST['configuration_id'] : "";
  $configuration_key = isset($_POST['configuration_key']) ? $_POST['configuration_key'] : "";
  $configuration_value = isset($_POST['configuration_value']) ? $_POST['configuration_value'] : "";


  $sql = "select * from " . TABLE_CONFIGURATION . "_1 where id = '" . $configuration_id . "'";

  $config_query = mysqli_query($db, $sql);
  if ($configurations = mysqli_fetch_array($config_query, MYSQLI_BOTH)) {
    $exists = 1;
  } else {
    $exists = 0;
  }


  // Variablen nur �berschreiben, wenn sie als Parameter �bergeben worden sind
  $configuration_key         = isset($_POST['configuration_key']) ? $_POST['configuration_key'] : "";
  $configuration_value       = isset($_POST['configuration_value']) ? $_POST['configuration_value'] : "";

  $sql_data_array = array(
    'config_key'     => $configuration_key,
    'config_value'   => $configuration_value
  );


  if ($exists == 0)        // Neuanlage
  {

    $insert_sql_data = array('id'           => $configuration_id);

    $sql_data_array = array_merge($sql_data_array, $insert_sql_data);
    database_insert(TABLE_CONFIGURATION, $sql_data_array);
  } elseif ($exists == 1)    //Aktualisieren
  {
    database_insert(TABLE_CONFIGURATION, $sql_data_array, 'update', 'id = \'' . $configuration_id . '\'');
  }


  $schema = '<STATUS_DATA>' . "\n" .
    '<EXISTS>' . $exists . '</EXISTS>' . "\n" .
    '<CONFIGURATION_ID>' . $configuration_id . '</CONFIGURATION_ID>' . "\n" .
    '<CONFIGURATION_KEY>' . $configuration_key . '</CONFIGURATION_KEY>' . "\n" .
    '<CONFIGURATION_VALUE>' . $configuration_value . '</CONFIGURATION_VALUE>' . "\n" .
    '<MESSAGE>' . 'Keine Meldung vorhanden' . '</MESSAGE>' . "\n" .
    '</STATUS_DATA>' . "\n";


  $footer = '</STATUS>' . "\n";

  //Ergebnis als XML ausgeben
  create_xml($header . $schema . $footer);
}



function save_categories()
{
  /**
   * @var mysqli $db
   */
  global $db;

  $header = '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
    '<STATUS>' . "\n";


  $categories_id = isset($_POST['categories_id']) ? $_POST['categories_id'] : "";
  $parent_id = isset($_POST['parent_id']) ? $_POST['parent_id'] : "";


  //Kategorie laden
  $sql = "select categories_id, external_id, permission_id, categories_owner, categories_image, parent_id, categories_status, categories_template, listing_template, sort_order, products_sorting, products_sorting2, top_category, date_added, last_modified, google_product_cat " .
    "from " . TABLE_CATEGORIES . " where categories_id = '" . $categories_id . "'";


  $count_query = mysqli_query($db, $sql);
  if ($categorie = mysqli_fetch_array($count_query, MYSQLI_BOTH)) {
    $exists = 1;
  } else {
    $exists = 0;
  }


  // Variablen nur �berschreiben, wenn sie als Parameter �bergeben worden sind
  $external_id         = isset($_POST['external_id']) ? $_POST['external_id'] : "";
  $permission_id       = isset($_POST['permission_id']) ? $_POST['permission_id'] : "";
  $categories_owner    = isset($_POST['categories_owner']) ? $_POST['categories_owner'] : "";
  $categories_image    = isset($_POST['categories_image']) ? $_POST['categories_image'] : "";
  $parent_id           = isset($_POST['parent_id']) ? $_POST['parent_id'] : "";
  $categories_status   = isset($_POST['categories_status']) ? $_POST['categories_status'] : "";
  $categories_template = isset($_POST['categories_template']) ? ClearSTANDARTTEMPLATE($_POST['categories_template']) : "";
  $listing_template    = isset($_POST['listing_template']) ? ClearSTANDARTTEMPLATE($_POST['listing_template']) : "";
  $sort_order          = isset($_POST['sort_order']) ? $_POST['sort_order'] : "";
  $products_sorting    = isset($_POST['products_sorting']) ? $_POST['products_sorting'] : "";
  $products_sorting2   = isset($_POST['products_sorting2']) ? $_POST['products_sorting2'] : "";
  $top_category        = isset($_POST['top_category']) ? $_POST['top_category'] : "";
  $date_added          = isset($_POST['date_added']) ? $_POST['date_added'] : "";
  $last_modified       = isset($_POST['last_modified']) ? $_POST['last_modified'] : "";
  $google_product_cat  = isset($_POST['google_product_cat']) ? $_POST['google_product_cat'] : "";


  $sql_data_array = array(
    'external_id'         => $external_id,
    'permission_id'       => $permission_id,
    'categories_owner'    => 1,
    'categories_image'    => $categories_image,
    'parent_id'           => $parent_id,
    'categories_status'   => $categories_status,
    'categories_template' => $categories_template,
    'listing_template'    => $listing_template,
    'sort_order'          => $sort_order,
    'products_sorting'    => $products_sorting,
    'products_sorting2'   => $products_sorting2,
    'top_category'        => $top_category,
    'last_modified'       => 'now()',
    'google_product_cat'  => $google_product_cat
  );

  if ($exists == 0)        // Neuanlage
  {

    $insert_sql_data = array(
      'categories_id'       => $categories_id,
      'date_added'          => 'now()'
    );


    $sql_data_array = array_merge($sql_data_array, $insert_sql_data);
    database_insert(TABLE_CATEGORIES, $sql_data_array);
    $categories_id = mysqli_insert_id($db);
  } elseif ($exists == 1)    //Aktualisieren
  {
    database_insert(TABLE_CATEGORIES, $sql_data_array, 'update', 'categories_id = \'' . $categories_id . '\'');
  }



  // Beschreibungstexte zur Kategorie laden
  $sql = "select categories_id, language_code, categories_name, categories_heading_title, categories_description, " .
    "categories_description_bottom" . " from " . TABLE_CATEGORIES_DESCRIPTION . " where categories_id = '" . $categories_id . "' and language_code = '" . LANG_CODE . "'";


  $desc_query = mysqli_query($db, $sql);


  if ($description = mysqli_fetch_array($desc_query, MYSQLI_BOTH)) {
    $categories_name                    = $description['categories_name'];
    $categories_heading_title           = $description['categories_heading_title'];
    $categories_description             = $description['categories_description'];
    $categories_description_bottom      = $description['categories_description_bottom'];
  }


  // Variablen nur �berschreiben, wenn sie als Parameter �bergeben worden sind
  $categories_name                    = isset($_POST['categories_name']) ? $_POST['categories_name'] : "";
  $categories_heading_title           = isset($_POST['categories_heading_title']) ? $_POST['categories_heading_title'] : "";
  $categories_description             = isset($_POST['categories_description']) ? $_POST['categories_description'] : "";
  $categories_description_bottom      = isset($_POST['categories_description_bottom']) ? $_POST['categories_description_bottom'] : "";

  $sql_data_array = array(
    'categories_name'                    => $categories_name,
    'categories_heading_title'           => $categories_heading_title,
    'categories_description'             => $categories_description,
    'categories_description_bottom'      => $categories_description_bottom,
    'categories_store_id' => 1
  ); //NEEDED TO PROPERLY SHOW UP


  if ($exists == 0)        // Neuanlage
  {
    $insert_sql_data = array(
      'categories_id'   => $categories_id,
      'language_code'   => LANG_CODE
    );

    $sql_data_array = array_merge($sql_data_array, $insert_sql_data);
    database_insert(TABLE_CATEGORIES_DESCRIPTION, $sql_data_array);
  } elseif ($exists == 1)    //Aktualisieren
  {
    database_insert(TABLE_CATEGORIES_DESCRIPTION, $sql_data_array, 'update', 'categories_id = \'' . $categories_id . '\' and language_code = \'' . LANG_CODE . '\'');
  }



  $schema = '<STATUS_DATA>' . "\n" .
    '<EXISTS>' . $exists . '</EXISTS>' . "\n" .
    '<CATEGORIES_ID>' . $categories_id . '</CATEGORIES_ID>' . "\n" .
    '<MESSAGE>' . 'Keine Meldung vorhanden' . '</MESSAGE>' . "\n" .
    '</STATUS_DATA>' . "\n";


  $footer = '</STATUS>' . "\n";

  //Ergebnis als XML ausgeben
  create_xml($header . $schema . $footer);
}





function save_categories_descriptions()
{
  /**
   * @var mysqli $db
   */
  global $db;

  $header = '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
    '<STATUS>' . "\n";


  $categories_id = isset($_POST['categories_id']) ? $_POST['categories_id'] : "";
  $language_code = isset($_POST['language_code']) ? $_POST['language_code'] : "";


  //Beschreibungstexte laden
  $sql = "select categories_id, language_code, categories_name, categories_heading_title, categories_description, categories_description_bottom " .
    "from " . TABLE_CATEGORIES_DESCRIPTION . " where categories_id = '" . $categories_id . "' and language_code = '" . $language_code . "'";

  $count_query = mysqli_query($db, $sql);

  if ($decriptions = mysqli_fetch_array($count_query, MYSQLI_BOTH)) {
    $exists = 1;
  } else {
    $exists = 0;
  }

  // Variablen nur �berschreiben, wenn sie als Parameter �bergeben worden sind
  $categories_name                    = isset($_POST['categories_name']) ? $_POST['categories_name'] : "";
  $categories_heading_title           = isset($_POST['categories_heading_title']) ? $_POST['categories_heading_title'] : "";
  $categories_description             = isset($_POST['categories_description']) ? $_POST['categories_description'] : "";
  $categories_description_bottom      = isset($_POST['categories_description_bottom']) ? $_POST['categories_description_bottom'] : "";


  $sql_data_array = array(
    'categories_name'                    => $categories_name,
    'categories_heading_title'           => $categories_heading_title,
    'categories_description'             => $categories_description,
    'categories_description_bottom'      => $categories_description,
    'categories_store_id' => 1
  ); //NEEDED TO PROPERLY SHOW UP


  if ($exists == 0)        // Neuanlage
  {

    $insert_sql_data = array(
      'categories_id'   => $categories_id,
      'language_code'   => $language_code
    );

    $sql_data_array = array_merge($sql_data_array, $insert_sql_data);
    database_insert(TABLE_CATEGORIES_DESCRIPTION, $sql_data_array);
  } elseif ($exists == 1)    //Aktualisieren

  {
    database_insert(TABLE_CATEGORIES_DESCRIPTION, $sql_data_array, 'update', 'categories_id = \'' . $categories_id . '\' and language_code = \'' . $language_code . '\'');
  }



  $schema = '<STATUS_DATA>' . "\n" .
    '<EXISTS>' . $exists . '</EXISTS>' . "\n" .
    '<LANGUAGE_CODE>' . $language_code . '</LANGUAGE_CODE>' . "\n" .
    '<MESSAGE>' . 'Die Beschreibungstexte wurden synchronisiert' . '</MESSAGE>' . "\n" .
    '</STATUS_DATA>' . "\n";


  $footer = '</STATUS>' . "\n";

  //Ergebnis als XML ausgeben
  create_xml($header . $schema . $footer);
}



function save_categorie_image()
{
  /**
   * @var mysqli $db
   */
  global $db;

  $header = '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
    '<STATUS>' . "\n";


  $categories_id = isset($_POST['categories_id']) ? $_POST['categories_id'] : "";
  $media_id = isset($_POST['media_id']) ? $_POST['media_id'] : "";



  $sql = "select categories_id, categories_image from " . TABLE_CATEGORIES . " where categories_id = '" . $categories_id . "'";

  $count_query = mysqli_query($db, $sql);
  if ($categorie = mysqli_fetch_array($count_query, MYSQLI_BOTH)) {
    $exists = 1;
  } else {
    $exists = 0;
  }


  // Variablen nur �berschreiben, wenn sie als Parameter �bergeben worden sind
  $categories_image    = isset($_POST['categories_image']) ? $_POST['categories_image'] : "";

  $sql_data_array = array(
    'categories_id'       => $categories_id,
    'categories_image'    => $categories_image,
    'last_modified'       => 'now()'
  );


  if ($exists == 0)        // Neuanlage

  {

    $insert_sql_data = array('date_added' => 'now()');

    $sql_data_array = array_merge($sql_data_array, $insert_sql_data);
    database_insert(TABLE_CATEGORIES, $sql_data_array);

    $categories_id = mysqli_insert_id($db);
  } elseif ($exists == 1)    //Aktualisieren

  {
    database_insert(TABLE_CATEGORIES, $sql_data_array, 'update', 'categories_id = \'' . $categories_id . '\'');
  }


  // Bild in MEDIA anlegen bzw. editieren

  $media_query = mysqli_query($db, "select * from " . TABLE_MEDIA . " where id = '" . $media_id . "'");

  if ($media = mysqli_fetch_array($media_query, MYSQLI_BOTH)) {
    $exists = 1;
  } else {
    $exists = 0;
  }

  // uebergebene Daten einsetzen
  $file                    = isset($_POST['file']) ? $_POST['file'] : "";
  $type                    = isset($_POST['type']) ? $_POST['type'] : "";
  $class                   = isset($_POST['class']) ? $_POST['class'] : "";
  $download_status         = isset($_POST['download_status']) ? $_POST['download_status'] : "";
  $status                  = isset($_POST['status']) ? $_POST['status'] : "";
  $owner                   = isset($_POST['owner']) ? $_POST['owner'] : "";
  $date_added              = isset($_POST['date_added']) ? $_POST['date_added'] : "";
  $last_modified           = isset($_POST['last_modified']) ? $_POST['last_modified'] : "";
  $max_dl_count            = isset($_POST['max_dl_count']) ? $_POST['max_dl_count'] : "";
  $max_dl_days             = isset($_POST['max_dl_days']) ? $_POST['max_dl_days'] : "";
  $total_downloads         = isset($_POST['total_downloads']) ? $_POST['total_downloads'] : "";


  $sql_data_array = array(
    'file'                 => $file,
    'type'                 => $type,
    'class'                => $class,
    'download_status'      => $download_status,
    'status'               => $status,
    'owner'                => $owner,
    'last_modified'        => 'now()',
    'max_dl_count'         => $max_dl_count,
    'max_dl_days'          => $max_dl_days,
    'total_downloads'      => $total_downloads
  );


  if ($exists == 0)        // Neuanlage
  {

    $insert_sql_data = array(
      'id'                 => $media_id,
      'date_added'         => 'now()'
    );

    $sql_data_array = array_merge($sql_data_array, $insert_sql_data);
    database_insert(TABLE_MEDIA, $sql_data_array);
    $media_id = mysqli_insert_id($db);
  } elseif ($exists == 1)    //Aktualisieren
  {
    database_insert(TABLE_MEDIA, $sql_data_array, 'update', 'id = \'' . $media_id . '\'');
  }



  // Bild in MEDIA-Gallery anlegen bzw. editieren

  $media_gallery_query = mysqli_query($db, "select * from " . TABLE_MEDIA_TO_MEDIA_GALLERY . " where m_id = '" . $media_id . "'");

  if ($media_gallery = mysqli_fetch_array($media_gallery_query, MYSQLI_BOTH)) {
    $exists = 1;
  } else {
    $exists = 0;
  }

  // uebergebene Daten einsetzen
  $ml_id                    = isset($_POST['ml_id']) ? $_POST['ml_id'] : "";
  $mg_id                    = isset($_POST['mg_id']) ? $_POST['mg_id'] : "";


  $sql_data_array = array(/* 'ml_id'                => $ml_id,  CHRASHT MIT DEM AUTO INCREMENT*/
    'mg_id'                => $mg_id
  );



  if ($exists == 0)        // Neuanlage
  {

    $insert_sql_data = array('m_id'               => $media_id);

    $sql_data_array = array_merge($sql_data_array, $insert_sql_data);
    database_insert(TABLE_MEDIA_TO_MEDIA_GALLERY, $sql_data_array);
    $media_gallery_id = mysqli_insert_id($db);
  } elseif ($exists == 1)    //Aktualisieren
  {
    database_insert(TABLE_MEDIA_TO_MEDIA_GALLERY, $sql_data_array, 'update', 'm_id = \'' . $media_id . '\'');
  }




  $schema = '<STATUS_DATA>' . "\n" .
    '<EXISTS>' . $exists . '</EXISTS>' . "\n" .
    '<CATEGORIES_ID>' . $categories_id . '</CATEGORIES_ID>' . "\n" .
    '<CATEGORIES_IMAGE>' . $categories_image . '</CATEGORIES_IMAGE>' . "\n" .
    '<MEDIA_ID>' . $media_id . '</MEDIA_ID>' . "\n" .
    '<MEDIA_GALLERY_ID>' . $media_gallery_id . '</MEDIA_GALLERY_ID>' . "\n" .
    '</STATUS_DATA>' . "\n";


  $footer = '</STATUS>' . "\n";

  //Ergebnis als XML ausgeben
  create_xml($header . $schema . $footer);
}



function save_languages()
{
  /**
   * @var mysqli $db
   */
  global $db;

  $header = '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
    '<STATUS>' . "\n";


  $languages_id = isset($_POST['languages_id']) ? $_POST['languages_id'] : "";

  $sql = "select languages_id, language_status, name, content_language, code, allow_edit, image, sort_order, language_charset, default_currency, font, font_size, font_position, setlocale from " . TABLE_LANGUAGES .
    " where languages_id = '" . $languages_id . "'";

  $count_query = mysqli_query($db, $sql);
  if ($languages = mysqli_fetch_array($count_query, MYSQLI_BOTH)) {
    $exists = 1;
  } else {
    $exists = 0;
  }


  // Variablen nur �berschreiben, wenn sie als Parameter �bergeben worden sind
  $language_status   = isset($_POST['language_status']) ? $_POST['language_status'] : "";
  $name              = isset($_POST['name']) ? $_POST['name'] : "";
  $content_language  = isset($_POST['content_language']) ? $_POST['content_language'] : "";
  $code              = isset($_POST['code']) ? $_POST['code'] : "";
  $allow_edit        = isset($_POST['allow_edit']) ? $_POST['allow_edit'] : "";
  $image             = isset($_POST['image']) ? $_POST['image'] : "";
  $sort_order        = isset($_POST['sort_order']) ? $_POST['sort_order'] : "";
  $language_charset  = isset($_POST['language_charset']) ? $_POST['language_charset'] : "";
  $default_currency  = isset($_POST['default_currency']) ? $_POST['default_currency'] : "";
  $font              = isset($_POST['font']) ? $_POST['font'] : "";
  $font_size         = isset($_POST['font_size']) ? $_POST['font_size'] : "";
  $font_position     = isset($_POST['font_position']) ? $_POST['font_position'] : "";
  $setlocale         = isset($_POST['setlocale']) ? $_POST['setlocale'] : "";


  $sql_data_array = array(
    'language_status'   => $language_status,
    'name'              => $name,
    'content_language'  => $content_language,
    'code'              => $code,
    'allow_edit'        => $allow_edit,
    'image'             => $image,
    'sort_order'        => $sort_order,
    'language_charset'  => $language_charset,
    'default_currency'  => $default_currency,
    'font'              => $font,
    'font_size'         => $font_size,
    'font_position'     => $font_position,
    'setlocale'         => $setlocale
  );


  if ($exists == 0)        // Neuanlage
  {

    $insert_sql_data = array('languages_id'      => $languages_id);

    $sql_data_array = array_merge($sql_data_array, $insert_sql_data);
    database_insert(TABLE_LANGUAGES, $sql_data_array);

    if (isset($_POST['languages_id']) && ($languages_id != '')) {
      $languages_id = isset($_POST['languages_id']) ? $_POST['languages_id'] : "";
    } else {
      $languages_id = mysqli_insert_id($db);
    }
  } elseif ($exists == 1)    //Aktualisieren
  {
    database_insert(TABLE_LANGUAGES, $sql_data_array, 'update', 'languages_id = \'' . $languages_id . '\'');
  }


  $schema = '<STATUS_DATA>' . "\n" .
    '<EXISTS>' . $exists . '</EXISTS>' . "\n" .
    '<LANGUAGES_ID>' . $languages_id . '</LANGUAGES_ID>' . "\n" .
    '<MESSAGE>' . 'Keine Meldung vorhanden' . '</MESSAGE>' . "\n" .
    '</STATUS_DATA>' . "\n";


  $footer = '</STATUS>' . "\n";

  //Ergebnis als XML ausgeben
  create_xml($header . $schema . $footer);
}


function update_order()
{
  /**
   * @var mysqli $db
   */
  global $db;

  $header = '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
    '<STATUS>' . "\n";

  $order_id = isset($_POST['order_id']) ? $_POST['order_id'] : "";
  $status = isset($_POST['status']) ? $_POST['status'] : "";
  $statustext = isset($_POST['statustext']) ? $_POST['statustext'] : "";
  $comments = isset($_POST['comments']) ? $_POST['comments'] : "";
  $notify = isset($_POST['notify']) ? $_POST['notify'] : "";                         //  on=E-Mail an kunden senden, off=keine E-Mail senden
  $notify_comment = isset($_POST['notify_comment']) ? $_POST['notify_comment'] : "";         //  on=Kommentar in der E-Mail senden, off=Kommentar  n i c h t  als E-Mail senden


  $btime = date('Y-m-d H:i:s', time());

  $data =  json_encode(array(
    "function" => "setOrderStatus",
    "paras" => array(
      "user" => "finosScript",
      "pass" => "vx#Ne/x5u!Q5YS225H$",
      "order_id" => $order_id,
      "new_status_id" => $status,
      "comments" => $comments,
      "sendmail" => 1,
      "indivFieldList" => [],
      "sendcomments" => 1,
      "shippings" => []
    )
  ));

  $response =  CallAPI('POST', 'https://shop.aquarus.net/index.php?page=xt_api', $data);

  $schema = '<STATUS_DATA>' . "\n" .
    '<ORDER_ID>' . $order_id . '</ORDER_ID>' . "\n" .
    '<ORDER_STATUS>' . $status . '</ORDER_STATUS>' . "\n" .
    '<CODE>' . '0' . '</CODE>' . "\n" .
    '<COMMENTS>' . $comments . '</COMMENTS>' . "\n" .
    '<MESSAGE>' . 'Der Bestellstatus wurde erfolgreich fortgeschrieben, keine E-Mail gesendet.' . '</MESSAGE>' . "\n" .
    '</STATUS_DATA>' . "\n";
  $footer = '</STATUS>' . "\n";
  //Ergebnis als XML ausgeben
  create_xml($header . $schema . $footer);
}

// Call xtcommerce api to update order status
function CallAPI($method, $url, $data = false)
{
  $curl = curl_init();

  switch ($method) {
    case "POST":
      curl_setopt($curl, CURLOPT_POST, 1);

      if ($data)
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
      break;
    case "PUT":
      curl_setopt($curl, CURLOPT_PUT, 1);
      break;
    default:
      if ($data)
        $url = sprintf("%s?%s", $url, json_encode($data));
  }

  // Optional Authentication:
  // curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
  //curl_setopt($curl, CURLOPT_USERPWD, "finosScript:Superkarl123!");

  // -----------------------
  // curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
  // curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
  // ---------

  curl_setopt($curl, CURLOPT_URL, $url);
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

  $result = curl_exec($curl);

  printf(curl_error($curl));
  curl_close($curl);

  return $result;
}


function save_seo_url()
{
  /**
   * @var mysqli $db
   */
  global $db;

  $header = '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
    '<STATUS>' . "\n";

  $products_id = isset($_POST['link_id']) ? $_POST['link_id'] : null;
  $linkId = isset($_POST['link_type']) ? $_POST['link_type'] : null;

  switch ($linkId) {
    case 1:
      UpdateArticleFromPost($products_id);
      break;
    case 2:
      break;
    case 4: // Manufacture
      break;
  }

  $schema = "<STATUS_DATA>" . "\n" .
    "<EXISTS>" . 1 . "</EXISTS>" . "\n" .
    "<URL_TEXT>" . (isset($_POST['url_text']) ? $_POST['url_text'] : "") . "</URL_TEXT>" . "\n" .
    "<MESSAGE>" . "Keine Meldung vorhanden" . "</MESSAGE>" . "\n" .
    "</STATUS_DATA>" . "\n";

  $footer = '</STATUS>' . "\n";
  //Ergebnis als XML ausgeben
  create_xml($header . $schema . $footer);
}




/* ********************************************* LESEN *****************************************
                              XML f�r die R�ckgabe an Finos generieren
********************************************************************************************* */





function read_categories()
{
  /**
   * @var mysqli $db
   */
  global $db;

  $header = '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
    '<CATEGORIES>' . "\n";


  $sql = mysqli_query($db, "select categories_id, external_id, permission_id, categories_owner, categories_image, parent_id, categories_status, categories_template, listing_template, sort_order, products_sorting, products_sorting2, top_category, date_added, last_modified, google_product_cat " .
    " from " . TABLE_CATEGORIES . " order by parent_id, sort_order, categories_id");

  while ($cat = mysqli_fetch_array($sql, MYSQLI_BOTH)) {
    $schema  = '<CATEGORIES_DATA>' . "\n" .
      '<ID>' . $cat['categories_id'] . '</ID>' . "\n" .
      '<EXTERNAL_ID>' . htmlspecialchars($cat['external_id']) . '</EXTERNAL_ID>' . "\n" .
      '<PERMISSION_ID>' . $cat['permission_id'] . '</PERMISSION_ID>' . "\n" .
      '<CATEGORIES_OWNER>' . $cat['categories_owner'] . '</CATEGORIES_OWNER>' . "\n" .
      '<IMAGE>' . htmlspecialchars($cat['categories_image']) . '</IMAGE>' . "\n" .
      '<IMAGE_URL_ICON>' . _SRV_WEB_IMAGES . 'category/icon/' . htmlspecialchars($cat['categories_image']) . '</IMAGE_URL_ICON>' . "\n" .
      '<IMAGE_URL_INFO>' . _SRV_WEB_IMAGES . 'category/info/' . htmlspecialchars($cat['categories_image']) . '</IMAGE_URL_INFO>' . "\n" .
      '<IMAGE_URL_POPUP>' . _SRV_WEB_IMAGES . 'category/popup/' . htmlspecialchars($cat['categories_image']) . '</IMAGE_URL_POPUP>' . "\n" .
      '<IMAGE_URL_THUMB>' . _SRV_WEB_IMAGES . 'category/thumb/' . htmlspecialchars($cat['categories_image']) . '</IMAGE_URL_THUMB>' . "\n" .
      '<PARENT_ID>' . $cat['parent_id'] . '</PARENT_ID>' . "\n" .
      '<CATEGORIES_STATUS>' . htmlspecialchars($cat['categories_status']) . '</CATEGORIES_STATUS>' . "\n" .
      '<CATEGORIES_TEMPLATE>' . htmlspecialchars($cat['categories_template']) . '</CATEGORIES_TEMPLATE>' . "\n" .
      '<LISTING_TEMPLATE>' . htmlspecialchars($cat['listing_template']) . '</LISTING_TEMPLATE>' . "\n" .
      '<SORT_ORDER>' . htmlspecialchars($cat['sort_order']) . '</SORT_ORDER>' . "\n" .
      '<PRODUCTS_SORTING>' . htmlspecialchars($cat['products_sorting']) . '</PRODUCTS_SORTING>' . "\n" .
      '<PRODUCTS_SORTING2>' . htmlspecialchars($cat['products_sorting2']) . '</PRODUCTS_SORTING2>' . "\n" .
      '<TOP_CATEGORY>' . htmlspecialchars($cat['top_category']) . '</TOP_CATEGORY>' . "\n" .
      '<DATE_ADDED>' . htmlspecialchars($cat['date_added']) . '</DATE_ADDED>' . "\n" .
      '<LAST_MODIFIED>' . htmlspecialchars($cat['last_modified']) . '</LAST_MODIFIED>' . "\n" .
      '<GOOGLE_PRODUCT_CAT>' . htmlspecialchars($cat['google_product_cat']) . '</GOOGLE_PRODUCT_CAT>' . "\n";


    $cmd_categories = "SELECT categories_id, language_code, categories_name, categories_heading_title, categories_description, categories_description_bottom, " . TABLE_LANGUAGES . ".languages_id as lang_id, " . TABLE_LANGUAGES . ".name as lang_name from " . TABLE_CATEGORIES_DESCRIPTION . "," . TABLE_LANGUAGES . " where " . TABLE_CATEGORIES_DESCRIPTION . ".categories_id = " . $cat['categories_id'] . " and " . TABLE_LANGUAGES . ".code = " . TABLE_CATEGORIES_DESCRIPTION . " . language_code";
    $categories_query = mysqli_query($db, $cmd_categories);

    while ($cat_details = mysqli_fetch_array($categories_query, MYSQLI_BOTH)) {

      $cmd_seo = "select * from " . TABLE_SEO_URL . " where link_id = " . "'" . $cat['categories_id'] . "' and link_type = '2' and language_code = '" . $cat_details["language_code"] . "'";
      $seo_query = mysqli_query($db, $cmd_seo);
      $seo = mysqli_fetch_array($seo_query, MYSQLI_BOTH);

      $schema .= "<CATEGORIES_DESCRIPTION ID='" . $cat_details["lang_id"] . "' CODE='" . $cat_details["language_code"] . "' NAME='" . $cat_details["lang_name"] . "'>\n";
      $schema .= "<NAME>" . htmlspecialchars($cat_details["categories_name"], ENT_COMPAT, 'ISO-8859-1', true) . "</NAME>" . "\n";
      $schema .= "<HEADING_TITLE>" . htmlspecialchars($cat_details["categories_heading_title"], ENT_COMPAT, 'ISO-8859-1', true) . "</HEADING_TITLE>" . "\n";
      $schema .= "<DESCRIPTION>" . htmlspecialchars($cat_details["categories_description"], ENT_COMPAT, 'ISO-8859-1', true) . "</DESCRIPTION>" . "\n";
      $schema .= "<DESCRIPTION_BOTTOM>" . htmlspecialchars($cat_details["categories_description_bottom"], ENT_COMPAT, 'ISO-8859-1', true) . "</DESCRIPTION_BOTTOM>" . "\n";
      $schema .= "<META_TITLE>" . htmlspecialchars($seo["meta_title"], ENT_COMPAT, 'ISO-8859-1', true) . "</META_TITLE>" . "\n";
      $schema .= "<META_DESCRIPTION>" . htmlspecialchars($seo["meta_description"], ENT_COMPAT, 'ISO-8859-1', true) . "</META_DESCRIPTION>" . "\n";
      $schema .= "<META_KEYWORDS>" . htmlspecialchars($seo["meta_keywords"], ENT_COMPAT, 'ISO-8859-1', true) . "</META_KEYWORDS>" . "\n";
      $schema .= "</CATEGORIES_DESCRIPTION>\n";
    }


    // Produkte dieser Kategorie auflisten
    $prod2cat_query = mysqli_query($db, "select categories_id, products_id from " . TABLE_PRODUCTS_TO_CATEGORIES .
      " where categories_id = '" . $cat['categories_id'] . "'");

    while ($prod2cat = mysqli_fetch_array($prod2cat_query, MYSQLI_BOTH)) {
      $schema .= "<PRODUCTS ID='" . $prod2cat["products_id"] . "'></PRODUCTS>" . "\n";
    }



    // Bild (Media-Werte auslesen)

    $cmd_media = "select * from " . TABLE_MEDIA . " where class = 'category' and type = 'images' and file = '" . $cat['categories_image'] . "'";
    $media_query = mysqli_query($db, $cmd_media);
    while ($media = mysqli_fetch_array($media_query, MYSQLI_BOTH)) {
      $bildname = $media['file'];
      if ($bildname != '') {
        $schema .= "<MEDIA_ID>" . htmlspecialchars($media['id']) . "</MEDIA_ID>" . "\n";
      }
    }



    // Bild (Original auslesen)

    $bildname = $cat['categories_image'];
    $bild = '';
    $pfad = "../" . _SRV_WEB_IMAGES . "category/" . _DIR_INFO;

    if ($bildname != '' && file_exists($pfad . $bildname)) {
      $bild = @implode("", @file($pfad . $bildname));
    }

    $schema .= '<CATEGORIES_IMAGE_FILENAME>' . htmlspecialchars($cat['categories_image'], ENT_COMPAT, 'ISO-8859-1', true) . '</CATEGORIES_IMAGE_FILENAME>' . "\n" .
      '<CATEGORIES_IMAGE_INFO>' . base64_encode($bild) . "</CATEGORIES_IMAGE_INFO>" . "\n";


    $schema .= '</CATEGORIES_DATA>' . "\n";
  }

  $footer = '</CATEGORIES>' . "\n";

  //Ergebnis als XML ausgeben
  create_xml($header . $schema . $footer);
}



function read_customers()
{
  /**
   * @var mysqli $db
   */
  global $db;

  $header = '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
    '<CUSTOMERS>' . "\n";

  $id = filter_input(INPUT_GET, 'customers_id');

  if (isset($id)) {
    $sql = mysqli_query($db, "select customers_id, external_id, customers_cid, customers_vat_id, customers_vat_id_status, customers_status, customers_email_address, customers_password, account_type, password_request_key, payment_unallowed, shipping_unallowed, date_added, last_modified, shop_id, customers_default_currency, customers_default_language, campaign_id  " .
      " from " . TABLE_CUSTOMERS . " where customers_id = " . $id . " order by customers_id");
  } else {
    $sql = mysqli_query($db, "select customers_id, external_id, customers_cid, customers_vat_id, customers_vat_id_status, customers_status, customers_email_address, customers_password, account_type, password_request_key, payment_unallowed, shipping_unallowed, date_added, last_modified, shop_id, customers_default_currency, customers_default_language, campaign_id  " .
      " from " . TABLE_CUSTOMERS . " order by customers_id");
  }


  while ($cust = mysqli_fetch_array($sql, MYSQLI_BOTH)) {
    $schema  = '<CUSTOMERS_DATA>' . "\n" .
      '<ID>' . htmlspecialchars($cust['customers_id']) . '</ID>' . "\n" .
      '<EXTERNAL_ID>' . htmlspecialchars($cust['external_id']) . '</EXTERNAL_ID>' . "\n" .
      '<CUSTOMERS_CID>' . htmlspecialchars($cust['customers_cid'], ENT_COMPAT, 'ISO-8859-1', true) . '</CUSTOMERS_CID>' . "\n" .
      '<CUSTOMERS_VAT_ID>' . htmlspecialchars($cust['customers_vat_id'], ENT_COMPAT, 'ISO-8859-1', true) . '</CUSTOMERS_VAT_ID>' . "\n" .
      '<CUSTOMERS_VAT_ID_STATUS>' . htmlspecialchars($cust['customers_vat_id_status'], ENT_COMPAT, 'ISO-8859-1', true) . '</CUSTOMERS_VAT_ID_STATUS>' . "\n" .
      '<CUSTOMERS_STATUS>' . htmlspecialchars($cust['customers_status'], ENT_COMPAT, 'ISO-8859-1', true) . '</CUSTOMERS_STATUS>' . "\n" .
      '<CUSTOMERS_EMAIL_ADDRESS>' . htmlspecialchars($cust['customers_email_address'], ENT_COMPAT, 'ISO-8859-1', true) . '</CUSTOMERS_EMAIL_ADDRESS>' . "\n" .
      '<CUSTOMERS_PASSWORD>' . htmlspecialchars($cust['customers_password'], ENT_COMPAT, 'ISO-8859-1', true) . '</CUSTOMERS_PASSWORD>' . "\n" .
      '<ACCOUNT_TYPE>' . htmlspecialchars($cust['account_type'], ENT_COMPAT, 'ISO-8859-1', true) . '</ACCOUNT_TYPE>' . "\n" .
      '<PASSWORD_REQUEST_KEY>' . htmlspecialchars($cust['password_request_key'], ENT_COMPAT, 'ISO-8859-1', true) . '</PASSWORD_REQUEST_KEY>' . "\n" .
      '<PAYMENT_UNALLOWED>' . htmlspecialchars($cust['payment_unallowed'], ENT_COMPAT, 'ISO-8859-1', true) . '</PAYMENT_UNALLOWED>' . "\n" .
      '<SHIPPING_UNALLOWED>' . htmlspecialchars($cust['shipping_unallowed'], ENT_COMPAT, 'ISO-8859-1', true) . '</SHIPPING_UNALLOWED>' . "\n" .
      '<DATE_ADDED>' . htmlspecialchars($cust['date_added']) . '</DATE_ADDED>' . "\n" .
      '<LAST_MODIFIED>' . htmlspecialchars($cust['last_modified']) . '</LAST_MODIFIED>' . "\n" .
      '<SHOP_ID>' . htmlspecialchars($cust['shop_id']) . '</SHOP_ID>' . "\n" .
      '<CUSTOMERS_DEFAULT_CURRENCY>' . htmlspecialchars($cust['customers_default_currency'], ENT_COMPAT, 'ISO-8859-1', true) . '</CUSTOMERS_DEFAULT_CURRENCY>' . "\n" .
      '<CUSTOMERS_DEFAULT_LANGUAGE>' . htmlspecialchars($cust['customers_default_language'], ENT_COMPAT, 'ISO-8859-1', true) . '</CUSTOMERS_DEFAULT_LANGUAGE>' . "\n" .
      '<CAMPAIGN_ID>' . htmlspecialchars($cust['campaign_id']) . '</CAMPAIGN_ID>' . "\n";


    $detail_query = mysqli_query($db, "SELECT address_book_id, external_id, customers_id, customers_gender, customers_dob, customers_phone, customers_fax, customers_company, customers_company_2, customers_company_3, customers_firstname, customers_lastname, customers_street_address, customers_suburb, customers_postcode, customers_city, customers_country_code, address_class, date_added, last_modified from " . TABLE_CUSTOMERS_ADDRESSES .
      " where customers_id = " . $cust['customers_id']);

    while ($address = mysqli_fetch_array($detail_query, MYSQLI_BOTH)) {
      $schema .= "<CUSTOMERS_ADDRESS>\n";
      $schema .= "<ADDRESS_BOOK_ID>" . htmlspecialchars($address["address_book_id"]) . "</ADDRESS_BOOK_ID>" . "\n";
      $schema .= "<EXTERNAL_ID>" . htmlspecialchars($address["external_id"]) . "</EXTERNAL_ID>" . "\n";
      $schema .= "<CUSTOMERS_ID>" . htmlspecialchars($address["customers_id"]) . "</CUSTOMERS_ID>" . "\n";
      $schema .= "<CUSTOMERS_GENDER>" . htmlspecialchars($address["customers_gender"]) . "</CUSTOMERS_GENDER>" . "\n";
      $schema .= "<CUSTOMERS_DOB>" . htmlspecialchars($address["customers_dob"], ENT_COMPAT, 'ISO-8859-1', true) . "</CUSTOMERS_DOB>" . "\n";
      $schema .= "<CUSTOMERS_PHONE>" . htmlspecialchars($address["customers_phone"], ENT_COMPAT, 'ISO-8859-1', true) . "</CUSTOMERS_PHONE>" . "\n";
      $schema .= "<CUSTOMERS_FAX>" . htmlspecialchars($address["customers_fax"], ENT_COMPAT, 'ISO-8859-1', true) . "</CUSTOMERS_FAX>" . "\n";
      $schema .= "<CUSTOMERS_COMPANY>" . htmlspecialchars($address["customers_company"], ENT_COMPAT, 'ISO-8859-1', true) . "</CUSTOMERS_COMPANY>" . "\n";
      $schema .= "<CUSTOMERS_COMPANY_2>" . htmlspecialchars($address["customers_company_2"], ENT_COMPAT, 'ISO-8859-1', true) . "</CUSTOMERS_COMPANY_2>" . "\n";
      $schema .= "<CUSTOMERS_COMPANY_3>" . htmlspecialchars($address["customers_company_3"], ENT_COMPAT, 'ISO-8859-1', true) . "</CUSTOMERS_COMPANY_3>" . "\n";
      $schema .= "<CUSTOMERS_FIRSTNAME>" . htmlspecialchars($address["customers_firstname"], ENT_COMPAT, 'ISO-8859-1', true) . "</CUSTOMERS_FIRSTNAME>" . "\n";
      $schema .= "<CUSTOMERS_LASTNAME>" . htmlspecialchars($address["customers_lastname"], ENT_COMPAT, 'ISO-8859-1', true) . "</CUSTOMERS_LASTNAME>" . "\n";
      $schema .= "<CUSTOMERS_STREET_ADDRESS>" . htmlspecialchars($address["customers_street_address"], ENT_COMPAT, 'ISO-8859-1', true) . "</CUSTOMERS_STREET_ADDRESS>" . "\n";
      $schema .= "<CUSTOMERS_SUBURB>" . htmlspecialchars($address["customers_suburb"], ENT_COMPAT, 'ISO-8859-1', true) . "</CUSTOMERS_SUBURB>" . "\n";
      $schema .= "<CUSTOMERS_POSTCODE>" . htmlspecialchars($address["customers_postcode"], ENT_COMPAT, 'ISO-8859-1', true) . "</CUSTOMERS_POSTCODE>" . "\n";
      $schema .= "<CUSTOMERS_CITY>" . htmlspecialchars($address["customers_city"], ENT_COMPAT, 'ISO-8859-1', true) . "</CUSTOMERS_CITY>" . "\n";
      $schema .= "<CUSTOMERS_COUNTRY_CODE>" . htmlspecialchars($address["customers_country_code"], ENT_COMPAT, 'ISO-8859-1', true) . "</CUSTOMERS_COUNTRY_CODE>" . "\n";
      $schema .= "<ADDRESS_CLASS>" . htmlspecialchars($address["address_class"], ENT_COMPAT, 'ISO-8859-1', true) . "</ADDRESS_CLASS>" . "\n";
      $schema .= "<DATE_ADDED>" . htmlspecialchars($address["date_added"]) . "</DATE_ADDED>" . "\n";
      $schema .= "<LAST_MODIFIED>" . htmlspecialchars($address["last_modified"]) . "</LAST_MODIFIED>" . "\n";
      $schema .= "</CUSTOMERS_ADDRESS>\n";
    }

    $schema .= '</CUSTOMERS_DATA>' . "\n";
  }

  $footer = '</CUSTOMERS>' . "\n";

  //Ergebnis als XML ausgeben
  create_xml($header . $schema . $footer);
}



function read_customers_status()
{
  /**
   * @var mysqli $db
   */
  global $db;

  $header = '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
    '<CUSTOMERS_STATUS>' . "\n";

  $cmd = "select * from " . TABLE_CUSTOMERS_STATUS_DESCRIPTION . " where language_code = '" . LANG_CODE . "' order by customers_status_id";
  $customers_status_query = mysqli_query($db, $cmd);

  while ($customers_status = mysqli_fetch_array($customers_status_query)) {
    $schema = '<CUSTOMERS_STATUS_DATA>' . "\n" .
      '<ID>' . htmlspecialchars($customers_status['customers_status_id'], ENT_COMPAT, 'ISO-8859-1', true) . '</ID>' . "\n" .
      '<NAME>' . htmlspecialchars($customers_status['customers_status_name'], ENT_COMPAT, 'ISO-8859-1', true) . '</NAME>' . "\n" .
      '<LANGUAGE>' . htmlspecialchars($customers_status['language_code'], ENT_COMPAT, 'ISO-8859-1', true) . '</LANGUAGE>' . "\n";


    $cmd_customers_status_options_query = mysqli_query($db, "SELECT * from " . TABLE_CUSTOMERS_STATUS . " where customers_status_id = " . $customers_status['customers_status_id']);

    while ($customers_status_options = mysqli_fetch_array($cmd_customers_status_options_query)) {
      $schema .= "<OPTIONS>\n";
      $schema .= "<MIN_ORDER>" . htmlspecialchars($customers_status_options["customers_status_min_order"]) . "</MIN_ORDER>" . "\n";
      $schema .= "<MAX_ORDER>" . htmlspecialchars($customers_status_options["customers_status_max_order"]) . "</MAX_ORDER>" . "\n";
      $schema .= "<SHOW_PRICE>" . htmlspecialchars($customers_status_options["customers_status_show_price"]) . "</SHOW_PRICE>" . "\n";
      $schema .= "<SHOW_PRICE_TAX>" . htmlspecialchars($customers_status_options["customers_status_show_price_tax"]) . "</SHOW_PRICE_TAX>" . "\n";
      $schema .= "<ADD_TAX_OT>" . htmlspecialchars($customers_status_options["customers_status_add_tax_ot"]) . "</ADD_TAX_OT>" . "\n";
      $schema .= "<GRADUATED_PRICES>" . htmlspecialchars($customers_status_options["customers_status_graduated_prices"]) . "</GRADUATED_PRICES>" . "\n";
      $schema .= "<FSK18>" . htmlspecialchars($customers_status_options["customers_fsk18"]) . "</FSK18>" . "\n";
      $schema .= "<FSK18_DISPLAY>" . htmlspecialchars($customers_status_options["customers_fsk18_display"]) . "</FSK18_DISPLAY>" . "\n";
      $schema .= "<MASTER>" . htmlspecialchars($customers_status_options["customers_status_master"]) . "</MASTER>" . "\n";
      $schema .= "<TEMPLATE>" . htmlspecialchars($customers_status_options["customers_status_template"]) . "</TEMPLATE>" . "\n";
      $schema .= "</OPTIONS>\n";
    }


    $schema .= '</CUSTOMERS_STATUS_DATA>' . "\n";
  }

  $footer = '</CUSTOMERS_STATUS>' . "\n";

  //Ergebnis als XML ausgeben
  create_xml($header . $schema . $footer);
}



function read_currencies()
{
  /**
   * @var mysqli $db
   */
  global $db;

  $header = '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
    '<CURRENCIES>' . "\n";

  $cmd = "select currencies_id, title, code, prefix, suffix, dec_point, thousands_sep, decimals from " . TABLE_CURRENCIES . " order by currencies_id";
  $currencies_query = mysqli_query($db, $cmd);

  while ($currency = mysqli_fetch_array($currencies_query, MYSQLI_BOTH)) {

    $schema = '<CURRENCIES_DATA>' . "\n" .
      '<ID>' . htmlspecialchars($currency['currencies_id']) . '</ID>' . "\n" .
      '<TITLE>' . htmlspecialchars($currency['title']) . '</TITLE>' . "\n" .
      '<PREFIX>' . htmlspecialchars($currency['prefix']) . '</PREFIX>' . "\n" .
      '<SUFFIX>' . htmlspecialchars($currency['suffix']) . '</SUFFIX>' . "\n" .
      '<DEC_POINT>' . htmlspecialchars($currency['dec_point']) . '</DEC_POINT>' . "\n" .
      '<THOUSANDS_SEP>' . htmlspecialchars($currency['thousands_sep']) . '</THOUSANDS_SEP>' . "\n" .
      '<DECIMALS>' . htmlspecialchars($currency['decimals']) . '</DECIMALS>' . "\n" .
      '<CODE>' . htmlspecialchars($currency['code']) . '</CODE>' . "\n";

    $schema .= '</CURRENCIES_DATA>' . "\n";
  }

  $footer = '</CURRENCIES>' . "\n";

  //Ergebnis als XML ausgeben
  create_xml($header . $schema . $footer);
}



function read_products_specials()
{
  /**
   * @var mysqli $db
   */
  global $db;

  $header = '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
    '<SPECIALS>' . "\n";


  $sql = "select * from " . TABLE_PRODUCTS_PRICE_SPECIAL;
  $specials_query = mysqli_query($db, $sql);

  while ($specials = mysqli_fetch_array($specials_query, MYSQLI_BOTH)) {

    $schema = '<SPECIALS_DATA>' . "\n" .
      '<ID>' . $specials['id'] . '</ID>' . "\n" .
      '<PRODUCTS_ID>' . $specials['products_id'] . '</PRODUCTS_ID>' . "\n" .
      '<SPECIALS_PRICE>' . htmlspecialchars($specials['specials_price']) . '</SPECIALS_PRICE>' . "\n" .
      '<DATE_AVAILABLE>' . htmlspecialchars($specials['date_available']) . '</DATE_AVAILABLE>' . "\n" .
      '<DATE_EXPIRED>' . htmlspecialchars($specials['date_expired']) . '</DATE_EXPIRED>' . "\n" .
      '<STATUS>' . htmlspecialchars($specials['status']) . '</STATUS>' . "\n" .
      '</SPECIALS_DATA>' . "\n";
  }

  $footer = '</SPECIALS>' . "\n";

  //Ergebnis als XML ausgeben
  create_xml($header . $schema . $footer);
}



function read_orders()
{
  /**
   * @var mysqli $db
   */
  global $db;

  $order_from = filter_input(INPUT_GET, 'order_from');
  $order_to = filter_input(INPUT_GET, 'order_to');
  $order_status = filter_input(INPUT_GET, 'order_status');


  $header = '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
    '<ORDER>' . "\n";

  $cmd = "select * from " . TABLE_ORDERS . " where orders_id >= '" . $order_from . "'";

  if (!isset($order_status) && !isset($order_from)) {
    $order_status = 16;
    $cmd .= "and orders_status = " . $order_status;
  }

  if ($order_status != '') {
    $cmd .= " and orders_status IN (" . $order_status . ")";  //Aufruf: '1','3'
  }

  //$cmd .= " order by date_purchased";
  $orders_query = mysqli_query($db, $cmd);

  while ($orders = mysqli_fetch_array($orders_query, MYSQLI_BOTH)) {


    //Kundendaten zur Bestellung lesen: Geburtsdatum und Geschlecht auslesen
    $cmd_customers = "select * from " . TABLE_CUSTOMERS_ADDRESSES . " where customers_id = " . $orders['customers_id'];
    $customers_query = mysqli_query($db, $cmd_customers);

    if (($customers_query) && ($customers = mysqli_fetch_array($customers_query, MYSQLI_BOTH))) {
      $cust_dob = $customers['customers_dob'];
      $cust_gender = $customers['customers_gender'];
      $cust_name = $customers['customers_lastname'] . ", " . $customers['customers_firstname'];
      $cust_telephone = $customers['customers_phone'];
    } else {
      $cust_dob = '';
      $cust_gender = '';
      $cust_name = '';
      $cust_telephone = '';
    }


    if ($orders['billing_gender'] == '')         $orders['billing_company'] = $orders['delivery_gender'];
    if ($orders['billing_company'] == '')        $orders['billing_company'] = $orders['delivery_company'];
    if ($orders['billing_lastname'] == '')       $orders['billing_name'] = $orders['delivery_lastname'];
    if ($orders['billing_street_address'] == '') $orders['billing_street_address'] = $orders['delivery_street_address'];
    if ($orders['billing_postcode'] == '')       $orders['billing_postcode'] = $orders['delivery_postcode'];
    if ($orders['billing_city'] == '')           $orders['billing_city'] = $orders['delivery_city'];
    if ($orders['billing_suburb'] == '')         $orders['billing_suburb'] = $orders['delivery_suburb'];
    if ($orders['billing_country'] == '')        $orders['billing_country'] = $orders['delivery_country'];


    $schema = '<ORDER_INFO>' . "\n" .
      '<ORDER_HEADER>' . "\n" .
      '<ORDER_ID>' . $orders['orders_id'] . '</ORDER_ID>' . "\n" .
      '<CUSTOMER_ID>' . $orders['customers_id'] . '</CUSTOMER_ID>' . "\n" .
      '<CUSTOMER_CID>' . $orders['customers_cid'] . '</CUSTOMER_CID>' . "\n" .
      '<CUSTOMER_VAT_ID>' . htmlspecialchars($orders['customers_vat_id'], ENT_COMPAT, 'ISO-8859-1', true) . '</CUSTOMER_VAT_ID>' . "\n" .
      '<CUSTOMER_EMAIL>' . htmlspecialchars($orders['customers_email_address'], ENT_COMPAT, 'ISO-8859-1', true) . '</CUSTOMER_EMAIL>' . "\n" .
      '<CUSTOMER_GROUP>' . $orders['customers_status'] . '</CUSTOMER_GROUP>' . "\n" .
      '<CUSTOMER_BIRTHDAY>' . htmlspecialchars($cust_dob) . '</CUSTOMER_BIRTHDAY>' . "\n" .
      '<CUSTOMER_NAME>' . htmlspecialchars($cust_name, ENT_COMPAT, 'ISO-8859-1', true) . '</CUSTOMER_NAME>' . "\n" .
      '<CUSTOMER_TELEPHONE>' . htmlspecialchars($cust_telephone, ENT_COMPAT, 'ISO-8859-1', true) . '</CUSTOMER_TELEPHONE>' . "\n" .
      '<ACCOUNT_TYPE>' . $orders['account_type'] . '</ACCOUNT_TYPE>' . "\n" .
      '<LANGUAGE_CODE>' . $orders['language_code'] . '</LANGUAGE_CODE>' . "\n" .
      '<ORDER_DATE>' . $orders['date_purchased'] . '</ORDER_DATE>' . "\n" .
      '<ORDER_DATE_FINISHED>' . $orders['orders_date_finished'] . '</ORDER_DATE_FINISHED>' . "\n" .
      '<ORDER_STATUS>' . $orders['orders_status'] . '</ORDER_STATUS>' . "\n" .
      '<ORDER_IP>' . htmlspecialchars($orders['customers_ip']) . '</ORDER_IP>' . "\n" .
      '<ORDER_CURRENCY>' . htmlspecialchars($orders['currency_code']) . '</ORDER_CURRENCY>' . "\n" .
      '<ORDER_CURRENCY_VALUE>' . $orders['currency_value'] . '</ORDER_CURRENCY_VALUE>' . "\n" .
      '<ORDER_COMMENTS>' . htmlspecialchars($orders['comments']) . '</ORDER_COMMENTS>' . "\n" .
      '</ORDER_HEADER>' . "\n";

    if (htmlspecialchars($orders['billing_gender']) == "m") {
      $billing_gender = "Herr";
    } else if (htmlspecialchars($orders['billing_gender']) == "f") {
      $billing_gender = "Frau";
    } else if (htmlspecialchars($orders['billing_gender']) == "c") {
      $billing_gender = "Firma";
    } else {
      $billing_gender = "";
    }


    $schema .= '<BILLING_ADDRESS>' . "\n" .
      '<GENDER>' . $billing_gender . '</GENDER>' . "\n" .
      '<TELEPHONE>' . htmlspecialchars($orders['billing_phone'], ENT_COMPAT, 'ISO-8859-1', true) . '</TELEPHONE>' . "\n" .
      '<FAX>' . htmlspecialchars($orders['billing_fax'], ENT_COMPAT, 'ISO-8859-1', true) . '</FAX>' . "\n" .
      '<FIRSTNAME>' . htmlspecialchars($orders['billing_firstname'], ENT_COMPAT, 'ISO-8859-1', true) . '</FIRSTNAME>' . "\n" .
      '<LASTNAME>' . htmlspecialchars($orders['billing_lastname'], ENT_COMPAT, 'ISO-8859-1', true) . '</LASTNAME>' . "\n" .
      '<COMPANY>' . htmlspecialchars($orders['billing_company'], ENT_COMPAT, 'ISO-8859-1', true) . '</COMPANY>' . "\n" .
      '<COMPANY2>' . htmlspecialchars($orders['billing_company_2'], ENT_COMPAT, 'ISO-8859-1', true) . '</COMPANY2>' . "\n" .
      '<COMPANY3>' . htmlspecialchars($orders['billing_company_3'], ENT_COMPAT, 'ISO-8859-1', true) . '</COMPANY3>' . "\n" .
      '<STREET>' . htmlspecialchars($orders['billing_street_address'], ENT_COMPAT, 'ISO-8859-1', true) . '</STREET>' . "\n" .
      '<CITY>' . htmlspecialchars($orders['billing_city'], ENT_COMPAT, 'ISO-8859-1', true) . " " . htmlspecialchars($orders['billing_suburb'], ENT_COMPAT, 'ISO-8859-1', true) . '</CITY>' . "\n" .
      '<POSTCODE>' . htmlspecialchars($orders['billing_postcode'], ENT_COMPAT, 'ISO-8859-1', true) . '</POSTCODE>' . "\n" .
      '<SUBURB>' . htmlspecialchars($orders['billing_suburb'], ENT_COMPAT, 'ISO-8859-1', true) . '</SUBURB>' . "\n" .
      '<ZONE>' . htmlspecialchars($orders['billing_zone'], ENT_COMPAT, 'ISO-8859-1', true) . '</ZONE>' . "\n" .
      '<ZONE_CODE>' . htmlspecialchars($orders['billing_zone_code'], ENT_COMPAT, 'ISO-8859-1', true) . '</ZONE_CODE>' . "\n" .
      '<COUNTRY>' . htmlspecialchars($orders['billing_country'], ENT_COMPAT, 'ISO-8859-1', true) . '</COUNTRY>' . "\n" .
      '<COUNTRY_CODE>' . htmlspecialchars($orders['billing_country_code'], ENT_COMPAT, 'ISO-8859-1', true) . '</COUNTRY_CODE>' . "\n" .
      '</BILLING_ADDRESS>' . "\n";


    if (htmlspecialchars($orders['delivery_gender']) == "m") {
      $delivery_gender = "Herr";
    } else if (htmlspecialchars($orders['delivery_gender']) == "f") {
      $delivery_gender = "Frau";
    } else if (htmlspecialchars($orders['delivery_gender']) == "c") {
      $delivery_gender = "Firma";
    } else {
      $delivery_gender = "";
    }

    $schema .= '<DELIVERY_ADDRESS>' . "\n" .
      '<GENDER>' . $delivery_gender . '</GENDER>' . "\n" .
      '<TELEPHONE>' . htmlspecialchars($orders['delivery_phone'], ENT_COMPAT, 'ISO-8859-1', true) . '</TELEPHONE>' . "\n" .
      '<FAX>' . htmlspecialchars($orders['delivery_fax'], ENT_COMPAT, 'ISO-8859-1', true) . '</FAX>' . "\n" .
      '<FIRSTNAME>' . htmlspecialchars($orders['delivery_firstname'], ENT_COMPAT, 'ISO-8859-1', true) . '</FIRSTNAME>' . "\n" .
      '<LASTNAME>' . htmlspecialchars($orders['delivery_lastname'], ENT_COMPAT, 'ISO-8859-1', true) . '</LASTNAME>' . "\n" .
      '<COMPANY>' . htmlspecialchars($orders['delivery_company'], ENT_COMPAT, 'ISO-8859-1', true) . '</COMPANY>' . "\n" .
      '<COMPANY2>' . htmlspecialchars($orders['delivery_company_2'], ENT_COMPAT, 'ISO-8859-1', true) . '</COMPANY2>' . "\n" .
      '<COMPANY2>' . htmlspecialchars($orders['delivery_company_2'], ENT_COMPAT, 'ISO-8859-1', true) . '</COMPANY2>' . "\n" .
      '<STREET>' . htmlspecialchars($orders['delivery_street_address'], ENT_COMPAT, 'ISO-8859-1', true) . '</STREET>' . "\n" .
      '<CITY>' . htmlspecialchars($orders['delivery_city'], ENT_COMPAT, 'ISO-8859-1', true) . " " . htmlspecialchars($orders['delivery_suburb'], ENT_COMPAT, 'ISO-8859-1', true) . '</CITY>' . "\n" .
      '<POSTCODE>' . htmlspecialchars($orders['delivery_postcode'], ENT_COMPAT, 'ISO-8859-1', true) . '</POSTCODE>' . "\n" .
      '<SUBURB>' . htmlspecialchars($orders['delivery_suburb'], ENT_COMPAT, 'ISO-8859-1', true) . '</SUBURB>' . "\n" .
      '<ZONE>' . htmlspecialchars($orders['delivery_zone'], ENT_COMPAT, 'ISO-8859-1', true) . '</ZONE>' . "\n" .
      '<ZONE_CODE>' . htmlspecialchars($orders['delivery_zone_code'], ENT_COMPAT, 'ISO-8859-1', true) . '</ZONE_CODE>' . "\n" .
      '<COUNTRY>' . htmlspecialchars($orders['delivery_country'], ENT_COMPAT, 'ISO-8859-1', true) . '</COUNTRY>' . "\n" .
      '<COUNTRY_CODE>' . htmlspecialchars($orders['delivery_country_code'], ENT_COMPAT, 'ISO-8859-1', true) . '</COUNTRY_CODE>' . "\n" .
      '</DELIVERY_ADDRESS>' . "\n";


    //Zahlungs-Informationen auswerten und ausgeben
    switch ($orders['payment_code']) {
      case 'xt_cashondelivery':
        //Nachnahme

        $schema .= '<PAYMENT>' . "\n" .
          '<PAYMENT_METHOD>' . htmlspecialchars($orders['payment_code'], ENT_COMPAT, 'ISO-8859-1', true) . '</PAYMENT_METHOD>' . "\n" .
          '</PAYMENT>' . "\n";
        break;


      case 'xt_prepayment':
        //Vorkasse

        $schema .= '<PAYMENT>' . "\n" .
          '<PAYMENT_METHOD>' . htmlspecialchars($orders['payment_code'], ENT_COMPAT, 'ISO-8859-1', true) . '</PAYMENT_METHOD>' . "\n" .
          '</PAYMENT>' . "\n";
        break;


      case 'xt_invoice':
        //Rechnung

        $schema .= '<PAYMENT>' . "\n" .
          '<PAYMENT_METHOD>' . htmlspecialchars($orders['payment_code'], ENT_COMPAT, 'ISO-8859-1', true) . '</PAYMENT_METHOD>' . "\n" .
          '</PAYMENT>' . "\n";
        break;


      case 'xt_cashpayment':
        //Barzahlung

        $schema .= '<PAYMENT>' . "\n" .
          '<PAYMENT_METHOD>' . htmlspecialchars($orders['payment_code'], ENT_COMPAT, 'ISO-8859-1', true) . '</PAYMENT_METHOD>' . "\n" .
          '</PAYMENT>' . "\n";
        break;


      case 'xt_creditcard':
        //Kreditkarten-Informationen �bergeben

        $arr = $orders['orders_data'];
        $c_array = preg_split('[\"]', $arr);

        $creditcard_type = array_search('cc_type', $c_array);
        $creditcard_owner = array_search('cc_owner', $c_array);
        $creditcard_number = array_search('cc_number', $c_array);
        $creditcard_expires = array_search('cc_expires', $c_array);
        $creditcard_start = array_search('cc_start', $c_array);
        $creditcard_cvv = array_search('cc_cvv', $c_array);

        $schema .= '<PAYMENT>' . "\n" .
          '<PAYMENT_METHOD>' . htmlspecialchars($orders['payment_code']) . '</PAYMENT_METHOD>' . "\n" .
          '<PAYMENT_CC_TYPE>' . htmlspecialchars($c_array[$creditcard_type + 2]) . '</PAYMENT_CC_TYPE>' . "\n" .
          '<PAYMENT_CC_OWNER>' . htmlspecialchars($c_array[$creditcard_owner + 2]) . '</PAYMENT_CC_OWNER>' . "\n" .
          '<PAYMENT_CC_NUMBER>' . htmlspecialchars($c_array[$creditcard_number + 2]) . '</PAYMENT_CC_NUMBER>' . "\n" .
          '<PAYMENT_CC_EXPIRES>' . htmlspecialchars($c_array[$creditcard_expires + 2]) . '</PAYMENT_CC_EXPIRES>' . "\n" .
          '<PAYMENT_CC_START>' . htmlspecialchars($c_array[$creditcard_start + 2]) . '</PAYMENT_CC_START>' . "\n" .
          '<PAYMENT_CC_CVV>' . htmlspecialchars($c_array[$creditcard_cvv + 2]) . '</PAYMENT_CC_CVV>' . "\n" .
          '</PAYMENT>' . "\n";
        break;


      case 'xt_banktransfer':
        // Bankverbindung laden, wenn aktiv

        $arr = $orders['orders_data'];
        $b_array = preg_split('[\"]', $arr);

        $banktransfer_owner = array_search('banktransfer_owner', $b_array);
        $banktransfer_bankname = array_search('banktransfer_bank_name', $b_array);
        $banktransfer_blz = array_search('banktransfer_blz', $b_array);
        $banktransfer_number = array_search('banktransfer_number', $b_array);
        $banktransfer_bic = array_search('banktransfer_bic', $b_array);
        $banktransfer_iban = array_search('banktransfer_iban', $b_array);

        $schema .= '<PAYMENT>' . "\n" .
          '<PAYMENT_METHOD>' . htmlspecialchars($orders['payment_code']) . '</PAYMENT_METHOD>' . "\n" .
          '<PAYMENT_BANKTRANSFER_OWNER>' . htmlspecialchars($b_array[$banktransfer_owner + 2]) . '</PAYMENT_BANKTRANSFER_OWNER>' . "\n" .
          '<PAYMENT_BANKTRANSFER_BANKNAME>' . htmlspecialchars($b_array[$banktransfer_bankname + 2]) . '</PAYMENT_BANKTRANSFER_BANKNAME>' . "\n" .
          '<PAYMENT_BANKTRANSFER_BLZ>' . htmlspecialchars($b_array[$banktransfer_blz + 2]) . '</PAYMENT_BANKTRANSFER_BLZ>' . "\n" .
          '<PAYMENT_BANKTRANSFER_NUMBER>' . htmlspecialchars($b_array[$banktransfer_number + 2]) . '</PAYMENT_BANKTRANSFER_NUMBER>' . "\n" .
          '<PAYMENT_BANKTRANSFER_BIC>' . htmlspecialchars($b_array[$banktransfer_bic + 2]) . '</PAYMENT_BANKTRANSFER_BIC>' . "\n" .
          '<PAYMENT_BANKTRANSFER_IBAN>' . htmlspecialchars($b_array[$banktransfer_iban + 2]) . '</PAYMENT_BANKTRANSFER_IBAN>' . "\n" .
          '</PAYMENT>' . "\n";
        break;


      default:
        // Keine der o.g. tritt f�r diese Bestellung zu

        $schema .= '<PAYMENT>' . "\n" .
          '<PAYMENT_METHOD>' . htmlspecialchars($orders['payment_code'], ENT_COMPAT, 'ISO-8859-1', true) . '</PAYMENT_METHOD>' . "\n" .
          '</PAYMENT>' . "\n";
        break;
    }


    $schema .= '<SHIPPING>' . "\n" .
      '<SHIPPING_METHOD>' . htmlspecialchars($orders['shipping_code'], ENT_COMPAT, 'ISO-8859-1', true) . '</SHIPPING_METHOD>' . "\n" .
      '</SHIPPING>' . "\n";


    $schema .= '<ORDER_PRODUCTS>' . "\n";

    $products_query = "select * from " . TABLE_ORDERS_PRODUCTS . " where orders_id = '" . $orders['orders_id'] . "'";
    $products_result = mysqli_query($db, $products_query);
    while ($products = mysqli_fetch_array($products_result, MYSQLI_BOTH)) {

      $schema .= '<PRODUCT>' . "\n" .
        '<PRODUCTS_ID>' . $products['products_id'] . '</PRODUCTS_ID>' . "\n" .
        '<PRODUCTS_MODEL>' . htmlspecialchars($products['products_model'], ENT_COMPAT, 'ISO-8859-1', true) . '</PRODUCTS_MODEL>' . "\n" .
        '<PRODUCTS_NAME>' . htmlspecialchars($products['products_name'], ENT_COMPAT, 'ISO-8859-1', true) . '</PRODUCTS_NAME>' . "\n" .
        '<PRODUCTS_PRICE>' . $products['products_price'] . '</PRODUCTS_PRICE>' . "\n" .
        '<PRODUCTS_DISCOUNT>' . $products['products_discount'] . '</PRODUCTS_DISCOUNT>' . "\n" .
        '<PRODUCTS_TAX>' . $products['products_tax'] . '</PRODUCTS_TAX>' . "\n" .
        '<PRODUCTS_TAX_CLASS>' . $products['products_tax_class'] . '</PRODUCTS_TAX_CLASS>' . "\n" .
        '<PRODUCTS_QUANTITY>' . $products['products_quantity'] . '</PRODUCTS_QUANTITY>' . "\n" .
        '<PRODUCTS_DATA>' . $products['products_data'] . '</PRODUCTS_DATA>' . "\n" .
        '<PRODUCTS_TAX_FLAG>' . $products['allow_tax'] . '</PRODUCTS_TAX_FLAG>' . "\n" .
        '<PRODUCTS_SHIPPING_TIME>' . $products['products_shipping_time'] . '</PRODUCTS_SHIPPING_TIME>' . "\n" .
        '</PRODUCT>' . "\n";
    }
    mysqli_free_result($products_result);

    $schema .= '</ORDER_PRODUCTS>' . "\n";


    $schema .= '<ORDER_TOTAL>' . "\n";

    $total_query = "select * from " . TABLE_ORDERS_TOTAL . " where orders_id = '" . $orders['orders_id'] . "'";
    $total_result = mysqli_query($db, $total_query);
    while ($totals = mysqli_fetch_array($total_result, MYSQLI_BOTH)) {

      $schema .= '<TOTAL>' . "\n" .
        '<TOTAL_ID>' . htmlspecialchars($totals['orders_total_id'], ENT_COMPAT, 'ISO-8859-1', true) . '</TOTAL_ID>' . "\n" .
        '<TOTAL_KEY>' . htmlspecialchars($totals['orders_total_key'], ENT_COMPAT, 'ISO-8859-1', true) . '</TOTAL_KEY>' . "\n" .
        '<TOTAL_KEY_ID>' . htmlspecialchars($totals['orders_total_key_id'], ENT_COMPAT, 'ISO-8859-1', true) . '</TOTAL_KEY_ID>' . "\n" .
        '<TOTAL_MODEL>' . htmlspecialchars($totals['orders_total_model'], ENT_COMPAT, 'ISO-8859-1', true) . '</TOTAL_MODEL>' . "\n" .
        '<TOTAL_NAME>' . htmlspecialchars($totals['orders_total_name'], ENT_COMPAT, 'ISO-8859-1', true) . '</TOTAL_NAME>' . "\n" .
        '<TOTAL_PRICE>' . htmlspecialchars($totals['orders_total_price'], ENT_COMPAT, 'ISO-8859-1', true) . '</TOTAL_PRICE>' . "\n" .
        '<TOTAL_TAX>' . htmlspecialchars($totals['orders_total_tax'], ENT_COMPAT, 'ISO-8859-1', true) . '</TOTAL_TAX>' . "\n" .
        '<TOTAL_TAX_CLASS>' . htmlspecialchars($totals['orders_total_tax_class'], ENT_COMPAT, 'ISO-8859-1', true) . '</TOTAL_TAX_CLASS>' . "\n" .
        '<TOTAL_QUANTITY>' . htmlspecialchars($totals['orders_total_quantity'], ENT_COMPAT, 'ISO-8859-1', true) . '</TOTAL_QUANTITY>' . "\n" .
        '<TOTAL_ALLOW_TAX>' . htmlspecialchars($totals['allow_tax'], ENT_COMPAT, 'ISO-8859-1', true) . '</TOTAL_ALLOW_TAX>' . "\n" .
        '</TOTAL>' . "\n";
    }
    mysqli_free_result($total_result);

    $schema .= '</ORDER_TOTAL>' . "\n";



    $schema .= '<ORDER_STATS>' . "\n";

    $stats_query = "select * from " . TABLE_ORDERS_STATS . " where orders_id = '" . $orders['orders_id'] . "'";
    $stats_result = mysqli_query($db, $stats_query);
    while ($stats = mysqli_fetch_array($stats_result, MYSQLI_BOTH)) {

      $schema .= '<STATS>' . "\n" .
        '<ORDER_ID>' . htmlspecialchars($stats['orders_id']) . '</ORDER_ID>' . "\n" .
        '<ORDER_STATS_PRICE>' . htmlspecialchars($stats['orders_stats_price']) . '</ORDER_STATS_PRICE>' . "\n" .
        '<PRODUCTS_COUNT>' . htmlspecialchars($stats['products_count']) . '</PRODUCTS_COUNT>' . "\n" .
        '</STATS>' . "\n";
    }
    mysqli_free_result($stats_result);

    $schema .= '</ORDER_STATS>' . "\n";


    $schema .= '<ORDER_HISTORY>' . "\n";

    $history_query = "select * from " . TABLE_ORDERS_STATUS_HISTORY . " where orders_id = '" . $orders['orders_id'] . "'";
    $history_result = mysqli_query($db, $history_query);
    while ($history = mysqli_fetch_array($history_result, MYSQLI_BOTH)) {

      $schema .= '<HISTORY>' . "\n" .
        '<ORDER_STATUS_HISTORY_ID>' . htmlspecialchars($history['orders_status_history_id']) . '</ORDER_STATUS_HISTORY_ID>' . "\n" .
        '<ORDER_ID>' . htmlspecialchars($history['orders_id']) . '</ORDER_ID>' . "\n" .
        '<ORDER_STATUS_ID>' . htmlspecialchars($history['orders_status_id']) . '</ORDER_STATUS_ID>' . "\n" .
        '<DATE_ADDED>' . htmlspecialchars($history['date_added']) . '</DATE_ADDED>' . "\n" .
        '<CUSTOMER_NOTIFIED>' . htmlspecialchars($history['customer_notified']) . '</CUSTOMER_NOTIFIED>' . "\n" .
        '<COMMENTS>' . htmlspecialchars($history['comments']) . '</COMMENTS>' . "\n" .
        '<CHANGE_TRIGGER>' . htmlspecialchars($history['change_trigger']) . '</CHANGE_TRIGGER>' . "\n" .
        '<CALLBACK_ID>' . htmlspecialchars($history['callback_id']) . '</CALLBACK_ID>' . "\n" .
        '<CUSTOMER_SHOW_COMMENT>' . htmlspecialchars($history['customer_show_comment']) . '</CUSTOMER_SHOW_COMMENT>' . "\n" .
        '<CALLBACK_MESSAGE>' . htmlspecialchars($history['callback_message']) . '</CALLBACK_MESSAGE>' . "\n" .
        '</HISTORY>' . "\n";
    }
    mysqli_free_result($history_result);

    $schema .= '</ORDER_HISTORY>' . "\n";


    $schema .= '</ORDER_INFO>' . "\n";
  }
  mysqli_free_result($orders_query);


  $footer = '</ORDER>' . "\n";

  //Ergebnis als XML ausgeben
  create_xml($header . $schema . $footer);
}


function read_orders_status()
{
  /**
   * @var mysqli $db
   */
  global $db;

  $header = '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
    '<ORDERS_STATUS>' . "\n";

  $cmd = "SELECT " . TABLE_SYSTEM_STATUS . ".status_id, " . TABLE_SYSTEM_STATUS . ".status_class, " . TABLE_SYSTEM_STATUS . ".status_values, " . TABLE_SYSTEM_STATUS_DESCRIPTION . ".status_name FROM " . TABLE_SYSTEM_STATUS . " LEFT JOIN " . TABLE_SYSTEM_STATUS_DESCRIPTION . " ON " . TABLE_SYSTEM_STATUS_DESCRIPTION . ".status_id = " . TABLE_SYSTEM_STATUS . ".status_id WHERE " . TABLE_SYSTEM_STATUS . ".status_class = 'order_status' AND " . TABLE_SYSTEM_STATUS_DESCRIPTION . ".language_code = '" . LANG_CODE . "' ORDER BY " . TABLE_SYSTEM_STATUS . ".status_id";
  $orderstatus_query = mysqli_query($db, $cmd);

  while ($orderstatus = mysqli_fetch_array($orderstatus_query, MYSQLI_BOTH)) {
    $schema = '<ORDERS_STATUS_DATA>' . "\n" .
      '<ID>' . htmlspecialchars($orderstatus['status_id']) . '</ID>' . "\n" .
      '<NAME>' . htmlspecialchars($orderstatus['status_name']) . '</NAME>' . "\n";
    $schema .= '</ORDERS_STATUS_DATA>' . "\n";
  }

  $footer = '</ORDERS_STATUS>' . "\n";

  //Ergebnis als XML ausgeben
  create_xml($header . $schema . $footer);
}



function read_shipping_status()
{
  /**
   * @var mysqli $db
   */
  global $db;

  $header = '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
    '<SHIPPING>' . "\n";

  $cmd = "SELECT " . TABLE_SYSTEM_STATUS . ".status_id, " . TABLE_SYSTEM_STATUS . ".status_class, " . TABLE_SYSTEM_STATUS . ".status_values, " . TABLE_SYSTEM_STATUS_DESCRIPTION . ".status_name FROM " . TABLE_SYSTEM_STATUS . " LEFT JOIN " . TABLE_SYSTEM_STATUS_DESCRIPTION . " ON " . TABLE_SYSTEM_STATUS_DESCRIPTION . ".status_id = " . TABLE_SYSTEM_STATUS . ".status_id WHERE " . TABLE_SYSTEM_STATUS . ".status_class = 'shipping_status' AND " . TABLE_SYSTEM_STATUS_DESCRIPTION . ".language_code = '" . LANG_CODE . "' ORDER BY " . TABLE_SYSTEM_STATUS . ".status_id";
  $shipping_query = mysqli_query($db, $cmd);

  while ($shipping = mysqli_fetch_array($shipping_query, MYSQLI_BOTH)) {
    $schema = '<SHIPPING_DATA>' . "\n" .
      '<ID>' . htmlspecialchars($shipping['status_id']) . '</ID>' . "\n" .
      '<NAME>' . htmlspecialchars($shipping['status_name']) . '</NAME>' . "\n";
    $schema .= '</SHIPPING_DATA>' . "\n";
  }

  $footer = '</SHIPPING>' . "\n";

  //Ergebnis als XML ausgeben
  create_xml($header . $schema . $footer);
}



function read_stock_rules()
{
  /**
   * @var mysqli $db
   */
  global $db;

  $header = '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
    '<STOCK_RULES>' . "\n";

  $cmd = "SELECT " . TABLE_SYSTEM_STATUS . ".status_id, " . TABLE_SYSTEM_STATUS . ".status_class, " . TABLE_SYSTEM_STATUS . ".status_values, " . TABLE_SYSTEM_STATUS_DESCRIPTION . ".status_name FROM " . TABLE_SYSTEM_STATUS . " LEFT JOIN " . TABLE_SYSTEM_STATUS_DESCRIPTION . " ON " . TABLE_SYSTEM_STATUS_DESCRIPTION . ".status_id = " . TABLE_SYSTEM_STATUS . ".status_id WHERE " . TABLE_SYSTEM_STATUS . ".status_class = 'stock_rule' AND " . TABLE_SYSTEM_STATUS_DESCRIPTION . ".language_code = '" . LANG_CODE . "' ORDER BY " . TABLE_SYSTEM_STATUS . ".status_id";
  $stockrules_query = mysqli_query($db, $cmd);

  while ($stockrules = mysqli_fetch_array($stockrules_query, MYSQLI_BOTH)) {
    $schema = '<STOCK_RULES_DATA>' . "\n" .
      '<ID>' . htmlspecialchars($stockrules['status_id']) . '</ID>' . "\n" .
      '<NAME>' . htmlspecialchars($stockrules['status_name']) . '</NAME>' . "\n";
    $schema .= '</STOCK_RULES_DATA>' . "\n";
  }

  $footer = '</STOCK_RULES>' . "\n";

  //Ergebnis als XML ausgeben
  create_xml($header . $schema . $footer);
}



function read_base_price()
{
  /**
   * @var mysqli $db
   */
  global $db;

  $header = '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
    '<STOCK_RULES>' . "\n";

  $cmd = "SELECT " . TABLE_SYSTEM_STATUS . ".status_id, " . TABLE_SYSTEM_STATUS . ".status_class, " . TABLE_SYSTEM_STATUS . ".status_values, " . TABLE_SYSTEM_STATUS_DESCRIPTION . ".status_name FROM " . TABLE_SYSTEM_STATUS . " LEFT JOIN " . TABLE_SYSTEM_STATUS_DESCRIPTION . " ON " . TABLE_SYSTEM_STATUS_DESCRIPTION . ".status_id = " . TABLE_SYSTEM_STATUS . ".status_id WHERE " . TABLE_SYSTEM_STATUS . ".status_class = 'base_price' AND " . TABLE_SYSTEM_STATUS_DESCRIPTION . ".language_code = '" . LANG_CODE . "' ORDER BY " . TABLE_SYSTEM_STATUS . ".status_id";
  $stockrules_query = mysqli_query($db, $cmd);

  while ($stockrules = mysqli_fetch_array($stockrules_query, MYSQLI_BOTH)) {
    $schema = '<STOCK_RULES_DATA>' . "\n" .
      '<ID>' . htmlspecialchars($stockrules['status_id']) . '</ID>' . "\n" .
      '<NAME>' . htmlspecialchars($stockrules['status_name']) . '</NAME>' . "\n";
    $schema .= '</STOCK_RULES_DATA>' . "\n";
  }

  $footer = '</STOCK_RULES>' . "\n";

  //Ergebnis als XML ausgeben
  create_xml($header . $schema . $footer);
}



function read_products()
{
  /**
   * @var mysqli $db
   */
  global $db;

  $header = '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
    '<PRODUCTS>' . "\n";

  $cmd = "select * from " . TABLE_PRODUCTS;

  $from = filter_input(INPUT_GET, 'products_from');
  $anz  = filter_input(INPUT_GET, 'products_count');
  $id  = filter_input(INPUT_GET, 'products_id');


  /* wenn 'products_id' beim Aufruf �bergeben wird, nur der entsprechende Artikel zur�ckgeben.
          Andernfalls ALLE Artikel zur�ckgeben */
  if (isset($id)) {
    $cmd .= " where products_id = " . $id;
  }


  if (isset($from)) {
    if (!isset($anz)) $anz = 1000;
    $cmd .= " limit " . $from . "," . $anz;
  }



  $products_query = mysqli_query($db, $cmd);
  while ($products = mysqli_fetch_array($products_query, MYSQLI_BOTH)) {

    $schema = '<PRODUCT_INFO>' . "\n" .
      '<PRODUCT_DATA>' . "\n" .
      '<PRODUCT_ID>' . $products['products_id'] . '</PRODUCT_ID>' . "\n" .
      '<PRODUCT_EXTERNAL_ID>' . $products['external_id'] . '</PRODUCT_EXTERNAL_ID>' . "\n" .
      '<PRODUCT_PERMISSION_ID>' . $products['permission_id'] . '</PRODUCT_PERMISSION_ID>' . "\n" .
      '<PRODUCT_OWNER>' . htmlspecialchars($products['products_owner']) . '</PRODUCT_OWNER>' . "\n" .
      '<PRODUCT_EAN>' . htmlspecialchars($products['products_ean']) . '</PRODUCT_EAN>' . "\n" .
      '<PRODUCT_QUANTITY>' . htmlspecialchars($products['products_quantity']) . '</PRODUCT_QUANTITY>' . "\n" .
      '<PRODUCT_AVERAGE_QUANTITY>' . htmlspecialchars($products['products_average_quantity']) . '</PRODUCT_AVERAGE_QUANTITY>' . "\n" .
      '<PRODUCT_SHIPPINGTIME>' . htmlspecialchars($products['products_shippingtime']) . '</PRODUCT_SHIPPINGTIME>' . "\n" .
      '<PRODUCT_MODEL>' . htmlspecialchars($products['products_model']) . '</PRODUCT_MODEL>' . "\n" .
      '<PRODUCT_MASTER_MODEL>' . htmlspecialchars($products['products_master_model']) . '</PRODUCT_MASTER_MODEL>' . "\n" .
      '<PRODUCT_MASTER_FLAG>' . htmlspecialchars($products['products_master_flag']) . '</PRODUCT_MASTER_FLAG>' . "\n" .
      '<PRODUCT_OPTION_MASTER_PRICE>' . htmlspecialchars($products['products_option_master_price']) . '</PRODUCT_OPTION_MASTER_PRICE>' . "\n" .
      '<PRODUCT_OPTION_TEMPLATE>' . htmlspecialchars($products['products_option_template']) . '</PRODUCT_OPTION_TEMPLATE>' . "\n" .
      '<PRODUCT_OPTION_LIST_TEMPLATE>' . htmlspecialchars($products['products_option_list_template']) . '</PRODUCT_OPTION_LIST_TEMPLATE>' . "\n" .

      '<PRODUCT_PRICE_FLAG_GRADUATED_ALL>' . htmlspecialchars($products['price_flag_graduated_all']) . '</PRODUCT_PRICE_FLAG_GRADUATED_ALL>' . "\n" .
      '<PRODUCT_PRICE_FLAG_GRADUATED_1>' . htmlspecialchars($products['price_flag_graduated_1']) . '</PRODUCT_PRICE_FLAG_GRADUATED_1>' . "\n" .
      '<PRODUCT_PRICE_FLAG_GRADUATED_2>' . htmlspecialchars($products['price_flag_graduated_2']) . '</PRODUCT_PRICE_FLAG_GRADUATED_2>' . "\n" .
      '<PRODUCT_PRICE_FLAG_GRADUATED_3>' . htmlspecialchars($products['price_flag_graduated_3']) . '</PRODUCT_PRICE_FLAG_GRADUATED_3>' . "\n" .

      '<PRODUCT_SORT>' . htmlspecialchars($products['products_sort']) . '</PRODUCT_SORT>' . "\n" .
      '<PRODUCT_PRICE>' . htmlspecialchars($products['products_price']) . '</PRODUCT_PRICE>' . "\n" .

      '<PRODUCT_DATE_ADDED>' . htmlspecialchars($products['date_added']) . '</PRODUCT_DATE_ADDED>' . "\n" .
      '<PRODUCT_LAST_MODIFIED>' . htmlspecialchars($products['last_modified']) . '</PRODUCT_LAST_MODIFIED>' . "\n" .
      '<PRODUCT_DATE_AVAILABLE>' . htmlspecialchars($products['date_available']) . '</PRODUCT_DATE_AVAILABLE>' . "\n" .

      '<PRODUCT_WEIGHT>' . htmlspecialchars($products['products_weight']) . '</PRODUCT_WEIGHT>' . "\n" .
      '<PRODUCT_STATUS>' . htmlspecialchars($products['products_status']) . '</PRODUCT_STATUS>' . "\n" .
      '<PRODUCT_TAX_CLASS_ID>' . htmlspecialchars($products['products_tax_class_id']) . '</PRODUCT_TAX_CLASS_ID>' . "\n" .

      '<PRODUCT_TEMPLATE>' . htmlspecialchars($products['product_template']) . '</PRODUCT_TEMPLATE>' . "\n" .
      '<PRODUCT_LIST_TEMPLATE>' . htmlspecialchars($products['product_list_template']) . '</PRODUCT_LIST_TEMPLATE>' . "\n" .

      '<PRODUCT_MANUFACTURERS_ID>' . htmlspecialchars($products['manufacturers_id']) . '</PRODUCT_MANUFACTURERS_ID>' . "\n" .
      '<PRODUCT_ORDERED>' . htmlspecialchars($products['products_ordered']) . '</PRODUCT_ORDERED>' . "\n" .
      '<PRODUCT_TRANSACTIONS>' . htmlspecialchars($products['products_transactions']) . '</PRODUCT_TRANSACTIONS>' . "\n" .
      '<PRODUCT_FSK18>' . htmlspecialchars($products['products_fsk18']) . '</PRODUCT_FSK18>' . "\n" .
      '<PRODUCT_VPE>' . htmlspecialchars($products['products_vpe']) . '</PRODUCT_VPE>' . "\n" .
      '<PRODUCT_VPE_STATUS>' . htmlspecialchars($products['products_vpe_status']) . '</PRODUCT_VPE_STATUS>' . "\n" .
      '<PRODUCT_VPE_VALUE>' . htmlspecialchars($products['products_vpe_value']) . '</PRODUCT_VPE_VALUE>' . "\n" .
      '<PRODUCT_STARTPAGE>' . htmlspecialchars($products['products_startpage']) . '</PRODUCT_STARTPAGE>' . "\n" .
      '<PRODUCT_STARTPAGE_SORT>' . htmlspecialchars($products['products_startpage_sort']) . '</PRODUCT_STARTPAGE_SORT>' . "\n" .
      '<PRODUCT_AVERAGE_RATING>' . htmlspecialchars($products['products_average_rating']) . '</PRODUCT_AVERAGE_RATING>' . "\n" .
      '<PRODUCT_RATING_COUNT>' . htmlspecialchars($products['products_rating_count']) . '</PRODUCT_RATING_COUNT>' . "\n" .
      '<PRODUCT_DIGITAL>' . htmlspecialchars($products['products_digital']) . '</PRODUCT_DIGITAL>' . "\n" .
      '<PRODUCT_FLAG_HAS_SPECIALS>' . htmlspecialchars($products['flag_has_specials']) . '</PRODUCT_FLAG_HAS_SPECIALS>' . "\n" .
      '<PRODUCT_SERIALS>' . htmlspecialchars($products['products_serials']) . '</PRODUCT_SERIALS>' . "\n" .
      '<PRODUCT_TOTAL_DOWNLOADS>' . htmlspecialchars($products['total_downloads']) . '</PRODUCT_TOTAL_DOWNLOADS>' . "\n";


    //Alle Kategorien zum Artikel auslesen

    $cmd_categories = "select categories_id from " . TABLE_PRODUCTS_TO_CATEGORIES . " where products_id = '" . $products['products_id'] . "' order by master_link desc";
    $categories_query = mysqli_query($db, $cmd_categories);

    $categories = array();

    while ($categories_data = mysqli_fetch_array($categories_query, MYSQLI_BOTH)) {
      $categories[] = $categories_data['categories_id'];
    }

    $categories = implode(',', $categories);
    $schema .= '<PRODUCT_CATEGORIES>' . $categories . '</PRODUCT_CATEGORIES>' . "\n";



    // Bild (Original auslesen)

    $bildname = $products['products_image'];
    $bild = '';
    $pfad = "../" . DIR_IMAGES_ORG;

    if ($bildname != '' && file_exists($pfad . $bildname)) {
      $bild = @implode("", @file($pfad . $bildname));
    }


    $schema .= '<PRODUCT_IMAGE_FILENAME>' . htmlspecialchars($products['products_image']) . '</PRODUCT_IMAGE_FILENAME>' . "\n" .
      '<PRODUCT_IMAGE_ORIGINAL>' . base64_encode($bild) . "</PRODUCT_IMAGE_ORIGINAL>" . "\n" .
      '<PRODUCT_IMAGE_URL>' . DIR_IMAGES_ORG . $products['products_image'] . "</PRODUCT_IMAGE_URL>" . "\n";


    // Bild (Media-Werte auslesen)

    $cmd_media = "select * from " . TABLE_MEDIA . " where class = 'product' and type = 'images' and file = '" . $products['products_image'] . "'";
    $media_query = mysqli_query($db, $cmd_media);
    while ($media = mysqli_fetch_array($media_query, MYSQLI_BOTH)) {
      $bildname = $media['file'];
      if ($bildname != '') {
        $schema .= '<PRODUCT_IMAGE_MEDIA_ID>' . htmlspecialchars($media['id']) . '</PRODUCT_IMAGE_MEDIA_ID>' . "\n";
      }
    }




    //Zus�tzliche Bilder auslesen

    $cmd_images = "select L.m_id, L.ml_id, L.link_id, L.class, L.type, L.sort_order, id, file from " . TABLE_MEDIA_LINK . " L " .
      "inner join " . TABLE_MEDIA . " on L.m_id = " . TABLE_MEDIA . ".id " .
      "where L.class = 'product' and L.type = 'images' and L.link_id='" . $products['products_id'] . "'";

    $images_query = mysqli_query($db, $cmd_images);
    while ($images = mysqli_fetch_array($images_query, MYSQLI_BOTH)) {
      $bildname = $images['file'];
      $bild = '';
      if ($bildname != '' && file_exists($pfad . $bildname)) {

        $bild = @implode("", @file($pfad . $bildname));

        //$schema .= "<PRODUCT_IMAGES IMAGE_ID = '" . $images["id"] . "' PRODUCT_ID = '" . $products["products_id"] . "' IMAGE_NUMBER = '" . $images["ml_id"] . "' IMAGE_NAME = '" . $images["file"] . "'>\n";
        $schema .= "<PRODUCT_IMAGES IMAGE_ID = '" . $images["id"] . "' PRODUCT_ID = '" . $products["products_id"] . "' IMAGE_NUMBER = '" . $images["sort_order"] . "' IMAGE_MEDIA_LINK = '" . $images["ml_id"] . "' IMAGE_NAME = '" . $images["file"] . "'>\n";
        $schema .= '<IMAGE_BINARY>' . base64_encode($bild) . '</IMAGE_BINARY>' . "\n";
        $schema .= "</PRODUCT_IMAGES>\n";
      }
    }


    //Texte und Beschreibungen

    $cmd_desc = "select * from " . TABLE_PRODUCTS_DESCRIPTION . " where products_id = " . $products['products_id'];
    $descriptions_query = mysqli_query($db, $cmd_desc);

    while ($descriptions = mysqli_fetch_array($descriptions_query, MYSQLI_BOTH)) {

      $cmd_lang = "select languages_id from " . TABLE_LANGUAGES . " where code = " . "'" . $descriptions["language_code"] . "'";
      $languages_query = mysqli_query($db, $cmd_lang);
      $languages = mysqli_fetch_array($languages_query, MYSQLI_BOTH);

      $cmd_seo = "select * from " . TABLE_SEO_URL . " where link_id = " . "'" . $products['products_id'] . "' and link_type = '1' and language_code = ''";
      $seo_query = mysqli_query($db, $cmd_seo);
      $seo = mysqli_fetch_array($seo_query, MYSQLI_BOTH);


      $schema .= "<PRODUCT_DESCRIPTION ID='" . $languages['languages_id'] . "' CODE='" . htmlspecialchars($descriptions['language_code']) . "'>\n";

      $schema .= '<NAME>' . htmlspecialchars($descriptions['products_name']) . '</NAME>' . "\n" .
        '<DESCRIPTION>' . htmlspecialchars($descriptions['products_description']) . '</DESCRIPTION>' . "\n" .
        '<SHORT_DESCRIPTION>' . htmlspecialchars($descriptions['products_short_description']) . '</SHORT_DESCRIPTION>' . "\n" .
        '<KEYWORDS>' . htmlspecialchars($descriptions['products_keywords']) . '</KEYWORDS>' . "\n" .
        '<META_TITLE>' . htmlspecialchars($seo['meta_title']) . '</META_TITLE>' . "\n" .
        '<META_DESCRIPTION>' . htmlspecialchars($seo['meta_description']) . '</META_DESCRIPTION>' . "\n" .
        '<META_KEYWORDS>' . htmlspecialchars($seo['meta_keywords']) . '</META_KEYWORDS>' . "\n" .
        '<URL>' . htmlspecialchars($seo['products_url']) . '</URL>' . "\n";

      $schema .= "</PRODUCT_DESCRIPTION>\n";
    }


    //Kundentyp-Preise

    $cmd_custstat = "select customers_status_id from " . TABLE_CUSTOMERS_STATUS;
    $customers_status_query = mysqli_query($db, $cmd_custstat);
    while ($customers_status = mysqli_fetch_array($customers_status_query, MYSQLI_BOTH)) {

      $schema .= "<PRODUCT_GROUP_PRICES ID='" . htmlspecialchars($customers_status["customers_status_id"]) . "'>" . "\n";

      $cmd_prices = "select * from " . TABLE_PRODUCTS_PRICE_GROUP . $customers_status["customers_status_id"] . " where products_id = " . $products['products_id'];
      $prices_query = mysqli_query($db, $cmd_prices);
      while ($prices = mysqli_fetch_array($prices_query, MYSQLI_BOTH)) {

        $schema .= "<PRICES_BLOCKING>\n";

        $schema .= '<PRICE_ID>' . $prices["id"] . '</PRICE_ID>' . "\n" .
          '<PRODUCT_ID>' . $products['products_id'] . '</PRODUCT_ID>' . "\n" .
          '<QTY>' . $prices["discount_quantity"] . '</QTY>' . "\n" .
          '<PRICE>' . $prices["price"] . '</PRICE>' . "\n";

        $schema .= "</PRICES_BLOCKING>\n";
      }

      $schema .= "</PRODUCT_GROUP_PRICES>\n";
    }


    //Sonderpreise

    $cmd_specials = "select * from " . TABLE_PRODUCTS_PRICE_SPECIAL . " where products_id = " . $products['products_id'];
    $specials_query = mysqli_query($db, $cmd_specials);
    while ($specials = mysqli_fetch_array($specials_query, MYSQLI_BOTH)) {

      $schema .= "<PRODUCT_PRICE_SPECIALS>\n";

      $schema .= '<ID>' . $specials["id"] . '</ID>' . "\n" .
        '<PRICE>' . $specials["specials_price"] . '</PRICE>' . "\n" .
        '<DATE_AVAILABLE>' . $specials["date_available"] . '</DATE_AVAILABLE>' . "\n" .
        '<DATE_EXPIRED>' . $specials["date_expired"] . '</DATE_EXPIRED>' . "\n" .
        '<STATUS>' . $specials["status"] . '</STATUS>' . "\n";

      $schema .= "</PRODUCT_PRICE_SPECIALS>\n";
    }


    //Cross-Selling

    $cmd_selling = "select * from " . TABLE_PRODUCTS_CROSS_SELL . " where products_id = " . $products['products_id'];
    $selling_query = mysqli_query($db, $cmd_selling);
    while ($selling = mysqli_fetch_array($selling_query, MYSQLI_BOTH)) {

      $schema .= "<PRODUCT_CROSS_SELLING>\n";
      $schema .= '<ID>' . $selling["products_id_cross_sell"] . '</ID>' . "\n";
      $schema .= "</PRODUCT_CROSS_SELLING>\n";
    }


    $schema .= '</PRODUCT_DATA>' . "\n" .
      '</PRODUCT_INFO>' . "\n";
  }


  $footer = '</PRODUCTS>' . "\n";

  //Ergebnis als XML ausgeben
  create_xml($header . $schema . $footer);
}



function read_products_stockings()
{
  /**
   * @var mysqli $db
   */
  global $db;

  $id  = filter_input(INPUT_GET, 'products_id');


  $header = '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
    '<PRODUCTS>' . "\n";


  $cmd = "select * from " . TABLE_PRODUCTS;
  if (isset($id)) {
    $cmd .= " where products_id = " . $id;
  }

  $products_query = mysqli_query($db, $cmd);

  while ($products = mysqli_fetch_array($products_query, MYSQLI_BOTH)) {

    $schema = '<PRODUCT_DATA>' . "\n" .
      '<PRODUCT_ID>' . $products['products_id'] . '</PRODUCT_ID>' . "\n" .
      '<PRODUCT_MODEL>' . htmlspecialchars($products['products_model']) . '</PRODUCT_MODEL>' . "\n" .
      '<PRODUCT_QUANTITY>' . htmlspecialchars($products['products_quantity']) . '</PRODUCT_QUANTITY>' . "\n" .
      '<PRODUCT_SHIPPINGTIME>' . htmlspecialchars($products['products_shippingtime']) . '</PRODUCT_SHIPPINGTIME>' . "\n" .
      '<PRODUCT_STATUS>' . htmlspecialchars($products['products_status']) . '</PRODUCT_STATUS>' . "\n" .
      '</PRODUCT_DATA>' . "\n";
  }


  $footer = '</PRODUCTS>' . "\n";

  //Ergebnis als XML ausgeben
  create_xml($header . $schema . $footer);
}



function read_manufacturers()
{
  /**
   * @var mysqli $db
   */
  global $db;

  $header = '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
    '<MANUFACTURERS>' . "\n";

  $cmd = "select manufacturers_id, external_id, manufacturers_name, manufacturers_image, manufacturers_status, manufacturers_sort, products_sorting, products_sorting2, date_added, last_modified from " . TABLE_MANUFACTURERS . " order by manufacturers_id";
  $manufacturers_query = mysqli_query($db, $cmd);

  while ($manufacturers = mysqli_fetch_array($manufacturers_query, MYSQLI_BOTH)) {
    $schema = '<MANUFACTURERS_DATA>' . "\n" .
      '<ID>' . htmlspecialchars($manufacturers['manufacturers_id']) . '</ID>' . "\n" .
      '<EXTERNAL_ID>' . htmlspecialchars($manufacturers['external_id']) . '</EXTERNAL_ID>' . "\n" .
      '<NAME>' . htmlspecialchars($manufacturers['manufacturers_name']) . '</NAME>' . "\n" .
      '<IMAGE>' . htmlspecialchars($manufacturers['manufacturers_image']) . '</IMAGE>' . "\n" .
      '<STATUS>' . htmlspecialchars($manufacturers['manufacturers_status']) . '</STATUS>' . "\n" .
      '<SORT>' . htmlspecialchars($manufacturers['manufacturers_sort']) . '</SORT>' . "\n" .
      '<SORTING>' . htmlspecialchars($manufacturers['products_sorting']) . '</SORTING>' . "\n" .
      '<SORTING2>' . htmlspecialchars($manufacturers['products_sorting2']) . '</SORTING2>' . "\n" .
      '<DATE_ADDED>' . htmlspecialchars($manufacturers['date_added']) . '</DATE_ADDED>' . "\n" .
      '<LAST_MODIFIED>' . htmlspecialchars($manufacturers['last_modified']) . '</LAST_MODIFIED>' . "\n";
    $schema .= '</MANUFACTURERS_DATA>' . "\n";
  }

  $footer = '</MANUFACTURERS>' . "\n";

  //Ergebnis als XML ausgeben
  create_xml($header . $schema . $footer);
}


function read_taxes()
{
  /**
   * @var mysqli $db
   */
  global $db;

  $header = '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
    '<TAXES>' . "\n";

  $cmd = "select tax_rates_id, tax_rate from " . TABLE_TAX_RATES . " order by tax_rates_id";
  $taxes_query = mysqli_query($db, $cmd);

  while ($taxes = mysqli_fetch_array($taxes_query, MYSQLI_BOTH)) {
    $schema = '<TAXES_DATA>' . "\n" .
      '<ID>' . htmlspecialchars($taxes['tax_rates_id']) . '</ID>' . "\n" .
      '<RATE>' . htmlspecialchars($taxes['tax_rate']) . '</RATE>' . "\n";
    $schema .= '</TAXES_DATA>' . "\n";
  }

  $footer = '</TAXES>' . "\n";

  //Ergebnis als XML ausgeben
  create_xml($header . $schema . $footer);
}


function read_products_list()
{
  /**
   * @var mysqli $db
   */
  global $db;

  $header = '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
    '<PRODUCTS>' . "\n";


  $cmd = "select * from " . TABLE_PRODUCTS;
  $id  = filter_input(INPUT_GET, 'products_id');


  /* wenn 'products_id' beim Aufruf �bergeben wird, nur der entsprechende Artikel zur�ckgeben.
          Andernfalls ALLE Artikel zur�ckgeben */
  if (isset($id)) {
    $cmd .= " where products_id = " . $id;
  }

  $products_query = mysqli_query($db, $cmd);
  while ($products = mysqli_fetch_array($products_query, MYSQLI_BOTH)) {

    $schema = '<PRODUCT_INFO>' . "\n" .
      '<PRODUCT_DATA>' . "\n" .
      '<PRODUCT_ID>' . $products['products_id'] . '</PRODUCT_ID>' . "\n" .
      '<PRODUCT_MODEL>' . htmlspecialchars($products['products_model'], ENT_COMPAT, 'ISO-8859-1', true) . '</PRODUCT_MODEL>' . "\n" .
      '<PRODUCT_EAN>' . htmlspecialchars($products['products_ean'], ENT_COMPAT, 'ISO-8859-1', true) . '</PRODUCT_EAN>' . "\n" .
      '<PRODUCT_QUANTITY>' . htmlspecialchars($products['products_quantity']) . '</PRODUCT_QUANTITY>' . "\n" .
      '<PRODUCT_SHIPPINGTIME>' . htmlspecialchars($products['products_shippingtime']) . '</PRODUCT_SHIPPINGTIME>' . "\n" .
      '<PRODUCT_WEIGHT>' . htmlspecialchars($products['products_weight']) . '</PRODUCT_WEIGHT>' . "\n" .
      '<PRODUCT_STATUS>' . htmlspecialchars($products['products_status']) . '</PRODUCT_STATUS>' . "\n" .
      '<PRODUCT_PRICE>' . htmlspecialchars($products['products_price']) . '</PRODUCT_PRICE>' . "\n" .
      '<PRODUCT_DATE_ADDED>' . htmlspecialchars($products['date_added']) . '</PRODUCT_DATE_ADDED>' . "\n" .
      '<PRODUCT_LAST_MODIFIED>' . htmlspecialchars($products['last_modified']) . '</PRODUCT_LAST_MODIFIED>' . "\n";


    //Texte und Beschreibungen

    $cmd_desc = "select * from " . TABLE_PRODUCTS_DESCRIPTION . " where products_id = " . $products['products_id'];
    $descriptions_query = mysqli_query($db, $cmd_desc);

    while ($descriptions = mysqli_fetch_array($descriptions_query, MYSQLI_BOTH)) {

      $cmd_lang = "select languages_id from " . TABLE_LANGUAGES . " where code = " . "'" . $descriptions["language_code"] . "'";
      $languages_query = mysqli_query($db, $cmd_lang);
      $languages = mysqli_fetch_array($languages_query, MYSQLI_BOTH);


      $schema .= "<PRODUCT_DESCRIPTION ID='" . $languages['languages_id'] . "' CODE='" . htmlspecialchars($descriptions['language_code']) . "'>\n";

      $schema .= '<NAME>' . htmlspecialchars($descriptions['products_name'], ENT_COMPAT, 'ISO-8859-1', true) . '</NAME>' . "\n" .
        '<DESCRIPTION>' . htmlspecialchars($descriptions['products_description'], ENT_COMPAT, 'ISO-8859-1', true) . '</DESCRIPTION>' . "\n" .
        '<SHORT_DESCRIPTION>' . htmlspecialchars($descriptions['products_short_description'], ENT_COMPAT, 'ISO-8859-1', true) . '</SHORT_DESCRIPTION>' . "\n";

      $schema .= "</PRODUCT_DESCRIPTION>\n";
    }

    $schema .= '</PRODUCT_DATA>' . "\n" .
      '</PRODUCT_INFO>' . "\n";
  }

  $footer = '</PRODUCTS>' . "\n";

  //Ergebnis als XML ausgeben
  create_xml($header . $schema . $footer);
}


function read_shop_configuration()
{
  /**
   * @var mysqli $db
   */
  global $db;

  $header = '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
    '<CONFIG>' . "\n" .
    '<CONFIG_DATA>' . "\n";


  $config_sql = 'select * from xt_config_1 order by id';
  $config_res = mysqli_query($db, $config_sql);

  while ($config = mysqli_fetch_array($config_res, MYSQLI_BOTH)) {

    $schema = '<ENTRY ID="' . $config['id'] . '">' .  "\n" .
      '<KEY>' . htmlspecialchars($config['config_key']) . '</KEY>' . "\n" .
      '<VALUE>' . htmlspecialchars($config['config_value']) . '</VALUE>' . "\n" .
      '<GROUP_ID>' . htmlspecialchars($config['group_id']) . '</GROUP_ID>' . "\n" .
      '<SORT_ORDER>' . htmlspecialchars($config['sort_order']) . '</SORT_ORDER>' . "\n" .
      '<TYPE>' . htmlspecialchars($config['type']) . '</TYPE>' . "\n" .
      '</ENTRY>' . "\n";
  }


  $footer = '</CONFIG_DATA>' . "\n" .
    '</CONFIG>' . "\n";

  //Ergebnis als XML ausgeben
  create_xml($header . $schema . $footer);
}


function read_image_options()
{
  /**
   * @var mysqli $db
   */
  global $db;

  $id  = filter_input(INPUT_GET, 'id');  //ggf. Abfrage nach 'folder'

  $header = '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
    '<CONFIG>' . "\n" .
    '<CONFIG_DATA>' . "\n";



  $cmd = "select * from " . TABLE_IMAGE_TYPE;
  if (isset($id)) {
    $cmd .= " where id = " . $id;
  }

  $imageopt_query = mysqli_query($db, $cmd);

  while ($imageopt = mysqli_fetch_array($imageopt_query, MYSQLI_BOTH)) {

    $schema = '<ENTRY ID="' . $imageopt['id'] . '">' .  "\n" .
      '<FOLDER>' . htmlspecialchars($imageopt['folder']) . '</FOLDER>' . "\n" .
      '<WIDTH>' . htmlspecialchars($imageopt['width']) . '</WIDTH>' . "\n" .
      '<HEIGHT>' . htmlspecialchars($imageopt['height']) . '</HEIGHT>' . "\n" .
      '<WATERMARK>' . htmlspecialchars($imageopt['watermark']) . '</WATERMARK>' . "\n" .
      '<PROCESS>' . htmlspecialchars($imageopt['process']) . '</PROCESS>' . "\n" .
      '<CLASS>' . htmlspecialchars($imageopt['class']) . '</CLASS>' . "\n" .
      '</ENTRY>' . "\n";
  }


  $footer = '</CONFIG_DATA>' . "\n" .
    '</CONFIG>' . "\n";

  //Ergebnis als XML ausgeben
  create_xml($header . $schema . $footer);
}



// **************** Lesen ENDE ******************************************************************




/* ********************************************* L�schen *****************************************
                                Werte im Shop aus Finos heraus l�schen
************************************************************************************************ */


function delete_categories()
{
  /**
   * @var mysqli $db
   */
  global $db;

  $header = '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
    '<CATEGORIES>' . "\n";


  $categories_id  = filter_input(INPUT_GET, 'categories_id');


  if (isset($categories_id)) {

    // Kategorie l�schen
    $result1 = mysqli_query($db, "delete from " . TABLE_CATEGORIES . " where categories_id = '" . $categories_id . "'");

    // Beschreibungstexte der Kategorie l�schen
    $result2 = mysqli_query($db, "delete from " . TABLE_CATEGORIES_DESCRIPTION . " where categories_id = '" . $categories_id . "'");

    // Produkte (Zuordnungen) zur Kategorie aktualisieren und in den Root verschieben
    //$result3 = mysqli_query($db,"delete from " . TABLE_PRODUCTS_TO_CATEGORIES . " where categories_id = '" . $categories_id . "'");

    $products_categories_query = mysqli_query($db, "select products_id from " . TABLE_PRODUCTS_TO_CATEGORIES . " where categories_id = '" . $categories_id . "'");
    while ($products_query = mysqli_fetch_array($products_categories_query, MYSQLI_BOTH)) {
      mysqli_query($db, "update " . TABLE_PRODUCTS_TO_CATEGORIES . " set categories_id = '" . CATEGORIES_ROOT . "' where products_id = '" . $products_query['products_id'] . "'");
    }


    //Kategorien in SEO-URL l�schen (xt_seo_url -- DELETE FROM `xt_seo_url` where `link_type` = 2 and url_text = 'de/widerrufsrecht')


    $schema = '<CATEGORIES_DATA>' . "\n" .
      '<ACTION>' . $_GET['action'] . '</ACTION>' . "\n" .
      '<CATEGORIES_ID>' . $categories_id . '</CATEGORIES_ID>' . "\n" .
      '<ERROR>' . '0' . '</ERROR>' . "\n" .
      '<MESSAGE>' . 'Die Kategorie wurde gel�scht' . '</MESSAGE>' . "\n" .
      '</CATEGORIES_DATA>' . "\n";
  } else {

    $schema = '<CATEGORIES_DATA>' . "\n" .
      '<ACTION>' . $_GET['action'] . '</ACTION>' . "\n" .
      '<CATEGORIES_ID>' . $categories_id . '</CATEGORIES_ID>' . "\n" .
      '<ERROR>' . '99' . '</ERROR>' . "\n" .
      '<MESSAGE>' . 'Die Kategorie konnte nicht gel�scht werden' . '</MESSAGE>' . "\n" .
      '</CATEGORIES_DATA>' . "\n";
  }


  $footer = '</CATEGORIES>' . "\n";

  //Ergebnis als XML ausgeben
  create_xml($header . $schema . $footer);
}


function delete_categories_all()
{
  /**
   * @var mysqli $db
   */
  global $db;

  $header = '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
    '<CATEGORIES>' . "\n";


  // Kategorien l�schen
  $result1 = mysqli_query($db, "delete from " . TABLE_CATEGORIES);

  // Beschreibungstexte der Kategorien l�schen
  $result2 = mysqli_query($db, "delete from " . TABLE_CATEGORIES_DESCRIPTION);

  // Produkte (Zuordnungen) zu den Kategorien aktualisieren und in den Root verschieben
  //$result3 = mysqli_query($db,"delete from " . TABLE_PRODUCTS_TO_CATEGORIES);

  $products_categories_query = mysqli_query($db, "select products_id from " . TABLE_PRODUCTS_TO_CATEGORIES);
  while ($products_query = mysqli_fetch_array($products_categories_query, MYSQLI_BOTH)) {
    mysqli_query($db, "update " . TABLE_PRODUCTS_TO_CATEGORIES . " set categories_id = '" . CATEGORIES_ROOT . "' where products_id = '" . $products_query['products_id'] . "'");
  }

  //Kategorien in SEO-URL l�schen (xt_seo_url -- DELETE FROM `xt_seo_url` where `link_type` = 2)


  $schema = '<CATEGORIES_DATA>' . "\n" .
    '<ACTION>' . $_GET['action'] . '</ACTION>' . "\n" .
    '<ERROR>' . '0' . '</ERROR>' . "\n" .
    '<MESSAGE>' . 'Es wurden alle Kategorien gel�scht' . '</MESSAGE>' . "\n" .
    '</CATEGORIES_DATA>' . "\n";

  $footer = '</CATEGORIES>' . "\n";

  //Ergebnis als XML ausgeben
  create_xml($header . $schema . $footer);
}



function delete_products_specials()
{
  /**
   * @var mysqli $db
   */
  global $db;

  $header = '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
    '<SPECIALS>' . "\n";


  $specials_id  = filter_input(INPUT_GET, 'specials_id');

  if (isset($specials_id)) {

    // Sonderpreise zum Produkt l�schen
    $result = mysqli_query($db, "delete from " . TABLE_PRODUCTS_PRICE_SPECIAL . " where id = '" . $specials_id . "'");

    $schema = '<SPECIALS_DATA>' . "\n" .
      '<ACTION>' . $_GET['action'] . '</ACTION>' . "\n" .
      '<SPECIALS_ID>' . $specials_id . '</SPECIALS_ID>' . "\n" .
      '<ERROR>' . '0' . '</ERROR>' . "\n" .
      '<MESSAGE>' . 'Der Sonderpreis wurde gel�scht' . '</MESSAGE>' . "\n" .
      '</SPECIALS_DATA>' . "\n";
  } else {

    $schema = '<SPECIALS_DATA>' . "\n" .
      '<ACTION>' . $_GET['action'] . '</ACTION>' . "\n" .
      '<SPECIALS_ID>' . $specials_id . '</SPECIALS_ID>' . "\n" .
      '<ERROR>' . '99' . '</ERROR>' . "\n" .
      '<MESSAGE>' . 'Der Sonderpreis konnte nicht gel�scht werden' . '</MESSAGE>' . "\n" .
      '</SPECIALS_DATA>' . "\n";
  }


  $footer = '</SPECIALS>' . "\n";

  //Ergebnis als XML ausgeben
  create_xml($header . $schema . $footer);
}



function delete_products_specials_all()
{
  /**
   * @var mysqli $db
   */
  global $db;

  $header = '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
    '<SPECIALS>' . "\n";


  // Sonderpreise l�schen
  $result = mysqli_query($db, "delete from " . TABLE_PRODUCTS_PRICE_SPECIAL);


  $schema = '<SPECIALS_DATA>' . "\n" .
    '<ACTION>' . $_GET['action'] . '</ACTION>' . "\n" .
    '<ERROR>' . '0' . '</ERROR>' . "\n" .
    '<MESSAGE>' . 'Es wurden alle Sonderpreise gel�scht' . '</MESSAGE>' . "\n" .
    '</SPECIALS_DATA>' . "\n";

  $footer = '</SPECIALS>' . "\n";

  //Ergebnis als XML ausgeben
  create_xml($header . $schema . $footer);
}



function delete_products_categories()
{
  /**
   * @var mysqli $db
   */
  global $db;

  $header = '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
    '<PRODUCTS_CATEGORIES>' . "\n";


  $products_id  = filter_input(INPUT_GET, 'products_id');

  if (isset($products_id)) {

    // Kategorien zum Produkt l�schen (auch ohne Schleife werden alle Datens�tze mit Produkt-ID x gel�scht)
    $result = mysqli_query($db, "delete from " . TABLE_PRODUCTS_TO_CATEGORIES . " where products_id = '" . $products_id . "'");

    $schema = '<PRODUCTS_CATEGORIES_DATA>' . "\n" .
      '<ACTION>' . $_GET['action'] . '</ACTION>' . "\n" .
      '<PRODUCTS_ID>' . $products_id . '</PRODUCTS_ID>' . "\n" .
      '<ERROR>' . '0' . '</ERROR>' . "\n" .
      '<MESSAGE>' . 'Die Kategorien zum Produkt wurden gel�scht' . '</MESSAGE>' . "\n" .
      '</PRODUCTS_CATEGORIES_DATA>' . "\n";
  } else {

    $schema = '<PRODUCTS_CATEGORIES_DATA>' . "\n" .
      '<ACTION>' . $_GET['action'] . '</ACTION>' . "\n" .
      '<PRODUCTS_ID>' . $products_id . '</PRODUCTS_ID>' . "\n" .
      '<ERROR>' . '99' . '</ERROR>' . "\n" .
      '<MESSAGE>' . 'Die Kategorien konnten nicht gel�scht werden' . '</MESSAGE>' . "\n" .
      '</PRODUCTS_CATEGORIES_DATA>' . "\n";
  }


  $footer = '</PRODUCTS_CATEGORIES>' . "\n";

  //Ergebnis als XML ausgeben
  create_xml($header . $schema . $footer);
}



function delete_products_categories_all()
{
  /**
   * @var mysqli $db
   */
  global $db;

  $header = '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
    '<PRODUCTS_CATEGORIES>' . "\n";


  // Kategorien l�schen
  $result = mysqli_query($db, "delete from " . TABLE_PRODUCTS_TO_CATEGORIES);


  $schema = '<PRODUCTS_CATEGORIES_DATA>' . "\n" .
    '<ACTION>' . $_GET['action'] . '</ACTION>' . "\n" .
    '<ERROR>' . '0' . '</ERROR>' . "\n" .
    '<MESSAGE>' . 'Es wurden alle Kategorien zu den Produkten gel�scht' . '</MESSAGE>' . "\n" .
    '</PRODUCTS_CATEGORIES_DATA>' . "\n";

  $footer = '</PRODUCTS_CATEGORIES>' . "\n";

  //Ergebnis als XML ausgeben
  create_xml($header . $schema . $footer);
}



function delete_products_cross_selling()
{
  /**
   * @var mysqli $db
   */
  global $db;

  $header = '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
    '<PRODUCTS_CROSS_SELLING>' . "\n";


  $products_id  = filter_input(INPUT_GET, 'products_id');

  if (isset($products_id)) {

    $result = mysqli_query($db, "delete from " . TABLE_PRODUCTS_CROSS_SELL . " where products_id = '" . $products_id . "'");

    $schema = '<PRODUCTS_CROSS_SELLING_DATA>' . "\n" .
      '<ACTION>' . $_GET['action'] . '</ACTION>' . "\n" .
      '<PRODUCTS_ID>' . $products_id . '</PRODUCTS_ID>' . "\n" .
      '<ERROR>' . '0' . '</ERROR>' . "\n" .
      '<MESSAGE>' . 'Die Cross-Selling-Produkte zum Produkt wurden gel�scht' . '</MESSAGE>' . "\n" .
      '</PRODUCTS_CROSS_SELLING_DATA>' . "\n";
  } else {

    $schema = '<PRODUCTS_CROSS_SELLING_DATA>' . "\n" .
      '<ACTION>' . $_GET['action'] . '</ACTION>' . "\n" .
      '<PRODUCTS_ID>' . $products_id . '</PRODUCTS_ID>' . "\n" .
      '<ERROR>' . '99' . '</ERROR>' . "\n" .
      '<MESSAGE>' . 'Die Cross-Selling-Produkte konnten nicht gel�scht werden' . '</MESSAGE>' . "\n" .
      '</PRODUCTS_CROSS_SELLING_DATA>' . "\n";
  }


  $footer = '</PRODUCTS_CROSS_SELLING>' . "\n";

  //Ergebnis als XML ausgeben
  create_xml($header . $schema . $footer);
}



function delete_products_cross_selling_all()
{
  /**
   * @var mysqli $db
   */
  global $db;

  $header = '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
    '<PRODUCTS_CROSS_SELLING>' . "\n";


  // Cross-Selling-Produkte l�schen
  $result = mysqli_query($db, "delete from " . TABLE_PRODUCTS_CROSS_SELL);


  $schema = '<PRODUCTS_CROSS_SELLING_DATA>' . "\n" .
    '<ACTION>' . $_GET['action'] . '</ACTION>' . "\n" .
    '<ERROR>' . '0' . '</ERROR>' . "\n" .
    '<MESSAGE>' . 'Es wurden alle Cross-Selling-Produkte zu den Produkten gel�scht' . '</MESSAGE>' . "\n" .
    '</PRODUCTS_CROSS_SELLING_DATA>' . "\n";

  $footer = '</PRODUCTS_CROSS_SELLING>' . "\n";

  //Ergebnis als XML ausgeben
  create_xml($header . $schema . $footer);
}



function delete_products()
{
  /**
   * @var mysqli $db
   */
  global $db;

  $header = '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
    '<STATUS>' . "\n";


  $products_id = filter_input(INPUT_GET, 'products_id');

  if (isset($products_id)) {

    // Artikel in der Kategorien-Zuordnung l�schen
    $result = mysqli_query($db, "delete from " . TABLE_PRODUCTS_TO_CATEGORIES . " where products_id = '" . $products_id . "'");
    veyton_remove_product($products_id);

    $schema = '<STATUS_DATA>' . "\n" .
      '<ACTION>' . $_GET['action'] . '</ACTION>' . "\n" .
      '<PRODUCTS_ID>' . $products_id . '</PRODUCTS_ID>' . "\n" .
      '<ERROR>' . '0' . '</ERROR>' . "\n" .
      '<MESSAGE>' . 'Der Artikel wurde gel�scht' . '</MESSAGE>' . "\n" .
      '</STATUS_DATA>' . "\n";
  } else {

    $schema = '<STATUS_DATA>' . "\n" .
      '<ACTION>' . $_GET['action'] . '</ACTION>' . "\n" .
      '<PRODUCTS_ID>' . $products_id . '</PRODUCTS_ID>' . "\n" .
      '<ERROR>' . '99' . '</ERROR>' . "\n" .
      '<MESSAGE>' . 'Der Artikel konnte nicht gel�scht werden' . '</MESSAGE>' . "\n" .
      '</STATUS_DATA>' . "\n";
  }


  $footer = '</STATUS>' . "\n";

  //Ergebnis als XML ausgeben
  create_xml($header . $schema . $footer);
}


function veyton_remove_product($product_id)
{


  /*

   $product_image_query = mysqli_query($db,"select products_image from " . TABLE_PRODUCTS . " where products_id = '" . $product_id . "'");
   $product_image = mysqli_fetch_array($product_image_query, MYSQLI_BOTH);

   $duplicate_image_query = mysqli_query($db,"select count(*) as total from " . TABLE_PRODUCTS . " where products_image = '" . $product_image['products_image'] . "'");
   $duplicate_image = mysqli_fetch_array($duplicate_image_query, MYSQLI_BOTH);

    if ($duplicate_image['total'] < 2)
    {
      if (file_exists(DIR_FS_CATALOG_POPUP_IMAGES . $product_image['products_image']))
      {
        @unlink(DIR_FS_CATALOG_POPUP_IMAGES . $product_image['products_image']);
      }
        $image_subdir = BIG_IMAGE_SUBDIR;
        if (substr($image_subdir, -1) != '/') $image_subdir .= '/';
        if (file_exists(DIR_FS_CATALOG_IMAGES . $image_subdir . $product_image['products_image']))
        {
          @unlink(DIR_FS_CATALOG_IMAGES . $image_subdir . $product_image['products_image']);
        }
    }


     mysqli_query($db,"delete from " . TABLE_SPECIALS . " where products_id = '" . $product_id . "'");
     mysqli_query($db,"delete from " . TABLE_PRODUCTS . " where products_id = '" . $product_id . "'");
     mysqli_query($db,"delete from " . TABLE_PRODUCTS_TO_CATEGORIES . " where products_id = '" . $product_id . "'");
     mysqli_query($db,"delete from " . TABLE_PRODUCTS_DESCRIPTION . " where products_id = '" . $product_id . "'");
     mysqli_query($db,"delete from " . TABLE_PRODUCTS_ATTRIBUTES . " where products_id = '" . $product_id . "'");
     mysqli_query($db,"delete from " . TABLE_CUSTOMERS_BASKET . " where products_id = '" . $product_id . "'");
     mysqli_query($db,"delete from " . TABLE_CUSTOMERS_BASKET_ATTRIBUTES . " where products_id = '" . $product_id . "'");

      if (defined('TABLE_PRODUCTS_IMAGES'))
      {
        mysqli_query($db,"delete from " . TABLE_PRODUCTS_IMAGES . " where products_id = '" . $product_id . "'");
      }


       // get statuses
        $customers_statuses_array = array(array());
        $customers_statuses_query = mysqli_query($db,"select * from " . TABLE_CUSTOMERS_STATUS . " where language_id = '". LANG_ID ."' order by customers_status_id");
         while ($customers_statuses = mysqli_fetch_array($customers_statuses_query, MYSQLI_BOTH))
         {
            $customers_statuses_array[] = array('id' => $customers_statuses['customers_status_id'],
                                                'text' => $customers_statuses['customers_status_name']);
         }


        for ($i=0,$n=sizeof($customers_status_array);$i<$n;$i++)
        {
          mysqli_query($db,"delete from personal_offers_by_customers_status_" . $i . " where products_id = '" . $product_id . "'");
        }


        $product_reviews_query = mysqli_query($db,"select reviews_id from " . TABLE_REVIEWS . " where products_id = '" . $product_id . "'");
         while ($product_reviews = mysqli_fetch_array($product_reviews_query, MYSQLI_BOTH))
         {
           mysqli_query($db,"delete from " . TABLE_REVIEWS_DESCRIPTION . " where reviews_id = '" . $product_reviews['reviews_id'] . "'");
         }
        mysqli_query($db,"delete from " . TABLE_REVIEWS . " where products_id = '" . $product_id . "'");


  */
}


function delete_manufacturers()
{
  /**
   * @var mysqli $db
   */
  global $db;

  $header = '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
    '<STATUS>' . "\n";


  $manufacturers_id  = filter_input(INPUT_GET, 'manufacturers_id');

  if (isset($manufacturers_id)) {

    $result = mysqli_query($db, "delete from " . TABLE_MANUFACTURERS . " where manufacturers_id = '" . $manufacturers_id . "'");
    $result2 = mysqli_query($db, "delete from " . TABLE_MANUFACTURERS_INFO . " where manufacturers_id = '" . $manufacturers_id . "'");

    $schema = '<STATUS_DATA>' . "\n" .
      '<ACTION>' . $_GET['action'] . '</ACTION>' . "\n" .
      '<MANUFACTURERS_ID>' . $manufacturers_id . '</MANUFACTURERS_ID>' . "\n" .
      '<ERROR>' . '0' . '</ERROR>' . "\n" .
      '<MESSAGE>' . 'Der Hersteller wurde gel�scht' . '</MESSAGE>' . "\n" .
      '</STATUS_DATA>' . "\n";
  } else {

    $schema = '<STATUS_DATA>' . "\n" .
      '<ACTION>' . $_GET['action'] . '</ACTION>' . "\n" .
      '<MANUFACTURERS_ID>' . $manufacturers_id . '</MANUFACTURERS_ID>' . "\n" .
      '<ERROR>' . '99' . '</ERROR>' . "\n" .
      '<MESSAGE>' . 'Der Hersteller konnte nicht gel�scht werden' . '</MESSAGE>' . "\n" .
      '</STATUS_DATA>' . "\n";
  }


  $footer = '</STATUS>' . "\n";

  //Ergebnis als XML ausgeben
  create_xml($header . $schema . $footer);
}




function delete_manufacturers_all()
{
  /**
   * @var mysqli $db
   */
  global $db;

  $header = '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
    '<STATUS>' . "\n";

  // Hersteller l�schen
  $result = mysqli_query($db, "delete from " . TABLE_MANUFACTURERS);
  $result2 = mysqli_query($db, "delete from " . TABLE_MANUFACTURERS_INFO);

  $schema = '<STATUS_DATA>' . "\n" .
    '<ACTION>' . $_GET['action'] . '</ACTION>' . "\n" .
    '<ERROR>' . '0' . '</ERROR>' . "\n" .
    '<MESSAGE>' . 'Alle Hersteller gel�scht' . '</MESSAGE>' . "\n" .
    '</STATUS_DATA>' . "\n";

  $footer = '</STATUS>' . "\n";

  //Ergebnis als XML ausgeben
  create_xml($header . $schema . $footer);
}

function products_exists()
{
  /**
   * @var mysqli $db
   */
  global $db;

  $header = '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
    '<STATUS>' . "\n";


  $products_id = filter_input(INPUT_GET, 'products_id');

  $sql = "select * from " . TABLE_PRODUCTS . " where products_id = '" . $products_id . "'";

  $query_count = mysqli_query($db, $sql);
  if ($product = mysqli_fetch_array($query_count, MYSQLI_BOTH)) {

    //Produkt ist vorhanden ---------------------------------------
    $exists = 1;
    $message = 'Der Artikel ist bereits vorhanden';
  } else {

    //Produkt ist nicht vorhanden ---------------------------------
    $exists = 0;
    $message = 'Der Artikel ist nicht vorhanden';
  }


  $schema = '<STATUS_DATA>' . "\n" .
    '<EXISTS>' . $exists . '</EXISTS>' . "\n" .
    '<PRODUCTS_ID>' . $products_id . '</PRODUCTS_ID>' . "\n" .
    '<ERROR>' . '0' . '</ERROR>' . "\n" .
    '<MESSAGE>' . $message . '</MESSAGE>' . "\n" .
    '</STATUS_DATA>' . "\n";

  $footer = '</STATUS>' . "\n";


  //Ergebnis als XML ausgeben
  create_xml($header . $schema . $footer);
}


function get_ot_class($ot)
{

  // - - - xtc4 Klassenbezeichnung in xtc3 ot_Klasse umbenennen - - -

  if ($ot == "shipping") {
    $otc = "ot_shipping";
  } elseif ($ot == "Coupon/Gutschein") {
    $otc = "ot_coupon";
  } elseif ($ot == "discount") {
    $otc = "ot_discount";
  } elseif ($ot == "loworderfee") {
    $otc = "ot_loworderfee";
  } elseif ($ot == "gv") {
    $otc = "ot_gv";
  } elseif ($ot == "total") {
    $otc = "ot_total";
  } elseif ($ot == "fixcod") {
    $otc = "ot_fixcod";
  } elseif ($ot == "cod_fee") {
    $otc = "ot_cod_fee";
  } elseif ($ot == "payment") {
    $otc = "ot_payment";
  } else {
    $otc = "ot_shipping";
  }
  return $otc;
}






/* ---------------------------------------------------------------------------------------------------------
   --------- ADMIN-Check- Funktionen -----------------------------------------------------------------------
   --------------------------------------------------------------------------------------------------------- */


function administration_account_check()
{
  /**
   * @var mysqli $db
   */
  global $db;

  $code = '0';
  $msg = 'Es handelt sich um einen g�ltigen Administrator.';


  $header = '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
    '<ADMIN>' . "\n" .
    '<ADMIN_DATA>' . "\n";


  $username = filter_input(INPUT_GET, 'adminuser');
  $password = filter_input(INPUT_GET, 'pwd');


  //pr�fen, ob es sich bei dem User um einen Administrator handelt
  $check_customer_query = mysqli_query($db, "select * from " . TABLE_ADMIN_ACL_AREA_USER . " where handle = '" . $username . "'");

  if (!mysqli_num_rows($check_customer_query)) {

    $code = '91';
    $msg = 'Es ist kein Administrator zu diesem Benutzernamen vorhanden.';
  } else {

    $check_customer = mysqli_fetch_array($check_customer_query, MYSQLI_BOTH);

    // Pr�fen, ob der Benutzer ein Administrator ist
    if ($check_customer['status'] != '1') {

      $code = '92';
      $msg = 'Bei dem Benutzer handelt es sich um einen deaktivierten Administrator.';
    }

    // Passwort pr�fen
    if (!(($check_customer['user_password'] == $password) or
      ($check_customer['user_password'] == md5($password)) or
      ($check_customer['user_password'] == md5(substr($password, 2, 40)))
    )) {

      $code = '93';
      $msg = 'Sie haben ein falsches bzw. kein Passwort angegeben.';
    }
  }


  $schema = '<CODE>' . $code . '</CODE>' . "\n" .
    '<MESSAGE>' . $msg . '</MESSAGE>' . "\n";


  $footer = '</ADMIN_DATA>' . "\n" .
    '</ADMIN>' . "\n";

  //Ergebnis als XML ausgeben
  create_xml($header . $schema . $footer);
}




/* ---------------------------------------------------------------------------------------------------------
   --------- Allgemeine-Funktionen -------------------------------------------------------------------------
   --------------------------------------------------------------------------------------------------------- */

function create_xml($content, $encoding = "0")
{

  //XML erzeugen aus $schema erzeugen --------------------

  /*
  $fh = fopen('export.xml','w') or die($php_errormsg);
        fwrite($fh,$content);
        fclose($fh) or die($php_errormsg);
 */

  //echo wird als Request ausgelesen

  if ($encoding == 0) {
    echo $content;
  } elseif ($encoding == 1) {
    echo htmlspecialchars(base64_encode($content));
  }
}


function read_version($version, $datum, $action)
{

  $schema = '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
    '<STATUS>' . "\n" .
    '<STATUS_DATA>' . "\n" .
    '<ACTION>' . $action . '</ACTION>' . "\n" .
    '<SCRIPT_VERSION>' . $version . '</SCRIPT_VERSION>' . "\n" .
    '<SCRIPT_DATE>' . $datum . '</SCRIPT_DATE>' . "\n" .
    '</STATUS_DATA>' . "\n" .
    '</STATUS>' . "\n\n";

  //Ergebnis als XML ausgeben
  create_xml($schema, xml_encoding);
}




/* ---------------------------------------------------------------------------------------------------------
   --------- Datenbank-Funktionen --------------------------------------------------------------------------
   --------------------------------------------------------------------------------------------------------- */


function database_insert($tabelle, $data, $action = 'insert', $parameters = '')
{
  /**
   * @var mysqli $db
   */
  //15.12.20    

  global $db;

  reset($data);


  // Datens�tze hinzuf�gen ***********************************************************

  if ($action == 'insert') {

    $insert_query =   'insert into ' . $tabelle .  ' (';
    foreach ($data as $columns => $value) {
      $insert_query .= $columns . ', ';
    }
    //  while (list($columns, ) = each($data))
    //   {
    //      $insert_query .= $columns . ', ';
    //   }

    $insert_query = substr($insert_query, 0, -2) . ') values (';
    reset($data);

    foreach ($data as $key => $value) {
      #while (list(, $value) = each($data))
      #{

      $value = (is_Float($value)) ? sprintf("%.F", $value) : (string)($value);
      //$value = (is_Float($value) & PHP4_3_10) ? sprintf("%.F",$value) : (string)($value);
      switch ($value) {
        case 'now()':
          $insert_query .= 'now(), ';
          break;
        case 'null':
          $insert_query .= 'null, ';
          break;
        default:
          $insert_query .= '\'' . $value . '\', ';
          break;
      }
    }

    $insert_query = substr($insert_query, 0, -2) . ')';
    // echo "<hr/><pre>\n";
    // echo $insert_query."\n";//DEBUG
    // echo "</pre>\n";                       
    return mysqli_query($db, $insert_query) or die("MySQLFehler: $insert_query;\n" . mysqli_error($db));
  }

  // Datens�tze �ndern ***********************************************************
  elseif ($action == 'update') {


    $update_query = 'update ' . $tabelle . ' set ';
    foreach ($data as $columns => $value)
    #while (list($columns, $value) = each($data))
    {
      $value = (is_Float($value)) ? sprintf("%.F", $value) : (string)($value);
      //$value = (is_Float($value) & PHP4_3_10) ? sprintf("%.F",$value) : (string)($value);
      switch ($value) {
        case 'now()':
          $update_query .= $columns . ' = now(), ';
          break;
        case 'null':
          $update_query .= $columns . ' = null, ';
          break;
        default:
          $update_query .= $columns . ' = \'' . $value . '\', ';
          break;
      }
    }

    $update_query = substr($update_query, 0, -2) . ' where ' . $parameters;


    // echo $update_query."\n";//DEBUG


    return mysqli_query($db, $update_query) or die("MySQLFehler: $update_query;\n" . mysqli_error($db));
  }
}

function ClearSTANDARTTEMPLATE($string)
{

  if ($string === "Standardtemplate") {
    return "";
  } else {
    return $string;
  }
}
