[2.2.3]
- Fallback-Link im Signup, wenn Rechnungskauf nicht vorhanden in den capabilities

[2.2.2]
- Anpassungen für 3t-Hersteller-Plugins
  - FIX Uncaught SyntaxError: redeclaration of const ppc_button_selector (Template-Hooks mehrfach verwendet)
  - FIX Artikel wird zweifach in den Warenkorb gelegt (coe_ajax_cart) paypal-checkout-cart-and-product_ignored_containers.tpl.html
- Ansicht Onboarding-Status überarbeitet

## [2.2.1]
- FIX Undefined constant "XT_PAYPAL_CHECKOUT_PAYPAL_EXPRESS_ACTIVATED"  
  Source-Mix 2.1.6 / 2.2.0

## [2.2.0]
- NEU Express Checkout Warenkorb / Produktseite lässt sich separat konfigurieren
- FIX wenn im Express Checkout Paypal den durch zB Versandkosten erhöhten Endbetrag ablehnt,  
  wird mit entsprechendem Hinweis in den Warenkorb gesprungen
  https://xtcommerce.atlassian.net/wiki/spaces/MANUAL/pages/2825682945/New+overcapture+requirements
- xt-Bestellnummern werden ohne Zusatz an PayPal übertragen
- bei 'Details anzeigen' jetzt - wenn so - 'Bestellung in PayPal nicht gefunden. sonst SOME ERROR '

## [2.1.6]
- FIX Fehler Für Giropay wird kein Button auf der confirmation-Seite angezeigt, wenn Button-Farbe blue gewählt
  js-Fehler Error: Unexpected style.color for giropay button: blue, expected default, silver, white, black
- FIX für Rechnungskauf (xt_paypal_checkout_pui) funktioniert automatische Rechnungserstellung nicht

## [2.1.5]
- FIX Fehler Ust wurde nicht an PayPal übertragen für Händler

## [2.1.4]
- FIX durch Rundungen wird falscher Gesamtbetrag an PayPal übertragen
- NEU serverseitige Prüfung ob Telefon/Geburtsdatum vorhanden für Rechnungskauf
- Shop-Frontend  Abhängigkeit bootstrap/jquery entfernt

## [2.1.3]
- FIX CSRF
- FIX Paypal-Button wurde am Artikel angezeigt, obwohl Paypal für Shop/Gruppe deaktiviert
- Bei Refund wir die Xt-Bestellnummer nicht mehr gesendet (neuerdings kam es zum Fehler 'Invoice ID was previously used to refund a capture')

## [2.1.2]
- Logging erweitert
- Kreditkarten-Bezahlablauf angepasst, Berücksichtigung payment declined bei capture
- FIX Fehler bei Express-Checkout bei Wechsel der MwSt durch von PayPal übergebene Lieferadresse
- Backend: 'Status neu abrufen' jetzt 'Details anzeigen' es wird kein Status-Mapping durchgeführt (kommt verändert wieder in späterer Version)
- FIX Direkt nach Refund erfolgt kein weiterer Status COMPLETED Zahlung erhalten
- Bestelldetails: wenn vorhanden wird die IPN-ID angezeigt, welche zum Statuswechsel führte
- Bestelldetails: in den Kommentaren der Statusänderung wird angezeigt, worauf sich die Paypal ID bezieht (order, capture, refund)

## [2.1.1]
- FIX Begrenzung des Artikelnamen auf 127 Zeichen bei Übergabe an PayPal

## [2.1.0]
- APM (alternative Zahlungsweisen)
  - für APM's (Sofort, EPS etc) wird die Bestellung bereits angelegt bevor Sprung zum Zahlungsdienstleister
  - die Bestellung wird per ORDER_COMPLETE_ON_PAYMENT_APPROVAL in PayPal angelegt  
    Dadurch existiert die Bestellung im Shop und in PayPal, sobald der Kunde beim Zahlungsdienstleister auf 'Zahlen' klickt  
    Dadurch wird versucht drop-off abzufangen (Kunde kehrt nicht in den Shop zurück)  
- ~~FIX Fehler bei Express-Checkout bei Wechsel der MwSt durch von PayPal übergebene Lieferadresse~~
- FIX Fehler Ust wurde falsch an PayPal übertragen für Händler
- FIX Express-Checkout für registrierte Nutzer nach Anmeldung nach Rückkehr von Paypal, von PayPal gelieferte Adresse wird nicht mehr überschrieben
- FIX für doppelte Einbindung des Hooks in älteren xt-Versionen
- Festlegung:
  - Express Checkout: registrierte Kunden können Rechnungsadresse aus Adressbuch wählen, neu anlegen nicht
  - Express Checkout Gastbestellung: Rechnungs- und Lieferadresse werden von PayPal übernommen, Änderungen nicht möglich
- DEV oderCreate/Patch Code zusammengeführt

## [2.0.5]
- Templates überarbeitet
- Unnötige Email-Templates entfernt

## [2.0.4]
- Anpassungen für Dritthersteller in Template und Javascript
- Javascript-Anpassungen auf checkout-confirmation, wenn Pflichtfelder nicht ausgefüllt sind
- Deinstallation entfernt nun die Zahlungsweisen

## [2.0.3]
- übergeordnete js-onError-Funktion entfernt
- Fallback für Formular-Ermittlung bei Express

## [2.0.2]
- Onboarding Status überarbeitet: Webhooks werden angezeigt

## [2.0.1]
- Fehlerbehandlung/Log erweitert 
- FIX orderPatch überträgt gelegentlich ungerundete Werte
- abgelehnte Rechnungskäufe werden in Bestell-Historie und IPN-Log gespeichert
- FIX Unterscheidung Live/Sandbox

## [2.0.0]
- Onboarding und Multishop-Funktionen überarbeitet

## [1.0.15]
- FIX es wurden ungerundete Werte an PayPal übertragen
- FIX es wurde teilweise ein falsche Webhook-Url ermittelt
- onboarding Ansicht überarbeitet
- OAauth-Tocken caching verbessert, Fix für RATE_LIMIT_REACHED

## [1.0.14]
- FIX Festwert-Gutscheine werden nun als discount an PayPal gesendet, nicht als line item
- Admin Bei Statusaktualisierung wird nun eine Fehlermeldung angezeigt, wenn zB die Bestell-Id in PayPal nicht gefunden wurde
- FIX Fälschlicherweise wurde Versand als handling (Verpackung) an PayPal gesendet, nicht als shipping

## [1.0.13]
- FIX Rechnungskauf wurde von Ratepay abgelehnt
- FIX Button-Farben Gold/Blue führen zu Fehler bei Verwendung alternativer Zahlungsweisen.
  Bezahlen-Button wurde nicht angezeigt
- Installer kopiert - wenn nicht vorhanden - Paypal-Logo aus dem Plugin nach media/payment
- FIX xt_klarna_kp behindert Formular Rechnungskauf, wenn Angaben fehlen, Installer überschreibt kp js
- FIX js Fehler bzgl paypal_checkout_constant

##[1.0.12]
- Anpassungen für ew_evelations
- Onboarding überarbeitet

##[1.0.11]
- APM Flow verbessert (Sofort, EPS, Giropay)
- FIX Logo-Url für Rechnungskauf korrigiert
- FIX nicht akzeptieren der AGB's führte zu Rücksprung zu den Zahlungsweisen

##[1.0.10]
- logging im onboarding

##[1.0.9]
- js für confirmation-Seite überarbeitet
- PAYPAL_CHECKOUT_DEBUG_SDK Konstante für js-sdk-debug
- Einstellung für PayPal-Express für _paypal und _paylater

##[1.0.8] erstes öffentliches Release
