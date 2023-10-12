##[3.0.6]
- FIX falsche Benennung Hookpoint \
  paypal_hooks:cart_tpl_form_bottom
  in plugins/xt_paypal/hooks/product_info_tpl_cart.php korrigiert auf \
  paypal_hooks:product_info_tpl_cart_bottom
- Anpassungen javascript für xt 6.3

##[3.0.5]
- FIX: gelegentliche Fehler 'Total mismatch' bei xt-Einstellung Netto-Admin

##[3.0.4]
- Filter 'Paypal-Transaktionen' überarbeitet
- Logging überarbeitet
- FIX: gelegentliche Fehler 'Total mismatch' bei xt-Einstellung Netto-Admin

## [3.0.3]
- Session-ID's aus den Paypal-Rücksprungadressen entfernt
- SEC prüfung der msg in Paypal-Rücksprungadresse

## [3.0.2]
- FIX Gastbestellung ohne vorherige Registrierung als Gast führt zu Gast-Konten ohne Anmeldung,\
  dass heisst Kunde kann bei Rückkehr von Paypal-Express die Bestellung nicht beenden
- CSS in Unterordener css verschoben  

## [3.0.1]
- Anpassung xt_paypal.xml: Status Sandbox 0\
  dadurch wird vermieden, dass bei Updates von Live auf Sandbox umgeschaltet wird\
  (dev: der Updater kann das leider nicht automatisch prüfen/setzen wg Umbennenung der conf)
- Button am Artikel: Prüfung auf allow_add_cart korregiert  

## [3.0.0]
- NEU Konfiguration 'Express Checkbox Kunden-Registrierung'
  wenn konfiguriert, entscheidet bei Express-Bestellungen der Neu-Kunde selbst ob mit/ohne Konto/Passwort;
  bei nicht aktivierter Option bleibt der Neu-Kunde in der Kundengruppe Gast des Shops 
  dadurch entfällt die Konfiguration 'Sende Email wenn Account generiert wird'
- Zahlungseinstellungen überarbeitet: Sortierung  / checkbox statt dropdown wo sinnvoll
- debug: Log der Paypal-Aufrufe und -Antworten in xtLogs/paypal.log per define('XT_PAYPAL_DEBUG', true)
- FIX debug: callback gibt jetzt die richtige Plg-Version bei Aufruf per ?page=callback&page_action=xt_paypal&_showPluginVersion=1
- UPD debug: callback schreibt xtLogs/paypal_callback_curl.txt nur bei define('XT_PAYPAL_DEBUG', true)

## [2.10.4]
- Anpassungen um Crawler vom Artikel-Paypal-Button fernzuhalten\
  robots.txt sollte manuell erweitert werden: Disallow: /*?action=paypalExpressCheckout
- SetExpressCheckout mit Amt=0 verhindern
- FIX Fehler bei Ermittlung der Template-Datei plugins/xt_paypal/templates/modal_paypal_express.tpl.html

## [2.10.3]
- Verbindungs/SSL-Test entfernt

## [2.10.2]
- FIX DB-Fehler Verwendung shop_domain_ssl statt shop_ssl_domain

## [2.10.1]
- FIX Express-Button am Artikel berücksichtigt `allow_add_cart`
- Anzeige Versandzeit auf pp-confirmation, Anpassungen für `products_shippingtime_nostock` (xt 6.2)
- FIX bei IPN nicht `orders_data` in `TABLE_ORDERS` überschreiben

## [2.10.0]
- NEU Express-Button am Artikel
- Button-Erzeugung über editierbare Templates
- separates css in xt_paypal_shop.css
- nicht benutzte Dateien entfernt
- benötigt Anpassung in product.html für form-id:
  {form type=form name=product id="main_product_form" action='dynamic' link_params=getParams method=post}

## [2.9.0]
- Anpassung für xt 6.1.2
- Korrektur Marketplace-Links im Installer