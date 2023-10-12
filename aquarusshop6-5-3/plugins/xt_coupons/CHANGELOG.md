## [5.2.20]
- Fix: Warenkorb ermittlung für mindestebestellwert beachtet nun auch Sonderpreise

## [5.2.19]
- FIX für PHP 8, bei Ermittlung qualifizierter Artikel für den Warenkorb
- FIX nach Speichern einer Vorlage wird bei Kundengruppe 'null' eingetragen,
  dadurch musste alle Kundengruppen explizit angegeben werden, wenn es keine Einschränkung geben sollte
- Bezüge zu xt_campaigntracking und xt_socialcommerce entfernt
- bugfix for coupons with thousands of products

## [5.2.18]
- DEV Neue Hookpoints, Vorbereitung _copy Coupon 

## [5.2.17]
- FIX 5.2.16 prozentuale Gutscheine werden nicht verrechnet (Fehler in PHP 8 fix)

## [5.2.16]
- FIX es wird jetzt gewährleistet, dass Vorlagen-Codes nicht verwendet werden können,  
  wenn für diese Vorlage Codes existieren
- FIX bei Verwendung von Codes wird nun der eigentlichen Code angezeigt (Warenkorb, Checkout)  
  nicht der Code der verwendeten Vorlage
- Anpassungen für PHP 8

## [5.2.15]
- neue Hookpoints

## [5.2.14]
- csrf-protection fix
- php8 code fix

## [5.2.13]
- neue Hookpoints für 3t-Hersteller
- Versuch Performance zu verbessern in _get_coupon_products_in_cart  
  (Übergabe der $cart_products des cart an xt_coupons in hooks/class.cart.php_getContent_data.php)

## [5.2.12]
- neue Hookpoints für 3t-Hersteller

## [5.2.11]
- FIX gelegentlicher 'falscher' Fehler \
  "Mindestbestellwert zum Einlösen dieses Kupons/Gutscheines noch nicht erreicht"
- FIX Gast-Accounts können Vorlagen-Codes mehrfach verwenden, obwohl für einmalige Verwendung vorgesehen

## [5.2.10]
- Anpassung für PHP 7.3 (preg_match)

## [5.2.9]
- Anpassungen für Single-Shops mit white listing\
  Ergänzung zu den commits ec7dcb2b1 / a717c1934  (nicht speichern permissions / shop-tab ausblenden)\
  known issues: in multisshop-installationen mit white listing kann es sein,\
  dass zb für coupons die Shop-Berechtigungen neu gesetzt werden müssen, nachdem ein 2ter shop angelegt wurde

## [5.2.8]
- Fix: Link 'Gutschein entfernen' korregiert

## [5.2.7]
- Fix: prozentuale Gutscheine berücksichtigen eigentlichen Warenkorb (also ohne Rabatte)
- Fix: Zuordung Gutschein/Kunde wieder verfügbar
- FIX: Berechnung ```cart_total_full_coupons```