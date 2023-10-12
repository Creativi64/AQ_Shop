<?php

/*  ----------------------------------------------------------------------------------------

    --- AbamSoft Finos 13.x
    --- Script für xt:Commerce 3.04 SP2, xtc-modified, commerce-seo, ...

    ----------------------------------------------------------------------------------------  */

/*
define('CHARSET','iso-8859-1');
define('CHARSET','utf-8');
*/

define('LANG_ID',2);
define('LANG_FOLDER','german');

define('CATEGORIES_ROOT','0');

define('DATE_FORMAT_LONG', '%A, %d. %B %Y');                      // this is used for strftime()
define('SET_TIME_LIMIT',0);                                       //use xtc_set_time_limit(0);

require('../includes/application_top_export.php');

$version_nummer = '15.0';
$version_datum = '2020.11.19';

define('xml_encoding','1');



/*
header ("Last-Modified: ". gmdate ("D, d M Y H:i:s"). " GMT");
header ("Cache-Control: no-cache, must-revalidate");
header ("Pragma: no-cache");
header ("Content-Type: text/xml; charset=iso-8859-1");            //header ("Content-Type: text/xml; charset=utf-8");
header ("Accept-Charset: iso-8859-1,utf-8;q=0.7,*;q=0.7");
header ("Expect: 100-continue");
*/




  //prüfen, ob eine Aktion, E-Mail-Adresse sowie Passwort eines Administrators beim Aufruf übergeben wurden
   $action = db_prepare_input(isset($_POST['action']) ? $_POST['action'] : $_GET['action']);
   $user = db_prepare_input(isset($_POST['user']) ? $_POST['user'] : $_GET['user']);
   $password = db_prepare_input(isset($_POST['password']) ? $_POST['password'] : $_GET['password']);



   initialize_charset();


  // %% = das Passwort ist kein md5 Hash - in der Datenbank wird das Passwort jedoch nur als MD5-Hash gespeichert
   if (substr($password,0,2)=='%%')
   {
    $password=md5(substr($password,2,40));
   }


       if (($action!='check_admin_account'))
       {

           if (($user!='') && ($password!=''))
           {

             // User und Passwort wurden übergeben
              require_once(DIR_FS_INC . 'xtc_not_null.inc.php');
              require_once(DIR_FS_INC . 'xtc_redirect.inc.php');
              require_once(DIR_FS_INC . 'xtc_rand.inc.php');


                //Prüfen, ob es sich bei dem User um einen Administrator handelt
                 $check_customer_query = mysql_query("select customers_id, customers_status, customers_password from " . TABLE_CUSTOMERS . " where customers_email_address = '" . $user . "'");

                  if (!mysql_num_rows($check_customer_query))
                  {
                    write_xml_error (101, $action, 'Es ist kein Benutzer zu dieser E-Mail-Adresse vorhanden.');
                    exit;
                  }
                  else
                  {
                    $check_customer = mysql_fetch_array($check_customer_query);
                    // Prüfen, ob der Benutzer ein Administrator ist
                    if ($check_customer['customers_status']!='0')
                    {
                      write_xml_error (102, $action, 'Der Benutzer ' . $user . ' ist kein Administrator.');
                      exit;
                    }

                    // Passwort prüfen
                    if (!( ($check_customer['customers_password'] == $password) or
                           ($check_customer['customers_password'] == md5($password)) or
                           ($check_customer['customers_password'] == md5(substr($password,2,40)))
                       ))
                    {
                      write_xml_error (103, $action, 'Sie haben ein falsches Passwort angegeben');
                      exit;
                    }
                  }


           }
           else
           {

             // kein User bzw. Passwort übergeben
             write_xml_error (100, $action, 'Benutzername oder Passwort fehlt');
             exit;

           }

       }
       elseif ($action=='')
       {
         write_xml_error (104, $action, 'Es wurde keine Funktion bzw. Abfrage angefügt.');
         exit;
       }



// Verbindung zur Datenbank herstellen sowie Authentifizierung durchführen
 if (database_connection())
 {


       // Aktion wählen und ausführen ---------------------------------------------------------

            if ($_SERVER['REQUEST_METHOD']=='GET')
            {

               switch ($action)
               {

                 case 'get_version':
                  read_version ($version_nummer, $version_datum, $action);
                  exit;

                 case 'get_categories':
                  read_categories ();
                  exit;

                 case 'get_customers':
                  read_customers ();
                  exit;

                 case 'get_manufacturers':
                  read_manufacturers ();
                  exit;

                 case 'get_customers_newsletter':
                  read_customers_newsletter ();
                  exit;

                 case 'get_shop_configuration':
                  read_shop_configuration ();
                  exit;

                 case 'get_languages':
                  read_languages ();
                  exit;

                 case 'get_products':
                  read_products ();
                  exit;
                 case 'get_products_specials':
                  read_products_specials ();
                  exit;
                 case 'get_products_stockings':
                  read_products_stockings ();
                  exit;
                 case 'get_products_stockings_attributes':
                  read_products_stockings_attributes ();
                  exit;
                 case 'get_products_vpe':
                  read_products_vpe ();
                  exit;
                 case 'get_products_categories':
                  read_products_categories ();
                  exit;
                 case 'get_products_list':
                  read_products_list ();
                  exit;
                 case 'get_products_options_id':
                  read_products_options_id ();
                  exit;
                 case 'get_products_options_value_id':
                  read_products_options_value_id ();
                  exit;
                 case 'get_products_synch':
                  read_products_synch ();
                  exit;


                 case 'get_orders':
                  read_orders ();
                  exit;

                 case 'get_orders_history':
                  read_orders_history ();
                  exit;

                 case 'get_new_orders_count':
                  read_new_orders_count ();
                  exit;



                 case 'get_customers_status':
                  read_customers_status ();
                  exit;

                 case 'get_orders_status':
                  read_orders_status ();
                  exit;

                 case 'get_shipping_status':
                  read_shipping_status ();
                  exit;

                 case 'get_products_exists':
                  products_exists ();
                  exit;

                 case 'get_coupon_code':
                  create_couponcode ();
                  exit;

                 case 'set_order_update':
                  update_order ();
                  exit;
                 case 'set_orders_history':
                  save_orders_history ();
                  exit;

                 case 'check_admin_account':
                  administration_account_check ();
                  exit;


                 case 'del_products':
                  delete_products ();
                  exit;

                 case 'del_languages':
                  delete_languages ();
                  exit;
                 case 'del_languages_all':
                  delete_languages_all ();
                  exit;

                 case 'del_categories':
                  delete_categories ();
                  exit;
                 case 'del_categories_all':
                  delete_categories_all ();
                  exit;

                 case 'del_customers_status':
                  delete_customers_status ();
                  exit;
                 case 'del_customers_status_all':
                  delete_customers_status_all ();
                  exit;

                 case 'del_orders_status':
                  delete_orders_status ();
                  exit;
                 case 'del_orders_status_all':
                  delete_orders_status_all ();
                  exit;

                 case 'del_orders_history':
                  delete_orders_history ();
                  exit;

                 case 'del_shipping_status':
                  delete_shipping_status ();
                  exit;
                 case 'del_shipping_status_all':
                  delete_shipping_status_all ();
                  exit;

                 case 'del_products_specials':
                  delete_products_specials ();
                  exit;
                 case 'del_products_specials_all':
                  delete_products_specials_all ();
                  exit;

                 case 'del_products_vpe':
                  delete_products_vpe ();
                  exit;
                 case 'del_products_vpe_all':
                  delete_products_vpe_all ();
                  exit;

                 case 'del_products_categories':
                  delete_products_categories ();
                  exit;
                 case 'del_products_categories_all':
                  delete_products_categories_all ();
                  exit;



                 case 'update_tables':
                  updating_tables ();
                  exit;

               }

            }

            else

            {

              if ($_SERVER['REQUEST_METHOD']=='POST')
              {

                switch ($action)
                {

                 // Produkte
                  case 'set_products':
                   save_products ();
                   exit;

                  case 'set_products_defaultimage':
                   save_products_defaultimage ();
                   exit;

                  case 'set_products_descriptions':
                   save_products_descriptions ();
                   exit;

                  case 'set_products_personal_offers':
                   save_products_personal_offers ();
                   exit;

                  case 'set_products_price':
                   save_products_price ();
                   exit;

                  case 'set_products_attributes':
                   save_products_attributes ();
                   exit;

                  case 'set_products_categories':
                   save_products_categories ();
                   exit;

                  case 'set_products_images':
                   save_products_images ();
                   exit;

                  case 'set_products_stockvalues':
                   save_products_stockvalues ();
                   exit;

                  case 'set_products_variants_stockvalues':
                   save_products_variants_stockvalues ();
                   exit;

                  case 'set_products_options':
                   save_products_options ();
                   exit;

                  case 'set_products_options_values':
                   save_products_options_values ();
                   exit;

                  case 'set_products_specials':
                   save_products_specials ();
                   exit;

                  case 'set_products_vpe':
                   save_products_vpe ();
                   exit;


                  case 'set_customers':
                   save_customers ();
                   exit;

                  case 'set_customers_defaultaddress':
                   save_customers_defaultaddress ();
                   exit;

                  case 'set_customers_info':
                   save_customers_info ();
                   exit;


                  case 'set_customers_status':
                   save_customers_status ();
                   exit;


                  case 'set_orders_status':
                   save_orders_status ();
                   exit;

                  case 'set_shipping_status':
                   save_shipping_status ();
                   exit;

                  case 'set_languages':
                   save_languages ();
                   exit;


                  case 'set_categories':
                   save_categories ();
                   exit;
                  case 'set_categories_descriptions':
                   save_categories_descriptions ();
                   exit;
                  case 'set_categorie_image':
                   save_categorie_image ();
                   exit;


                  case 'set_shop_configuration':
                   save_shop_configuration ();
                   exit;


                  case 'set_coupons':
                   save_coupons ();
                   exit;
                  case 'set_coupons_email':
                   save_coupons_email ();
                   exit;

                  case 'set_products_list':
                   save_products_list ();
                   exit;


                  case 'set_products_attributmatrix':
                   save_products_attributmatrix ();
                   exit;


                  case 'set_manufacturers':
                   save_manufacturers ();
                   exit;



                }

              }

            }


  mysql_close();
 }




function save_products ()
{


  $header = '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
            '<STATUS>' . "\n";


      $products_id = db_prepare_input($_POST['products_id']);
      $categories_id = db_prepare_input($_POST['categories_id']);

      $sql_data_array = array();


      //Produkt laden
      $sql = "select * from " . TABLE_PRODUCTS . " where products_id = '" . $products_id . "'";


        //Produkt auf Existenz prüfen ---------------------------------------

          $query_count = mysql_query($sql);
          if ($product = mysql_fetch_array($query_count))
          {
            $exists = 1;
          }
          else
          {
            $exists = 0;
          }


              //Variablen nur überschreiben, wenn diese als Parameter übergeben worden sind
               $products_ean              = db_prepare_input($_POST['products_ean']);
               $products_quantity         = db_prepare_input($_POST['products_quantity']);
               $products_shippingtime     = db_prepare_input($_POST['products_shippingtime']);
               $products_model            = db_prepare_input($_POST['products_model']);
               $products_sort             = db_prepare_input($_POST['products_sort']);
               $products_image            = db_prepare_input($_POST['products_image']);
               $products_price            = db_prepare_input($_POST['products_price']);
               $products_discount_allowed = db_prepare_input($_POST['products_discount_allowed']);
               $products_date_added       = db_prepare_input($_POST['products_date_added']);
               $products_date_available   = db_prepare_input($_POST['products_date_available']);
               $products_weight           = db_prepare_input($_POST['products_weight']);
               $products_status           = db_prepare_input($_POST['products_status']);
               $products_tax_class_id     = db_prepare_input($_POST['products_tax_class_id']);
               $product_template          = db_prepare_input($_POST['product_template']);
               $options_template          = db_prepare_input($_POST['options_template']);
               $manufacturers_id          = db_prepare_input($_POST['manufacturers_id']);
               $products_ordered          = db_prepare_input($_POST['products_ordered']);
               $products_fsk18            = db_prepare_input($_POST['products_fsk18']);
               $products_vpe              = db_prepare_input($_POST['products_vpe']);
               $products_vpe_status       = db_prepare_input($_POST['products_vpe_status']);
               $products_vpe_value        = db_prepare_input($_POST['products_vpe_value']);
               $products_startpage        = db_prepare_input($_POST['products_startpage']);
               $products_startpage_sort   = db_prepare_input($_POST['products_startpage_sort']);


               $sql_data_array = array('products_ean'              => $products_ean,
                                       'products_quantity'         => $products_quantity,
                                       'products_shippingtime'     => $products_shippingtime,
                                       'products_model'            => $products_model,
                                       'products_sort'             => $products_sort,
                                       'products_image'            => $products_image,
                                       'products_price'            => $products_price,
                                       'products_discount_allowed' => $products_discount_allowed,
                                       'products_date_available'   => $products_date_available,
                                       'products_weight'           => $products_weight,
                                       'products_status'           => $products_status,
                                       'products_tax_class_id'     => $products_tax_class_id,
                                       'product_template'          => $product_template,
                                       'options_template'          => $options_template,
                                       'manufacturers_id'          => $manufacturers_id,
                                       'products_ordered'          => $products_ordered,
                                       'products_fsk18'            => $products_fsk18,
                                       'products_vpe'              => $products_vpe,
                                       'products_vpe_status'       => $products_vpe_status,
                                       'products_vpe_value'        => $products_vpe_value,
                                       'products_startpage'        => $products_startpage,
                                       'products_startpage_sort'   => $products_startpage_sort,
                                       'products_last_modified'    => 'now()');


          if ($exists==0)        // Neuanlage
          {

              $insert_sql_data = array('products_date_added'     => 'now()');

              $sql_data_array = array_merge($sql_data_array, $insert_sql_data);
              database_insert(TABLE_PRODUCTS, $sql_data_array);
              $products_id = mysql_insert_id();

          }
          elseif ($exists==1)    //Aktualisieren
          {
              database_insert(TABLE_PRODUCTS, $sql_data_array, 'update', 'products_id = \'' . $products_id . '\'');
          }


       //Produkt muss in den 'products_to_categories' eingetragen werden -------------------------------------------
        mysql_query("replace into " . TABLE_PRODUCTS_TO_CATEGORIES . " set products_id = '" . $products_id . "', categories_id = '" . $categories_id . "'");


       //Beschreibungen und Texte übernehmen -----------------------------------------------------------------------
         $desc_query = mysql_query("select * from " .
                                      TABLE_PRODUCTS_DESCRIPTION . "
                                              where products_id='" . $products_id . "'
                                              and language_id='" . LANG_ID . "'");



          if ($desc = mysql_fetch_array($desc_query))
          {
             $exists = 1;
          }
          else
          {
             $exists = 0;
          }


            // uebergebene Daten einsetzen
            $products_name              = db_prepare_input($_POST['products_name']);
            $products_description       = db_prepare_input($_POST['products_description']);
            $products_short_description = db_prepare_input($_POST['products_short_description']);
            $products_keywords          = db_prepare_input($_POST['products_keywords']);
            $products_meta_title        = db_prepare_input($_POST['products_meta_title']);
            $products_meta_description  = db_prepare_input($_POST['products_meta_description']);
            $products_meta_keywords     = db_prepare_input($_POST['products_meta_keywords']);
            $products_url               = db_prepare_input($_POST['products_url']);
            $products_viewed            = db_prepare_input($_POST['products_viewed']);

            $sql_data_array = array('products_name'              => $products_name,
                                    'products_description'       => $products_description,
                                    'products_short_description' => $products_short_description,
                                    'products_keywords'          => $products_keywords,
                                    'products_meta_title'        => $products_meta_title,
                                    'products_meta_description'  => $products_meta_description,
                                    'products_meta_keywords'     => $products_meta_keywords,
                                    'products_url'               => $products_url,
                                    'products_viewed'            => $products_viewed);


           if ($exists==0)        // Neuanlage
           {

             $insert_sql_data = array('products_id' => $products_id,
                                      'language_id' => LANG_ID);

             $sql_data_array = array_merge($sql_data_array, $insert_sql_data);
             database_insert(TABLE_PRODUCTS_DESCRIPTION, $sql_data_array);

           }
           elseif ($exists==1)    //Aktualisieren
           {
             database_insert(TABLE_PRODUCTS_DESCRIPTION, $sql_data_array, 'update', 'products_id = \'' . $products_id . '\' and language_id = \'' . LANG_ID . '\'');
           }



    $schema = '<STATUS_DATA>' . "\n" .
              '<EXISTS>' . $exists . '</EXISTS>' . "\n" .
              '<PRODUCTS_ID>' . $products_id . '</PRODUCTS_ID>' . "\n" .
              '<PRODUCTS_NAME>' . htmlspecialchars($products_name, ENT_COMPAT,'ISO-8859-1', true) . '</PRODUCTS_NAME>' . "\n" .
              '<ERROR>' . '0' . '</ERROR>' . "\n" .
              '<MESSAGE>' . 'Keine Meldung vorhanden' . '</MESSAGE>' . "\n" .
              '</STATUS_DATA>' . "\n";


  $footer = '</STATUS>' . "\n";

 //Ergebnis als XML ausgeben
   create_xml ($header . $schema . $footer, xml_encoding);

}



function save_products_price ()
{


  $header = '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
            '<STATUS>' . "\n";


      $products_id = db_prepare_input($_POST['products_id']);

      $sql_data_array = array();


      //Produkt laden
      $sql = "select * from " . TABLE_PRODUCTS . " where products_id = '" . $products_id . "'";


        //Produkt auf Existenz prüfen ---------------------------------------

          $query_count = mysql_query($sql);
          if ($product = mysql_fetch_array($query_count))
          {
            $exists = 1;
          }
          else
          {
            $exists = 0;
          }


              //Variablen nur überschreiben, wenn diese als Parameter übergeben worden sind
               $products_price            = db_prepare_input($_POST['products_price']);


               $sql_data_array = array('products_price'            => $products_price);


          if ($exists==0)        // Neuanlage
          {

          }
          elseif ($exists==1)    //Aktualisieren
          {
              database_insert(TABLE_PRODUCTS, $sql_data_array, 'update', 'products_id = \'' . $products_id . '\'');
          }


    $schema = '<STATUS_DATA>' . "\n" .
              '<EXISTS>' . $exists . '</EXISTS>' . "\n" .
              '<PRODUCTS_ID>' . $products_id . '</PRODUCTS_ID>' . "\n" .
              '<ERROR>' . '0' . '</ERROR>' . "\n" .
              '<MESSAGE>' . 'Keine Meldung vorhanden' . '</MESSAGE>' . "\n" .
              '</STATUS_DATA>' . "\n";


  $footer = '</STATUS>' . "\n";

 //Ergebnis als XML ausgeben
   create_xml ($header . $schema . $footer, xml_encoding);

}




function save_products_defaultimage ()
{

  $header = '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
            '<STATUS>' . "\n";


     $products_id = db_prepare_input($_POST['products_id']);


       //Produkt laden
       $sql = "select products_image from " . TABLE_PRODUCTS . " where products_id = '" . $products_id . "'";

          $query_count = mysql_query($sql);
          if ($product = mysql_fetch_array($query_count))
          {
             $exists = 1;
          }
          else
          {
             $exists = 0;
          }


       //Variablen nur überschreiben, wenn diese als Parameter übergeben worden sind
        $products_image        = db_prepare_input($_POST['products_image']);


        $sql_data_array = array('products_image'         => $products_image);


              if ($exists==0)        // Neuanlage
              {
                  //Produkt kann nicht überarbeitet werden
                  $Meldung = "Das Standard-Bild kann nicht gespeichert werden, da der Artikel nicht vorhanden ist";
              }
              elseif ($exists==1)    //Aktualisieren
              {
                  database_insert(TABLE_PRODUCTS, $sql_data_array, 'update', 'products_id = \'' . $products_id . '\'');
                  $Meldung = "Das Standard-Bild wurde erfolgreich aktualisiert";
              }


    $schema = '<STATUS_DATA>' . "\n" .
              '<EXISTS>' . $exists . '</EXISTS>' . "\n" .
              '<PRODUCTS_ID>' . $products_id . '</PRODUCTS_ID>' . "\n" .
              '<ERROR>' . '0' . '</ERROR>' . "\n" .
              '<MESSAGE>' . $Meldung . '</MESSAGE>' . "\n" .
              '</STATUS_DATA>' . "\n";


    $footer = '</STATUS>' . "\n";

  //Ergebnis als XML ausgeben
   create_xml ($header . $schema . $footer, xml_encoding);

}



function save_products_descriptions ()
{


  $header = '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
            '<STATUS>' . "\n";


  $products_id = db_prepare_input($_POST['products_id']);
  $language_id = db_prepare_input($_POST['language_id']);


    //Beschreibungstexte laden
    $sql = "select products_id, language_id, products_name, products_description, products_short_description, products_keywords, products_meta_title, products_meta_description, products_meta_keywords, products_url, products_viewed " .
           "from " . TABLE_PRODUCTS_DESCRIPTION . " where products_id = '" . $products_id . "' and language_id = '" . $language_id . "'";

    $count_query = mysql_query($sql);
    if ($decriptions = mysql_fetch_array($count_query))
    {
      $exists = 1;
    }
    else
    {
      $exists = 0;
    }


        // Variablen nur überschreiben, wenn sie als Parameter übergeben worden sind
         $products_id                  = db_prepare_input($_POST['products_id']);
         $language_id                  = db_prepare_input($_POST['language_id']);
         $products_name                = db_prepare_input($_POST['products_name']);
         $products_description         = db_prepare_input($_POST['products_description']);
         $products_short_description   = db_prepare_input($_POST['products_short_description']);
         $products_keywords            = db_prepare_input($_POST['products_keywords']);
         $products_meta_title          = db_prepare_input($_POST['products_meta_title']);
         $products_meta_description    = db_prepare_input($_POST['products_meta_description']);
         $products_meta_keywords       = db_prepare_input($_POST['products_meta_keywords']);
         $products_url                 = db_prepare_input($_POST['products_url']);
         $products_viewed              = db_prepare_input($_POST['products_viewed']);

         $sql_data_array = array('products_id'                 => $products_id,
                                 'language_id'                 => $language_id,
                                 'products_name'               => $products_name,
                                 'products_description'        => $products_description,
                                 'products_short_description'  => $products_short_description,
                                 'products_keywords'           => $products_keywords,
                                 'products_meta_title'         => $products_meta_title,
                                 'products_meta_description'   => $products_meta_description,
                                 'products_meta_keywords'      => $products_meta_keywords,
                                 'products_url'                => $products_url,
                                 'products_viewed'             => $products_viewed);

      if ($exists==0)        // Neuanlage
      {

         database_insert(TABLE_PRODUCTS_DESCRIPTION, $sql_data_array);

      }
      elseif ($exists==1)    //Aktualisieren
      {
         database_insert(TABLE_PRODUCTS_DESCRIPTION, $sql_data_array, 'update', 'products_id = \'' . $products_id . '\' and language_id = \'' . $language_id . '\'');
      }


    $schema = '<STATUS_DATA>' . "\n" .
              '<EXISTS>' . $exists . '</EXISTS>' . "\n" .
              '<MESSAGE>' . 'Die Beschreibungstexte wurden synchronisiert' . '</MESSAGE>' . "\n" .
              '</STATUS_DATA>' . "\n";


    $footer = '</STATUS>' . "\n";

  //Ergebnis als XML ausgeben
   create_xml ($header . $schema . $footer, xml_encoding);

}



function save_products_personal_offers ()
{

  $header = '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
            '<STATUS>' . "\n";


  $customers_status_id = db_prepare_input($_POST['customers_status_id']);
  $price_id = db_prepare_input($_POST['price_id']);


    //Staffelpreis(e) zum Kundentyp laden
    $sql = "select price_id, products_id, quantity, personal_offer " .
           "from " . TABLE_PERSONAL_OFFERS_BY . $customers_status_id . " where price_id = '" . $price_id . "'";

    $count_query = mysql_query($sql);
    if ($options = mysql_fetch_array($count_query))
    {
      $exists = 1;         //mysql_query("delete from " . TABLE_PERSONAL_OFFERS_BY . $customers_status_id . " where price_id = '" . $price_id . "'");
    }
    else
    {
      $exists = 0;
    }


     // Variablen nur überschreiben, wenn sie als Parameter übergeben worden sind
      $price_id        = db_prepare_input($_POST['price_id']);
      $products_id     = db_prepare_input($_POST['products_id']);
      $quantity        = db_prepare_input($_POST['quantity']);
      $personal_offer  = db_prepare_input($_POST['personal_offer']);


      $sql_data_array = array('price_id'        => $price_id,
                              'products_id'     => $products_id,
                              'quantity'        => $quantity,
                              'personal_offer'  => $personal_offer);


       if ($exists==0)        //Neuanlage
       {
          database_insert(TABLE_PERSONAL_OFFERS_BY . $customers_status_id, $sql_data_array);
          $price_id = mysql_insert_id();
       }

       elseif ($exists==1)    //Aktualisieren

       {
          database_insert(TABLE_PERSONAL_OFFERS_BY . $customers_status_id, $sql_data_array, 'update', 'price_id = \'' . $price_id . '\'');
       }


    $schema = '<STATUS_DATA>' . "\n" .
              '<EXISTS>' . $exists . '</EXISTS>' . "\n" .
              '<PRICE_ID>' . $price_id . '</PRICE_ID>' . "\n" .
              '<MESSAGE>' . 'Keine Meldung vorhanden' . '</MESSAGE>' . "\n" .
              '</STATUS_DATA>' . "\n";


    $footer = '</STATUS>' . "\n";

  //Ergebnis als XML ausgeben
   create_xml ($header . $schema . $footer, xml_encoding);


}


function save_products_attributes ()
{

  $header = '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
            '<STATUS>' . "\n";


  $products_attributes_id = db_prepare_input($_POST['products_attributes_id']);
  $products_attributes_text = db_prepare_input($_POST['products_attributes_text']);


    //Attribute- / Varianten zum Artikel laden
    $sql = "select products_attributes_id, products_id, options_id, options_values_id, options_values_price, price_prefix, attributes_model, attributes_stock, options_values_weight, weight_prefix, sortorder " .
           "from " . TABLE_PRODUCTS_ATTRIBUTES . " where products_attributes_id = '" . $products_attributes_id . "'";

    $count_query = mysql_query($sql);
    if ($options = mysql_fetch_array($count_query))
    {
      $exists = 1;
    }
    else
    {
      $exists = 0;
    }


     //Vorbelegung, da '+' nicht über URL übermittels werden kann...
      //$price_prefix = '+';
      //$weight_prefix = '+';

     // Variablen nur überschreiben, wenn sie als Parameter übergeben worden sind
      $products_attributes_id       = db_prepare_input($_POST['products_attributes_id']);
      $products_id                  = db_prepare_input($_POST['products_id']);
      $options_id                   = db_prepare_input($_POST['options_id']);
      $options_values_id            = db_prepare_input($_POST['options_values_id']);
      $options_values_price         = db_prepare_input($_POST['options_values_price']);
      $price_prefix                 = db_prepare_input($_POST['price_prefix']);
      $attributes_model             = db_prepare_input($_POST['attributes_model']);
      $attributes_stock             = db_prepare_input($_POST['attributes_stock']);
      $options_values_weight        = db_prepare_input($_POST['options_values_weight']);
      $weight_prefix                = db_prepare_input($_POST['weight_prefix']);
      $sortorder                    = db_prepare_input($_POST['sortorder']);


      $sql_data_array = array('products_attributes_id'    => $products_attributes_id,
                              'products_id'               => $products_id,
                              'options_id'                => $options_id,
                              'options_values_id'         => $options_values_id,
                              'options_values_price'      => $options_values_price,
                              'price_prefix'              => $price_prefix,
                              'attributes_model'          => $attributes_model,
                              'attributes_stock'          => $attributes_stock,
                              'options_values_weight'     => $options_values_weight,
                              'weight_prefix'             => $weight_prefix,
                              'sortorder'                 => $sortorder);


       if ($exists==0)        //Neuanlage
       {
          database_insert(TABLE_PRODUCTS_ATTRIBUTES, $sql_data_array);
          $products_attributes_id = mysql_insert_id();
       }
       elseif ($exists==1)    //Aktualisieren
       {
          database_insert(TABLE_PRODUCTS_ATTRIBUTES, $sql_data_array, 'update', 'products_attributes_id = \'' . $products_attributes_id . '\'');
       }


    //PRODUCTS_ATTRIBUTES_VALUE = 8\\1 Stück\+0.0000\+0.0000\27 (0=OPTIONS_ID\1=MODEL\2=TEXT\3=WEIGHT\4=PRICE\5=ATTRIBUTES_ID)
    $schema = '<STATUS_DATA>' . "\n" .
              '<EXISTS>' . $exists . '</EXISTS>' . "\n" .
              '<PRODUCTS_ATTRIBUTES_ID>' . $products_attributes_id . '</PRODUCTS_ATTRIBUTES_ID>' . "\n" .
              '<PRODUCTS_ATTRIBUTES_VALUE>' . $options_values_id . '\\' . $attributes_model . '\\' . $products_attributes_text . '\\' . $weight_prefix . $options_values_weight . '\\' . $price_prefix . $options_values_price . '\\' . $products_attributes_id . '</PRODUCTS_ATTRIBUTES_VALUE>' . "\n" .
              '<MESSAGE>' . 'Keine Meldung vorhanden' . '</MESSAGE>' . "\n" .
              '</STATUS_DATA>' . "\n";


    $footer = '</STATUS>' . "\n";

  //Ergebnis als XML ausgeben
   create_xml ($header . $schema . $footer, xml_encoding);

}


function save_products_categories ()
{

  $header = '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
            '<STATUS>' . "\n";


  $products_id = db_prepare_input($_POST['products_id']);
  $categories_id = db_prepare_input($_POST['categories_id']);


    //Kategorien zum Artikel laden
    $sql = "select products_id, categories_id " .
           "from " . TABLE_PRODUCTS_TO_CATEGORIES . " where products_id = '" . $products_id . "' and categories_id = '" . $categories_id . "'";

    $count_query = mysql_query($sql);
    if ($products_categories = mysql_fetch_array($count_query))
    {
      $exists = 1;
     }
    else
    {
      $exists = 0;
    }


        // Variablen nur überschreiben, wenn sie als Parameter übergeben worden sind
         $categories_id       = db_prepare_input($_POST['categories_id']);

         $sql_data_array = array('categories_id'     => $categories_id);


      if ($exists==0)        // Neuanlage
      {

         $insert_sql_data = array('products_id'    => $products_id);

         $sql_data_array = array_merge($sql_data_array, $insert_sql_data);
         database_insert(TABLE_PRODUCTS_TO_CATEGORIES, $sql_data_array);

      }
      elseif ($exists==1)    //Aktualisieren
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
   create_xml ($header . $schema . $footer, xml_encoding);

}


function save_products_images ()
{

  $header = '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
            '<STATUS>' . "\n";


  $image_id = db_prepare_input($_POST['image_id']);


    //Images laden
    $sql = "select image_id, products_id, image_nr, image_name from " . TABLE_PRODUCTS_IMAGES . " where image_id = '" . $image_id . "'";

    $count_query = mysql_query($sql);
    if ($images = mysql_fetch_array($count_query))
    {
      $exists = 1;
    }
    else
    {
      $exists = 0;
    }


        // Variablen nur überschreiben, wenn sie als Parameter übergeben worden sind
         $products_id     = db_prepare_input($_POST['products_id']);
         $image_nr        = db_prepare_input($_POST['image_nr']);
         $image_name      = db_prepare_input($_POST['image_name']);

         $sql_data_array = array('products_id'  => $products_id,
                                 'image_nr'     => $image_nr,
                                 'image_name'   => $image_name);


      if ($exists==0)        // Neuanlage
      {
         database_insert(TABLE_PRODUCTS_IMAGES, $sql_data_array);
         $image_id = mysql_insert_id();
      }
      elseif ($exists==1)    //Aktualisieren
      {
         database_insert(TABLE_PRODUCTS_IMAGES, $sql_data_array, 'update', 'image_id = \'' . $image_id . '\'');
      }


    $schema = '<STATUS_DATA>' . "\n" .
              '<EXISTS>' . $exists . '</EXISTS>' . "\n" .
              '<IMAGE_ID>' . $image_id . '</IMAGE_ID>' . "\n" .
              '<MESSAGE>' . 'Keine Meldung vorhanden' . '</MESSAGE>' . "\n" .
              '</STATUS_DATA>' . "\n";


    $footer = '</STATUS>' . "\n";

  //Ergebnis als XML ausgeben
   create_xml ($header . $schema . $footer, xml_encoding);

}


function save_products_stockvalues ()
{

  $header = '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
            '<STATUS>' . "\n";

  $products_id = db_prepare_input($_POST['products_id']);

  //Produkt laden
  $sql = "select products_quantity, products_shippingtime, products_status from " . TABLE_PRODUCTS . " where products_id = '" . $products_id . "'";


  $query_count = mysql_query($sql);
  if ($product = mysql_fetch_array($query_count))
  {
     $exists = 1;
  }
  else
  {
     $exists = 0;
  }


      //Variablen nur überschreiben, wenn diese als Parameter übergeben worden sind
       $products_quantity         = db_prepare_input($_POST['products_quantity']);
       $products_shippingtime     = db_prepare_input($_POST['products_shippingtime']);
       $products_status           = db_prepare_input($_POST['products_status']);


      //Nur Bestand schreiben, wenn Liefertermin und Artikelstatus leer sind, ansonsten alles übertragen und setzen
       if (isset($_POST['products_shippingtime']) && ($products_shippingtime!=''))
       {

       $sql_data_array = array('products_quantity'         => $products_quantity,
                               'products_shippingtime'     => $products_shippingtime,
                               'products_status'           => $products_status);
       }
       else
       {

       $sql_data_array = array('products_quantity'         => $products_quantity);
       }



      if ($exists==0)        // Neuanlage
      {
          //Produkt kann nicht überarbeitet werden
          $Meldung = "Bestandswerte können nicht aktualisiert werden, da der Artikel nicht vorhanden ist";
      }
      elseif ($exists==1)    //Aktualisieren
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
   create_xml ($header . $schema . $footer, xml_encoding);

}


function save_products_variants_stockvalues ()
{

  $header = '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
            '<STATUS>' . "\n";

  $products_id = db_prepare_input($_POST['products_id']);
  $token_count = db_prepare_input($_POST['token_count']);                  //Anzahl der Merkmale
  $options_values_id_1 = db_prepare_input($_POST['options_values_id_1']);  //Ausprägung 1 (Matrix1)
  $options_values_id_2 = db_prepare_input($_POST['options_values_id_2']);  //Ausprägung 2 (Matrix2)


   switch ($token_count)
       {

       case '1': // 1 Merkmal

          //Produkt-Attribute laden
          $sql = "select products_id, options_values_id, attributes_stock from " . TABLE_PRODUCTS_ATTRIBUTES . " where products_id = '" . $products_id . "' and options_values_id = '" . $options_values_id_1 . "'";

          $query_count = mysql_query($sql);
          if ($product = mysql_fetch_array($query_count))
          {
             $exists = 1;
          }
          else
          {
             $exists = 0;
          }

              //Variablen nur überschreiben, wenn diese als Parameter übergeben worden sind
               $options_values_id_1      = db_prepare_input($_POST['options_values_id_1']);
               $attributes_stock         = db_prepare_input($_POST['attributes_stock']);

               $sql_data_array = array('options_values_id'        => $options_values_id_1,
                                       'attributes_stock'         => $attributes_stock);


              if ($exists==0)        // Neuanlage
              {
                 $Meldung = "Bestandswerte können nicht aktualisiert werden, da der Artikel nicht vorhanden ist";
              }
              elseif ($exists==1)    //Aktualisieren
              {
                 $Meldung = "Bestandswerte wurden erfolgreich aktualisiert";
                 database_insert(TABLE_PRODUCTS_ATTRIBUTES, $sql_data_array, 'update', 'products_id = \'' . $products_id . '\' and options_values_id = \'' . $options_values_id_1 . '\'');
              }

         break;


       case '2':  //2 Merkmale

          //Produkt-Attribut-Matrix laden
          $sql = "select products_id, matrix1, matrix2, quantity from " . TABLE_PRODUCTS_ATTRIBUTES_MATRIX . " where products_id = '" . $products_id . "' and matrix1 = '" . $options_values_id_1 . "' and matrix2 = '" . $options_values_id_2 . "'";

          $query_count = mysql_query($sql);
          if ($product = mysql_fetch_array($query_count))
          {
            $exists = 1;
          }
          else
          {
            $exists = 0;
          }

              //Variablen nur überschreiben, wenn diese als Parameter übergeben worden sind
               $options_values_id_1      = db_prepare_input($_POST['options_values_id_1']);
               $options_values_id_2      = db_prepare_input($_POST['options_values_id_2']);
               $quantity                 = db_prepare_input($_POST['quantity']);

               $sql_data_array = array('matrix1'        => $options_values_id_1,
                                       'matrix2'        => $options_values_id_2,
                                       'quantity'       => $quantity);


              if ($exists==0)        // Neuanlage
              {
                  $insert_sql_data = array('products_id'    => $products_id);

                  $sql_data_array = array_merge($sql_data_array, $insert_sql_data);
                  database_insert(TABLE_PRODUCTS_ATTRIBUTES_MATRIX, $sql_data_array);
                  $Meldung = "Bestandswerte der Attribut-Matrix wurden aktualisiert";
              }
              elseif ($exists==1)    //Aktualisieren
              {
                  database_insert(TABLE_PRODUCTS_ATTRIBUTES_MATRIX, $sql_data_array, 'update', 'products_id = \'' . $products_id . '\' and matrix1 = \'' . $options_values_id_1 . '\' and matrix2 = \'' . $options_values_id_2 . '\'');
                  $Meldung = "Bestandswerte wurden erfolgreich aktualisiert";
              }

         break;

       }


    $schema = '<STATUS_DATA>' . "\n" .
              '<EXISTS>' . $exists . '</EXISTS>' . "\n" .
              '<PRODUCTS_ID>' . $products_id . '</PRODUCTS_ID>' . "\n" .
              '<ERROR>' . '0' . '</ERROR>' . "\n" .
              '<MESSAGE>' . $Meldung . '</MESSAGE>' . "\n" .
              '</STATUS_DATA>' . "\n";


    $footer = '</STATUS>' . "\n";

  //Ergebnis als XML ausgeben
   create_xml ($header . $schema . $footer, xml_encoding);

}


function save_products_options ()
{

  $header = '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
            '<STATUS>' . "\n";


  $products_options_id = db_prepare_input($_POST['products_options_id']);


    //Merkmale zum Artikel laden
    $sql = "select products_options_id, products_options_name, language_id from " . TABLE_PRODUCTS_OPTIONS . " where products_options_id = '" . $products_options_id . "' and language_id = " . LANG_ID;

    $count_query = mysql_query($sql);
    if ($options = mysql_fetch_array($count_query))
    {
      $exists = 1;
    }
    else
    {
      $exists = 0;
    }


     // Variablen nur überschreiben, wenn sie als Parameter übergeben worden sind
      $products_options_id       = db_prepare_input($_POST['products_options_id']);
      $products_options_name     = db_prepare_input($_POST['products_options_name']);


      $sql_data_array = array('products_options_id'      => $products_options_id,
                              'products_options_name'    => $products_options_name);


       if ($exists==0)        //Neuanlage
       {

          $insert_sql_data = array('language_id' => LANG_ID);
          $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

          database_insert(TABLE_PRODUCTS_OPTIONS, $sql_data_array);
          //$products_options_id = mysql_insert_id();

       }

       elseif ($exists==1)    //Aktualisieren

       {
          database_insert(TABLE_PRODUCTS_OPTIONS, $sql_data_array, 'update', 'products_options_id = \'' . $products_options_id . '\' and language_id = \'' . LANG_ID . '\'');
       }


    $schema = '<STATUS_DATA>' . "\n" .
              '<EXISTS>' . $exists . '</EXISTS>' . "\n" .
              '<PRODUCTS_OPTIONS_ID>' . $products_options_id . '</PRODUCTS_OPTIONS_ID>' . "\n" .
              '<PRODUCTS_OPTIONS_NAME>' . htmlspecialchars($products_options_name, ENT_COMPAT,'ISO-8859-1', true) . '</PRODUCTS_OPTIONS_NAME>' . "\n" .
              '<MESSAGE>' . 'Keine Meldung vorhanden' . '</MESSAGE>' . "\n" .
              '</STATUS_DATA>' . "\n";


    $footer = '</STATUS>' . "\n";

  //Ergebnis als XML ausgeben
   create_xml ($header . $schema . $footer, xml_encoding);

}


function save_products_options_values ()
{

  $header = '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
            '<STATUS>' . "\n";


  $products_options_values_id = db_prepare_input($_POST['products_options_values_id']);
  $products_options_id = db_prepare_input($_POST['products_options_id']);
  $products_options_values_to_products_options_id = db_prepare_input($_POST['products_options_values_to_products_options_id']);


    //Ausprägungen zum Merkmal laden
    $sql = "select products_options_values_id, products_options_values_name, language_id from " . TABLE_PRODUCTS_OPTIONS_VALUES . " where products_options_values_id = '" . $products_options_values_id . "' and language_id = " . LANG_ID;

    $count_query = mysql_query($sql);
    if ($options = mysql_fetch_array($count_query))
    {
      $exists = 1;
    }
    else
    {
      $exists = 0;
    }


    // Merkmal muss mit den Ausprägungen verknüpft werden -------------------------------------------
     mysql_query("replace into " . TABLE_PRODUCTS_OPTIONS_VALUES_TO_PRODUCTS_OPTIONS . " set products_options_values_to_products_options_id = '" . $products_options_values_to_products_options_id . "', products_options_id = '" . $products_options_id . "', products_options_values_id = '" . $products_options_values_id . "'");


     // Variablen nur überschreiben, wenn sie als Parameter übergeben worden sind
      $products_options_values_id       = db_prepare_input($_POST['products_options_values_id']);
      $products_options_values_name     = db_prepare_input($_POST['products_options_values_name']);


      $sql_data_array = array('products_options_values_id'      => $products_options_values_id,
                              'products_options_values_name'    => $products_options_values_name);


       if ($exists==0)        //Neuanlage
       {

          $insert_sql_data = array('language_id' => LANG_ID);
          $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

          database_insert(TABLE_PRODUCTS_OPTIONS_VALUES, $sql_data_array);
          //$products_options_values_id = mysql_insert_id();

       }

       elseif ($exists==1)    //Aktualisieren

       {
          database_insert(TABLE_PRODUCTS_OPTIONS_VALUES, $sql_data_array, 'update', 'products_options_values_id = \'' . $products_options_values_id . '\' and language_id = \'' . LANG_ID . '\'');
       }


    $schema = '<STATUS_DATA>' . "\n" .
              '<EXISTS>' . $exists . '</EXISTS>' . "\n" .
              '<PRODUCTS_OPTIONS_VALUES_ID>' . $products_options_values_id . '</PRODUCTS_OPTIONS_VALUES_ID>' . "\n" .
              '<PRODUCTS_OPTIONS_VALUES_NAME>' . htmlspecialchars($products_options_values_name, ENT_COMPAT,'ISO-8859-1', true) . '</PRODUCTS_OPTIONS_VALUES_NAME>' . "\n" .
              '<MESSAGE>' . 'Keine Meldung vorhanden' . '</MESSAGE>' . "\n" .
              '</STATUS_DATA>' . "\n";


    $footer = '</STATUS>' . "\n";

  //Ergebnis als XML ausgeben
   create_xml ($header . $schema . $footer, xml_encoding);

}


function save_products_specials ()
{

  $header = '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
            '<STATUS>' . "\n";


  $specials_id = db_prepare_input($_POST['specials_id']);


    //Sonderpreise laden
    $sql = "select specials_id, products_id, specials_quantity, specials_new_products_price, specials_date_added, specials_last_modified, expires_date, date_status_change, status from " . TABLE_SPECIALS . " where specials_id = '" . $specials_id . "'";

    $count_query = mysql_query($sql);
    if ($specials = mysql_fetch_array($count_query))
    {
      $exists = 1;
    }
    else
    {
      $exists = 0;
    }


        // Variablen nur überschreiben, wenn sie als Parameter übergeben worden sind
         $products_id                  = db_prepare_input($_POST['products_id']);
         $specials_quantity            = db_prepare_input($_POST['specials_quantity']);
         $specials_new_products_price  = db_prepare_input($_POST['specials_new_products_price']);
         $expires_date                 = db_prepare_input($_POST['expires_date']);
         $date_status_change           = db_prepare_input($_POST['date_status_change']);
         $status                       = db_prepare_input($_POST['status']);

         $sql_data_array = array('products_id'                 => $products_id,
                                 'specials_quantity'           => $specials_quantity,
                                 'specials_new_products_price' => $specials_new_products_price,
                                 'expires_date'                => $expires_date,
                                 'date_status_change'          => $date_status_change,
                                 'status'                      => $status);


      if ($exists==0)        // Neuanlage
      {

          $insert_sql_data = array('specials_id'            => $specials_id,
                                   'specials_date_added'    => 'now()',
                                   'specials_last_modified' => 'now()');

          $sql_data_array = array_merge($sql_data_array, $insert_sql_data);
          database_insert(TABLE_SPECIALS, $sql_data_array);

          if (isset($_POST['specials_id']) && ($specials_id!=''))
          {
           $specials_id = db_prepare_input($_POST['specials_id']);
          }
          else
          {
           $specials_id = mysql_insert_id();
          }

      }
      elseif ($exists==1)    //Aktualisieren
      {
          database_insert(TABLE_SPECIALS, $sql_data_array, 'update', 'specials_id = \'' . $specials_id . '\'');
      }


    $schema = '<STATUS_DATA>' . "\n" .
              '<EXISTS>' . $exists . '</EXISTS>' . "\n" .
              '<SPECIALS_ID>' . $specials_id . '</SPECIALS_ID>' . "\n" .
              '<MESSAGE>' . 'Keine Meldung vorhanden' . '</MESSAGE>' . "\n" .
              '</STATUS_DATA>' . "\n";


    $footer = '</STATUS>' . "\n";

  //Ergebnis als XML ausgeben
   create_xml ($header . $schema . $footer, xml_encoding);

}



function save_products_vpe ()
{

  $header = '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
            '<STATUS>' . "\n";


  $products_vpe_id = db_prepare_input($_POST['products_vpe_id']);


    //Verpackungseinheiten laden
    $sql = "select products_vpe_id, products_vpe_name, language_id from " . TABLE_PRODUCTS_VPE . " where products_vpe_id = '" . $products_vpe_id . "' and language_id = " . LANG_ID;

    $count_query = mysql_query($sql);
    if ($products_vpe = mysql_fetch_array($count_query))
    {
      $exists = 1;
    }
    else
    {
      $exists = 0;
    }


        // Variablen nur überschreiben, wenn sie als Parameter übergeben worden sind
         $products_vpe_name       = db_prepare_input($_POST['products_vpe_name']);

         $sql_data_array = array('products_vpe_name'     => $products_vpe_name);


      if ($exists==0)        // Neuanlage
      {

          $insert_sql_data = array('products_vpe_id'    => $products_vpe_id,
                                   'language_id'        => LANG_ID);

          $sql_data_array = array_merge($sql_data_array, $insert_sql_data);
          database_insert(TABLE_PRODUCTS_VPE, $sql_data_array);

      }
      elseif ($exists==1)    //Aktualisieren
      {
          database_insert(TABLE_PRODUCTS_VPE, $sql_data_array, 'update', 'products_vpe_id = \'' . $products_vpe_id . '\' and language_id = \'' . LANG_ID . '\'');
      }


    $schema = '<STATUS_DATA>' . "\n" .
              '<EXISTS>' . $exists . '</EXISTS>' . "\n" .
              '<PRODUCTS_VPE_ID>' . $products_vpe_id . '</PRODUCTS_VPE_ID>' . "\n" .
              '<MESSAGE>' . 'Keine Meldung vorhanden' . '</MESSAGE>' . "\n" .
              '</STATUS_DATA>' . "\n";


    $footer = '</STATUS>' . "\n";

  //Ergebnis als XML ausgeben
   create_xml ($header . $schema . $footer, xml_encoding);

}



function save_customers ()
{

  $header = '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
            '<STATUS>' . "\n";


  $customers_id = db_prepare_input($_POST['customers_id']);
  $address_book_id = db_prepare_input($_POST['address_book_id']);


    $sql = "select * from " . TABLE_CUSTOMERS . " where customers_id = '" . $customers_id . "'";


    $count_query = mysql_query($sql);
    if ($customers = mysql_fetch_array($count_query))
    {
      $exists = 1;
    }
    else
    {
      $exists = 0;
    }


      // Variablen nur überschreiben, wenn sie als Parameter übergeben worden sind
       $customers_cid            = db_prepare_input($_POST['customers_cid']);
       $customers_vat_id         = db_prepare_input($_POST['customers_vat_id']);
       $customers_status         = db_prepare_input($_POST['customers_status']);
       $customers_gender         = db_prepare_input($_POST['customers_gender']);
       $customers_firstname      = db_prepare_input($_POST['customers_firstname']);
       $customers_lastname       = db_prepare_input($_POST['customers_lastname']);
       $customers_email_address  = db_prepare_input($_POST['customers_email_address']);
       $customers_password       = db_prepare_input($_POST['customers_password']);
       $customers_telephone      = db_prepare_input($_POST['customers_telephone']);
       $customers_fax            = db_prepare_input($_POST['customers_fax']);

       $sql_data_array = array('customers_cid'              => $customers_cid,
                               'customers_vat_id'           => $customers_vat_id,
                               'customers_status'           => $customers_status,
                               'customers_gender'           => $customers_gender,
                               'customers_firstname'        => $customers_firstname,
                               'customers_lastname'         => $customers_lastname,
                               'customers_email_address'    => $customers_email_address,
                               'customers_password'         => $customers_password,
                               'customers_telephone'        => $customers_telephone,
                               'customers_fax'              => $customers_fax);


      if ($exists==0)        // Neuanlage
      {
          $insert_sql_data = array('customers_id'              => $customers_id,
                                   'customers_date_added'      => 'now()');

          $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

          database_insert(TABLE_CUSTOMERS, $sql_data_array);
          $customers_id  = mysql_insert_id();

      }
      elseif ($exists==1)    //Aktualisieren
      {
          database_insert(TABLE_CUSTOMERS, $sql_data_array, 'update', 'customers_id = \'' . $customers_id . '\'');
      }



      // Anschrift zum Kunden laden/speichern
      $addressbook_query = mysql_query("select * from " . TABLE_ADDRESS_BOOK . " where customers_id = '" . $customers_id . "'");
      if ($addressbook = mysql_fetch_array($addressbook_query))
      {
        $exists = 1;
      }
      else
      {
        $exists = 0;
      }


      // Variablen nur überschreiben, wenn sie als Parameter übergeben worden sind
      $entry_gender         = db_prepare_input($_POST['entry_gender']);
      $entry_company        = db_prepare_input($_POST['entry_company']);
      $entry_firstname      = db_prepare_input($_POST['entry_firstname']);
      $entry_lastname       = db_prepare_input($_POST['entry_lastname']);
      $entry_street_address = db_prepare_input($_POST['entry_street_address']);
      $entry_suburb         = db_prepare_input($_POST['entry_suburb']);
      $entry_postcode       = db_prepare_input($_POST['entry_postcode']);
      $entry_city           = db_prepare_input($_POST['entry_city']);
      $entry_state          = db_prepare_input($_POST['entry_state']);
      $entry_country_id     = db_prepare_input($_POST['entry_country_id']);
      $entry_zone_id        = db_prepare_input($_POST['entry_zone_id']);



        $sql_data_array = array('entry_gender'         => $entry_gender,
                                'entry_company'        => $entry_company,
                                'entry_firstname'      => $entry_firstname,
                                'entry_lastname'       => $entry_lastname,
                                'entry_street_address' => $entry_street_address,
                                'entry_suburb'         => $entry_suburb,
                                'entry_postcode'       => $entry_postcode,
                                'entry_city'           => $entry_city,
                                'entry_state'          => $entry_state,
                                'entry_country_id'     => $entry_country_id,
                                'entry_zone_id'        => $entry_zone_id);

      if ($exists==0)        // Neuanlage
      {
        $insert_sql_data = array('customers_id'        => $customers_id,
                                 'address_date_added'  => 'now()');

        $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

        database_insert(TABLE_ADDRESS_BOOK, $sql_data_array);
        $address_book_id = mysql_insert_id();

      }
      elseif ($exists==1)    //Aktualisieren
      {
        database_insert(TABLE_ADDRESS_BOOK, $sql_data_array, 'update', 'customers_id = \'' . $customers_id . '\'');
      }


    $schema = '<STATUS_DATA>' . "\n" .
              '<EXISTS>' . $exists . '</EXISTS>' . "\n" .
              '<CUSTOMERS_ID>' . $customers_id . '</CUSTOMERS_ID>' . "\n" .
              '<ADDRESS_BOOK_ID>' . $address_book_id . '</ADDRESS_BOOK_ID>' . "\n" .
              '<MESSAGE>' . 'Keine Meldung vorhanden' . '</MESSAGE>' . "\n" .
              '</STATUS_DATA>' . "\n";


    $footer = '</STATUS>' . "\n";

  //Ergebnis als XML ausgeben
   create_xml ($header . $schema . $footer, xml_encoding);

}


function save_customers_defaultaddress ()
{

  $header = '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
            '<STATUS>' . "\n";


     $customers_id = db_prepare_input($_POST['customers_id']);


       //Kunde laden
        $sql = "select * from " . TABLE_CUSTOMERS . " where customers_id = '" . $customers_id . "'";

          $query_count = mysql_query($sql);
          if ($product = mysql_fetch_array($query_count))
          {
             $exists = 1;
          }
          else
          {
             $exists = 0;
          }


       //Variablen nur überschreiben, wenn diese als Parameter übergeben worden sind
        $customers_default_address_id        = db_prepare_input($_POST['customers_default_address_id']);


        $sql_data_array = array('customers_default_address_id'         => $customers_default_address_id);


              if ($exists==0)        // Neuanlage
              {
                  //Kunde kann nicht überarbeitet werden
                  $Meldung = "Die Standardversandadresse kann nicht gespeichert werden, da der Kunde nicht vorhanden ist";
              }
              elseif ($exists==1)    //Aktualisieren
              {
                  database_insert(TABLE_CUSTOMERS, $sql_data_array, 'update', 'customers_id = \'' . $customers_id . '\'');
                  $Meldung = "Die Standardversandadresse wurde erfolgreich aktualisiert";
              }


    $schema = '<STATUS_DATA>' . "\n" .
              '<EXISTS>' . $exists . '</EXISTS>' . "\n" .
              '<CUSTOMERS_ID>' . $customers_id . '</CUSTOMERS_ID>' . "\n" .
              '<ERROR>' . '0' . '</ERROR>' . "\n" .
              '<MESSAGE>' . $Meldung . '</MESSAGE>' . "\n" .
              '</STATUS_DATA>' . "\n";


    $footer = '</STATUS>' . "\n";

  //Ergebnis als XML ausgeben
   create_xml ($header . $schema . $footer, xml_encoding);

}


function save_customers_info ()
{

  $header = '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
            '<STATUS>' . "\n";


     $customers_id = db_prepare_input($_POST['customers_id']);


       //Kunde laden
        $sql = "select * from " . TABLE_CUSTOMERS_INFO . " where customers_info_id = '" . $customers_id . "'";

          $query_count = mysql_query($sql);
          if ($product = mysql_fetch_array($query_count))
          {
             $exists = 1;
          }
          else
          {
             $exists = 0;
          }


       //Variablen nur überschreiben, wenn diese als Parameter übergeben worden sind
        $customers_info_date_of_last_logon        = db_prepare_input($_POST['customers_info_date_of_last_logon']);
        $customers_info_number_of_logons          = db_prepare_input($_POST['customers_info_number_of_logons']);

        $sql_data_array = array('customers_info_date_of_last_logon'     => $customers_info_date_of_last_logon,
                                'customers_info_number_of_logons'       => $customers_info_number_of_logons);


              if ($exists==0)        // Neuanlage
              {

                   $insert_sql_data = array('customers_info_id'                       => $customers_id,
                                            'customers_info_date_account_created'     => 'now()');


                   $sql_data_array = array_merge($sql_data_array, $insert_sql_data);
                   database_insert(TABLE_CUSTOMERS_INFO, $sql_data_array);

                   if (isset($_POST['customers_id']) && ($customers_id!=''))
                   {
                    $customers_id = db_prepare_input($_POST['customers_id']);
                   }
                   else
                   {
                    $customers_id = mysql_insert_id();
                   }


                  //Kunde kann nicht überarbeitet werden
                  $Meldung = "Die Kunden-Info wurde erfolgreich gespeichert";

              }
              elseif ($exists==1)    //Aktualisieren
              {
                  database_insert(TABLE_CUSTOMERS_INFO, $sql_data_array, 'update', 'customers_info_id = \'' . $customers_id . '\'');
                  $Meldung = "Die Kunden-Info wurde erfolgreich aktualisiert";
              }


    $schema = '<STATUS_DATA>' . "\n" .
              '<EXISTS>' . $exists . '</EXISTS>' . "\n" .
              '<CUSTOMERS_INFO_ID>' . $customers_id . '</CUSTOMERS_INFO_ID>' . "\n" .
              '<ERROR>' . '0' . '</ERROR>' . "\n" .
              '<MESSAGE>' . $Meldung . '</MESSAGE>' . "\n" .
              '</STATUS_DATA>' . "\n";


    $footer = '</STATUS>' . "\n";

  //Ergebnis als XML ausgeben
   create_xml ($header . $schema . $footer, xml_encoding);

}




function save_customers_status ()
{

  $header = '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
            '<STATUS>' . "\n";

  $customers_status_id = db_prepare_input($_POST['customers_status_id']);


    //Kundengruppen laden
    $sql = "select customers_status_id, customers_status_name, customers_status_image, language_id " .
           "from " . TABLE_CUSTOMERS_STATUS . " where customers_status_id = '" . $customers_status_id . "' and language_id = " . LANG_ID;

    $count_query = mysql_query($sql);
    if ($customers = mysql_fetch_array($count_query))
    {
      $exists = 1;
    }
    else
    {
      $exists = 0;
    }


        // Variablen nur überschreiben, wenn sie als Parameter übergeben worden sind
         $customers_status_name    = db_prepare_input($_POST['customers_status_name']);
         $customers_status_image   = db_prepare_input($_POST['customers_status_image']);

         $sql_data_array = array('customers_status_name'     => $customers_status_name,
                                 'customers_status_image'    => $customers_status_image);


      if ($exists==0)        // Neuanlage
      {

          $insert_sql_data = array('customers_status_id'     => $customers_status_id,
                                   'language_id'             => LANG_ID);

          $sql_data_array = array_merge($sql_data_array, $insert_sql_data);
          database_insert(TABLE_CUSTOMERS_STATUS, $sql_data_array);

          if (isset($_POST['customers_status_id']) && ($customers_status_id!=''))
          {
           $customers_status_id = db_prepare_input($_POST['customers_status_id']);
          }
          else
          {
           $customers_status_id = mysql_insert_id();
          }


      }
      elseif ($exists==1)    //Aktualisieren
      {
          database_insert(TABLE_CUSTOMERS_STATUS, $sql_data_array, 'update', 'customers_status_id = \'' . $customers_status_id . '\' and language_id = \'' . LANG_ID . '\'');
      }


    $schema = '<STATUS_DATA>' . "\n" .
              '<EXISTS>' . $exists . '</EXISTS>' . "\n" .
              '<CUSTOMERS_STATUS_ID>' . $customers_status_id . '</CUSTOMERS_STATUS_ID>' . "\n" .
              '<MESSAGE>' . 'Keine Meldung vorhanden' . '</MESSAGE>' . "\n" .
              '</STATUS_DATA>' . "\n";


    $footer = '</STATUS>' . "\n";

  //Ergebnis als XML ausgeben
   create_xml ($header . $schema . $footer, xml_encoding);

}


function save_orders_status ()
{

  $header = '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
            '<STATUS>' . "\n";


  $orders_status_id = db_prepare_input($_POST['orders_status_id']);


    $sql = "select orders_status_id, orders_status_name, language_id from " . TABLE_ORDERS_STATUS . " where orders_status_id = '" . $orders_status_id . "' and language_id = " . LANG_ID;

    $count_query = mysql_query($sql);
    if ($orders = mysql_fetch_array($count_query))
    {
      $exists = 1;
    }
    else
    {
      $exists = 0;
    }


     // Variablen nur überschreiben, wenn sie als Parameter übergeben worden sind
      $orders_status_name       = db_prepare_input($_POST['orders_status_name']);

      $sql_data_array = array('orders_status_name'     => $orders_status_name);


      if ($exists==0)
      {

          $insert_sql_data = array('orders_status_id'    => $orders_status_id,
                                   'language_id'         => LANG_ID);

          $sql_data_array = array_merge($sql_data_array, $insert_sql_data);
          database_insert(TABLE_ORDERS_STATUS, $sql_data_array);

      }
      elseif ($exists==1)    //Aktualisieren
      {
          database_insert(TABLE_ORDERS_STATUS, $sql_data_array, 'update', 'orders_status_id = \'' . $orders_status_id . '\' and language_id = \'' . LANG_ID . '\'');
      }


    $schema = '<STATUS_DATA>' . "\n" .
              '<EXISTS>' . $exists . '</EXISTS>' . "\n" .
              '<ORDERS_STATUS_ID>' . $orders_status_id . '</ORDERS_STATUS_ID>' . "\n" .
              '<MESSAGE>' . 'Keine Meldung vorhanden' . '</MESSAGE>' . "\n" .
              '</STATUS_DATA>' . "\n";


    $footer = '</STATUS>' . "\n";

  //Ergebnis als XML ausgeben
   create_xml ($header . $schema . $footer, xml_encoding);

}



function save_shipping_status ()
{

  $header = '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
            '<STATUS>' . "\n";


  $shipping_status_id = db_prepare_input($_POST['shipping_status_id']);


    $sql = "select shipping_status_id, shipping_status_name, shipping_status_image, language_id from " . TABLE_SHIPPING_STATUS . " where shipping_status_id = '" . $shipping_status_id . "' and language_id = " . LANG_ID;

    $count_query = mysql_query($sql);
    if ($shipping = mysql_fetch_array($count_query))
    {
      $exists = 1;
    }
    else
    {
      $exists = 0;
    }


     // Variablen nur überschreiben, wenn sie als Parameter übergeben worden sind
      $shipping_status_name       = db_prepare_input($_POST['shipping_status_name']);
      $shipping_status_image      = db_prepare_input($_POST['shipping_status_image']);

      $sql_data_array = array('shipping_status_name'     => $shipping_status_name,
                              'shipping_status_image'    => $shipping_status_image);


      if ($exists==0)        // Neuanlage
      {

          $insert_sql_data = array('shipping_status_id'    => $shipping_status_id,
                                   'language_id'           => LANG_ID);

          $sql_data_array = array_merge($sql_data_array, $insert_sql_data);
          database_insert(TABLE_SHIPPING_STATUS, $sql_data_array);

      }
      elseif ($exists==1)    //Aktualisieren
      {
          database_insert(TABLE_SHIPPING_STATUS, $sql_data_array, 'update', 'shipping_status_id = \'' . $shipping_status_id . '\' and language_id = \'' . LANG_ID . '\'');
      }


    $schema = '<STATUS_DATA>' . "\n" .
              '<EXISTS>' . $exists . '</EXISTS>' . "\n" .
              '<SHIPPING_STATUS_ID>' . $shipping_status_id . '</SHIPPING_STATUS_ID>' . "\n" .
              '<MESSAGE>' . 'Keine Meldung vorhanden' . '</MESSAGE>' . "\n" .
              '</STATUS_DATA>' . "\n";


    $footer = '</STATUS>' . "\n";

  //Ergebnis als XML ausgeben
   create_xml ($header . $schema . $footer, xml_encoding);

}


function save_languages ()
{

  $header = '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
            '<STATUS>' . "\n";


  $languages_id = db_prepare_input($_POST['languages_id']);



    $sql = "select languages_id, name, code, image, directory, sort_order, language_charset " .
           "from " . TABLE_LANGUAGES . " where languages_id = '" . $languages_id . "'";

    $count_query = mysql_query($sql);
    if ($languages = mysql_fetch_array($count_query))
    {
      $exists = 1;
    }
    else
    {
      $exists = 0;
    }


     // Variablen nur überschreiben, wenn sie als Parameter übergeben worden sind
      $name              = db_prepare_input($_POST['name']);
      $code              = db_prepare_input($_POST['code']);
      $image             = db_prepare_input($_POST['image']);
      $directory         = db_prepare_input($_POST['directory']);
      $sort_order        = db_prepare_input($_POST['sort_order']);
      $language_charset  = db_prepare_input($_POST['language_charset']);

      $sql_data_array = array('name'              => $name,
                              'code'              => $code,
                              'image'             => $image,
                              'directory'         => $directory,
                              'sort_order'        => $sort_order,
                              'language_charset'  => $language_charset);


      if ($exists==0)        // Neuanlage
      {

          $insert_sql_data = array('languages_id'      => $languages_id);

          $sql_data_array = array_merge($sql_data_array, $insert_sql_data);
          database_insert(TABLE_LANGUAGES, $sql_data_array);

          if (isset($_POST['languages_id']) && ($languages_id!=''))
          {
           $languages_id = db_prepare_input($_POST['languages_id']);
          }
          else
          {
           $languages_id = mysql_insert_id();
          }


      }
      elseif ($exists==1)    //Aktualisieren
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
   create_xml ($header . $schema . $footer, xml_encoding);

}



function save_categories ()
{

  $header = '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
            '<STATUS>' . "\n";


  $categories_id = db_prepare_input($_POST['categories_id']);
  $parent_id = db_prepare_input($_POST['parent_id']);


    $sql = "select categories_id, categories_image, parent_id, categories_status, categories_template, listing_template, sort_order, products_sorting, products_sorting2, date_added " .
           "from " . TABLE_CATEGORIES . " where categories_id = '" . $categories_id . "'";


    $count_query = mysql_query($sql);
    if ($categorie = mysql_fetch_array($count_query))
    {
      $exists = 1;
    }
    else
    {
      $exists = 0;
    }


      // Variablen nur überschreiben, wenn sie als Parameter übergeben worden sind
       $categories_image    = db_prepare_input($_POST['categories_image']);
       $parent_id           = db_prepare_input($_POST['parent_id']);
       $categories_status   = db_prepare_input($_POST['categories_status']);
       $categories_template = db_prepare_input($_POST['categories_template']);
       $listing_template    = db_prepare_input($_POST['listing_template']);
       $sort_order          = db_prepare_input($_POST['sort_order']);
       $products_sorting    = db_prepare_input($_POST['products_sorting']);
       $products_sorting2   = db_prepare_input($_POST['products_sorting2']);
       $date_added          = db_prepare_input($_POST['date_added']);

       $sql_data_array = array('categories_image'    => $categories_image,
                               'parent_id'           => $parent_id,
                               'categories_status'   => $categories_status,
                               'categories_template' => $categories_template,
                               'listing_template'    => $listing_template,
                               'sort_order'          => $sort_order,
                               'products_sorting'    => $products_sorting,
                               'products_sorting2'   => $products_sorting2,
                               'last_modified'       => 'now()');


      if ($exists==0)        // Neuanlage
      {
          $insert_sql_data = array('categories_id'   => $categories_id,
                                   'date_added'      => 'now()');

          $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

          database_insert(TABLE_CATEGORIES, $sql_data_array);
          $categories_id  = mysql_insert_id();

      }
      elseif ($exists==1)    //Aktualisieren
      {
          database_insert(TABLE_CATEGORIES, $sql_data_array, 'update', 'categories_id = \'' . $categories_id . '\'');
      }



      // Beschreibungstexte zur Kategorie laden
      $sql = "select categories_id, language_id, categories_name, categories_description, categories_heading_title, ".
             "categories_meta_title, categories_meta_description, categories_meta_keywords";

      $desc_query = mysql_query($sql . " from " . TABLE_CATEGORIES_DESCRIPTION . " where categories_id = '" . $categories_id . "' and language_id = " . LANG_ID);
      if ($description = mysql_fetch_array($desc_query))
      {
        $exists = 1;
      }
      else
      {
        $exists = 0;
      }


      // Variablen nur überschreiben, wenn sie als Parameter übergeben worden sind
      $categories_name             = db_prepare_input($_POST['categories_name']);
      $categories_description      = db_prepare_input($_POST['categories_description']);
      $categories_heading_title    = db_prepare_input($_POST['categories_heading_title']);
      $categories_meta_title       = db_prepare_input($_POST['categories_meta_title']);
      $categories_meta_description = db_prepare_input($_POST['categories_meta_description']);
      $categories_meta_keywords    = db_prepare_input($_POST['categories_meta_keywords']);

        $sql_data_array = array('categories_name'             => $categories_name,
                                'categories_description'      => $categories_description,
                                'categories_heading_title'    => $categories_heading_title,
                                'categories_meta_title'       => $categories_meta_title,
                                'categories_meta_description' => $categories_meta_description,
                                'categories_meta_keywords'    => $categories_meta_keywords);


      if ($exists==0)        // Neuanlage
      {
        $insert_sql_data = array('categories_id' => $categories_id,
                                 'language_id'   => LANG_ID);

        $sql_data_array = array_merge($sql_data_array, $insert_sql_data);
        database_insert(TABLE_CATEGORIES_DESCRIPTION, $sql_data_array);

      }
      elseif ($exists==1)    //Aktualisieren
      {
        database_insert(TABLE_CATEGORIES_DESCRIPTION, $sql_data_array, 'update', 'categories_id = \'' . $categories_id . '\' and language_id = \'' . LANG_ID . '\'');
      }


    $schema = '<STATUS_DATA>' . "\n" .
              '<EXISTS>' . $exists . '</EXISTS>' . "\n" .
              '<CATEGORIES_ID>' . $categories_id . '</CATEGORIES_ID>' . "\n" .
              '<MESSAGE>' . 'Keine Meldung vorhanden' . '</MESSAGE>' . "\n" .
              '</STATUS_DATA>' . "\n";


    $footer = '</STATUS>' . "\n";

  //Ergebnis als XML ausgeben
   create_xml ($header . $schema . $footer, xml_encoding);

}



function save_categories_descriptions ()
{

  $header = '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
            '<STATUS>' . "\n";


  $categories_id = db_prepare_input($_POST['categories_id']);
  $language_id = db_prepare_input($_POST['language_id']);


    $sql = "select categories_id, language_id, categories_name, categories_description, categories_heading_title, categories_meta_title, categories_meta_description, categories_meta_keywords " .
           "from " . TABLE_CATEGORIES_DESCRIPTION . " where categories_id = '" . $categories_id . "' and language_id = '" . $language_id . "'";

    $count_query = mysql_query($sql);
    if ($decriptions = mysql_fetch_array($count_query))
    {
      $exists = 1;
    }
    else
    {
      $exists = 0;
    }

      // Variablen nur überschreiben, wenn sie als Parameter übergeben worden sind
      $categories_name             = db_prepare_input($_POST['categories_name']);
      $categories_description      = db_prepare_input($_POST['categories_description']);
      $categories_heading_title    = db_prepare_input($_POST['categories_heading_title']);
      $categories_meta_title       = db_prepare_input($_POST['categories_meta_title']);
      $categories_meta_description = db_prepare_input($_POST['categories_meta_description']);
      $categories_meta_keywords    = db_prepare_input($_POST['categories_meta_keywords']);

      $sql_data_array = array('categories_name'             => $categories_name,
                              'categories_description'      => $categories_description,
                              'categories_heading_title'    => $categories_heading_title,
                              'categories_meta_title'       => $categories_meta_title,
                              'categories_meta_description' => $categories_meta_description,
                              'categories_meta_keywords'    => $categories_meta_keywords);


      if ($exists==0)        // Neuanlage
      {
        $insert_sql_data = array('categories_id' => $categories_id,
                                 'language_id'   => $language_id);

        $sql_data_array = array_merge($sql_data_array, $insert_sql_data);
        database_insert(TABLE_CATEGORIES_DESCRIPTION, $sql_data_array);
      }
      elseif ($exists==1)    //Aktualisieren
      {
        database_insert(TABLE_CATEGORIES_DESCRIPTION, $sql_data_array, 'update', 'categories_id = \'' . $categories_id . '\' and language_id = \'' . $language_id . '\'');
      }


    $schema = '<STATUS_DATA>' . "\n" .
              '<EXISTS>' . $exists . '</EXISTS>' . "\n" .
              '<LANGUAGE_ID>' . $language_id . '</LANGUAGE_ID>' . "\n" .
              '<MESSAGE>' . 'Die Beschreibungstexte wurden synchronisiert' . '</MESSAGE>' . "\n" .
              '</STATUS_DATA>' . "\n";


    $footer = '</STATUS>' . "\n";

  //Ergebnis als XML ausgeben
   create_xml ($header . $schema . $footer, xml_encoding);

}


function save_categorie_image ()
{

  $header = '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
            '<STATUS>' . "\n";


  $categories_id = db_prepare_input($_POST['categories_id']);


    $sql = "select categories_id, categories_image, parent_id, categories_status, categories_template, listing_template, sort_order, products_sorting, products_sorting2, date_added " .
           "from " . TABLE_CATEGORIES . " where categories_id = '" . $categories_id . "'";


    $count_query = mysql_query($sql);
    if ($categorie = mysql_fetch_array($count_query))
    {
      $exists = 1;
    }
    else
    {
      $exists = 0;
    }


     // Variablen nur überschreiben, wenn sie als Parameter übergeben worden sind
      $categories_image    = db_prepare_input($_POST['categories_image']);

      $sql_data_array = array('categories_id'       => $categories_id,
                              'categories_image'    => $categories_image,
                              'last_modified'       => 'now()');


      if ($exists==0)        // Neuanlage
      {
          $insert_sql_data = array('date_added' => 'now()');
          $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

          database_insert(TABLE_CATEGORIES, $sql_data_array);
          $categories_id  = mysql_insert_id();

      }
      elseif ($exists==1)    //Aktualisieren
      {
          database_insert(TABLE_CATEGORIES, $sql_data_array, 'update', 'categories_id = \'' . $categories_id . '\'');
      }


    $schema = '<STATUS_DATA>' . "\n" .
              '<EXISTS>' . $exists . '</EXISTS>' . "\n" .
              '<CATEGORIES_ID>' . $categories_id . '</CATEGORIES_ID>' . "\n" .
              '<CATEGORIES_IMAGE>' . htmlspecialchars($categories_image, ENT_COMPAT,'ISO-8859-1', true) . '</CATEGORIES_IMAGE>' . "\n" .
              '</STATUS_DATA>' . "\n";


    $footer = '</STATUS>' . "\n";

  //Ergebnis als XML ausgeben
   create_xml ($header . $schema . $footer, xml_encoding);

}


function save_shop_configuration ()
{

  $header = '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
            '<STATUS>' . "\n";


  $configuration_id = db_prepare_input($_POST['configuration_id']);
  $configuration_value = db_prepare_input($_POST['configuration_value']);


    $sql = "select * from " . TABLE_CONFIGURATION . " where configuration_id = '" . $configuration_id . "'";

    $count_query = mysql_query($sql);
    if ($configurations = mysql_fetch_array($count_query))
    {
      $exists = 1;
    }
    else
    {
      $exists = 0;
    }


     // Variablen nur überschreiben, wenn sie als Parameter übergeben worden sind
      $configuration_value       = db_prepare_input($_POST['configuration_value']);

      $sql_data_array = array('configuration_value'     => $configuration_value);


      if ($exists==0)        // Neuanlage
      {

          $insert_sql_data = array('configuration_id'    => $configuration_id);

          $sql_data_array = array_merge($sql_data_array, $insert_sql_data);
          database_insert(TABLE_CONFIGURATION, $sql_data_array);

      }
      elseif ($exists==1)    //Aktualisieren
      {
          database_insert(TABLE_CONFIGURATION, $sql_data_array, 'update', 'configuration_id = \'' . $configuration_id . '\'');
      }


    $schema = '<STATUS_DATA>' . "\n" .
              '<EXISTS>' . $exists . '</EXISTS>' . "\n" .
              '<CONFIGURATION_ID>' . $configuration_id . '</CONFIGURATION_ID>' . "\n" .
              '<CONFIGURATION_VALUE>' . $configuration_value . '</CONFIGURATION_VALUE>' . "\n" .
              '<MESSAGE>' . 'Keine Meldung vorhanden' . '</MESSAGE>' . "\n" .
              '</STATUS_DATA>' . "\n";


    $footer = '</STATUS>' . "\n";

  //Ergebnis als XML ausgeben
   create_xml ($header . $schema . $footer, xml_encoding);

}


function save_coupons ()
{

  $header = '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
            '<STATUS>' . "\n";


  $coupon_id = db_prepare_input($_POST['coupon_id']);


    $sql = "select coupon_id, coupon_type, coupon_code, coupon_amount, coupon_minimum_order, coupon_start_date, " .
           "coupon_expire_date, uses_per_coupon, uses_per_user, restrict_to_products, restrict_to_categories, " .
           "restrict_to_customers, coupon_active, date_created, date_modified " .
           "from " . TABLE_COUPONS . " where coupon_id = '" . $coupon_id . "'";

    $count_query = mysql_query($sql);
    if ($coupons = mysql_fetch_array($count_query))
    {
      $exists = 1;
    }
    else
    {
      $exists = 0;
    }


      // Variablen nur überschreiben, wenn sie als Parameter übergeben worden sind
       $coupon_type              = db_prepare_input($_POST['coupon_type']);
       $coupon_code              = db_prepare_input($_POST['coupon_code']);
       $coupon_amount            = db_prepare_input($_POST['coupon_amount']);
       $coupon_minimum_order     = db_prepare_input($_POST['coupon_minimum_order']);
       $coupon_start_date        = db_prepare_input($_POST['coupon_start_date']);
       $coupon_expire_date       = db_prepare_input($_POST['coupon_expire_date']);
       $uses_per_coupon          = db_prepare_input($_POST['uses_per_coupon']);
       $uses_per_user            = db_prepare_input($_POST['uses_per_user']);
       $restrict_to_products     = db_prepare_input($_POST['restrict_to_products']);
       $restrict_to_customers    = db_prepare_input($_POST['restrict_to_customers']);
       $restrict_to_categories   = db_prepare_input($_POST['restrict_to_categories']);
       $restrict_to_customers    = db_prepare_input($_POST['restrict_to_customers']);
       $coupon_active            = db_prepare_input($_POST['coupon_active']);
       $date_created             = db_prepare_input($_POST['date_created']);
       $date_modified            = db_prepare_input($_POST['date_modified']);

       $sql_data_array = array('coupon_type'              => $coupon_type,
                               'coupon_code'              => $coupon_code,
                               'coupon_amount'            => $coupon_amount,
                               'coupon_minimum_order'     => $coupon_minimum_order,
                               'coupon_start_date'        => $coupon_start_date,
                               'coupon_expire_date'       => $coupon_expire_date,
                               'uses_per_coupon'          => $uses_per_coupon,
                               'uses_per_user'            => $uses_per_user,
                               'restrict_to_products'     => $restrict_to_products,
                               'restrict_to_customers'    => $restrict_to_customers,
                               'restrict_to_categories'   => $restrict_to_categories,
                               'restrict_to_customers'    => $restrict_to_customers,
                               'coupon_active'            => $coupon_active,
                               'date_modified'            => $date_modified);


      if ($exists==0)        // Neuanlage
      {

          $insert_sql_data = array('coupon_id'                => $coupon_id,
                                   'date_created'             => 'now()');

          $sql_data_array = array_merge($sql_data_array, $insert_sql_data);
          database_insert(TABLE_COUPONS, $sql_data_array);

          if (isset($_POST['coupon_id']) && ($coupon_id!=''))
          {
           $coupon_id = db_prepare_input($_POST['coupon_id']);
          }
          else
          {
           $coupon_id = mysql_insert_id();
          }


      }
      elseif ($exists==1)    //Aktualisieren
      {
          database_insert(TABLE_COUPONS, $sql_data_array, 'update', 'coupon_id = \'' . $coupon_id . '\'');
      }


    $schema = '<STATUS_DATA>' . "\n" .
              '<EXISTS>' . $exists . '</EXISTS>' . "\n" .
              '<COUPON_ID>' . $coupon_id . '</COUPON_ID>' . "\n" .
              '<MESSAGE>' . 'Keine Meldung vorhanden' . '</MESSAGE>' . "\n" .
              '</STATUS_DATA>' . "\n";


    $footer = '</STATUS>' . "\n";

  //Ergebnis als XML ausgeben
   create_xml ($header . $schema . $footer, xml_encoding);

}



function save_coupons_email ()
{

  $header = '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
            '<STATUS>' . "\n";


  $unique_id = db_prepare_input($_POST['unique_id']);


    $sql = "select unique_id, coupon_id, customer_id_sent, sent_firstname, sent_lastname, emailed_to, date_sent " .
           "from " . TABLE_COUPON_EMAIL_TRACK . " where unique_id = '" . $unique_id . "'";

    $count_query = mysql_query($sql);
    if ($emails = mysql_fetch_array($count_query))
    {
      $exists = 1;
    }
    else
    {
      $exists = 0;
    }


        // Variablen nur überschreiben, wenn sie als Parameter übergeben worden sind
         $coupon_id                = db_prepare_input($_POST['coupon_id']);
         $customer_id_sent         = db_prepare_input($_POST['customer_id_sent']);
         $sent_firstname           = db_prepare_input($_POST['sent_firstname']);
         $sent_lastname            = db_prepare_input($_POST['sent_lastname']);
         $emailed_to               = db_prepare_input($_POST['emailed_to']);
         $date_sent                = db_prepare_input($_POST['date_sent']);

         $sql_data_array = array('coupon_id'         => $coupon_id,
                                 'customer_id_sent'  => $customer_id_sent,
                                 'sent_firstname'    => $sent_firstname,
                                 'sent_lastname'     => $sent_lastname,
                                 'emailed_to'        => $emailed_to);


      if ($exists==0)        // Neuanlage
      {

          $insert_sql_data = array('unique_id'                => $unique_id,
                                   'date_sent'                => 'now()');

          $sql_data_array = array_merge($sql_data_array, $insert_sql_data);
          database_insert(TABLE_COUPON_EMAIL_TRACK, $sql_data_array);

          if (isset($_POST['unique_id']) && ($unique_id!=''))
          {
           $unique_id = db_prepare_input($_POST['unique_id']);
          }
          else
          {
           $unique_id = mysql_insert_id();
          }


      }
      elseif ($exists==1)    //Aktualisieren
      {
          database_insert(TABLE_COUPON_EMAIL_TRACK, $sql_data_array, 'update', 'unique_id = \'' . $unique_id . '\'');
      }


    $schema = '<STATUS_DATA>' . "\n" .
              '<EXISTS>' . $exists . '</EXISTS>' . "\n" .
              '<UNIQUE_ID>' . $unique_id . '</UNIQUE_ID>' . "\n" .
              '<MESSAGE>' . 'Keine Meldung vorhanden' . '</MESSAGE>' . "\n" .
              '</STATUS_DATA>' . "\n";


    $footer = '</STATUS>' . "\n";

  //Ergebnis als XML ausgeben
   create_xml ($header . $schema . $footer, xml_encoding);

}



function save_products_list ()
{

  $header = '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
            '<STATUS>' . "\n";

  $products_id = db_prepare_input($_POST['products_id']);


    $sql = "select * from " . TABLE_PRODUCTS . " where products_id = '" . $products_id . "'";


    $query_count = mysql_query($sql);
    if ($product = mysql_fetch_array($query_count))
    {
      $exists = 1;
    }
    else
    {
      $exists = 0;
    }


      //Variablen nur überschreiben, wenn diese als Parameter übergeben worden sind
       $products_model                 = db_prepare_input($_POST['products_model']);
       $products_ean                   = db_prepare_input($_POST['products_ean']);
       $products_price                 = db_prepare_input($_POST['products_price']);
       $products_quantity              = db_prepare_input($_POST['products_quantity']);
       $products_weight                = db_prepare_input($_POST['products_weight']);
       $products_shippingtime          = db_prepare_input($_POST['products_shippingtime']);
       $products_status                = db_prepare_input($_POST['products_status']);


       $sql_data_array = array('products_model'              => $products_model,
                               'products_ean'                => $products_ean,
                               'products_price'              => $products_price,
                               'products_quantity'           => $products_quantity,
                               'products_weight'             => $products_weight,
                               'products_shippingtime'       => $products_shippingtime,
                               'products_status'             => $products_status);


      if ($exists==0)
      {
         $Meldung = "Produktwerte können nicht aktualisiert werden, da der Artikel nicht vorhanden ist";
      }
      elseif ($exists==1)
      {
         database_insert(TABLE_PRODUCTS, $sql_data_array, 'update', 'products_id = \'' . $products_id . '\'');
         $Meldung = "Produktwerte wurden erfolgreich aktualisiert";
      }


    $schema = '<STATUS_DATA>' . "\n" .
              '<EXISTS>' . $exists . '</EXISTS>' . "\n" .
              '<PRODUCTS_ID>' . $products_id . '</PRODUCTS_ID>' . "\n" .
              '<ERROR>' . '0' . '</ERROR>' . "\n" .
              '<MESSAGE>' . $Meldung . '</MESSAGE>' . "\n" .
              '</STATUS_DATA>' . "\n";


    $footer = '</STATUS>' . "\n";

  //Ergebnis als XML ausgeben
   create_xml ($header . $schema . $footer, xml_encoding);

}



function save_products_attributmatrix ()
{

  $header = '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
            '<STATUS>' . "\n";

  $products_id = db_prepare_input($_POST['products_id']);


     $sql = "select products_matrix1, products_matrix2 from " . TABLE_PRODUCTS . " where products_id = '" . $products_id . "'";

     $query_count = mysql_query($sql);
     if ($product = mysql_fetch_array($query_count))
     {
       $exists = 1;
     }
     else
     {
       $exists = 0;
     }


      //Variablen nur überschreiben, wenn diese als Parameter übergeben worden sind
       $products_matrix1     = db_prepare_input($_POST['products_matrix1']);
       $products_matrix2     = db_prepare_input($_POST['products_matrix2']);


       $sql_data_array = array('products_matrix1'     => $products_matrix1,
                               'products_matrix2'     => $products_matrix2);


      if ($exists==0)
      {
          //Produkt kann nicht überarbeitet werden
          $Meldung = "Attribut-Matrix-Werte können nicht aktualisiert werden, da der Artikel nicht vorhanden ist";
      }
      elseif ($exists==1)
      {
          database_insert(TABLE_PRODUCTS, $sql_data_array, 'update', 'products_id = \'' . $products_id . '\'');
          $Meldung = "Attribut-Matrix-Werte wurden erfolgreich aktualisiert";
      }


    $schema = '<STATUS_DATA>' . "\n" .
              '<EXISTS>' . $exists . '</EXISTS>' . "\n" .
              '<PRODUCTS_ID>' . $products_id . '</PRODUCTS_ID>' . "\n" .
              '<ERROR>' . '0' . '</ERROR>' . "\n" .
              '<MESSAGE>' . $Meldung . '</MESSAGE>' . "\n" .
              '</STATUS_DATA>' . "\n";


    $footer = '</STATUS>' . "\n";

  //Ergebnis als XML ausgeben
   create_xml ($header . $schema . $footer, xml_encoding);

}



function save_orders_history ()
{

  $header = '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
            '<STATUS>' . "\n";


  $history_id = db_prepare_input($_POST['history_id']);


    $sql = "select * from " . TABLE_ORDERS_STATUS_HISTORY . " where orders_status_history_id = '" . $history_id . "'";

    $count_query = mysql_query($sql);
    if ($history = mysql_fetch_array($count_query))
    {
      $exists = 1;
    }
    else
    {
      $exists = 0;
    }


     // Variablen nur überschreiben, wenn sie als Parameter übergeben worden sind
      $orders_id         = db_prepare_input($_POST['orders_id']);
      $orders_status_id  = db_prepare_input($_POST['orders_status_id']);
      $customer_notified = db_prepare_input($_POST['customer_notified']);
      $comments          = db_prepare_input($_POST['comments']);

      $sql_data_array = array('orders_id'         => $orders_id,
                              'orders_status_id'  => $orders_status_id,
                              'customer_notified' => $customer_notified,
                              'comments'          => $comments);


      if ($exists==0)
      {

          $insert_sql_data = array('orders_status_history_id'       => $history_id,
                                   'date_added'                     => 'now()');

          $sql_data_array = array_merge($sql_data_array, $insert_sql_data);
          database_insert(TABLE_ORDERS_STATUS_HISTORY, $sql_data_array);

          if (isset($_POST['history_id']) && ($history_id!=''))
          {
           $historie_id = db_prepare_input($_POST['history_id']);
          }
          else
          {
           $history_id = mysql_insert_id();
          }


      }
      elseif ($exists==1)
      {
          database_insert(TABLE_ORDERS_STATUS_HISTORY, $sql_data_array, 'update', 'orders_status_history_id = \'' . $history_id . '\'');
      }


    $schema = '<STATUS_DATA>' . "\n" .
              '<EXISTS>' . $exists . '</EXISTS>' . "\n" .
              '<HISTORY_ID>' . $history_id . '</HISTORY_ID>' . "\n" .
              '<MESSAGE>' . 'Keine Meldung vorhanden' . '</MESSAGE>' . "\n" .
              '</STATUS_DATA>' . "\n";


    $footer = '</STATUS>' . "\n";

  //Ergebnis als XML ausgeben
   create_xml ($header . $schema . $footer, xml_encoding);

}






// ---------------- ^^ oben sind überarbeitete Funktionen ^^ ----------------------------




/* ============= [ Kunden   ] =========================================================== */

function read_customers ()
{

  global $_GET;

  if (defined('SET_TIME_LIMIT')) { @set_time_limit(0);}

    $header = '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
              '<CUSTOMERS>' . "\n";

      $from = db_prepare_input($_GET['customers_from']);
      $anz  = db_prepare_input($_GET['customers_count']);
      $id = db_prepare_input($_GET['customers_id']);

      $address_query = "select c.customers_gender,
                               c.customers_id,
                               c.customers_cid,
                               c.customers_dob,
                               c.customers_email_address,
                               c.customers_telephone,
                               c.customers_fax,
                               c.customers_newsletter,
                               c.customers_status,";

     //pruefen, ob mind. Version 3.x von xtCommerce installiert ist
     $res=mysql_query('show fields from ' . TABLE_CUSTOMERS . ' like "customers_vat_id"');
     if (mysql_fetch_array($res)) {  $address_query .= "c.customers_vat_id as vat_id,"; }

      $address_query .= "ci.customers_info_date_account_created,
                         a.entry_firstname,
                         a.entry_lastname,
                         a.entry_company,
                         a.entry_street_address,
                         a.entry_city,
                         a.entry_postcode,
                         a.entry_suburb,
                         a.entry_state,
                         co.countries_iso_code_2
                      from
                        " . TABLE_CUSTOMERS . " c,
                        " . TABLE_CUSTOMERS_INFO . " ci,
                        " . TABLE_ADDRESS_BOOK . " a,
                        " . TABLE_COUNTRIES . " co
                      where
                         c.customers_id = ci.customers_info_id AND
                         c.customers_id = a.customers_id AND
                         c.customers_default_address_id = a.address_book_id AND
                         a.entry_country_id  = co.countries_id";

       /* wenn 'customers_id' beim Aufruf übergeben wird, nur den entsprechenden Kunden zurückgeben.
          Andernfalls ALLE Kunden zurückgeben */
        if (isset($id))
        {
         $address_query.= " AND c.customers_id = " . $id;
        }


        if (isset($from))
        {
         if (!isset($anz)) $anz = 1000;
         $address_query.= " limit " . $from . "," . $anz;
        }


        $address_result = mysql_query($address_query);
        while ($address = mysql_fetch_array($address_result))
        {

          $schema .= '<CUSTOMERS_DATA>' . "\n" .
                     '<CUSTOMERS_ID>' . htmlspecialchars($address['customers_id'], ENT_COMPAT,'ISO-8859-1', true) . '</CUSTOMERS_ID>' . "\n" .
                     '<CUSTOMERS_CID>' . htmlspecialchars($address['customers_cid'], ENT_COMPAT,'ISO-8859-1', true) . '</CUSTOMERS_CID>' . "\n" .
                     '<GENDER>' . htmlspecialchars($address['customers_gender'], ENT_COMPAT,'ISO-8859-1', true) . '</GENDER>' . "\n" .
                     '<COMPANY>' . htmlspecialchars($address['entry_company'], ENT_COMPAT,'ISO-8859-1', true) . '</COMPANY>' . "\n" .
                     '<FIRSTNAME>' . htmlspecialchars($address['entry_firstname'], ENT_COMPAT,'ISO-8859-1', true) . '</FIRSTNAME>' . "\n" .
                     '<LASTNAME>' . htmlspecialchars($address['entry_lastname'], ENT_COMPAT,'ISO-8859-1', true) . '</LASTNAME>' . "\n" .
                     '<STREET>' . htmlspecialchars($address['entry_street_address'], ENT_COMPAT,'ISO-8859-1', true) . '</STREET>' . "\n" .
                     '<POSTCODE>' . htmlspecialchars($address['entry_postcode'], ENT_COMPAT,'ISO-8859-1', true) . '</POSTCODE>' . "\n" .
                     '<CITY>' . htmlspecialchars($address['entry_city'], ENT_COMPAT,'ISO-8859-1', true) . '</CITY>' . "\n" .
                     '<SUBURB>' . htmlspecialchars($address['entry_suburb'], ENT_COMPAT,'ISO-8859-1', true) . '</SUBURB>' . "\n" .
                     '<STATE>' . htmlspecialchars($address['entry_state'], ENT_COMPAT,'ISO-8859-1', true) . '</STATE>' . "\n" .
                     '<COUNTRY>' . htmlspecialchars($address['countries_iso_code_2'], ENT_COMPAT,'ISO-8859-1', true) . '</COUNTRY>' . "\n" .
                     '<TELEPHONE>' . htmlspecialchars($address['customers_telephone'], ENT_COMPAT,'ISO-8859-1', true) . '</TELEPHONE>' . "\n" .
                     '<FAX>' . htmlspecialchars($address['customers_fax'], ENT_COMPAT,'ISO-8859-1', true) . '</FAX>' . "\n" .
                     '<NEWSLETTER>' . htmlspecialchars($address['customers_newsletter'], ENT_COMPAT,'ISO-8859-1', true) . '</NEWSLETTER>' . "\n" .
                     '<EMAIL>' . htmlspecialchars($address['customers_email_address'], ENT_COMPAT,'ISO-8859-1', true) . '</EMAIL>' . "\n" .
                     '<BIRTHDAY>' . htmlspecialchars($address['customers_dob'], ENT_COMPAT,'ISO-8859-1', true) . '</BIRTHDAY>' . "\n" .
                     '<VAT_ID>' . htmlspecialchars($address['vat_id'], ENT_COMPAT,'ISO-8859-1', true) . '</VAT_ID>' . "\n" .
                     '<DATE_ACCOUNT_CREATED>' . htmlspecialchars($address['customers_info_date_account_created'], ENT_COMPAT,'ISO-8859-1', true) . '</DATE_ACCOUNT_CREATED>' . "\n";


                      //Informationen zum Kundentyp auslesen
                        $address_query_statuses = mysql_query("select customers_status_id, language_id, customers_status_name, customers_status_image, customers_status_discount, customers_status_ot_discount_flag, customers_status_ot_discount ".
                                                               " from " . TABLE_CUSTOMERS_STATUS . " where language_id = '" . LANG_ID . "' and customers_status_id = '" . $address['customers_status'] . "' order by customers_status_id");

                        while ($statuses = mysql_fetch_array($address_query_statuses))
                        {
                          $schema .= '<STATUS_DATA>' . "\n" .
                                     '<ID>' . htmlspecialchars($statuses['customers_status_id'], ENT_COMPAT,'ISO-8859-1', true) . '</ID>' . "\n" .
                                     '<NAME>' . htmlspecialchars($statuses['customers_status_name'], ENT_COMPAT,'ISO-8859-1', true) . '</NAME>' . "\n" .
                                     '<DISCOUNT>' . htmlspecialchars($statuses['customers_status_discount'], ENT_COMPAT,'ISO-8859-1', true) . '</DISCOUNT>' . "\n" .
                                     '<OT_DISCOUNT_FLAG>' . htmlspecialchars($statuses['customers_status_ot_discount_flag'], ENT_COMPAT,'ISO-8859-1', true) . '</OT_DISCOUNT_FLAG>' . "\n" .
                                     '<OT_DISCOUNT>' . htmlspecialchars($statuses['customers_status_ot_discount'], ENT_COMPAT,'ISO-8859-1', true) . '</OT_DISCOUNT>' . "\n" .
                                     '<IMAGE>' . htmlspecialchars($statuses['customers_status_image'], ENT_COMPAT,'ISO-8859-1', true) . '</IMAGE>' . "\n";
                          $schema .= '</STATUS_DATA>' . "\n";
                        }


          $schema .= '</CUSTOMERS_DATA>' . "\n";

        }

    $footer = '</CUSTOMERS>' . "\n";

  //Ergebnis als XML ausgeben
   create_xml ($header . $schema . $footer, xml_encoding);

}



function read_customers_newsletter ()
{
  global $_GET;

  if (defined('SET_TIME_LIMIT')) { @set_time_limit(0);}

    $header = '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
              '<CUSTOMERS>' . "\n";

      $from = db_prepare_input($_GET['customers_from']);
      $anz  = db_prepare_input($_GET['customers_count']);

      $address_query = "select *
                        from " . TABLE_CUSTOMERS. "
                        where customers_newsletter = 1";

         if (isset($from))
         {
           if (!isset($anz)) $anz = 1000;
           $address_query.= " limit " . $from . "," . $anz;
         }

          $address_result = mysql_query($address_query);
          while ($address = mysql_fetch_array($address_result))
          {
            $schema .= '<CUSTOMERS_DATA>' . "\n";
            $schema .= '<CUSTOMERS_ID>' . htmlspecialchars($address['customers_id'], ENT_COMPAT,'ISO-8859-1', true) . '</CUSTOMERS_ID>' . "\n";
            $schema .= '<CUSTOMERS_CID>' . htmlspecialchars($address['customers_cid'], ENT_COMPAT,'ISO-8859-1', true) . '</CUSTOMERS_CID>' . "\n";
            $schema .= '<CUSTOMERS_GENDER>' . htmlspecialchars($address['customers_gender'], ENT_COMPAT,'ISO-8859-1', true) . '</CUSTOMERS_GENDER>' . "\n";
            $schema .= '<CUSTOMERS_FIRSTNAME>' . htmlspecialchars($address['customers_firstname'], ENT_COMPAT,'ISO-8859-1', true) . '</CUSTOMERS_FIRSTNAME>' . "\n";
            $schema .= '<CUSTOMERS_LASTNAME>' . htmlspecialchars($address['customers_lastname'], ENT_COMPAT,'ISO-8859-1', true) . '</CUSTOMERS_LASTNAME>' . "\n";
            $schema .= '<CUSTOMERS_EMAIL_ADDRESS>' . htmlspecialchars($address['customers_email_address'], ENT_COMPAT,'ISO-8859-1', true) . '</CUSTOMERS_EMAIL_ADDRESS>' . "\n";
            $schema .= '</CUSTOMERS_DATA>' . "\n";
          }


    $footer = '</CUSTOMERS>' . "\n";

  //Ergebnis als XML ausgeben
   create_xml ($header . $schema . $footer, xml_encoding);

}

/* =========================================================================================
================================================================= [ Sprachen   ] =========*/

function read_shop_configuration ()
{

   $header = '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
             '<CONFIG>' . "\n" .
             '<CONFIG_DATA>' . "\n";


    $config_sql = 'select * from configuration order by configuration_id';
    $config_res = mysql_query($config_sql);

     while ($config = mysql_fetch_array($config_res))
     {

         $schema .= '<ENTRY ID="' . $config['configuration_id'] . '">' .  "\n" .
                    '<KEY>' . htmlspecialchars($config['configuration_key'], ENT_COMPAT,'ISO-8859-1', true) . '</KEY>' . "\n" .
                    '<VALUE>' . htmlspecialchars($config['configuration_value'], ENT_COMPAT,'ISO-8859-1', true) . '</VALUE>' . "\n" .
                    '<GROUP_ID>' . htmlspecialchars($config['configuration_group_id'], ENT_COMPAT,'ISO-8859-1', true) . '</GROUP_ID>' . "\n" .
                    '<SORT_ORDER>' . htmlspecialchars($config['sort_order'], ENT_COMPAT,'ISO-8859-1', true) . '</SORT_ORDER>' . "\n" .
                    '<USE_FUNCTION>' . htmlspecialchars($config['use_function'], ENT_COMPAT,'ISO-8859-1', true) . '</USE_FUNCTION>' . "\n" .
                    '<SET_FUNCTION>' . htmlspecialchars($config['set_function'], ENT_COMPAT,'ISO-8859-1', true) . '</SET_FUNCTION>' . "\n" .
                    '</ENTRY>' . "\n";
     }


   $footer = '</CONFIG_DATA>' . "\n" .
             '</CONFIG>' . "\n";

  //Ergebnis als XML ausgeben
   create_xml ($header . $schema . $footer, xml_encoding);

}


/* ============= [ Bestellungen ] ======================================================= */

function read_orders()
{

  global $_GET, $order_total_class;

  if (defined('SET_TIME_LIMIT')) { @set_time_limit(0);}


    $order_from = db_prepare_input($_GET['order_from']);
    $order_to = db_prepare_input($_GET['order_to']);
    $order_status = db_prepare_input($_GET['order_status']);


    $header = '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
              '<ORDER>' . "\n";


       $sql = "select * from " . TABLE_ORDERS . " where orders_id >= '" . fn_input($order_from) . "'";

          if (!isset($order_status) && !isset($order_from))
          {
           $order_status = 1;
           $sql .= "and orders_status = " . $order_status;
          }

          if ($order_status!='')
          {
           $sql .= " and orders_status IN (" . $order_status . ")";  //Aufruf: '1','3'
          }


       $orders_query = mysql_query($sql);
       while ($orders = mysql_fetch_array($orders_query))
       {

           // Geburtsdatum laden
            $cust_sql = "select * from " . TABLE_CUSTOMERS . " where customers_id=" . $orders['customers_id'];
            $cust_query = mysql_query ($cust_sql);

            if (($cust_query) && ($cust_data = mysql_fetch_array($cust_query)))
            {
              $cust_dob = $cust_data['customers_dob'];
              $cust_gender = $cust_data['customers_gender'];
            }
              else
            {
              $cust_dob = '';
              $cust_gender = '';
            }

          if ($orders['billing_company']=='')        $orders['billing_company']=$orders['delivery_company'];
          if ($orders['billing_name']=='')           $orders['billing_name']=$orders['delivery_name'];
          if ($orders['billing_street_address']=='') $orders['billing_street_address']=$orders['delivery_street_address'];
          if ($orders['billing_postcode']=='')       $orders['billing_postcode']=$orders['delivery_postcode'];
          if ($orders['billing_city']=='')           $orders['billing_city']=$orders['delivery_city'];
          if ($orders['billing_suburb']=='')         $orders['billing_suburb']=$orders['delivery_suburb'];
          if ($orders['billing_state']=='')          $orders['billing_state']=$orders['delivery_state'];
          if ($orders['billing_country']=='')        $orders['billing_country']=$orders['delivery_country'];

            $schema .= '<ORDER_INFO>' . "\n" .
                        '<ORDER_HEADER>' . "\n" .
                         '<ORDER_ID>' . htmlspecialchars($orders['orders_id'], ENT_COMPAT,'ISO-8859-1', true) . '</ORDER_ID>' . "\n" .
                         '<CUSTOMER_ID>' . htmlspecialchars($orders['customers_id'], ENT_COMPAT,'ISO-8859-1', true) . '</CUSTOMER_ID>' . "\n" .
                         '<CUSTOMER_NAME>' . htmlspecialchars($orders['customers_name'], ENT_COMPAT,'ISO-8859-1', true) . '</CUSTOMER_NAME>' . "\n" .
                         '<CUSTOMER_CID>' . htmlspecialchars($orders['customers_cid'], ENT_COMPAT,'ISO-8859-1', true) . '</CUSTOMER_CID>' . "\n" .
                         '<CUSTOMER_TELEPHONE>' . htmlspecialchars($orders['customers_telephone'], ENT_COMPAT,'ISO-8859-1', true) . '</CUSTOMER_TELEPHONE>' . "\n" .
                         '<CUSTOMER_EMAIL>' . htmlspecialchars($orders['customers_email_address'], ENT_COMPAT,'ISO-8859-1', true) . '</CUSTOMER_EMAIL>' . "\n" .
                         '<CUSTOMER_VAT_ID>' . htmlspecialchars($orders['customers_vat_id'], ENT_COMPAT,'ISO-8859-1', true) . '</CUSTOMER_VAT_ID>' . "\n" .
                         '<CUSTOMER_BIRTHDAY>' . htmlspecialchars($cust_dob, ENT_COMPAT,'ISO-8859-1', true) . '</CUSTOMER_BIRTHDAY>' . "\n" .
                         '<CUSTOMER_GENDER>' . htmlspecialchars($cust_gender, ENT_COMPAT,'ISO-8859-1', true) . '</CUSTOMER_GENDER>' . "\n" .
                         '<CUSTOMER_GROUP>' . htmlspecialchars($orders['customers_status'], ENT_COMPAT,'ISO-8859-1', true) . '</CUSTOMER_GROUP>' . "\n" .
                         '<CUSTOMER_GROUP_NAME>' . htmlspecialchars($orders['customers_status_name'], ENT_COMPAT,'ISO-8859-1', true) . '</CUSTOMER_GROUP_NAME>' . "\n" .
                         '<CUSTOMER_DISCOUNT>' . htmlspecialchars($orders['customers_status_discount'], ENT_COMPAT,'ISO-8859-1', true) . '</CUSTOMER_DISCOUNT>' . "\n" .
                         '<ORDER_DATE>' . htmlspecialchars($orders['date_purchased'], ENT_COMPAT,'ISO-8859-1', true) . '</ORDER_DATE>' . "\n" .
                         '<ORDER_STATUS>' . htmlspecialchars($orders['orders_status'], ENT_COMPAT,'ISO-8859-1', true) . '</ORDER_STATUS>' . "\n" .
                         '<ORDER_IP>' . htmlspecialchars($orders['customers_ip'], ENT_COMPAT,'ISO-8859-1', true) . '</ORDER_IP>' . "\n" .
                         '<ORDER_CURRENCY>' . htmlspecialchars($orders['currency'], ENT_COMPAT,'ISO-8859-1', true) . '</ORDER_CURRENCY>' . "\n" .
                         '<ORDER_CURRENCY_VALUE>' . htmlspecialchars($orders['currency_value'], ENT_COMPAT,'ISO-8859-1', true) . '</ORDER_CURRENCY_VALUE>' . "\n" .
                         '<ORDER_COMMENTS>' . htmlspecialchars($orders['comments'], ENT_COMPAT,'ISO-8859-1', true) . '</ORDER_COMMENTS>' . "\n" .
                        '</ORDER_HEADER>' . "\n" .
                        '<BILLING_ADDRESS>' . "\n" .
                         '<COMPANY>' . htmlspecialchars($orders['billing_company'], ENT_COMPAT,'ISO-8859-1', true) . '</COMPANY>' . "\n" .
                         '<NAME>' . htmlspecialchars($orders['billing_name'], ENT_COMPAT,'ISO-8859-1', true) . '</NAME>' . "\n" .
                         '<FIRSTNAME>' . htmlspecialchars($orders['billing_firstname'], ENT_COMPAT,'ISO-8859-1', true) . '</FIRSTNAME>' . "\n" .
                         '<LASTNAME>' . htmlspecialchars($orders['billing_lastname'], ENT_COMPAT,'ISO-8859-1', true) . '</LASTNAME>' . "\n" .
                         '<STREET>' . htmlspecialchars($orders['billing_street_address'], ENT_COMPAT,'ISO-8859-1', true) . '</STREET>' . "\n" .
                         '<POSTCODE>' . htmlspecialchars($orders['billing_postcode'], ENT_COMPAT,'ISO-8859-1', true) . '</POSTCODE>' . "\n" .
                         '<CITY>' . htmlspecialchars($orders['billing_city'], ENT_COMPAT,'ISO-8859-1', true) . '</CITY>' . "\n" .
                         '<SUBURB>' . htmlspecialchars($orders['billing_suburb'], ENT_COMPAT,'ISO-8859-1', true) . '</SUBURB>' . "\n" .
                         '<STATE>' . htmlspecialchars($orders['billing_state'], ENT_COMPAT,'ISO-8859-1', true) . '</STATE>' . "\n" .
                         '<COUNTRY>' . htmlspecialchars($orders['billing_country_iso_code_2'], ENT_COMPAT,'ISO-8859-1', true) . '</COUNTRY>' . "\n" .
                        '</BILLING_ADDRESS>' . "\n" .
                        '<DELIVERY_ADDRESS>' . "\n" .
                         '<COMPANY>' . htmlspecialchars($orders['delivery_company'], ENT_COMPAT,'ISO-8859-1', true) . '</COMPANY>' . "\n" .
                         '<NAME>' . htmlspecialchars($orders['delivery_name'], ENT_COMPAT,'ISO-8859-1', true) . '</NAME>' . "\n" .
                         '<FIRSTNAME>' . htmlspecialchars($orders['delivery_firstname'], ENT_COMPAT,'ISO-8859-1', true) . '</FIRSTNAME>' . "\n" .
                         '<LASTNAME>' . htmlspecialchars($orders['delivery_lastname'], ENT_COMPAT,'ISO-8859-1', true) . '</LASTNAME>' . "\n" .
                         '<STREET>' . htmlspecialchars($orders['delivery_street_address'], ENT_COMPAT,'ISO-8859-1', true) . '</STREET>' . "\n" .
                         '<POSTCODE>' . htmlspecialchars($orders['delivery_postcode'], ENT_COMPAT,'ISO-8859-1', true) . '</POSTCODE>' . "\n" .
                         '<CITY>' . htmlspecialchars($orders['delivery_city'], ENT_COMPAT,'ISO-8859-1', true) . '</CITY>' . "\n" .
                         '<SUBURB>' . htmlspecialchars($orders['delivery_suburb'], ENT_COMPAT,'ISO-8859-1', true) . '</SUBURB>' . "\n" .
                         '<STATE>' . htmlspecialchars($orders['delivery_state'], ENT_COMPAT,'ISO-8859-1', true) . '</STATE>' . "\n" .
                         '<COUNTRY>' . htmlspecialchars($orders['delivery_country_iso_code_2'], ENT_COMPAT,'ISO-8859-1', true) . '</COUNTRY>' . "\n" .
                        '</DELIVERY_ADDRESS>' . "\n" .
                        '<PAYMENT>' . "\n" .
                         '<PAYMENT_METHOD>' . htmlspecialchars($orders['payment_method'], ENT_COMPAT,'ISO-8859-1', true) . '</PAYMENT_METHOD>'  . "\n" .
                         '<PAYMENT_CLASS>' . htmlspecialchars($orders['payment_class'], ENT_COMPAT,'ISO-8859-1', true) . '</PAYMENT_CLASS>'  . "\n";

            switch ($orders['payment_class'])
            {
              case 'cod':
                     //Nachnahme

              case 'banktransfer':
                     // Bankverbindung laden, wenn aktiv
                     $bank_name = '';
                     $bank_blz  = '';
                     $bank_kto  = '';
                     $bank_inh  = '';
                     $bank_stat = -1;

                     $bank_sql = "select * from banktransfer where orders_id = " . $orders['orders_id'];
                     $bank_query = mysql_query($bank_sql);

                       if (($bank_query) && ($bankdata = mysql_fetch_array($bank_query)))
                       {
                         $bank_name = $bankdata['banktransfer_bankname'];
                         $bank_blz  = $bankdata['banktransfer_blz'];
                         $bank_kto  = $bankdata['banktransfer_number'];
                         $bank_inh  = $bankdata['banktransfer_owner'];
                         $bank_stat = $bankdata['banktransfer_status'];
                       }
                     $schema .= '<PAYMENT_BANKTRANS_BNAME>' . htmlspecialchars($bank_name, ENT_COMPAT,'ISO-8859-1', true) . '</PAYMENT_BANKTRANS_BNAME>' . "\n" .
                                '<PAYMENT_BANKTRANS_BLZ>' . htmlspecialchars($bank_blz, ENT_COMPAT,'ISO-8859-1', true) . '</PAYMENT_BANKTRANS_BLZ>' . "\n" .
                                '<PAYMENT_BANKTRANS_NUMBER>' . htmlspecialchars($bank_kto, ENT_COMPAT,'ISO-8859-1', true) . '</PAYMENT_BANKTRANS_NUMBER>' . "\n" .
                                '<PAYMENT_BANKTRANS_OWNER>' . htmlspecialchars($bank_inh, ENT_COMPAT,'ISO-8859-1', true) . '</PAYMENT_BANKTRANS_OWNER>' . "\n" .
                                '<PAYMENT_BANKTRANS_STATUS>' . htmlspecialchars($bank_stat, ENT_COMPAT,'ISO-8859-1', true) . '</PAYMENT_BANKTRANS_STATUS>' . "\n";
                     break;

              case 'cc':
                    //Kreditkarten-Informationen übergeben

                     $schema .= '<PAYMENT_CC_TYPE>' . htmlspecialchars($orders['cc_type'], ENT_COMPAT,'ISO-8859-1', true) . '</PAYMENT_CC_TYPE>' . "\n" .
                                '<PAYMENT_CC_OWNER>' . htmlspecialchars($orders['cc_owner'], ENT_COMPAT,'ISO-8859-1', true) . '</PAYMENT_CC_OWNER>' . "\n" .
                                '<PAYMENT_CC_NUMBER>' . htmlspecialchars($orders['cc_number'], ENT_COMPAT,'ISO-8859-1', true) . '</PAYMENT_CC_NUMBER>' . "\n" .
                                '<PAYMENT_CC_EXPIRES>' . htmlspecialchars($orders['cc_expires'], ENT_COMPAT,'ISO-8859-1', true) . '</PAYMENT_CC_EXPIRES>' . "\n" .
                                '<PAYMENT_CC_START>' . htmlspecialchars($orders['cc_start'], ENT_COMPAT,'ISO-8859-1', true) . '</PAYMENT_CC_START>' . "\n" .
                                '<PAYMENT_CC_CVV>' . htmlspecialchars($orders['cc_cvv'], ENT_COMPAT,'ISO-8859-1', true) . '</PAYMENT_CC_CVV>' . "\n";
                     break;
            }

             $schema .= '</PAYMENT>' . "\n" .
                        '<SHIPPING>' . "\n" .
                         '<SHIPPING_METHOD>' . htmlspecialchars($orders['shipping_method'], ENT_COMPAT,'ISO-8859-1', true) . '</SHIPPING_METHOD>'  . "\n" .
                         '<SHIPPING_CLASS>' . htmlspecialchars($orders['shipping_class'], ENT_COMPAT,'ISO-8859-1', true) . '</SHIPPING_CLASS>'  . "\n" .
                        '</SHIPPING>' . "\n";



            $schema .= '<ORDER_PRODUCTS>' . "\n";

            $sql = "select
                     orders_products_id,
                     allow_tax,
                     products_id,
                     products_model,
                     products_name,
                     final_price,
                     products_tax,
                     products_quantity,
                     products_discount_made
                    from " .
                     TABLE_ORDERS_PRODUCTS . "
                    where
                     orders_id = '" . $orders['orders_id'] . "'";

            $products_query = mysql_query($sql);
            while ($products = mysql_fetch_array($products_query))
            {
              if ($products['allow_tax']==1) $products['final_price']=$products['final_price']/(1+$products['products_tax']*0.01);
              $schema .= '<PRODUCT>' . "\n" .
                          '<PRODUCTS_ID>' . $products['products_id'] . '</PRODUCTS_ID>' . "\n" .
                          '<PRODUCTS_QUANTITY>' . htmlspecialchars($products['products_quantity'], ENT_COMPAT,'ISO-8859-1', true) . '</PRODUCTS_QUANTITY>' . "\n" .
                          '<PRODUCTS_DISCOUNT_MADE>' . htmlspecialchars($products['products_discount_made'], ENT_COMPAT,'ISO-8859-1', true) . '</PRODUCTS_DISCOUNT_MADE>' . "\n" .
                          '<PRODUCTS_MODEL>' . htmlspecialchars($products['products_model'], ENT_COMPAT,'ISO-8859-1', true) . '</PRODUCTS_MODEL>' . "\n" .
                          '<PRODUCTS_NAME>' . htmlspecialchars($products['products_name'], ENT_COMPAT,'ISO-8859-1', true) . '</PRODUCTS_NAME>' . "\n" .
                          '<PRODUCTS_PRICE>' . $products['final_price']/$products['products_quantity'] . '</PRODUCTS_PRICE>' . "\n" .
                          '<PRODUCTS_TAX>' . htmlspecialchars($products['products_tax'], ENT_COMPAT,'ISO-8859-1', true) . '</PRODUCTS_TAX>' . "\n".
                          '<PRODUCTS_TAX_FLAG>' . htmlspecialchars($products['allow_tax'], ENT_COMPAT,'ISO-8859-1', true) . '</PRODUCTS_TAX_FLAG>' . "\n";

              $attributes_query = mysql_query("select products_options, products_options_values, options_values_price, price_prefix from " . TABLE_ORDERS_PRODUCTS_ATTRIBUTES . " where orders_id = '" .$orders['orders_id'] . "' and orders_products_id = '" . $products['orders_products_id'] . "' order by products_options");
              if (mysql_num_rows($attributes_query))
              {
                while ($attributes = mysql_fetch_array($attributes_query))
                {

                  require_once(DIR_FS_INC . 'xtc_get_attributes_model.inc.php');

                  //Parameter: product_id, attribute_name, options_name, language='')   //ALTER AUFRUF= $attributes_model = xtc_get_attributes_model($products['products_id'],$attributes['products_options_values']);
                  $attributes_model = xtc_get_attributes_model($products['products_id'],$attributes['products_options_values'],$attributes['products_options'],LANG_ID);
                  $schema .= '<OPTION>' . "\n" .
                              '<PRODUCTS_OPTIONS>' .  htmlspecialchars($attributes['products_options'], ENT_COMPAT,'ISO-8859-1', true) . '</PRODUCTS_OPTIONS>' . "\n" .
                              '<PRODUCTS_OPTIONS_VALUES>' .  htmlspecialchars($attributes['products_options_values'], ENT_COMPAT,'ISO-8859-1', true) . '</PRODUCTS_OPTIONS_VALUES>' . "\n" .
                              '<PRODUCTS_OPTIONS_MODEL>' . htmlspecialchars($attributes_model, ENT_COMPAT,'ISO-8859-1', true) . '</PRODUCTS_OPTIONS_MODEL>'. "\n".
                              '<PRODUCTS_OPTIONS_PRICE>' . htmlspecialchars($attributes['price_prefix'], ENT_COMPAT,'ISO-8859-1', true) . ' ' . htmlspecialchars($attributes['options_values_price'], ENT_COMPAT,'ISO-8859-1', true) . '</PRODUCTS_OPTIONS_PRICE>' . "\n" .
                             '</OPTION>' . "\n";
                }
              }
              $schema .=  '</PRODUCT>' . "\n";
            }
            $schema .= '</ORDER_PRODUCTS>' . "\n";


            $schema .= '<ORDER_TOTAL>' . "\n";

            $totals_query = mysql_query("select title, value, class, sort_order from " . TABLE_ORDERS_TOTAL . " where orders_id = '" . $orders['orders_id'] . "' order by sort_order");
            while ($totals = mysql_fetch_array($totals_query))
            {
              $total_prefix = "";
              $total_tax  = "";
              $total_prefix = $order_total_class[$totals['class']]['prefix'];
              $total_tax = $order_total_class[$totals['class']]['tax'];

              $schema .= '<TOTAL>' . "\n" .
                          '<TOTAL_TITLE>' . htmlspecialchars($totals['title'], ENT_COMPAT,'ISO-8859-1', true) . '</TOTAL_TITLE>' . "\n" .
                          '<TOTAL_VALUE>' . htmlspecialchars($totals['value'], ENT_COMPAT,'ISO-8859-1', true) . '</TOTAL_VALUE>' . "\n" .
                          '<TOTAL_CLASS>' . htmlspecialchars($totals['class'], ENT_COMPAT,'ISO-8859-1', true) . '</TOTAL_CLASS>' . "\n" .
                          '<TOTAL_SORT_ORDER>' . htmlspecialchars($totals['sort_order'], ENT_COMPAT,'ISO-8859-1', true) . '</TOTAL_SORT_ORDER>' . "\n" .
                          '<TOTAL_PREFIX>' . htmlspecialchars($total_prefix, ENT_COMPAT,'ISO-8859-1', true) . '</TOTAL_PREFIX>' . "\n" .
                          '<TOTAL_TAX>' . htmlspecialchars($total_tax, ENT_COMPAT,'ISO-8859-1', true) . '</TOTAL_TAX>' . "\n" .
                         '</TOTAL>' . "\n";
            }
            $schema .= '</ORDER_TOTAL>' . "\n";


            $schema .= '<ORDER_HISTORY>' . "\n";
            $history_query = mysql_query("select * from " . TABLE_ORDERS_STATUS_HISTORY . " where orders_id = '" . $orders['orders_id'] . "'"); // and orders_status_id = '" . $orders['orders_status'] . "'"
            while ($history = mysql_fetch_array($history_query))
            {

              $schema .= '<HISTORY>' . "\n" .
                           '<ORDER_HISTORY_ID>' . htmlspecialchars($history['orders_status_history_id'], ENT_COMPAT,'ISO-8859-1', true) . '</ORDER_HISTORY_ID>' . "\n" .
                           '<ORDER_ID>' . htmlspecialchars($history['orders_id'], ENT_COMPAT,'ISO-8859-1', true) . '</ORDER_ID>' . "\n" .
                           '<ORDER_STATUS>' . htmlspecialchars($history['orders_status_id'], ENT_COMPAT,'ISO-8859-1', true) . '</ORDER_STATUS>' . "\n" .
                           '<ORDER_DATE_ADDED>' . htmlspecialchars($history['date_added'], ENT_COMPAT,'ISO-8859-1', true) . '</ORDER_DATE_ADDED>' . "\n" .
                           '<ORDER_CUSTOMER_NOTIFIED>' . htmlspecialchars($history['customer_notified'], ENT_COMPAT,'ISO-8859-1', true) . '</ORDER_CUSTOMER_NOTIFIED>' . "\n" .
                           '<ORDER_COMMENTS>' . htmlspecialchars($history['comments'], ENT_COMPAT,'ISO-8859-1', true) . '</ORDER_COMMENTS>' . "\n" .
                         '</HISTORY>' . "\n";

            }
            $schema .= '</ORDER_HISTORY>' . "\n";


       $schema .= '</ORDER_INFO>' . "\n\n";

       }


    $footer = '</ORDER>' . "\n";

  //Ergebnis als XML ausgeben
   create_xml ($header . $schema . $footer, xml_encoding);

}



function read_new_orders_count ()
{

  global $_GET, $order_total_class;

  if (defined('SET_TIME_LIMIT')) { @set_time_limit(0);}


    $order_status = db_prepare_input($_GET['order_status']);


    $header = '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
              '<STATUS>' . "\n";

       $sql ="select count(*) as orders_counter from " . TABLE_ORDERS;                   //$sql ="select count(*) as orders_counter from " . TABLE_ORDERS . " where orders_status IN (" . fn_input($order_status) . ")";  //Aufruf: '1','3'

       /* wenn 'order_status' beim Aufruf übergeben wird, nur die entsprechenden Bestellungen zählen und zurückgeben.
          Andernfalls ALLE Bestellungen bei der Zählung zurückgeben */
        if (isset($order_status))
        {
         $sql .= " where orders_status IN (" . fn_input($order_status) . ")";        //Aufruf: '1','3'    //$sql .= " where products_id = " . $id;
        }


       $orders_query = mysql_query($sql);
       while ($orders = mysql_fetch_array($orders_query))
       {

          $schema = '<STATUS_DATA>' . "\n" .
                     '<ORDERS_COUNT>' . $orders['orders_counter'] . '</ORDERS_COUNT>' . "\n" .
                     '<MESSAGE>' . 'Keine Meldung vorhanden' . '</MESSAGE>' . "\n" .
                    '</STATUS_DATA>' . "\n";

       }


    $footer = '</STATUS>' . "\n";

  //Ergebnis als XML ausgeben
   create_xml ($header . $schema . $footer, xml_encoding);

}




function delete_orders_history ()
{

  global $_GET;

  $header = '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
            '<STATUS>' . "\n";


  $history_id = db_prepare_input($_GET['history_id']);

  if (isset($history_id))
  {

    // History löschen
    $result = mysql_query("delete from " . TABLE_ORDERS_STATUS_HISTORY . " where orders_status_history_id = '" . $history_id . "'");


    $schema = '<STATUS_DATA>' . "\n" .
                '<ACTION>' . $_GET['action'] . '</ACTION>' . "\n" .
                '<HISTORY_ID>' . $history_id . '</HISTORY_ID>' . "\n" .
                '<ERROR>' . '0' . '</ERROR>' . "\n" .
                '<MESSAGE>' . 'Die Historie zur Bestellung wurde gelöscht' . '</MESSAGE>' . "\n" .
              '</STATUS_DATA>' . "\n";

  }
  else
  {

    $schema = '<STATUS_DATA>' . "\n" .
                '<ACTION>' . $_GET['action'] . '</ACTION>' . "\n" .
                '<HISTORY_ID>' . $history_id . '</HISTORY_ID>' . "\n" .
                '<ERROR>' . '99' . '</ERROR>' . "\n" .
                '<MESSAGE>' . 'Die Historie zur Bestellung konnte nicht gelöscht werden' . '</MESSAGE>' . "\n" .
              '</STATUS_DATA>' . "\n";
  }


    $footer = '</STATUS>' . "\n";

  //Ergebnis als XML ausgeben
   create_xml ($header . $schema . $footer, xml_encoding);

}



function read_orders_history()
{

  global $_GET;

   $header = '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
             '<HISTORY>' . "\n";


     $sql = "select * from " . TABLE_ORDERS_STATUS_HISTORY;

      $id  = db_prepare_input($_GET['orders_id']);
         /* wenn 'orders_id' beim Aufruf übergeben wird, nur der entsprechende Historie zurückgeben.
            Andernfalls ALLE Einträge in History zurückgeben
         */

        if (isset($id))
        {
         $sql .= " where orders_id = " . $id;
        }

     $history_query = mysql_query($sql);

      while ($history = mysql_fetch_array($history_query))
      {
       $schema .= '<HISTORY_DATA>' . "\n" .
                    '<HISTORY_ID>' . $history[orders_status_history_id] . '</HISTORY_ID>' . "\n" .
                    '<ORDERS_ID>' . htmlspecialchars($history["orders_id"], ENT_COMPAT,'ISO-8859-1', true) . '</ORDERS_ID>' . "\n" .
                    '<ORDERS_STATUS_ID>' . htmlspecialchars($history["orders_status_id"], ENT_COMPAT,'ISO-8859-1', true) . '</ORDERS_STATUS_ID>' . "\n" .
                    '<DATE_ADDED>' . htmlspecialchars($history["date_added"], ENT_COMPAT,'ISO-8859-1', true) . '</DATE_ADDED>' . "\n" .
                    '<CUSTOMER_NOTIFIED>' . htmlspecialchars($history["customer_notified"], ENT_COMPAT,'ISO-8859-1', true) . '</CUSTOMER_NOTIFIED>' . "\n" .
                    '<COMMENTS>' . htmlspecialchars($history["comments"], ENT_COMPAT,'ISO-8859-1', true) . '</COMMENTS>' . "\n" .
                  '</HISTORY_DATA>' . "\n";
      }

    $footer = '</HISTORY>' . "\n";

   //Ergebnis als XML ausgeben
    create_xml ($header . $schema . $footer, xml_encoding);


}



function update_order ()
{
  global $_GET;

  $header = '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
            '<STATUS>' . "\n";


  if ((isset($_GET['order_id'])) && (isset($_GET['status'])))
  {

    $order_id = db_prepare_input($_GET['order_id']);
    $status = db_prepare_input($_GET['status']);
    $comments = db_prepare_input($_GET['comments']);


    //Status überprüfen
    $check_status_query = mysql_query("select * from " . TABLE_ORDERS . " where orders_id = '" . fn_input($order_id) . "'");
    if ($check_status = mysql_fetch_array($check_status_query))
    {

      if ($check_status['orders_status'] != $status || $comments != '')
      {
        mysql_query("update " . TABLE_ORDERS . " set orders_status = '" . fn_input($status) . "', last_modified = now() where orders_id = '" . fn_input($order_id) . "'");
        $customer_notified = '0';

        if ($_GET['notify'] == 'on')
        {
          $customer_notified = '1';
        }

        mysql_query("insert into " . TABLE_ORDERS_STATUS_HISTORY . " (orders_id, orders_status_id, date_added, customer_notified, comments) values ('" . fn_input($order_id) . "', '" . fn_input($status) . "', now(), '" . $customer_notified . "', '" . fn_input($comments)  . "')");
         $schema = '<STATUS_DATA>' . "\n" .
                   '<ORDER_ID>' . $order_id . '</ORDER_ID>' . "\n" .
                   '<ORDER_STATUS>' . $status . '</ORDER_STATUS>' . "\n" .
                   '<ACTION>' . $_GET['action'] . '</ACTION>' . "\n" .
                   '<CODE>' . '0' . '</CODE>' . "\n" .
                   '<MESSAGE>' . 'Der Bestellstatus wurde erfolgreich fortgeschrieben.' . '</MESSAGE>' . "\n" .
                   '</STATUS_DATA>' . "\n";

      }

      else if ($check_status['orders_status'] == $status)
      {
        // Status wurde bereits gesetzt
         $schema = '<STATUS_DATA>' . "\n" .
                   '<ORDER_ID>' . $order_id . '</ORDER_ID>' . "\n" .
                   '<ORDER_STATUS>' . $status . '</ORDER_STATUS>' . "\n" .
                   '<ACTION>' . $_GET['action'] . '</ACTION>' . "\n" .
                   '<CODE>' . '1' . '</CODE>' . "\n" .
                   '<MESSAGE>' . 'Der Bestellstatus wurde nicht verändert.' . '</MESSAGE>' . "\n" .
                   '</STATUS_DATA>' . "\n";
      }
    }
      else
    {

      // Fehler: Bestellung existiert nicht
       $schema = '<STATUS_DATA>' . "\n" .
                 '<ORDER_ID>' . $order_id . '</ORDER_ID>' . "\n" .
                 '<ACTION>' . $_GET['action'] . '</ACTION>' . "\n" .
                 '<CODE>' . '2' . '</CODE>' . "\n" .
                 '<MESSAGE>' . 'Die Bestellung existiert nicht mehr.' . '</MESSAGE>' . "\n" .
                 '</STATUS_DATA>' . "\n";
    }
  }
    else
  {

    // Es wurden falsche Parameter übergeben
    $schema = '<STATUS_DATA>' . "\n" .
              '<ACTION>' . $_GET['action'] . '</ACTION>' . "\n" .
              '<CODE>' . '99' . '</CODE>' . "\n" .
              '<MESSAGE>' . 'Es wurden beim Aufruf falsche Parameter übergeben.' . '</MESSAGE>' . "\n" .
              '</STATUS_DATA>' . "\n";
  }

    $footer = '</STATUS>' . "\n";

  //Ergebnis als XML ausgeben
   create_xml ($header . $schema . $footer, xml_encoding);

}



function update_order_original ()
{
  global $_GET;

  $header = '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
            '<STATUS>' . "\n";


  if ((isset($_GET['order_id'])) && (isset($_GET['status'])))
  {

    $order_id = db_prepare_input($_GET['order_id']);
    $status = db_prepare_input($_GET['status']);
    $comments = db_prepare_input($_GET['comments']);


    //Status überprüfen
    $check_status_query = mysql_query("select * from " . TABLE_ORDERS . " where orders_id = '" . fn_input($order_id) . "'");
    if ($check_status = mysql_fetch_array($check_status_query))
    {

      if ($check_status['orders_status'] != $status || $comments != '')
      {
        mysql_query("update " . TABLE_ORDERS . " set orders_status = '" . fn_input($status) . "', last_modified = now() where orders_id = '" . fn_input($order_id) . "'");
        $customer_notified = '0';
        if ($_GET['notify'] == 'on')
        {

          // Falls eine Sprach ID zur Bestellung existiert die E-Mail-Bestätigung entsprechend ausführen
          if (isset($check_status['orders_language_id']) && $check_status['orders_language_id'] > 0 )
          {
            $orders_status_query = mysql_query("select orders_status_id, orders_status_name from " . TABLE_ORDERS_STATUS . " where language_id = '" . $check_status['orders_language_id'] . "'");
            if (mysql_num_rows($orders_status_query) == 0)
            {
              $orders_status_query = mysql_query("select orders_status_id, orders_status_name from " . TABLE_ORDERS_STATUS . " where language_id = '" . $languages_id . "'");
            }
          }
            else
          {
            $orders_status_query = mysql_query("select orders_status_id, orders_status_name from " . TABLE_ORDERS_STATUS . " where language_id = '" . $languages_id . "'");
          }
          $orders_statuses = array();
          $orders_status_array = array();
          while ($orders_status = mysql_fetch_array($orders_status_query))
          {
            $orders_statuses[] = array('id' => $orders_status['orders_status_id'],
                                       'text' => $orders_status['orders_status_name']);
            $orders_status_array[$orders_status['orders_status_id']] = $orders_status['orders_status_name'];
          }

          // status query
          $orders_status_query = mysql_query("select orders_status_name from " . TABLE_ORDERS_STATUS . " where language_id = '" . LANG_ID . "' and orders_status_id = '" . $status . "'");
          $o_status=mysql_fetch_array($orders_status_query);
          $o_status=$o_status['orders_status_name'];

          //ok lets generate the html/txt mail from Template
          if ($_GET['notify_comments'] == 'on')
          {
            $notify_comments = sprintf(EMAIL_TEXT_COMMENTS_UPDATE, $comments) . "\n\n";
          }
            else
          {
            $comments='';
          }

          // benötigte Funktionen zum Versenden der E-Mail -----------------------------------
          require_once(DIR_WS_CLASSES.'class.phpmailer.php');
          require_once(DIR_FS_INC . 'xtc_php_mail.inc.php');
          require_once(DIR_FS_INC . 'xtc_add_tax.inc.php');
          require_once(DIR_FS_INC . 'xtc_not_null.inc.php');
          require_once(DIR_FS_INC . 'changedataout.inc.php');
          require_once(DIR_FS_INC . 'xtc_href_link.inc.php');
          require_once(DIR_FS_INC . 'xtc_date_long.inc.php');
          require_once(DIR_FS_INC . 'xtc_check_agent.inc.php');
          $smarty = new Smarty;

          $smarty->assign('language', $check_status['language']);
          $smarty->caching = false;
          $smarty->template_dir=DIR_FS_CATALOG.'templates';
          $smarty->compile_dir=DIR_FS_CATALOG.'templates_c';
          $smarty->config_dir=DIR_FS_CATALOG.'lang';
          $smarty->assign('tpl_path','templates/'.CURRENT_TEMPLATE.'/');
          $smarty->assign('logo_path',HTTP_SERVER  . DIR_WS_CATALOG.'templates/'.CURRENT_TEMPLATE.'/img/');
          $smarty->assign('NAME',$check_status['customers_name']);
          $smarty->assign('ORDER_NR',$order_id);
          $smarty->assign('ORDER_LINK',xtc_href_link(FILENAME_CATALOG_ACCOUNT_HISTORY_INFO, 'order_id=' . $order_id, 'SSL'));
          $smarty->assign('ORDER_DATE',xtc_date_long($check_status['date_purchased']));
          $smarty->assign('NOTIFY_COMMENTS',$comments);
          $smarty->assign('ORDER_STATUS',$o_status);

          $html_mail=$smarty->fetch(CURRENT_TEMPLATE . '/admin/mail/'.$check_status['language'].'/change_order_mail.html');
          $txt_mail=$smarty->fetch(CURRENT_TEMPLATE . '/admin/mail/'.$check_status['language'].'/change_order_mail.txt');

          // E-Mail im html/txt-Format senden
          xtc_php_mail(EMAIL_BILLING_ADDRESS,
                       EMAIL_BILLING_NAME,
                       $check_status['customers_email_address'],
                       $check_status['customers_name'],
                       '',
                       EMAIL_BILLING_REPLY_ADDRESS,
                       EMAIL_BILLING_REPLY_ADDRESS_NAME,
                       '',
                       '',
                       EMAIL_BILLING_SUBJECT,
                       $html_mail,
                       $txt_mail);

          $customer_notified = '1';
        }

        mysql_query("insert into " . TABLE_ORDERS_STATUS_HISTORY . " (orders_id, orders_status_id, date_added, customer_notified, comments) values ('" . fn_input($order_id) . "', '" . fn_input($status) . "', now(), '" . $customer_notified . "', '" . fn_input($comments)  . "')");
         $schema = '<STATUS_DATA>' . "\n" .
                   '<ORDER_ID>' . $order_id . '</ORDER_ID>' . "\n" .
                   '<ORDER_STATUS>' . $status . '</ORDER_STATUS>' . "\n" .
                   '<ACTION>' . $_GET['action'] . '</ACTION>' . "\n" .
                   '<CODE>' . '0' . '</CODE>' . "\n" .
                   '<MESSAGE>' . 'Der Bestellstatus wurde erfolgreich fortgeschrieben.' . '</MESSAGE>' . "\n" .
                   '</STATUS_DATA>' . "\n";

      }

      else if ($check_status['orders_status'] == $status)
      {
        // Status wurde bereits gesetzt
         $schema = '<STATUS_DATA>' . "\n" .
                   '<ORDER_ID>' . $order_id . '</ORDER_ID>' . "\n" .
                   '<ORDER_STATUS>' . $status . '</ORDER_STATUS>' . "\n" .
                   '<ACTION>' . $_GET['action'] . '</ACTION>' . "\n" .
                   '<CODE>' . '1' . '</CODE>' . "\n" .
                   '<MESSAGE>' . 'Der Bestellstatus wurde nicht verändert.' . '</MESSAGE>' . "\n" .
                   '</STATUS_DATA>' . "\n";
      }
    }
      else
    {

      // Fehler: Bestellung existiert nicht
       $schema = '<STATUS_DATA>' . "\n" .
                 '<ORDER_ID>' . $order_id . '</ORDER_ID>' . "\n" .
                 '<ACTION>' . $_GET['action'] . '</ACTION>' . "\n" .
                 '<CODE>' . '2' . '</CODE>' . "\n" .
                 '<MESSAGE>' . 'Die Bestellung existiert nicht.' . '</MESSAGE>' . "\n" .
                 '</STATUS_DATA>' . "\n";
    }
  }
    else
  {

    // Es wurden falsche Parameter übergeben
    $schema = '<STATUS_DATA>' . "\n" .
              '<ACTION>' . $_GET['action'] . '</ACTION>' . "\n" .
              '<CODE>' . '99' . '</CODE>' . "\n" .
              '<MESSAGE>' . 'Es wurden beim Aufruf falsche Parameter übergeben.' . '</MESSAGE>' . "\n" .
              '</STATUS_DATA>' . "\n";
  }

    $footer = '</STATUS>' . "\n";

  //Ergebnis als XML ausgeben
   create_xml ($header . $schema . $footer, xml_encoding);

}

/* =========================================================================================
=================================================================== [ Bestellung ] =========*/



/* ============= [ Sprachen   ] =========================================================== */

function read_languages()
{

  global $_GET;

   $header = '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
             '<LANGUAGES>' . "\n";


     $sql = "select languages_id, name, code, image, directory, sort_order, language_charset from " . TABLE_LANGUAGES;
     $languages_query = mysql_query($sql);

      while ($languages = mysql_fetch_array($languages_query))
      {
       $schema .= '<LANGUAGES_DATA>' . "\n" .
                  '<ID>' . $languages[languages_id] . '</ID>' . "\n" .
                  '<NAME>' . htmlspecialchars($languages["name"], ENT_COMPAT,'ISO-8859-1', true) . '</NAME>' . "\n" .
                  '<CODE>' . htmlspecialchars($languages["code"], ENT_COMPAT,'ISO-8859-1', true) . '</CODE>' . "\n" .
                  '<IMAGE>' . htmlspecialchars($languages["image"], ENT_COMPAT,'ISO-8859-1', true) . '</IMAGE>' . "\n" .
                  '<DIRECTORY>' . htmlspecialchars($languages["directory"], ENT_COMPAT,'ISO-8859-1', true) . '</DIRECTORY>' . "\n" .
                  '<SORT_ORDER>' . htmlspecialchars($languages["sort_order"], ENT_COMPAT,'ISO-8859-1', true) . '</SORT_ORDER>' . "\n" .
                  '<LANGUAGE_CHARSET>' . htmlspecialchars($languages["language_charset"], ENT_COMPAT,'ISO-8859-1', true) . '</LANGUAGE_CHARSET>' . "\n" .
                  '</LANGUAGES_DATA>' . "\n";
      }

    $footer = '</LANGUAGES>' . "\n";

   //Ergebnis als XML ausgeben
    create_xml ($header . $schema . $footer, xml_encoding);

}


function delete_languages ()
{

  global $_GET;

  $header = '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
            '<LANGUAGES_STATUS>' . "\n";


  $languages_id  = db_prepare_input($_GET['languages_id']);

  if (isset($languages_id))
  {

    // Sprachen löschen
    $result = mysql_query("delete from " . TABLE_LANGUAGES . " where languages_id = '" . $languages_id . "'");


    $schema = '<LANGUAGES_DATA>' . "\n" .
              '<ACTION>' . $_GET['action'] . '</ACTION>' . "\n" .
              '<LANGUAGES_ID>' . $languages_id . '</LANGUAGES_ID>' . "\n" .
              '<ERROR>' . '0' . '</ERROR>' . "\n" .
              '<MESSAGE>' . 'Die Sprache wurde gelöscht' . '</MESSAGE>' . "\n" .
              '</LANGUAGES_DATA>' . "\n";

  }
  else
  {

    $schema = '<LANGUAGES_DATA>' . "\n" .
              '<ACTION>' . $_GET['action'] . '</ACTION>' . "\n" .
              '<LANGUAGES_ID>' . $languages_id . '</LANGUAGES_ID>' . "\n" .
              '<ERROR>' . '99' . '</ERROR>' . "\n" .
              '<MESSAGE>' . 'Die Sprache konnte nicht gelöscht werden' . '</MESSAGE>' . "\n" .
              '</LANGUAGES_DATA>' . "\n";
  }


    $footer = '</LANGUAGES_STATUS>' . "\n";

  //Ergebnis als XML ausgeben
   create_xml ($header . $schema . $footer, xml_encoding);

}



function delete_languages_all ()
{

  global $_GET;

  $header = '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
            '<LANGUAGES_STATUS>' . "\n";


    // Sprachen löschen
    $result = mysql_query("delete from " . TABLE_LANGUAGES);


    $schema = '<LANGUAGES_DATA>' . "\n" .
              '<ACTION>' . $_GET['action'] . '</ACTION>' . "\n" .
              '<ERROR>' . '0' . '</ERROR>' . "\n" .
              '<MESSAGE>' . 'Es wurden alle Sprachen gelöscht' . '</MESSAGE>' . "\n" .
              '</LANGUAGES_DATA>' . "\n";

    $footer = '</LANGUAGES_STATUS>' . "\n";

  //Ergebnis als XML ausgeben
   create_xml ($header . $schema . $footer, xml_encoding);

}



/* =========================================================================================
================================================================= [ Sprachen   ] =========*/


/* ============= [ Kategorien ] =========================================================== */


function read_categories ()
{

  if (defined('SET_TIME_LIMIT')) { @set_time_limit(0);}

    $header = '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
              '<CATEGORIES>' . "\n";


      $cat_query = mysql_query("select categories_id, parent_id, categories_image, categories_status, categories_template, listing_template, sort_order, products_sorting, products_sorting2, date_added, last_modified ".
                                " from " . TABLE_CATEGORIES . " order by parent_id, sort_order, categories_id");

       while ($cat = mysql_fetch_array($cat_query))
       {
         $schema  .= '<CATEGORIES_DATA>' . "\n" .
                     '<ID>' . $cat['categories_id'] . '</ID>' . "\n" .
                     '<PARENT_ID>' . $cat['parent_id'] . '</PARENT_ID>' . "\n" .
                     '<IMAGE>' . htmlspecialchars($cat['categories_image'], ENT_COMPAT,'ISO-8859-1', true) . '</IMAGE>' . "\n" .
                     '<IMAGE_URL>' . HTTP_SERVER . DIR_WS_CATALOG . DIR_WS_IMAGES . 'categories/' . htmlspecialchars($cat['categories_image']) . '</IMAGE_URL>' . "\n" .
                     '<CATEGORIES_STATUS>' . htmlspecialchars($cat['categories_status'], ENT_COMPAT,'ISO-8859-1', true) . '</CATEGORIES_STATUS>' . "\n" .
                     '<CATEGORIES_TEMPLATE>' . htmlspecialchars($cat['categories_template'], ENT_COMPAT,'ISO-8859-1', true) . '</CATEGORIES_TEMPLATE>' . "\n" .
                     '<LISTING_TEMPLATE>' . htmlspecialchars($cat['listing_template'], ENT_COMPAT,'ISO-8859-1', true) . '</LISTING_TEMPLATE>' . "\n" .
                     '<SORT_ORDER>' . htmlspecialchars($cat['sort_order'], ENT_COMPAT,'ISO-8859-1', true) . '</SORT_ORDER>' . "\n" .
                     '<PRODUCTS_SORTING>' . htmlspecialchars($cat['products_sorting'], ENT_COMPAT,'ISO-8859-1', true) . '</PRODUCTS_SORTING>' . "\n" .
                     '<PRODUCTS_SORTING2>' . htmlspecialchars($cat['products_sorting2'], ENT_COMPAT,'ISO-8859-1', true) . '</PRODUCTS_SORTING2>' . "\n" .
                     '<DATE_ADDED>' . htmlspecialchars($cat['date_added'], ENT_COMPAT,'ISO-8859-1', true) . '</DATE_ADDED>' . "\n" .
                     '<LAST_MODIFIED>' . htmlspecialchars($cat['last_modified'], ENT_COMPAT,'ISO-8859-1', true) . '</LAST_MODIFIED>' . "\n";


           $detail_query = mysql_query("select categories_id,
                                                language_id,
                                                categories_name,
                                                categories_heading_title,
                                                categories_description,
                                                categories_meta_title,
                                                categories_meta_description,
                                                categories_meta_keywords, " . TABLE_LANGUAGES . ".code as lang_code, " . TABLE_LANGUAGES . ".name as lang_name from " . TABLE_CATEGORIES_DESCRIPTION . "," . TABLE_LANGUAGES .
                                               " where " . TABLE_CATEGORIES_DESCRIPTION . ".categories_id=" . $cat['categories_id'] . " and " . TABLE_LANGUAGES . ".languages_id=" . TABLE_CATEGORIES_DESCRIPTION . ".language_id");

                  while ($details = mysql_fetch_array($detail_query))
                  {
                    $schema .= "<CATEGORIES_DESCRIPTION ID='" . $details["language_id"] . "' CODE='" . $details["lang_code"] . "' NAME='" . $details["lang_name"] . "'>\n";
                    $schema .= "<NAME>" . htmlspecialchars($details["categories_name"], ENT_COMPAT,'ISO-8859-1', true) . "</NAME>" . "\n";
                    $schema .= "<HEADING_TITLE>" . htmlspecialchars($details["categories_heading_title"], ENT_COMPAT,'ISO-8859-1', true) . "</HEADING_TITLE>" . "\n";
                    $schema .= "<DESCRIPTION>" . htmlspecialchars($details["categories_description"], ENT_COMPAT,'ISO-8859-1', true) . "</DESCRIPTION>" . "\n";
                    $schema .= "<META_TITLE>" . htmlspecialchars($details["categories_meta_title"], ENT_COMPAT,'ISO-8859-1', true) . "</META_TITLE>" . "\n";
                    $schema .= "<META_DESCRIPTION>" . htmlspecialchars($details["categories_meta_description"], ENT_COMPAT,'ISO-8859-1', true) . "</META_DESCRIPTION>" . "\n";
                    $schema .= "<META_KEYWORDS>" . htmlspecialchars($details["categories_meta_keywords"], ENT_COMPAT,'ISO-8859-1', true) . "</META_KEYWORDS>" . "\n";
                    $schema .= "</CATEGORIES_DESCRIPTION>\n";
                  }

                     // Produkte in dieser Categorie auflisten
                      $prod2cat_query = mysql_query("select categories_id, products_id from " . TABLE_PRODUCTS_TO_CATEGORIES .
                                                     " where categories_id='" . $cat['categories_id'] . "'");

                               while ($prod2cat = mysql_fetch_array($prod2cat_query))
                               {
                                 $schema .="<PRODUCTS ID='" . $prod2cat["products_id"] . "'></PRODUCTS>" . "\n";
                               }

         $schema .= '</CATEGORIES_DATA>' . "\n";

       }

    $footer = '</CATEGORIES>' . "\n";

  //Ergebnis als XML ausgeben
   create_xml ($header . $schema . $footer, xml_encoding);

}




function delete_categories ()
{

  global $_GET;

  $header = '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
            '<CATEGORIES>' . "\n";


  $categories_id  = db_prepare_input($_GET['categories_id']);


  if (isset($categories_id))
  {

    // Kategorie löschen
    $result1 = mysql_query("delete from " . TABLE_CATEGORIES . " where categories_id = '" . $categories_id . "'");

    // Beschreibungstexte der Kategorie löschen
    $result2 = mysql_query("delete from " . TABLE_CATEGORIES_DESCRIPTION . " where categories_id = '" . $categories_id . "'");

    // Produkte (Zuordnungen) zur Kategorie aktualisieren und in den Root verschieben
    //$result3 = mysql_query("delete from " . TABLE_PRODUCTS_TO_CATEGORIES . " where categories_id = '" . $categories_id . "'");

    $products_categories_query = mysql_query("select products_id from " . TABLE_PRODUCTS_TO_CATEGORIES . " where categories_id = '" . $categories_id . "'");
    while ($products_query = mysql_fetch_array($products_categories_query))
    {
      mysql_query("update " . TABLE_PRODUCTS_TO_CATEGORIES . " set categories_id = '" . CATEGORIES_ROOT . "' where products_id = '" . $products_query['products_id'] . "'");
    }


    $schema = '<CATEGORIES_DATA>' . "\n" .
              '<ACTION>' . $_GET['action'] . '</ACTION>' . "\n" .
              '<CATEGORIES_ID>' . $categories_id . '</CATEGORIES_ID>' . "\n" .
              '<ERROR>' . '0' . '</ERROR>' . "\n" .
              '<MESSAGE>' . 'Die Kategorie wurde gelöscht' . '</MESSAGE>' . "\n" .
              '</CATEGORIES_DATA>' . "\n";

  }
  else
  {

    $schema = '<CATEGORIES_DATA>' . "\n" .
              '<ACTION>' . $_GET['action'] . '</ACTION>' . "\n" .
              '<CATEGORIES_ID>' . $categories_id . '</CATEGORIES_ID>' . "\n" .
              '<ERROR>' . '99' . '</ERROR>' . "\n" .
              '<MESSAGE>' . 'Die Kategorie konnte nicht gelöscht werden' . '</MESSAGE>' . "\n" .
              '</CATEGORIES_DATA>' . "\n";
  }


    $footer = '</CATEGORIES>' . "\n";

  //Ergebnis als XML ausgeben
   create_xml ($header . $schema . $footer, xml_encoding);

}



function delete_categories_all ()
{

  global $_GET;

  $header = '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
            '<CATEGORIES>' . "\n";


    // Kategorien löschen
    $result1 = mysql_query("delete from " . TABLE_CATEGORIES);

    // Beschreibungstexte der Kategorien löschen
    $result2 = mysql_query("delete from " . TABLE_CATEGORIES_DESCRIPTION);


    // Merkmale löschen
    $result3 = mysql_query("delete from " . TABLE_PRODUCTS_OPTIONS);

    // Ausprägungen zu den Merkmale löschen
    $result4 = mysql_query("delete from " . TABLE_PRODUCTS_OPTIONS_VALUES);

    // Zuordnungen der Ausprägungen zu den Merkmalen löschen
    $result5 = mysql_query("delete from " . TABLE_PRODUCTS_OPTIONS_VALUES_TO_PRODUCTS_OPTIONS);


    // Produkte (Zuordnungen) zu den Kategorien aktualisieren und in den Root verschieben
    //$result6 = mysql_query("delete from " . TABLE_PRODUCTS_TO_CATEGORIES);

    $products_categories_query = mysql_query("select products_id from " . TABLE_PRODUCTS_TO_CATEGORIES);
    while ($products_query = mysql_fetch_array($products_categories_query))
    {
      mysql_query("update " . TABLE_PRODUCTS_TO_CATEGORIES . " set categories_id = '" . CATEGORIES_ROOT . "' where products_id = '" . $products_query['products_id'] . "'");
    }


    $schema = '<CATEGORIES_DATA>' . "\n" .
              '<ACTION>' . $_GET['action'] . '</ACTION>' . "\n" .
              '<ERROR>' . '0' . '</ERROR>' . "\n" .
              '<MESSAGE>' . 'Es wurden alle Kategorien gelöscht' . '</MESSAGE>' . "\n" .
              '</CATEGORIES_DATA>' . "\n";

    $footer = '</CATEGORIES>' . "\n";

  //Ergebnis als XML ausgeben
   create_xml ($header . $schema . $footer, xml_encoding);

}



/* =========================================================================================
================================================================= [ Kategorien ] =========*/



/* ============= [ Kundengruppen ] ====================================================== */


function read_customers_status ()
{

  if (defined('SET_TIME_LIMIT')) { @set_time_limit(0);}

    $header = '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
              '<CUSTOMERS_STATUS>' . "\n";


      $customers_query = mysql_query("select customers_status_id, customers_status_name, customers_status_image ".
                                " from " . TABLE_CUSTOMERS_STATUS . " where language_id = '" . LANG_ID . "' order by customers_status_id");


       while ($customers = mysql_fetch_array($customers_query))
       {
         $schema  .= '<CUSTOMERS_STATUS_DATA>' . "\n" .
                     '<ID>' . $customers['customers_status_id'] . '</ID>' . "\n" .
                     '<NAME>' . htmlspecialchars($customers['customers_status_name'], ENT_COMPAT,'ISO-8859-1', true) . '</NAME>' . "\n" .
                     '<IMAGE>' . htmlspecialchars($customers['customers_status_image'], ENT_COMPAT,'ISO-8859-1', true) . '</IMAGE>' . "\n";

         $schema .= '</CUSTOMERS_STATUS_DATA>' . "\n";

       }

    $footer = '</CUSTOMERS_STATUS>' . "\n";

  //Ergebnis als XML ausgeben
   create_xml ($header . $schema . $footer, xml_encoding);

}


function delete_customers_status ()
{

  global $_GET;

  $header = '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
            '<CUSTOMERS_STATUS>' . "\n";


  $customers_status_id  = db_prepare_input($_GET['customers_status_id']);

  if (isset($customers_status_id))
  {

    // Kundengruppe löschen
    $result = mysql_query("delete from " . TABLE_CUSTOMERS_STATUS . " where customers_status_id = '" . $customers_status_id . "'");


    $schema = '<CUSTOMERS_STATUS_DATA>' . "\n" .
              '<ACTION>' . $_GET['action'] . '</ACTION>' . "\n" .
              '<CUSTOMERS_STATUS_ID>' . $customers_status_id . '</CUSTOMERS_STATUS_ID>' . "\n" .
              '<ERROR>' . '0' . '</ERROR>' . "\n" .
              '<MESSAGE>' . 'Die Kundengruppe wurde gelöscht' . '</MESSAGE>' . "\n" .
              '</CUSTOMERS_STATUS_DATA>' . "\n";

  }
  else
  {

    $schema = '<CUSTOMERS_STATUS_DATA>' . "\n" .
              '<ACTION>' . $_GET['action'] . '</ACTION>' . "\n" .
              '<CUSTOMERS_STATUS_ID>' . $customers_status_id . '</CUSTOMERS_STATUS_ID>' . "\n" .
              '<ERROR>' . '99' . '</ERROR>' . "\n" .
              '<MESSAGE>' . 'Die Kundengruppe konnte nicht gelöscht werden' . '</MESSAGE>' . "\n" .
              '</CUSTOMERS_STATUS_DATA>' . "\n";
  }


    $footer = '</CUSTOMERS_STATUS>' . "\n";

  //Ergebnis als XML ausgeben
   create_xml ($header . $schema . $footer, xml_encoding);

}



function delete_customers_status_all ()
{

  global $_GET;

  $header = '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
            '<CUSTOMERS_STATUS>' . "\n";


    // Kundengruppen löschen
    $result = mysql_query("delete from " . TABLE_CUSTOMERS_STATUS);


    $schema = '<CUSTOMERS_STATUS_DATA>' . "\n" .
              '<ACTION>' . $_GET['action'] . '</ACTION>' . "\n" .
              '<ERROR>' . '0' . '</ERROR>' . "\n" .
              '<MESSAGE>' . 'Es wurden alle Kundengruppen gelöscht' . '</MESSAGE>' . "\n" .
              '</CUSTOMERS_STATUS_DATA>' . "\n";

    $footer = '</CUSTOMERS_STATUS>' . "\n";

  //Ergebnis als XML ausgeben
   create_xml ($header . $schema . $footer, xml_encoding);

}



/* =========================================================================================
================================================================= [ Kundengruppen ] ======*/



/* ============= [ Liefertermine ] =======================================================*/

function read_shipping_status ()
{

  if (defined('SET_TIME_LIMIT')) { @set_time_limit(0);}


    $header = '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
              '<SHIPPING>' . "\n";


      $shipping_query = mysql_query("select shipping_status_id, language_id, shipping_status_name, shipping_status_image ".
                                " from " . TABLE_SHIPPING_STATUS . " where language_id = '" . LANG_ID . "' order by shipping_status_id");

       while ($shipping = mysql_fetch_array($shipping_query))
       {
         $schema .= '<SHIPPING_DATA>' . "\n" .
                    '<ID>' . $shipping['shipping_status_id'] . '</ID>' . "\n" .
                    '<NAME>' . htmlspecialchars($shipping['shipping_status_name'], ENT_COMPAT,'ISO-8859-1', true) . '</NAME>' . "\n" .
                    '<IMAGE>' . htmlspecialchars($shipping['shipping_status_image'], ENT_COMPAT,'ISO-8859-1', true) . '</IMAGE>' . "\n";

         $schema .= '</SHIPPING_DATA>' . "\n";

       }


    $footer = '</SHIPPING>' . "\n";

  //Ergebnis als XML ausgeben
   create_xml ($header . $schema . $footer, xml_encoding);

}



function delete_shipping_status ()
{

  global $_GET;

  $header = '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
            '<SHIPPING_STATUS>' . "\n";


  $shipping_status_id  = db_prepare_input($_GET['shipping_status_id']);

  if (isset($shipping_status_id))
  {

    // Liefertermin löschen
    $result = mysql_query("delete from " . TABLE_SHIPPING_STATUS . " where shipping_status_id = '" . $shipping_status_id . "'");


    $schema = '<SHIPPING_STATUS_DATA>' . "\n" .
              '<ACTION>' . $_GET['action'] . '</ACTION>' . "\n" .
              '<SHIPPING_STATUS_ID>' . $shipping_status_id . '</SHIPPING_STATUS_ID>' . "\n" .
              '<ERROR>' . '0' . '</ERROR>' . "\n" .
              '<MESSAGE>' . 'Der Liefertermin wurde gelöscht' . '</MESSAGE>' . "\n" .
              '</SHIPPING_STATUS_DATA>' . "\n";

  }
  else
  {

    $schema = '<SHIPPING_STATUS_DATA>' . "\n" .
              '<ACTION>' . $_GET['action'] . '</ACTION>' . "\n" .
              '<SHIPPING_STATUS_ID>' . $shipping_status_id . '</SHIPPING_STATUS_ID>' . "\n" .
              '<ERROR>' . '99' . '</ERROR>' . "\n" .
              '<MESSAGE>' . 'Der Liefertermin konnte nicht gelöscht werden' . '</MESSAGE>' . "\n" .
              '</SHIPPING_STATUS_DATA>' . "\n";
  }


    $footer = '</SHIPPING_STATUS>' . "\n";

  //Ergebnis als XML ausgeben
   create_xml ($header . $schema . $footer, xml_encoding);

}



function delete_shipping_status_all ()
{

  global $_GET;

  $header = '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
            '<SHIPPING_STATUS>' . "\n";


    // Liefertermine löschen
    $result = mysql_query("delete from " . TABLE_SHIPPING_STATUS);


    $schema = '<SHIPPING_STATUS_DATA>' . "\n" .
              '<ACTION>' . $_GET['action'] . '</ACTION>' . "\n" .
              '<ERROR>' . '0' . '</ERROR>' . "\n" .
              '<MESSAGE>' . 'Es wurden alle Liefertermine gelöscht' . '</MESSAGE>' . "\n" .
              '</SHIPPING_STATUS_DATA>' . "\n";

    $footer = '</SHIPPING_STATUS>' . "\n";

  //Ergebnis als XML ausgeben
   create_xml ($header . $schema . $footer, xml_encoding);

}




/* =========================================================================================
================================================================= [ Liefertermine ] ======*/





/* ============= [ Bestellstatus ] ====================================================== */


function read_orders_status ()
{

  if (defined('SET_TIME_LIMIT')) { @set_time_limit(0);}

    $header = '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
              '<ORDERS_STATUS>' . "\n";


      $orders_query = mysql_query("select orders_status_id, orders_status_name " .
                                   " from " . TABLE_ORDERS_STATUS . " where language_id = '" . LANG_ID . "' order by orders_status_id");


       while ($orders = mysql_fetch_array($orders_query))
       {
         $schema  .= '<ORDERS_STATUS_DATA>' . "\n" .
                     '<ID>' . $orders['orders_status_id'] . '</ID>' . "\n" .
                     '<NAME>' . htmlspecialchars($orders['orders_status_name'], ENT_COMPAT,'ISO-8859-1', true) . '</NAME>' . "\n";
         $schema .= '</ORDERS_STATUS_DATA>' . "\n";

       }

    $footer = '</ORDERS_STATUS>' . "\n";

  //Ergebnis als XML ausgeben
   create_xml ($header . $schema . $footer, xml_encoding);

}



function delete_orders_status ()
{

  global $_GET;

  $header = '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
            '<ORDERS_STATUS>' . "\n";


  $orders_status_id  = db_prepare_input($_GET['orders_status_id']);

  if (isset($orders_status_id))
  {

    // Bestellstatus löschen
    $result = mysql_query("delete from " . TABLE_ORDERS_STATUS . " where orders_status_id = '" . $orders_status_id . "'");


    $schema = '<ORDERS_STATUS_DATA>' . "\n" .
              '<ACTION>' . $_GET['action'] . '</ACTION>' . "\n" .
              '<ORDERS_STATUS_ID>' . $orders_status_id . '</ORDERS_STATUS_ID>' . "\n" .
              '<ERROR>' . '0' . '</ERROR>' . "\n" .
              '<MESSAGE>' . 'Der Bestellstatus wurde gelöscht' . '</MESSAGE>' . "\n" .
              '</ORDERS_STATUS_DATA>' . "\n";

  }
  else
  {

    $schema = '<ORDERS_STATUS_DATA>' . "\n" .
              '<ACTION>' . $_GET['action'] . '</ACTION>' . "\n" .
              '<ORDERS_STATUS_ID>' . $orders_status_id . '</ORDERS_STATUS_ID>' . "\n" .
              '<ERROR>' . '99' . '</ERROR>' . "\n" .
              '<MESSAGE>' . 'Der Bestellstatus konnte nicht gelöscht werden' . '</MESSAGE>' . "\n" .
              '</ORDERS_STATUS_DATA>' . "\n";
  }


    $footer = '</ORDERS_STATUS>' . "\n";

  //Ergebnis als XML ausgeben
   create_xml ($header . $schema . $footer, xml_encoding);

}



function delete_orders_status_all ()
{

  global $_GET;

  $header = '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
            '<ORDERS_STATUS>' . "\n";


    // Bestellstatus löschen
    $result = mysql_query("delete from " . TABLE_ORDERS_STATUS);


    $schema = '<ORDERS_STATUS_DATA>' . "\n" .
              '<ACTION>' . $_GET['action'] . '</ACTION>' . "\n" .
              '<ERROR>' . '0' . '</ERROR>' . "\n" .
              '<MESSAGE>' . 'Es wurden alle Bestellstati gelöscht' . '</MESSAGE>' . "\n" .
              '</ORDERS_STATUS_DATA>' . "\n";

    $footer = '</ORDERS_STATUS>' . "\n";

  //Ergebnis als XML ausgeben
   create_xml ($header . $schema . $footer, xml_encoding);

}



/* =========================================================================================
================================================================= [ Bestellstatus ] =======*/


/* ============= [ Artikel ] ============================================================= */


function read_products ()
{

  if (defined('SET_TIME_LIMIT')) { @set_time_limit(0);}

   $header = '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
             '<PRODUCTS>' . "\n";


       $sql = "select products_id, products_fsk18, products_vpe, products_vpe_status, products_vpe_value, products_quantity, products_model, products_ean, products_image, products_price, " .
              "products_date_added, products_last_modified, products_date_available, products_weight, " .
              "products_status, products_shippingtime, products_startpage, products_startpage_sort, products_sort, product_template, options_template, products_tax_class_id, manufacturers_id, products_ordered, products_discount_allowed from " . TABLE_PRODUCTS;

       $from = db_prepare_input($_GET['products_from']);
       $anz  = db_prepare_input($_GET['products_count']);
       $id  = db_prepare_input($_GET['products_id']);


       /* wenn 'products_id' beim Aufruf übergeben wird, nur der entsprechende Artikel zurückgeben.
          Andernfalls ALLE Artikel zurückgeben */
        if (isset($id))
        {
         $sql .= " where products_id = " . $id;
        }


        if (isset($from))
        {
          if (!isset($anz)) $anz=1000;
          $sql .= " limit " . $from . "," . $anz;
        }


       $products_query = mysql_query($sql);
        while ($products = mysql_fetch_array($products_query))
        {

            $schema .= '<PRODUCT_INFO>' . "\n" .
                       '<PRODUCT_DATA>' . "\n" .
                       '<PRODUCT_ID>' . $products['products_id'] . '</PRODUCT_ID>' . "\n" .
                       '<PRODUCT_DEEPLINK>' . HTTP_SERVER.DIR_WS_CATALOG . 'product_info.php?products_id=' . $products['products_id'] . '</PRODUCT_DEEPLINK>' . "\n" .
                       '<PRODUCT_QUANTITY>' . htmlspecialchars($products['products_quantity'], ENT_COMPAT,'ISO-8859-1', true) . '</PRODUCT_QUANTITY>' . "\n" .
                       '<PRODUCT_MODEL>' . htmlspecialchars($products['products_model'], ENT_COMPAT,'ISO-8859-1', true) . '</PRODUCT_MODEL>' . "\n" .
                       '<PRODUCT_EAN>' . htmlspecialchars($products['products_ean'], ENT_COMPAT,'ISO-8859-1', true) . '</PRODUCT_EAN>' . "\n" .
                       '<PRODUCT_FSK18>' . htmlspecialchars($products['products_fsk18'], ENT_COMPAT,'ISO-8859-1', true) . '</PRODUCT_FSK18>' . "\n";


          // Einheit zum Artikel

              $products_vpe_query = mysql_query("select products_vpe_id, language_id, products_vpe_name ".
                                        " from " . TABLE_PRODUCTS_VPE . " where products_vpe_id = " . $products['products_vpe'] . " and language_id = '" . LANG_ID . "' order by products_vpe_id");

               while ($products_vpe = mysql_fetch_array($products_vpe_query))
               {
                 $schema .= '<PRODUCT_VPE_ID>' . $products_vpe['products_vpe_id'] . '</PRODUCT_VPE_ID>' . "\n" .        //  products_vpe = products_vpe_id
                            '<PRODUCT_VPE_NAME>' . htmlspecialchars($products_vpe['products_vpe_name'], ENT_COMPAT,'ISO-8859-1', true) . '</PRODUCT_VPE_NAME>' . "\n" .
                            '<PRODUCT_VPE_STATUS>' . htmlspecialchars($products['products_vpe_status'], ENT_COMPAT,'ISO-8859-1', true) . '</PRODUCT_VPE_STATUS>' . "\n" .
                            '<PRODUCT_VPE_VALUE>' . htmlspecialchars($products['products_vpe_value'], ENT_COMPAT,'ISO-8859-1', true) . '</PRODUCT_VPE_VALUE>' . "\n";
               }


          // Bild in allen Größen zum Artikel - als in BASE64

            $schema .= '<PRODUCT_IMAGE>' . $products['products_image'] . '</PRODUCT_IMAGE>' . "\n";

                          if ($products['products_image']!='')
                          {
                            $schema .= '<PRODUCT_IMAGE_POPUP>' . HTTP_SERVER.DIR_WS_CATALOG . DIR_WS_POPUP_IMAGES . $products['products_image'] . '</PRODUCT_IMAGE_POPUP>'. "\n" .
                                       '<PRODUCT_IMAGE_SMALL>' . HTTP_SERVER.DIR_WS_CATALOG . DIR_WS_INFO_IMAGES . $products['products_image'] . '</PRODUCT_IMAGE_SMALL>'. "\n" .
                                       '<PRODUCT_IMAGE_THUMBNAIL>' . HTTP_SERVER.DIR_WS_CATALOG . DIR_WS_THUMBNAIL_IMAGES . $products['products_image'] . '</PRODUCT_IMAGE_THUMBNAIL>'. "\n" .
                                       '<PRODUCT_IMAGE_ORIGINAL>' . HTTP_SERVER.DIR_WS_CATALOG . DIR_WS_ORIGINAL_IMAGES . $products['products_image'] . '</PRODUCT_IMAGE_ORIGINAL>'. "\n";
                          }

                        //   // Bild (Original auslesen)
                        //
                        //          $bildname = $products['products_image'];
                        //          $bild = '';
                        //          $pfad = "../".DIR_WS_ORIGINAL_IMAGES;
                        //
                        //            if ($bildname!='' && file_exists($pfad . $bildname))
                        //            {
                        //             $bild = @implode("",@file($pfad . $bildname));
                        //            }
                        //    $schema .= '<PRODUCT_IMAGE_BINARY>' . base64_encode($bild) . "</PRODUCT_IMAGE_BINARY>" . "\n";


          //Grundpreis des Artikels

            $schema .= '<PRODUCT_PRICE>' . htmlspecialchars($products['products_price'], ENT_COMPAT,'ISO-8859-1', true) . '</PRODUCT_PRICE>' . "\n";


          //Kundentyp-Preise

              $customers_status_array = article_get_customers_statuses();
               for ($i=1,$n=sizeof($customers_status_array);$i<$n; $i++)
               {
                 if ($customers_status_array[$i]['id']!=0)
                 {
                   $schema .= "<PRODUCT_GROUP_PRICES ID='" . htmlspecialchars($customers_status_array[$i]['id'], ENT_COMPAT,'ISO-8859-1', true) . "' NAME='" . htmlspecialchars($customers_status_array[$i]['text'], ENT_COMPAT,'ISO-8859-1', true) . "'>". "\n";

                     $group_price_query = mysql_query("SELECT * FROM personal_offers_by_customers_status_" . $customers_status_array[$i]['id'] . " where products_id = '" . $products['products_id'] . "'");

                       //Staffelpreise zum Kundentyp
                        while ($group_price_data=mysql_fetch_array($group_price_query))
                        {
                          $schema .= '<PRICES_BLOCKING>'. "\n" .
                                     '<PRICE_ID>' . $group_price_data['price_id'] . '</PRICE_ID>'. "\n" .
                                     '<PRODUCT_ID>' . $group_price_data['products_id'] . '</PRODUCT_ID>'. "\n" .
                                     '<QTY>' . htmlspecialchars($group_price_data['quantity'], ENT_COMPAT,'ISO-8859-1', true) . '</QTY>'. "\n" .
                                     '<PRICE>' . htmlspecialchars($group_price_data['personal_offer'], ENT_COMPAT,'ISO-8859-1', true) . '</PRICE>'. "\n" .
                                     '</PRICES_BLOCKING>'. "\n";
                        }

                   $schema .= "</PRODUCT_GROUP_PRICES>\n";
                 }
               }



            // Product Options ------------------------------------------------

              $products_attributes='';
              $products_options_data=array();
              $products_options_array =array();

              $products_attributes_query = mysql_query("select count(*) as total
                                                         from " . TABLE_PRODUCTS_OPTIONS . "
                                                          popt, " . TABLE_PRODUCTS_ATTRIBUTES . "
                                                          patrib where
                                                          patrib.products_id='" . $products['products_id'] . "'
                                                          and patrib.options_id = popt.products_options_id
                                                          and popt.language_id = '" . LANG_ID . "'");

              $products_attributes = mysql_fetch_array($products_attributes_query);

              if ($products_attributes['total'] > 0)
               {

                 $products_options_name_query = mysql_query("select distinct
                                                               popt.products_options_id,
                                                               popt.products_options_name
                                                              from " . TABLE_PRODUCTS_OPTIONS . "
                                                               popt, " . TABLE_PRODUCTS_ATTRIBUTES . " patrib
                                                              where patrib.products_id='" . $products['products_id'] . "'
                                                               and patrib.options_id = popt.products_options_id
                                                               and popt.language_id = '" . LANG_ID . "' order by popt.products_options_name");
                 $row = 0;
                 $col = 0;
                 $products_options_data=array();

                   while ($products_options_name = mysql_fetch_array($products_options_name_query))
                   {
                     $selected = 0;
                     $products_options_array = array();

                     $products_options_data[$row]=array('NAME' => $products_options_name['products_options_name'],
                                                        'ID'   => $products_options_name['products_options_id'],
                                                        'DATA' => '');

                     $products_options_query = mysql_query("select
                                                              pov.products_options_values_id,
                                                              pov.products_options_values_name,
                                                              pa.products_attributes_id,
                                                              pa.attributes_model,
                                                              pa.options_values_price,
                                                              pa.options_values_weight,
                                                              pa.price_prefix,
                                                              pa.weight_prefix,
                                                              pa.attributes_stock,
                                                              pa.sortorder
                                                             from " . TABLE_PRODUCTS_ATTRIBUTES . "
                                                              pa, " . TABLE_PRODUCTS_OPTIONS_VALUES . "
                                                              pov where
                                                              pa.products_id = '" . $products['products_id'] . "'
                                                              and pa.options_id = '" . $products_options_name['products_options_id'] . "' and
                                                              pa.options_values_id = pov.products_options_values_id and
                                                              pov.language_id = '" . LANG_ID . "' order by pa.sortorder");

                                                           /* Abschnitt 'order by' wurde verändert!
                                                               --> pov.language_id = '" . LANG_ID . "' order by pa.sortorder, pov.products_options_values_id");
                                                           */

                     $col = 0;

                         while ($products_options = mysql_fetch_array($products_options_query))
                         {
                            $products_options_array[] = array('id'   => $products_options['products_options_values_id'],
                                                              'text' => $products_options['products_options_values_name']);

                              if ($products_options['options_values_price'] != '0')
                              {
                               $products_options_array[sizeof($products_options_array)-1]['text'] .=  ' '.$products_options['price_prefix'].' '.$products_options['options_values_price'].' '.$_SESSION['currency'];
                              }

                            $price='';
                            $products_options_data[$row]['DATA'][$col]=array('ID'            => $products_options['products_options_values_id'],
                                                                             'TEXT'          => $products_options['products_options_values_name'],
                                                                             'MODEL'         => $products_options['attributes_model'],
                                                                             'WEIGHT'        => $products_options['options_values_weight'],
                                                                             'PRICE'         => $products_options['options_values_price'],
                                                                             'WEIGHT_PREFIX' => $products_options['weight_prefix'],
                                                                             'PREFIX'        => $products_options['price_prefix'],
                                                                             'ATTRIBUTES_ID' => $products_options['products_attributes_id']);
                             $col++;
                         }

                     $row++;

                   }

               }


                 if (sizeof($products_options_data)!=0)
                 {
                   for ($i=0,$n=sizeof($products_options_data);$i<$n;$i++)
                   {
                     $schema .= "<PRODUCT_ATTRIBUTES ID='" . $products_options_data[$i]['ID'] . "' NAME='" . htmlspecialchars($products_options_data[$i]['NAME'], ENT_COMPAT,'ISO-8859-1', true) . "'>". "\n";
                       for ($ii=0,$nn=sizeof($products_options_data[$i]['DATA']);$ii<$nn;$ii++)
                       {
                         $schema .= '<OPTION>';
                         $schema .= '<ID>' . htmlspecialchars($products_options_data[$i]['DATA'][$ii]['ID'], ENT_COMPAT,'ISO-8859-1', true) . '</ID>';
                         $schema .= '<MODEL>' . htmlspecialchars($products_options_data[$i]['DATA'][$ii]['MODEL'], ENT_COMPAT,'ISO-8859-1', true) . '</MODEL>';
                         $schema .= '<TEXT>' . htmlspecialchars($products_options_data[$i]['DATA'][$ii]['TEXT'], ENT_COMPAT,'ISO-8859-1', true) . '</TEXT>';
                         $schema .= '<WEIGHT>' . htmlspecialchars($products_options_data[$i]['DATA'][$ii]['WEIGHT'], ENT_COMPAT,'ISO-8859-1', true) . '</WEIGHT>';
                         $schema .= '<PRICE>' . htmlspecialchars($products_options_data[$i]['DATA'][$ii]['PRICE'], ENT_COMPAT,'ISO-8859-1', true) . '</PRICE>';
                         $schema .= '<WEIGHT_PREFIX>' . htmlspecialchars($products_options_data[$i]['DATA'][$ii]['WEIGHT_PREFIX'], ENT_COMPAT,'ISO-8859-1', true) . '</WEIGHT_PREFIX>';
                         $schema .= '<PREFIX>' . htmlspecialchars($products_options_data[$i]['DATA'][$ii]['PREFIX'], ENT_COMPAT,'ISO-8859-1', true) . '</PREFIX>';
                         $schema .= '<ATTRIBUTES_ID>' . htmlspecialchars($products_options_data[$i]['DATA'][$ii]['ATTRIBUTES_ID'], ENT_COMPAT,'ISO-8859-1', true) . '</ATTRIBUTES_ID>';
                         $schema .= '</OPTION>';
                       }
                     $schema .= '</PRODUCT_ATTRIBUTES>';
                   }
                 }


              $schema .= '<PRODUCT_WEIGHT>' . htmlspecialchars($products['products_weight'], ENT_COMPAT,'ISO-8859-1', true) . '</PRODUCT_WEIGHT>' . "\n" .
                         '<PRODUCT_STATUS>' . htmlspecialchars($products['products_status'], ENT_COMPAT,'ISO-8859-1', true) . '</PRODUCT_STATUS>' . "\n" .
                         '<PRODUCT_SHIPPINGTIME>' . htmlspecialchars($products['products_shippingtime'], ENT_COMPAT,'ISO-8859-1', true) . '</PRODUCT_SHIPPINGTIME>' . "\n" .
                         '<PRODUCT_STARTPAGE>' . htmlspecialchars($products['products_startpage'], ENT_COMPAT,'ISO-8859-1', true) . '</PRODUCT_STARTPAGE>' . "\n" .
                         '<PRODUCT_STARTPAGE_SORT>' . htmlspecialchars($products['products_startpage_sort'], ENT_COMPAT,'ISO-8859-1', true) . '</PRODUCT_STARTPAGE_SORT>' . "\n" .
                         '<PRODUCT_SORT>' . htmlspecialchars($products['products_sort'], ENT_COMPAT,'ISO-8859-1', true) . '</PRODUCT_SORT>' . "\n" .
                         '<PRODUCT_TEMPLATE>' . htmlspecialchars($products['product_template'], ENT_COMPAT,'ISO-8859-1', true) . '</PRODUCT_TEMPLATE>' . "\n" .
                         '<OPTIONS_TEMPLATE>' . htmlspecialchars($products['options_template'], ENT_COMPAT,'ISO-8859-1', true) . '</OPTIONS_TEMPLATE>' . "\n" .
                         '<PRODUCT_TAX_CLASS_ID>' . htmlspecialchars($products['products_tax_class_id'], ENT_COMPAT,'ISO-8859-1', true) . '</PRODUCT_TAX_CLASS_ID>' . "\n"  .
                         '<PRODUCT_TAX_RATE>' . articles_get_tax_rate(htmlspecialchars($products['products_tax_class_id'], ENT_COMPAT,'ISO-8859-1', true)) . '</PRODUCT_TAX_RATE>' . "\n"  .
                         '<MANUFACTURERS_ID>' . htmlspecialchars($products['manufacturers_id'], ENT_COMPAT,'ISO-8859-1', true) . '</MANUFACTURERS_ID>' . "\n" .
                         '<PRODUCT_DATE_ADDED>' . htmlspecialchars($products['products_date_added'], ENT_COMPAT,'ISO-8859-1', true) . '</PRODUCT_DATE_ADDED>' . "\n" .
                         '<PRODUCT_LAST_MODIFIED>' . htmlspecialchars($products['products_last_modified'], ENT_COMPAT,'ISO-8859-1', true) . '</PRODUCT_LAST_MODIFIED>' . "\n" .
                         '<PRODUCT_DATE_AVAILABLE>' . htmlspecialchars($products['products_date_available'], ENT_COMPAT,'ISO-8859-1', true) . '</PRODUCT_DATE_AVAILABLE>' . "\n" .
                         '<PRODUCT_ORDERED>' . htmlspecialchars($products['products_ordered'], ENT_COMPAT,'ISO-8859-1', true) . '</PRODUCT_ORDERED>' . "\n" .
                         '<PRODUCT_DISCOUNT_ALLOWED>' . htmlspecialchars($products['products_discount_allowed'], ENT_COMPAT,'ISO-8859-1', true) . '</PRODUCT_DISCOUNT_ALLOWED>' . "\n" ;

                $categories_query=mysql_query("SELECT
                                                 categories_id
                                                FROM " . TABLE_PRODUCTS_TO_CATEGORIES . "
                                                WHERE products_id='" . $products['products_id'] . "'");
                $categories=array();

                 while ($categories_data=mysql_fetch_array($categories_query))
                 {
                   $categories[]=$categories_data['categories_id'];
                 }

                $categories = implode(',',$categories);

                 $schema .= '<PRODUCT_CATEGORIES>' . $categories . '</PRODUCT_CATEGORIES>' . "\n" ;

                 $detail_query = mysql_query("select
                                                products_id,
                                                language_id,
                                                products_name, " . TABLE_PRODUCTS_DESCRIPTION . " .
                                                products_description,
                                                products_short_description,
                                                products_keywords,
                                                products_meta_title,
                                                products_meta_description,
                                                products_meta_keywords,
                                                products_url,
                                                name as language_name, code as language_code " .
                                                                                                    "from " . TABLE_PRODUCTS_DESCRIPTION . ", " . TABLE_LANGUAGES .
                                                                                                    " where " . TABLE_PRODUCTS_DESCRIPTION . ".language_id=" . TABLE_LANGUAGES . ".languages_id " .
                                                                                                    "and " . TABLE_PRODUCTS_DESCRIPTION . ".products_id=" . $products['products_id']);

                  while ($details = mysql_fetch_array($detail_query))
                  {
                    $schema .= "<PRODUCT_DESCRIPTION ID='" . $details["language_id"] ."' CODE='" . $details["language_code"] . "' NAME='" . htmlspecialchars($details["language_name"], ENT_COMPAT,'ISO-8859-1', true) . "'>\n";

                       if ($details["products_name"] !='Array')
                       {
                         $schema .= "<NAME>" . htmlspecialchars($details["products_name"], ENT_COMPAT,'ISO-8859-1', true) . "</NAME>" . "\n" ;
                       }

                         $schema .=  "<URL>" . $details["products_url"] . "</URL>" . "\n" ;

                          $prod_details = $details["products_description"];
                          if ($prod_details != 'Array')
                          {
                            $schema .=  "<DESCRIPTION>" . htmlspecialchars($details["products_description"], ENT_COMPAT,'ISO-8859-1', true) . "</DESCRIPTION>" . "\n";
                            $schema .=  "<SHORT_DESCRIPTION>" . htmlspecialchars($details["products_short_description"], ENT_COMPAT,'ISO-8859-1', true) . "</SHORT_DESCRIPTION>" . "\n";
                            $schema .=  "<KEYWORDS>" . htmlspecialchars($details["products_keywords"], ENT_COMPAT,'ISO-8859-1', true) . "</KEYWORDS>" . "\n";
                            $schema .=  "<META_TITLE>" . htmlspecialchars($details["products_meta_title"], ENT_COMPAT,'ISO-8859-1', true) . "</META_TITLE>" . "\n";
                            $schema .=  "<META_DESCRIPTION>" . htmlspecialchars($details["products_meta_description"], ENT_COMPAT,'ISO-8859-1', true) . "</META_DESCRIPTION>" . "\n";
                            $schema .=  "<META_KEYWORDS>" . htmlspecialchars($details["products_meta_keywords"], ENT_COMPAT,'ISO-8859-1', true) . "</META_KEYWORDS>" . "\n";
                          }
                    $schema .= "</PRODUCT_DESCRIPTION>\n";
                  }



            // zusätzliche Bilder zum Artikel

                 $images_sql = "SELECT image_id, products_id, image_nr, image_name FROM " . TABLE_PRODUCTS_IMAGES . " WHERE products_id='" . $products['products_id'] . "'";
                 $images_query = mysql_query($images_sql);

                  while ($images = mysql_fetch_array($images_query))
                  {
                    $schema .= "<PRODUCT_IMAGES IMAGE_ID = '" . $images["image_id"] . "' PRODUCT_ID = '" . $images["products_id"] . "' IMAGE_NUMBER = '" . $images["image_nr"] . "' IMAGE_NAME = '" . $images["image_name"] . "'>\n";

                         $schema .= '<IMAGE_POPUP>' . HTTP_SERVER . DIR_WS_CATALOG . DIR_WS_POPUP_IMAGES . $images['image_name'] . '</IMAGE_POPUP>'. "\n" .
                                    '<IMAGE_SMALL>' . HTTP_SERVER . DIR_WS_CATALOG . DIR_WS_INFO_IMAGES . $images['image_name'] . '</IMAGE_SMALL>'. "\n" .
                                    '<IMAGE_THUMBNAIL>' . HTTP_SERVER . DIR_WS_CATALOG . DIR_WS_THUMBNAIL_IMAGES . $images['image_name'] . '</IMAGE_THUMBNAIL>'. "\n" .
                                    '<IMAGE_ORIGINAL>' . HTTP_SERVER . DIR_WS_CATALOG . DIR_WS_ORIGINAL_IMAGES . $images['image_name'] . '</IMAGE_ORIGINAL>'. "\n";

                             //     // Bild (Original) in Base64 Codierung speichern
                             //
                             //          $bildname = $images['image_name'];
                             //            $bild = '';
                             //           $pfad = "../".DIR_WS_ORIGINAL_IMAGES;
                             //
                             //             if ($bildname!='' && file_exists($pfad . $bildname))
                             //             {
                             //              $bild = @implode("",@file($pfad . $bildname));
                             //             }
                             //             $schema .= '<IMAGE_BINARY>' . base64_encode($bild) . "</IMAGE_BINARY>" . "\n";

                    $schema .= "</PRODUCT_IMAGES>\n";

                  }


             $schema .= '</PRODUCT_DATA>' . "\n" .
                        '</PRODUCT_INFO>' . "\n";

        }

   $footer = '</PRODUCTS>' . "\n";

  //Ergebnis als XML ausgeben
   create_xml ($header . $schema . $footer, xml_encoding);

}



function read_products_synch ()
{

  if (defined('SET_TIME_LIMIT')) { @set_time_limit(0);}

   $header = '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
             '<PRODUCTS>' . "\n";

   $products_model = db_prepare_input($_GET['products_model']);


   //Produkt laden
   $sql = "select * from " . TABLE_PRODUCTS . " where products_model = '" . $products_model . "'";


       $products_query = mysql_query($sql);
        while ($products = mysql_fetch_array($products_query))
        {

            $schema .= '<PRODUCTS_DATA>' . "\n" .
                       '<PRODUCT_ID>' . $products['products_id'] . '</PRODUCT_ID>' . "\n" .
                       '<PRODUCT_MODEL>' . htmlspecialchars($products['products_model'], ENT_COMPAT,'ISO-8859-1', true) . '</PRODUCT_MODEL>' . "\n";
            $schema .= '</PRODUCTS_DATA>' . "\n";


        }

   $footer = '</PRODUCTS>' . "\n";

  //Ergebnis als XML ausgeben
   create_xml ($header . $schema . $footer, xml_encoding);

}




function article_get_customers_statuses()
{

     $customers_statuses_array = array(array());
     //$customers_statuses_query = mysql_query("select * from " . TABLE_CUSTOMERS_STATUS . " where language_id = '2' order by customers_status_id");
     $customers_statuses_query = mysql_query("select * from " . TABLE_CUSTOMERS_STATUS . " where language_id = '" . LANG_ID . "' order by customers_status_id");


     $i=1;
     while ($customers_statuses = mysql_fetch_array($customers_statuses_query)) {
       $i=$customers_statuses['customers_status_id'];
       $customers_statuses_array[] = array('id'                   => $customers_statuses['customers_status_id'],
                                           'text'                 => $customers_statuses['customers_status_name'],
                                           'csa_public'           => $customers_statuses['customers_status_public'],
                                           'csa_show_price'       => $customers_statuses['customers_status_show_price'],
                                           'csa_show_price_tax'   => $customers_statuses['customers_status_show_price_tax'],
                                           'csa_image'            => $customers_statuses['customers_status_image'],
                                           'csa_discount'         => $customers_statuses['customers_status_discount'],
                                           'csa_ot_discount_flag' => $customers_statuses['customers_status_ot_discount_flag'],
                                           'csa_ot_discount'      => $customers_statuses['customers_status_ot_discount'],
                                           'csa_graduated_prices' => $customers_statuses['customers_status_graduated_prices'],
                                           'csa_cod_permission'   => $customers_statuses['customers_status_cod_permission'],
                                           'csa_cc_permission'    => $customers_statuses['customers_status_cc_permission'],
                                           'csa_bt_permission'    => $customers_statuses['customers_status_bt_permission']);
     }

  return $customers_statuses_array;

}



function articles_get_tax_rate($class_id, $country_id = -1, $zone_id = -1)
{

/* Wird benötigt, da im Script "xtc_get_tax_rate.inc.php" ein Fehler ist.
Dort wird das SQL-Statement mit "xtDBquery" aufgerufen. (Die Funktion gibt es nicht!)
Aus diesem Grund ist hier noch einmal die Funktion, ohne Fehler. */

    if ( ($country_id == -1) && ($zone_id == -1) ) {
      if (!isset($_SESSION['customer_id'])) {
        $country_id = STORE_COUNTRY;
        $zone_id = STORE_ZONE;
      } else {
        $country_id = $_SESSION['customer_country_id'];
        $zone_id = $_SESSION['customer_zone_id'];
      }
     }else{
        $country_id = $country_id;
        $zone_id = $zone_id;
     }

    $tax_query = mysql_query("select sum(tax_rate) as tax_rate from " . TABLE_TAX_RATES . " tr left join " . TABLE_ZONES_TO_GEO_ZONES . " za on (tr.tax_zone_id = za.geo_zone_id) left join " . TABLE_GEO_ZONES . " tz on (tz.geo_zone_id = tr.tax_zone_id) where (za.zone_country_id is null or za.zone_country_id = '0' or za.zone_country_id = '" . $country_id . "') and (za.zone_id is null or za.zone_id = '0' or za.zone_id = '" . $zone_id . "') and tr.tax_class_id = '" . $class_id . "' group by tr.tax_priority");
    if (mysql_num_rows($tax_query) == true)
    {
      $tax_multiplier = 1.0;
      while ($tax = mysql_fetch_array($tax_query))
      {
        $tax_multiplier *= 1.0 + ($tax['tax_rate'] / 100);
      }
      return ($tax_multiplier - 1.0) * 100;
    }
    else
    {
      return 0;
    }

}


function delete_products ()
{

  global $_GET;

  $header = '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
            '<STATUS>' . "\n";


  $products_id = db_prepare_input($_GET['products_id']);

  if (isset($products_id))
  {

    // Artikel in der Kategorien-Zuordnung löschen
    $result = mysql_query("delete from " . TABLE_PRODUCTS_TO_CATEGORIES . " where products_id = '" . $products_id . "'");
    remove_product($products_id);

    $schema = '<STATUS_DATA>' . "\n" .
              '<ACTION>' . $_GET['action'] . '</ACTION>' . "\n" .
              '<PRODUCTS_ID>' . products_id . '</PRODUCTS_ID>' . "\n" .
              '<ERROR>' . '0' . '</ERROR>' . "\n" .
              '<MESSAGE>' . 'Der Artikel wurde gelöscht' . '</MESSAGE>' . "\n" .
              '</STATUS_DATA>' . "\n";

  }
  else
  {

    $schema = '<STATUS_DATA>' . "\n" .
              '<ACTION>' . $_GET['action'] . '</ACTION>' . "\n" .
              '<PRODUCTS_ID>' . $products_id . '</PRODUCTS_ID>' . "\n" .
              '<ERROR>' . '99' . '</ERROR>' . "\n" .
              '<MESSAGE>' . 'Der Artikel konnte nicht gelöscht werden' . '</MESSAGE>' . "\n" .
              '</STATUS_DATA>' . "\n";
  }


    $footer = '</STATUS>' . "\n";

  //Ergebnis als XML ausgeben
   create_xml ($header . $schema . $footer, xml_encoding);

}



function remove_product($product_id)
{

   $product_image_query = mysql_query("select products_image from " . TABLE_PRODUCTS . " where products_id = '" . fn_input($product_id) . "'");
   $product_image = mysql_fetch_array($product_image_query);

   $duplicate_image_query = mysql_query("select count(*) as total from " . TABLE_PRODUCTS . " where products_image = '" . fn_input($product_image['products_image']) . "'");
   $duplicate_image = mysql_fetch_array($duplicate_image_query);

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

     mysql_query("delete from " . TABLE_SPECIALS . " where products_id = '" . fn_input($product_id) . "'");
     mysql_query("delete from " . TABLE_PRODUCTS . " where products_id = '" . fn_input($product_id) . "'");
     mysql_query("delete from " . TABLE_PRODUCTS_TO_CATEGORIES . " where products_id = '" . fn_input($product_id) . "'");
     mysql_query("delete from " . TABLE_PRODUCTS_DESCRIPTION . " where products_id = '" . fn_input($product_id) . "'");
     mysql_query("delete from " . TABLE_PRODUCTS_ATTRIBUTES . " where products_id = '" . fn_input($product_id) . "'");
     mysql_query("delete from " . TABLE_CUSTOMERS_BASKET . " where products_id = '" . fn_input($product_id) . "'");
     mysql_query("delete from " . TABLE_CUSTOMERS_BASKET_ATTRIBUTES . " where products_id = '" . fn_input($product_id) . "'");

      if (defined('TABLE_PRODUCTS_IMAGES'))
      {
        mysql_query("delete from " . TABLE_PRODUCTS_IMAGES . " where products_id = '" . fn_input($product_id) . "'");
      }


       // get statuses
        $customers_statuses_array = array(array());
        $customers_statuses_query = mysql_query("select * from " . TABLE_CUSTOMERS_STATUS . " where language_id = '". LANG_ID ."' order by customers_status_id");
         while ($customers_statuses = mysql_fetch_array($customers_statuses_query))
         {
            $customers_statuses_array[] = array('id' => $customers_statuses['customers_status_id'],
                                                'text' => $customers_statuses['customers_status_name']);
         }


        for ($i=0,$n=sizeof($customers_status_array);$i<$n;$i++)
        {
          mysql_query("delete from personal_offers_by_customers_status_" . $i . " where products_id = '" . fn_input($product_id) . "'");
        }


        $product_reviews_query = mysql_query("select reviews_id from " . TABLE_REVIEWS . " where products_id = '" . fn_input($product_id) . "'");
         while ($product_reviews = mysql_fetch_array($product_reviews_query))
         {
           mysql_query("delete from " . TABLE_REVIEWS_DESCRIPTION . " where reviews_id = '" . $product_reviews['reviews_id'] . "'");
         }
        mysql_query("delete from " . TABLE_REVIEWS . " where products_id = '" . fn_input($product_id) . "'");

}


function products_exists ()
{
  global $_GET;

  $header = '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
            '<STATUS>' . "\n";

  $products_id = db_prepare_input($_GET['products_id']);


  //Produkt laden
  $sql = "select products_quantity,
                 products_model,
                 products_image,
                 products_price,
                 products_date_available,
                 products_weight,
                 products_status,
                 products_ean,
                 products_fsk18,
                 products_shippingtime,
                 products_tax_class_id,
                 manufacturers_id,
                 products_date_added from " . TABLE_PRODUCTS . " where products_id = '" . $products_id . "'";


  $query_count = mysql_query($sql);
  if ($product = mysql_fetch_array($query_count))
  {

    //Produkt ist vorhanden ---------------------------------------
     $exists = 1;
     $message = 'Der Artikel ist bereits vorhanden';

  }
  else
  {

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
   create_xml ($header . $schema . $footer, xml_encoding);

}



function read_products_specials ()
{

  global $_GET;

   $header = '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
             '<SPECIALS>' . "\n";


     $sql = "select specials_id, products_id, specials_quantity, specials_new_products_price, specials_date_added, specials_last_modified, expires_date, date_status_change, status from " . TABLE_SPECIALS;
     $specials_query = mysql_query($sql);

      while ($specials = mysql_fetch_array($specials_query))
      {

       $schema .= '<SPECIALS_DATA>' . "\n" .
                  '<ID>' . $specials['specials_id'] . '</ID>' . "\n" .
                  '<PRODUCTS_ID>' . $specials['products_id'] . '</PRODUCTS_ID>' . "\n" .
                  '<QUANTITY>' . htmlspecialchars($specials['specials_quantity'], ENT_COMPAT,'ISO-8859-1', true) . '</QUANTITY>' . "\n" .
                  '<NEW_PRICE>' . htmlspecialchars($specials['specials_new_products_price'], ENT_COMPAT,'ISO-8859-1', true) . '</NEW_PRICE>' . "\n" .
                  '<DATE_ADDED>' . htmlspecialchars($specials['specials_date_added'], ENT_COMPAT,'ISO-8859-1', true) . '</DATE_ADDED>' . "\n" .
                  '<LAST_MODIFIED>' . htmlspecialchars($specials['specials_last_modified'], ENT_COMPAT,'ISO-8859-1', true) . '</LAST_MODIFIED>' . "\n" .
                  '<EXPIRES_DATE>' . htmlspecialchars($specials['expires_date'], ENT_COMPAT,'ISO-8859-1', true) . '</EXPIRES_DATE>' . "\n" .
                  '<DATE_STATUS_CHANGE>' . htmlspecialchars($specials['date_status_change'], ENT_COMPAT,'ISO-8859-1', true) . '</DATE_STATUS_CHANGE>' . "\n" .
                  '<STATUS>' . htmlspecialchars($specials['status'], ENT_COMPAT,'ISO-8859-1', true) . '</STATUS>' . "\n" .
                  '</SPECIALS_DATA>' . "\n";
      }

    $footer = '</SPECIALS>' . "\n";

   //Ergebnis als XML ausgeben
    create_xml ($header . $schema . $footer, xml_encoding);

}

function delete_products_specials ()
{

  global $_GET;

  $header = '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
            '<SPECIALS>' . "\n";


  $specials_id  = db_prepare_input($_GET['specials_id']);

  if (isset($specials_id))
  {

    // Sonderpreise löschen
    $result = mysql_query("delete from " . TABLE_SPECIALS . " where specials_id = '" . $specials_id . "'");


    $schema = '<SPECIALS_DATA>' . "\n" .
              '<ACTION>' . $_GET['action'] . '</ACTION>' . "\n" .
              '<SPECIALS_ID>' . $specials_id . '</SPECIALS_ID>' . "\n" .
              '<ERROR>' . '0' . '</ERROR>' . "\n" .
              '<MESSAGE>' . 'Der Sonderpreis wurde gelöscht' . '</MESSAGE>' . "\n" .
              '</SPECIALS_DATA>' . "\n";

  }
  else
  {

    $schema = '<SPECIALS_DATA>' . "\n" .
              '<ACTION>' . $_GET['action'] . '</ACTION>' . "\n" .
              '<SPECIALS_ID>' . $specials_id . '</SPECIALS_ID>' . "\n" .
              '<ERROR>' . '99' . '</ERROR>' . "\n" .
              '<MESSAGE>' . 'Der Sonderpreis konnte nicht gelöscht werden' . '</MESSAGE>' . "\n" .
              '</SPECIALS_DATA>' . "\n";
  }


    $footer = '</SPECIALS>' . "\n";

  //Ergebnis als XML ausgeben
   create_xml ($header . $schema . $footer, xml_encoding);

}



function delete_products_specials_all ()
{

  global $_GET;

  $header = '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
            '<SPECIALS>' . "\n";


    // Sonderpreise löschen
    $result = mysql_query("delete from " . TABLE_SPECIALS);


    $schema = '<SPECIALS_DATA>' . "\n" .
              '<ACTION>' . $_GET['action'] . '</ACTION>' . "\n" .
              '<ERROR>' . '0' . '</ERROR>' . "\n" .
              '<MESSAGE>' . 'Es wurden alle Sonderpreise gelöscht' . '</MESSAGE>' . "\n" .
              '</SPECIALS_DATA>' . "\n";

    $footer = '</SPECIALS>' . "\n";

  //Ergebnis als XML ausgeben
   create_xml ($header . $schema . $footer, xml_encoding);

}


function read_products_stockings ()
{

  if (defined('SET_TIME_LIMIT')) { @set_time_limit(0);}

   $header = '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
             '<PRODUCTS>' . "\n";


       $sql = "select products_id, products_quantity, products_model, products_shippingtime, products_status from " . TABLE_PRODUCTS;

       $id  = db_prepare_input($_GET['products_id']);

       /* wenn 'products_id' beim Aufruf übergeben wird, nur der entsprechende Artikel zurückgeben.
          Andernfalls ALLE Artikel zurückgeben */
        if (isset($id))
        {
         $sql .= " where products_id = " . $id;
        }


       $products_query = mysql_query($sql);
        while ($products = mysql_fetch_array($products_query))
        {

            $schema .= '<PRODUCT_DATA>' . "\n" .
                       '<PRODUCT_ID>' . $products['products_id'].'</PRODUCT_ID>' . "\n" .
                       '<PRODUCT_MODEL>' . htmlspecialchars($products['products_model'], ENT_COMPAT,'ISO-8859-1', true) . '</PRODUCT_MODEL>' . "\n" .
                       '<PRODUCT_QUANTITY>' . htmlspecialchars($products['products_quantity'], ENT_COMPAT,'ISO-8859-1', true) . '</PRODUCT_QUANTITY>' . "\n" .
                       '<PRODUCT_SHIPPINGTIME>' . htmlspecialchars($products['products_shippingtime'], ENT_COMPAT,'ISO-8859-1', true) . '</PRODUCT_SHIPPINGTIME>' . "\n" .
                       '<PRODUCT_STATUS>' . htmlspecialchars($products['products_status'], ENT_COMPAT,'ISO-8859-1', true) . '</PRODUCT_STATUS>' . "\n" .
                       '</PRODUCT_DATA>' . "\n";

        }

   $footer = '</PRODUCTS>' . "\n";

  //Ergebnis als XML ausgeben
   create_xml ($header . $schema . $footer, xml_encoding);

}



function read_products_stockings_attributes ()
{

  if (defined('SET_TIME_LIMIT')) { @set_time_limit(0);}

   $header = '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
             '<PRODUCTS>' . "\n";


       $sql = "select products_id, attributes_model, attributes_stock from " . TABLE_PRODUCTS_ATTRIBUTES;

       $id  = db_prepare_input($_GET['products_id']);

       /* wenn 'products_id' beim Aufruf übergeben wird, nur der entsprechende Artikel zurückgeben.
          Andernfalls ALLE Artikel zurückgeben */
        if (isset($id))
        {
         $sql .= " where products_id = " . $id;
        }


       $products_query = mysql_query($sql);
        while ($products = mysql_fetch_array($products_query))
        {
            $schema .= '<PRODUCT_DATA>' . "\n" .
                       '<PRODUCT_ID>' . $products['products_id'].'</PRODUCT_ID>' . "\n" .
                       '<PRODUCT_MODEL>' . htmlspecialchars($products['attributes_model'], ENT_COMPAT,'ISO-8859-1', true) . '</PRODUCT_MODEL>' . "\n" .
                       '<PRODUCT_QUANTITY>' . htmlspecialchars($products['attributes_stock'], ENT_COMPAT,'ISO-8859-1', true) . '</PRODUCT_QUANTITY>' . "\n" .
                       '</PRODUCT_DATA>' . "\n";
        }

   $footer = '</PRODUCTS>' . "\n";

  //Ergebnis als XML ausgeben
   create_xml ($header . $schema . $footer, xml_encoding);

}



function read_products_vpe ()
{

  if (defined('SET_TIME_LIMIT')) { @set_time_limit(0);}


    $header = '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
              '<PRODUCTS_VPE>' . "\n";


      $products_vpe_query = mysql_query("select products_vpe_id, language_id, products_vpe_name ".
                                " from " . TABLE_PRODUCTS_VPE . " where language_id = '" . LANG_ID . "' order by products_vpe_id");

       while ($products_vpe = mysql_fetch_array($products_vpe_query))
       {
         $schema .= '<PRODUCTS_VPE_DATA>' . "\n" .
                    '<ID>' . $products_vpe['products_vpe_id'] . '</ID>' . "\n" .
                    '<NAME>' . htmlspecialchars($products_vpe['products_vpe_name'], ENT_COMPAT,'ISO-8859-1', true) . '</NAME>' . "\n";

         $schema .= '</PRODUCTS_VPE_DATA>' . "\n";

       }


    $footer = '</PRODUCTS_VPE>' . "\n";

  //Ergebnis als XML ausgeben
   create_xml ($header . $schema . $footer, xml_encoding);

}



function delete_products_vpe ()
{

  global $_GET;

  $header = '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
            '<PRODUCTS_VPE>' . "\n";


  $products_vpe_id  = db_prepare_input($_GET['products_vpe_id']);

  if (isset($products_vpe_id))
  {

    // Verpackungseinheit löschen
    $result = mysql_query("delete from " . TABLE_PRODUCTS_VPE . " where products_vpe_id = '" . $products_vpe_id . "'");


    $schema = '<PRODUCTS_VPE_DATA>' . "\n" .
              '<ACTION>' . $_GET['action'] . '</ACTION>' . "\n" .
              '<PRODUCTS_VPE_ID>' . $products_vpe_id . '</PRODUCTS_VPE_ID>' . "\n" .
              '<ERROR>' . '0' . '</ERROR>' . "\n" .
              '<MESSAGE>' . 'Die Verpackungseinheit wurde gelöscht' . '</MESSAGE>' . "\n" .
              '</PRODUCTS_VPE_DATA>' . "\n";

  }
  else
  {

    $schema = '<PRODUCTS_VPE_DATA>' . "\n" .
              '<ACTION>' . $_GET['action'] . '</ACTION>' . "\n" .
              '<PRODUCTS_VPE_ID>' . $products_vpe_id . '</PRODUCTS_VPE_ID>' . "\n" .
              '<ERROR>' . '99' . '</ERROR>' . "\n" .
              '<MESSAGE>' . 'Die Verpackungseinheit konnte nicht gelöscht werden' . '</MESSAGE>' . "\n" .
              '</PRODUCTS_VPE_DATA>' . "\n";
  }


    $footer = '</PRODUCTS_VPE>' . "\n";

  //Ergebnis als XML ausgeben
   create_xml ($header . $schema . $footer, xml_encoding);

}



function delete_products_vpe_all ()
{

  global $_GET;

  $header = '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
            '<PRODUCTS_VPE>' . "\n";


    // Verpackungseinheiten löschen
    $result = mysql_query("delete from " . TABLE_PRODUCTS_VPE);


    $schema = '<PRODUCTS_VPE_DATA>' . "\n" .
              '<ACTION>' . $_GET['action'] . '</ACTION>' . "\n" .
              '<ERROR>' . '0' . '</ERROR>' . "\n" .
              '<MESSAGE>' . 'Es wurden alle Verpackungseinheiten gelöscht' . '</MESSAGE>' . "\n" .
              '</PRODUCTS_VPE_DATA>' . "\n";

    $footer = '</PRODUCTS_VPE>' . "\n";

  //Ergebnis als XML ausgeben
   create_xml ($header . $schema . $footer, xml_encoding);

}



function read_products_categories ()
{

  if (defined('SET_TIME_LIMIT')) { @set_time_limit(0);}


    $header = '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
              '<PRODUCTS_CATEGORIES>' . "\n";


    $products_id = db_prepare_input($_GET['products_id']);


      $products_categories_query = mysql_query("select products_id, categories_id ".
                                " from " . TABLE_PRODUCTS_TO_CATEGORIES . " where products_id = '" . $products_id . "' order by categories_id");

       while ($products_categories = mysql_fetch_array($products_categories_query))
       {
         $schema .= '<PRODUCTS_CATEGORIES_DATA>' . "\n" .
                    '<PRODUCTS_ID>' . $products_categories['products_id'] . '</PRODUCTS_ID>' . "\n" .
                    '<CATEGORIES_ID>' . $products_categories['categories_id'] . '</CATEGORIES_ID>' . "\n";

         $schema .= '</PRODUCTS_CATEGORIES_DATA>' . "\n";

       }


    $footer = '</PRODUCTS_CATEGORIES>' . "\n";

  //Ergebnis als XML ausgeben
   create_xml ($header . $schema . $footer, xml_encoding);

}


function delete_products_categories ()
{

  global $_GET;

  $header = '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
            '<PRODUCTS_CATEGORIES>' . "\n";


  $products_id  = db_prepare_input($_GET['products_id']);

  if (isset($products_id))
  {

    // Kategorien zum Produkt löschen (auch ohne Schleife werden alle Datensätze mit produkt-id x gelöscht)
    $result = mysql_query("delete from " . TABLE_PRODUCTS_TO_CATEGORIES . " where products_id = '" . $products_id . "'");

    $schema = '<PRODUCTS_CATEGORIES_DATA>' . "\n" .
              '<ACTION>' . $_GET['action'] . '</ACTION>' . "\n" .
              '<PRODUCTS_ID>' . $products_id . '</PRODUCTS_ID>' . "\n" .
              '<ERROR>' . '0' . '</ERROR>' . "\n" .
              '<MESSAGE>' . 'Die Kategorien zum Produkt wurden gelöscht' . '</MESSAGE>' . "\n" .
              '</PRODUCTS_CATEGORIES_DATA>' . "\n";

  }
  else
  {

    $schema = '<PRODUCTS_CATEGORIES_DATA>' . "\n" .
              '<ACTION>' . $_GET['action'] . '</ACTION>' . "\n" .
              '<PRODUCTS_ID>' . $products_id . '</PRODUCTS_ID>' . "\n" .
              '<ERROR>' . '99' . '</ERROR>' . "\n" .
              '<MESSAGE>' . 'Die Kategorien konnten nicht gelöscht werden' . '</MESSAGE>' . "\n" .
              '</PRODUCTS_CATEGORIES_DATA>' . "\n";
  }


    $footer = '</PRODUCTS_CATEGORIES>' . "\n";

  //Ergebnis als XML ausgeben
   create_xml ($header . $schema . $footer, xml_encoding);

}



function delete_products_categories_all ()
{

  global $_GET;

  $header = '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
            '<PRODUCTS_CATEGORIES>' . "\n";


    // Kategorien löschen
    $result = mysql_query("delete from " . TABLE_PRODUCTS_TO_CATEGORIES);


    $schema = '<PRODUCTS_CATEGORIES_DATA>' . "\n" .
              '<ACTION>' . $_GET['action'] . '</ACTION>' . "\n" .
              '<ERROR>' . '0' . '</ERROR>' . "\n" .
              '<MESSAGE>' . 'Es wurden alle Kategorien zu den Produkten gelöscht' . '</MESSAGE>' . "\n" .
              '</PRODUCTS_CATEGORIES_DATA>' . "\n";

    $footer = '</PRODUCTS_CATEGORIES>' . "\n";

  //Ergebnis als XML ausgeben
   create_xml ($header . $schema . $footer, xml_encoding);

}



function read_products_list ()
{

  if (defined('SET_TIME_LIMIT')) { @set_time_limit(0);}

   $header = '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
             '<PRODUCTS>' . "\n";


       $sql = "select products_id, products_model, products_ean, products_quantity, products_shippingtime, products_weight, products_status, products_price, products_date_added, products_last_modified from " . TABLE_PRODUCTS;
       $id  = db_prepare_input($_GET['products_id']);


       /* wenn 'products_id' beim Aufruf übergeben wird, nur der entsprechende Artikel zurückgeben.
          Andernfalls ALLE Artikel zurückgeben */
        if (isset($id))
        {
         $sql .= " where products_id = " . $id;
        }

       $products_query = mysql_query($sql);
        while ($products = mysql_fetch_array($products_query))
        {

            $schema .= '<PRODUCT_INFO>' . "\n" .
                       '<PRODUCT_DATA>' . "\n" .
                       '<PRODUCT_ID>' . htmlspecialchars($products['products_id'], ENT_COMPAT,'ISO-8859-1', true) . '</PRODUCT_ID>' . "\n" .
                       '<PRODUCT_MODEL>' . htmlspecialchars($products['products_model'], ENT_COMPAT,'ISO-8859-1', true) . '</PRODUCT_MODEL>' . "\n" .
                       '<PRODUCT_EAN>' . htmlspecialchars($products['products_ean'], ENT_COMPAT,'ISO-8859-1', true) . '</PRODUCT_EAN>' . "\n" .
                       '<PRODUCT_QUANTITY>' . htmlspecialchars($products['products_quantity'], ENT_COMPAT,'ISO-8859-1', true) . '</PRODUCT_QUANTITY>' . "\n" .
                       '<PRODUCT_SHIPPINGTIME>' . htmlspecialchars($products['products_shippingtime'], ENT_COMPAT,'ISO-8859-1', true) . '</PRODUCT_SHIPPINGTIME>' . "\n" .
                       '<PRODUCT_WEIGHT>' . htmlspecialchars($products['products_weight'], ENT_COMPAT,'ISO-8859-1', true) . '</PRODUCT_WEIGHT>' . "\n" .
                       '<PRODUCT_STATUS>' . htmlspecialchars($products['products_status'], ENT_COMPAT,'ISO-8859-1', true) . '</PRODUCT_STATUS>' . "\n" .
                       '<PRODUCT_PRICE>' . htmlspecialchars($products['products_price'], ENT_COMPAT,'ISO-8859-1', true) . '</PRODUCT_PRICE>' . "\n" .
                       '<PRODUCT_DATE_ADDED>' . htmlspecialchars($products['products_date_added'], ENT_COMPAT,'ISO-8859-1', true) . '</PRODUCT_DATE_ADDED>' . "\n" .
                       '<PRODUCT_LAST_MODIFIED>' . htmlspecialchars($products['products_last_modified'], ENT_COMPAT,'ISO-8859-1', true) . '</PRODUCT_LAST_MODIFIED>' . "\n" ;


              $detail_query = mysql_query("select products_id, language_id, products_name, " . TABLE_PRODUCTS_DESCRIPTION . " .
                                                   products_short_description, name as language_name, code as language_code " .
                                           "from " . TABLE_PRODUCTS_DESCRIPTION . ", " . TABLE_LANGUAGES .
                                           " where " . TABLE_PRODUCTS_DESCRIPTION . ".language_id=" . TABLE_LANGUAGES . ".languages_id " .
                                           "and " . TABLE_PRODUCTS_DESCRIPTION . ".products_id=" . $products['products_id']);

                  while ($details = mysql_fetch_array($detail_query))
                  {
                    $schema .= "<PRODUCT_DESCRIPTION ID='" . $details["language_id"] ."' CODE='" . $details["language_code"] . "' NAME='" . $details["language_name"] . "'>\n";

                       if ($details["products_name"] !='Array')
                       {
                         $schema .= "<NAME>" . htmlspecialchars($details["products_name"], ENT_COMPAT,'ISO-8859-1', true) . "</NAME>" . "\n" ;
                       }

                          $prod_details = $details["products_description"];
                          if ($prod_details != 'Array')
                          {
                            $schema .=  "<SHORT_DESCRIPTION>" . htmlspecialchars($details["products_short_description"], ENT_COMPAT,'ISO-8859-1', true) . "</SHORT_DESCRIPTION>" . "\n";
                          }
                    $schema .= "</PRODUCT_DESCRIPTION>\n";
                  }

            $schema .= '</PRODUCT_DATA>' . "\n" .
                       '</PRODUCT_INFO>' . "\n";

        }

   $footer = '</PRODUCTS>' . "\n";

  //Ergebnis als XML ausgeben
   create_xml ($header . $schema . $footer, xml_encoding);

}



/* ============= [ Hersteller ] ============================================================= */

function read_manufacturers ()
{

  if (defined('SET_TIME_LIMIT')) { @set_time_limit(0);}

    $header = '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
              '<MANUFACTURERS>' . "\n";


      $manu_query = mysql_query("select manufacturers_id, manufacturers_name, manufacturers_image, date_added, last_modified from " . TABLE_MANUFACTURERS . " order by manufacturers_id");

       while ($manufacturers = mysql_fetch_array($manu_query))
       {
         $schema .= '<MANUFACTURERS_DATA>' . "\n" .
                    '<ID>' . $manufacturers['manufacturers_id'] . '</ID>' . "\n" .
                    '<NAME>' . htmlspecialchars($manufacturers['manufacturers_name'], ENT_COMPAT,'ISO-8859-1', true) . '</NAME>' . "\n" .
                    '<IMAGE>' . htmlspecialchars($manufacturers['manufacturers_image'], ENT_COMPAT,'ISO-8859-1', true) . '</IMAGE>' . "\n" .
                    '<DATE_ADDED>' . htmlspecialchars($manufacturers['date_added'], ENT_COMPAT,'ISO-8859-1', true) . '</DATE_ADDED>' . "\n" .
                    '<LAST_MODIFIED>' . htmlspecialchars($manufacturers['last_modified'], ENT_COMPAT,'ISO-8859-1', true) . '</LAST_MODIFIED>' . "\n";

           $sql = "select
                   manufacturers_id, " .
                   TABLE_MANUFACTURERS_INFO . ".languages_id,
                   manufacturers_url,
                   url_clicked,
                   date_last_click, " .
                   TABLE_LANGUAGES . ".code as lang_code, " .
                   TABLE_LANGUAGES . ".name as lang_name
                  from " .
                   TABLE_MANUFACTURERS_INFO . "," .
                   TABLE_LANGUAGES . "
                  where " .
                   TABLE_MANUFACTURERS_INFO . ".manufacturers_id=" . $manufacturers['manufacturers_id'] . " and " .
                   TABLE_LANGUAGES . ".languages_id=" . TABLE_MANUFACTURERS_INFO . ".languages_id";

           $manufacturers_details_query = mysql_query($sql);

            while ($manufacturers_details = mysql_fetch_array($manufacturers_details_query))
            {
              $schema .= "<MANUFACTURERS_DESCRIPTION ID='" . $manufacturers_details["languages_id"] ."' CODE='" . $manufacturers_details["lang_code"] . "' NAME='" . $manufacturers_details["lang_name"] . "'>\n";
              $schema .= "<URL>" . htmlspecialchars($manufacturers_details["manufacturers_url"], ENT_COMPAT,'ISO-8859-1', true) . "</URL>" . "\n" ;
              $schema .= "<URL_CLICK>" . htmlspecialchars($manufacturers_details["url_clicked"], ENT_COMPAT,'ISO-8859-1', true) . "</URL_CLICK>" . "\n" ;
              $schema .= "<DATE_LAST_CLICK>" . htmlspecialchars($manufacturers_details["date_last_click"], ENT_COMPAT,'ISO-8859-1', true) . "</DATE_LAST_CLICK>" . "\n" ;
              $schema .= "</MANUFACTURERS_DESCRIPTION>\n";
            }

         $schema .= '</MANUFACTURERS_DATA>' . "\n";

       }


    $footer = '</MANUFACTURERS>' . "\n";

  //Ergebnis als XML ausgeben
   create_xml ($header . $schema . $footer, xml_encoding);

}



function save_manufacturers()
{

  $header = '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
            '<STATUS>' . "\n";

     $manufacturers_id = db_prepare_input($_POST['manufacturers_id']);
     $manufacturers_name = db_prepare_input($_POST['manufacturers_name']);


     //Hersteller laden
      $sql = "select manufacturers_id, manufacturers_name, manufacturers_image, date_added, last_modified from " . TABLE_MANUFACTURERS . " where manufacturers_id = '" . $manufacturers_id . "'";


      $count_query = mysql_query($sql);
      if ($manufacturers = mysql_fetch_array($count_query))
      {
        $exists = 1;
      }
      else
      {
        $exists = 0;
      }


        // Variablen nur überschreiben, wenn sie als Parameter übergeben worden sind
         $manufacturers_id       = db_prepare_input($_POST['manufacturers_id']);
         $manufacturers_name     = db_prepare_input($_POST['manufacturers_name']);
         $manufacturers_image    = db_prepare_input($_POST['manufacturers_image']);
         $manufacturers_url      = db_prepare_input($_POST['manufacturers_url']);

         $sql_data_array = array('manufacturers_name'     => $manufacturers_name,
                                 'manufacturers_image'    => $manufacturers_image);


      if ($exists==0)        // Neuanlage
      {

          $insert_sql_data = array('date_added'         => 'now()',
                                   'last_modified'      => 'now()');

          $sql_data_array = array_merge($sql_data_array, $insert_sql_data);
          database_insert(TABLE_MANUFACTURERS, $sql_data_array);
          $manufacturers_id = mysql_insert_id();

      }
      elseif ($exists==1)    //Aktualisieren
      {
          database_insert(TABLE_MANUFACTURERS, $sql_data_array, 'update', 'manufacturers_id = \'' . $manufacturers_id . '\'');
      }



    //weitere Angaben des Herstellers

     $sql_data_array = array('manufacturers_url'         => $manufacturers_url);

      if ($exists==0)        // Neuanlage
      {

          $insert_sql_data = array('manufacturers_id'    => $manufacturers_id,
                                   'languages_id'        => LANG_ID);

          $sql_data_array = array_merge($sql_data_array, $insert_sql_data);
          database_insert(TABLE_MANUFACTURERS_INFO, $sql_data_array);

      }
      elseif ($exists==1)    //Aktualisieren
      {
          database_insert(TABLE_PRODUCTS_DESCRIPTION, $sql_data_array, 'update', 'manufacturers_id = \'' . $manufacturers_id . '\' and languages_id = \'' . LANG_ID . '\'');
      }


    $schema = '<STATUS_DATA>' . "\n" .
              '<EXISTS>' . $exists . '</EXISTS>' . "\n" .
              '<MANUFACTURERS_ID>' . $manufacturers_id . '</MANUFACTURERS_ID>' . "\n" .
              '<MESSAGE>' . 'Keine Meldung vorhanden' . '</MESSAGE>' . "\n" .
              '</STATUS_DATA>' . "\n";


    $footer = '</STATUS>' . "\n";

  //Ergebnis als XML ausgeben
   create_xml ($header . $schema . $footer, xml_encoding);

}



function create_couponcode ()
{

  global $_GET;

   $header = '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
            '<STATUS>' . "\n";


    $email = db_prepare_input($_GET['email']);


    $couponcode = create_coupon_code($email);
    $link = HTTP_SERVER  . DIR_WS_CATALOG . 'gv_redeem.php' . '?gv_no='.$couponcode;


    $schema = '<STATUS_DATA>' . "\n" .
              '<COUPON_CODE>' . $couponcode . '</COUPON_CODE>' . "\n" .
              '<LINK>' . $link . '</LINK>' . "\n" .
              '<MESSAGE>' . 'Keine Meldung vorhanden' . '</MESSAGE>' . "\n" .
              '</STATUS_DATA>' . "\n";


   $footer = '</STATUS>' . "\n";


 //Ergebnis als XML ausgeben
   create_xml ($header . $schema . $footer, xml_encoding);

}


// Create a Coupon Code. length may be between 1 and 16 Characters
// $salt needs some thought.

function create_coupon_code($salt="secret", $length = SECURITY_CODE_LENGTH)
{
    $ccid = md5(uniqid("","salt"));
    $ccid .= md5(uniqid("","salt"));
    $ccid .= md5(uniqid("","salt"));
    $ccid .= md5(uniqid("","salt"));
    srand((double)microtime()*1000000); // seed the random number generator
    $random_start = @rand(0, (128-$length));
    $good_result = 0;
    while ($good_result == 0) {
      $id1=substr($ccid, $random_start,$length);
      $query = mysql_query("select coupon_code from " . TABLE_COUPONS . " where coupon_code = '" . $id1 . "'");
      if (mysql_num_rows($query) == 0) $good_result = 1;
    }
    return $id1;
}


/* ============================================================================================
===================================================================== [ Gutscheine ] ========*/



function read_products_options_id ()
{

  global $_GET;

   $header = '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
             '<STATUS>' . "\n";


    //Merkmale einlesen
    $options_sql = "select products_options_id, products_options_name, language_id " .
           "from " . TABLE_PRODUCTS_OPTIONS . " ORDER BY `products_options_id` DESC LIMIT 1";


    $options_res = mysql_query($options_sql);

     while ($options = mysql_fetch_array($options_res))
     {

         $schema = '<STATUS_DATA>' . "\n" .
                   '<ID>' . $options['products_options_id'] . '</ID>' . "\n" .
                   '<NAME>' . htmlspecialchars($options['products_options_name'], ENT_COMPAT,'ISO-8859-1', true) . '</NAME>' . "\n" .
                   '<LANGUAGE>' . $options['language_id'] . '</LANGUAGE>' . "\n" .
                   '</STATUS_DATA>' . "\n";
     }


   $footer = '</STATUS>' . "\n";

  //Ergebnis als XML ausgeben
   create_xml ($header . $schema . $footer, xml_encoding);

}


function read_products_options_value_id ()
{

  global $_GET;

   $header = '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
             '<STATUS>' . "\n";


    //Ausprägungen einlesen
    $options_sql = "select products_options_values_id, products_options_values_name, language_id " .
           "from " . TABLE_PRODUCTS_OPTIONS_VALUES . " ORDER BY `products_options_values_id` DESC LIMIT 1";


    $options_res = mysql_query($options_sql);

     while ($options = mysql_fetch_array($options_res))
     {

         $schema = '<STATUS_DATA>' . "\n" .
                   '<ID>' . $options['products_options_values_id'] . '</ID>' . "\n" .
                   '<NAME>' . htmlspecialchars($options['products_options_values_name'], ENT_COMPAT,'ISO-8859-1', true) . '</NAME>' . "\n" .
                   '<LANGUAGE>' . $options['language_id'] . '</LANGUAGE>' . "\n" .
                   '</STATUS_DATA>' . "\n";
     }


   $footer = '</STATUS>' . "\n";

  //Ergebnis als XML ausgeben
   create_xml ($header . $schema . $footer, xml_encoding);

}












function write_xml_error ($code, $action, $msg)
{
 // Rückgabe einer Fehlermeldung
  $schema = '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
            '<STATUS>' . "\n" .
            '<STATUS_DATA>' . "\n" .
            '<CODE>' . $code . '</CODE>' . "\n" .
            '<ACTION>' . $action . '</ACTION>' . "\n" .
            '<MESSAGE>' . $msg . '</MESSAGE>' . "\n" .
            '</STATUS_DATA>' . "\n" .
            '</STATUS>' . "\n\n";

   echo $schema;
 return;
}



function create_xml($content, $encoding="0")
{

 //XML erzeugen aus $schema erzeugen --------------------

 /*
  $fh = fopen('export.xml','w') or die($php_errormsg);
        fwrite($fh,$content);
        fclose($fh) or die($php_errormsg);
 */

   //echo wird als Request ausgelesen

    if ($encoding==0)
    {
      echo $content;
    }
    elseif ($encoding==1)
    {
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
   create_xml ($schema, xml_encoding);

}


function administration_account_check()
{

  global $_GET;

  if (defined('SET_TIME_LIMIT')) { @set_time_limit(0);}

    $code = '0';
    $msg = 'Es handelt sich um einen gültigen Administrator.';


   $header = '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
             '<CUSTOMERS>' . "\n" .
             '<CUSTOMERS_DATA>' . "\n";



      $email = db_prepare_input($_GET['customers_email']);
      $password  = db_prepare_input($_GET['customers_password']);


     //prüfen, ob es sich bei dem User um einen Administrator handelt
      $check_customer_query=mysql_query("select customers_id, customers_status, customers_password from " . TABLE_CUSTOMERS . " where customers_email_address = '" . $email . "'");

          if (!mysql_num_rows($check_customer_query))

          {
             $code = '91';
             $msg = 'Es ist kein Benutzer zu dieser E-Mail-Adresse vorhanden.';

          }

            else

          {

            $check_customer = mysql_fetch_array($check_customer_query);

               // Prüfen, ob der Benutzer ein Administrator ist
               if ($check_customer['customers_status']!='0')
               {

                 $code = '92';
                 $msg = 'Bei dem Benutzer mit der E-Mail ' . $email . ' handelt es sich nicht um einen Administrator.';

               }

               // Passwort prüfen
               if (!( ($check_customer['customers_password'] == $password) or
                      ($check_customer['customers_password'] == md5($password)) or
                      ($check_customer['customers_password'] == md5(substr($password,2,40)))
                  ))
               {

                 $code = '93';
                 $msg = 'Sie haben ein falsches bzw. kein Passwort angegeben.';

               }

          }


       $schema .= '<CODE>' . $code . '</CODE>' . "\n" .
                  '<MESSAGE>' . $msg . '</MESSAGE>' . "\n";


   $footer = '</CUSTOMERS_DATA>' . "\n" .
             '</CUSTOMERS>' . "\n";

  //Ergebnis als XML ausgeben
   create_xml ($header . $schema . $footer, xml_encoding);


}




function updating_tables ()
{

   // Tabellen aktualisieren
    $sql1 = 'ALTER TABLE ' . TABLE_ORDERS_STATUS . ' CHANGE orders_status_id orders_status_id int(11) NOT NULL default "0" AUTO_INCREMENT';
    $result = mysql_query($sql1);

    $sql2 = 'ALTER TABLE ' . TABLE_CUSTOMERS_STATUS . ' CHANGE customers_status_id customers_status_id int(11) NOT NULL default "0" AUTO_INCREMENT';
    $result = mysql_query($sql2);

    $sql3 = 'ALTER TABLE ' . TABLE_SHIPPING_STATUS . ' CHANGE shipping_status_id shipping_status_id int(11) NOT NULL default "0" AUTO_INCREMENT';
    $result = mysql_query($sql3);


  /*
  $sql4 = 'CREATE TABLE finos_log (id int(11) NOT NULL auto_increment, date datetime NOT NULL default "0000-00-00 00:00:00",'.
         'user varchar(64) NOT NULL default "", pw varchar(64) NOT NULL default "", method varchar(64) NOT NULL default "",'.
         'action varchar(64) NOT NULL default "", post_data mediumtext, get_data mediumtext, PRIMARY KEY (id))';
  $sql5 = 'ALTER TABLE ' . TABLE_ORDERS . ' ADD delivery_firstname VARCHAR(32) NOT NULL AFTER delivery_lastname';
  */

}



function initialize_charset()
{

  $db=mysql_connect(DB_SERVER,DB_SERVER_USERNAME,DB_SERVER_PASSWORD);
  $charset = mysql_client_encoding($db);

  //print strlen('Ä'); // ergibt 2, wenn die Datei in UTF-8 ist, ergibt 1 bei latin1


              if ($_SERVER['REQUEST_METHOD']=='GET')
              {

                if ($charset=='utf8')
                {

                   define('OPENDB_ISO','1');
                   define('CHARSET','iso-8859-1');                                        //define('CHARSET','utf-8');

                   header ("Last-Modified: ". gmdate ("D, d M Y H:i:s"). " GMT");
                   header ("Cache-Control: no-cache, must-revalidate");
                   header ("Pragma: no-cache");
                   header ("Content-Type: text/xml; charset=utf-8");                 //header ("Content-Type: text/xml; charset=iso-8859-1");
                   header ("Accept-Charset: iso-8859-1,utf-8;q=0.7,*;q=0.7");
                   header ("Expect: 100-continue");

                   /*
                          $schema = '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
                                    '<STATUS>' . "\n" .
                                    '<STATUS_DATA>' . "\n" .
                                    '<CHARSET>' . $charset . '</CHARSET>' . "\n" .
                                    '<LEN>' . strlen('Ä') . '</LEN>' . "\n" .
                                    '<METHOD>' . 'GET' . '</METHOD>' . "\n" .
                                    '</STATUS_DATA>' . "\n" .
                                    '</STATUS>' . "\n\n";

                                 //Ergebnis als XML ausgeben
                                  create_xml ($schema);
                   */

                }
                else  //ISO
                {

                   define('OPENDB_ISO','1');
                   define('CHARSET','iso-8859-1');                                   //define('CHARSET','utf-8');

                   header ("Last-Modified: ". gmdate ("D, d M Y H:i:s"). " GMT");
                   header ("Cache-Control: no-cache, must-revalidate");
                   header ("Pragma: no-cache");
                   header ("Content-Type: text/xml; charset=iso-8859-1");            //header ("Content-Type: text/xml; charset=utf-8");
                   header ("Accept-Charset: iso-8859-1,utf-8;q=0.7,*;q=0.7");
                   header ("Expect: 100-continue");

                   /*
                          $schema = '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
                                    '<STATUS>' . "\n" .
                                    '<STATUS_DATA>' . "\n" .
                                    '<CHARSET>' . $charset . '</CHARSET>' . "\n" .
                                    '<LEN>' . strlen('Ä') . '</LEN>' . "\n" .
                                    '<METHOD>' . 'GET' . '</METHOD>' . "\n" .
                                    '</STATUS_DATA>' . "\n" .
                                    '</STATUS>' . "\n\n";

                                 //Ergebnis als XML ausgeben
                                  create_xml ($schema);
                   */

                }

              }
              elseif ($_SERVER['REQUEST_METHOD']=='POST')
              {

                   define('OPENDB_ISO','1');
                   define('CHARSET','iso-8859-1');                                   //define('CHARSET','utf-8');

                   header ("Last-Modified: ". gmdate ("D, d M Y H:i:s"). " GMT");
                   header ("Cache-Control: no-cache, must-revalidate");
                   header ("Pragma: no-cache");
                   header ("Content-Type: text/xml; charset=iso-8859-1");            //header ("Content-Type: text/xml; charset=utf-8");
                   header ("Accept-Charset: iso-8859-1,utf-8;q=0.7,*;q=0.7");
                   header ("Expect: 100-continue");

                   /*
                          $schema = '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
                                    '<STATUS>' . "\n" .
                                    '<STATUS_DATA>' . "\n" .
                                    '<CHARSET>' . $charset . '</CHARSET>' . "\n" .
                                    '<LEN>' . strlen('Ä') . '</LEN>' . "\n" .
                                    '<METHOD>' . 'POST' . '</METHOD>' . "\n" .
                                    '</STATUS_DATA>' . "\n" .
                                    '</STATUS>' . "\n\n";

                                 //Ergebnis als XML ausgeben
                                  create_xml ($schema);
                   */

              }


}



function read_charset()
{

  $db=mysql_connect(DB_SERVER,DB_SERVER_USERNAME,DB_SERVER_PASSWORD);
  $charset = mysql_client_encoding($db);

  //print strlen('Ä'); // ergibt 2, wenn die Datei in UTF-8 ist, ergibt 1 bei latin1


  $schema = '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
            '<STATUS>' . "\n" .
            '<STATUS_DATA>' . "\n" .
            '<CHARSET>' . $charset . '</CHARSET>' . "\n" .
            '<LEN>' . strlen('Ä') . '</LEN>' . "\n" .
            '</STATUS_DATA>' . "\n" .
            '</STATUS>' . "\n\n";

  //Ergebnis als XML ausgeben
   create_xml ($schema);

}




/* --------------------------------------------------------------------------------------------------------------
   --- Datenbank-Funktionen
   ------------------------------------------------------------------------------------------------------------*/


// --- Verbindung zur Datenbank aufbauen ---------------------------------------------------------
function database_connection()
{


           try {

                  $dbc=false;
                  $db=mysql_connect(DB_SERVER,DB_SERVER_USERNAME,DB_SERVER_PASSWORD);

                     if (OPENDB_ISO=='1')
                     {

                        $charset = mysql_client_encoding($db);
                          if ($charset=='utf8')
                          {
                            mysql_set_charset('latin1', $db);
                          }

                     }



                  if (!$db)

                        {
                          die('Kein Verbindungsaufbau zur Datenbank möglich: ' . mysql_error());
                        }

                         else

                        {
                          mysql_select_db(DB_DATABASE);
                          $dbc=true;
                        }

               }

                  catch (Exception $ms)

                  {

                    echo "Es ist ein Fehler aufgetreten: " . $ms->getMessage();

                    $dbc=false;
                    exit();

                  }

    return $dbc;

}



function db_prepare_input($string)
{
   if (is_string($string))
   {
    return trim(stripslashes($string));
   }
   elseif (is_array($string))
   {
     reset($string);
      while (list($key, $value) = each($string))
      {
       $string[$key] = db_prepare_input($value);
      }
    return $string;
   }
    else
   {
    return $string;
   }

}



function fn_input($string, $link = 'db_link')
{
  global $$link;

   if (function_exists('mysql_real_escape_string'))
    {
    return mysql_real_escape_string($string, $$link);
    }
     elseif (function_exists('mysql_escape_string'))
    {
     return mysql_escape_string($string);
    }

  return addslashes($string);

}



function database_insert($tabelle, $data, $action = 'insert', $parameters = '')
{

  reset($data);


      // Datensätze hinzufügen ***********************************************************

                if ($action == 'insert')
                {

                       $insert_query =   'insert into ' . $tabelle .  ' (';
                        while (list($columns, ) = each($data))
                        {
                           $insert_query .= $columns . ', ';
                        }

                        $insert_query = substr($insert_query, 0, -2) . ') values (';
                        reset($data);

                        while (list(, $value) = each($data))
                        {

                          $value = (is_Float($value) & PHP4_3_10) ? sprintf("%.F",$value) : (string)($value);
                                switch ($value)
                                {
                                  case 'now()':
                                    $insert_query .= 'now(), ';
                                    break;
                                  case 'null':
                                    $insert_query .= 'null, ';
                                    break;
                                  default:
                                    $insert_query .= '\'' . fn_input($value) . '\', ';
                                    break;
                                }

                        }

                        $insert_query = substr($insert_query, 0, -2) . ')';
                        return mysql_query($insert_query) or die("MySQLFehler: $insert_query;\n" . mysql_error());


                }

              // Datensätze ändern ***********************************************************
                elseif($action == 'update')
                {


                      $update_query = 'update ' . $tabelle . ' set ';
                      while (list($columns, $value) = each($data))
                      {
                         $value = (is_Float($value) & PHP4_3_10) ? sprintf("%.F",$value) : (string)($value);
                              switch ($value) {
                          case 'now()':
                            $update_query .= $columns . ' = now(), ';
                            break;
                          case 'null':
                            $update_query .= $columns . ' = null, ';
                            break;
                          default:
                            $update_query .= $columns . ' = \'' . fn_input($value) . '\', ';
                            break;
                        }
                      }

                      $update_query = substr($update_query, 0, -2) . ' where ' . $parameters;
                      return mysql_query($update_query) or die("MySQLFehler: $update_query;\n" . mysql_error());

                }
}


?>