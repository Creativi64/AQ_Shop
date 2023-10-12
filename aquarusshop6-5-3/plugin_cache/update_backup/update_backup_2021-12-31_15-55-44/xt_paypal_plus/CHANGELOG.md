## [1.7.3]
- FIX beim Speichern der Zahlungsweise wird gelegtlich fälschlischerweise auf Fehler hingewiesen, obwohl alles ok
- FIX im Checkout: Sprung von Bestätigungsseite-Seite zurück auf Zahlung führt dazu, dass die gewähtle Zahlart durch PP ersetzt wird

## [1.7.2]
- Erzeugung der IPN-Url überarbeitet  
  Multishop: es ist nun nicht mehr nötig für jeden Shop eine seperate App in Paypal anzulegen (aber immer noch möglich)
- FIX bei Anzeige PPP-Details, wenn die Bestellung letzlich nicht über PPP abgeschlossen wurde  

## [1.7.1]
- Anpassungen javascript für xt 6.3

## [1.7.0]
- FIX beim Ändern/Speichern der PP-API-Daten werden die Webhooks aktualisiert/angelegt\
  dadurch kann es dazu kommen, dass keine IPN's an den Shop gesendet werden

## [1.6.10]
- FIX unsaubere Einbindung des Browser-Checks behoben
- xt_paypal_plus/css/xt_paypal_plus.css lässt sich im Shop- bzw System-Template überschreiben
- paypal_plus_footer.html befreit von Smarty-literal's
- TODO css/js überarbeiten und die Template-xyz-Anpassungen entfernen bzw in Überschreibung verschieben

## [1.6.9]
- FIX Stylesheet-Einbindung

## [1.6.8]
- Verbindungs/SSL-Test entfernt

## [1.6.7]
- FIX Stylesheet-Einbindung

## [1.6.6]
- FIX DB-Fehler Verwendung shop_domain_ssl statt shop_ssl_domain

## [1.6.5]
- FIX wenn PPP für einen (Multi-)Shop deaktiviert ist, wird nicht mehr der PPP-Style geladen

## [1.6.4]
- Detailansicht Transaktion überarbeitet: Original pp-Bezeichnungen werden verwendet
- FIX php warnings

## [1.6.3]
- Anpassung für xt6, Ermittlung/Check SSL