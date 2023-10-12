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
