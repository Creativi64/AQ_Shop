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