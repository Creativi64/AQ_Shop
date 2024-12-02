## [6.1.0]
-GPSR Felder in setManufacturer

## [6.0.19]
- include_once in hook, segfault error on some environments

## [6.0.18]
- fix #311 double saving (1x net 1x gross) of prices on setArticle
- fix #312 allowing change of products_model when product updates via products_id on setArticle
- fix #180 error on getArticle when master_slave plugin is not activated

## [6.0.17]
- bugfix in setArticleFiles when api is in JSON mode, file upload was broken

## [6.0.16]
- Support for Sort & LIKE filter in getArticles

## [6.0.15]
- PHP 8.1 Support

## [6.0.14]
- support of tracking numbers on setOrderStatus https://xtcommerce.atlassian.net/wiki/spaces/XT41DUE/pages/10256522/setOrderStatus+JSON
- support for plugin xt_customer_prices with new call setCustomerPrice https://xtcommerce.atlassian.net/wiki/spaces/XT41DUE/pages/2764472321/setCustomerPrice+JSON
- support for paid/free product files via new api call setArticleFiles https://xtcommerce.atlassian.net/wiki/spaces/XT41DUE/pages/2765717505/setArticleFiles+JSON

## [6.0.13]
- option to keep linked file downloads on products update

## [6.0.12]
- fix for banktransfer on xt_mollie and xt_paypal_plus

## [6.0.11]
- adjustment 'salutation', _checkGender accepting now on of [f, m, c, n, z]  
  female, male, company, neutral, not-specified

## [6.0.10]
- compatibility issue with 6.3.3 and latest adodb version + 6.3.3 hotfix
- error on 6.2 installation for orders with xt_paypal_plus on getOrders

## [6.0.9]
- 502 error on getOrders

## [6.0.8]
- new hook

## [6.0.7]
- renamed service in wsdl to xtCommerce API for Visual Studio compatibility

## [6.0.6]
- new hooks

## [6.0.5]
- php clean up warning/notice

## [6.0.4]
- Fix $store_handler errors in setArticle

## [6.0.3]
- Fix Zeichenfolge ohne Anführungszeichen
- externe Ids im Backend ausblenden
- Hooks in Dateien ausgelagert
