## [3.4.0]
- FiX Format der an PP übertragenen Telefonnummer prüfen

## [3.4.0]
- FIX überarbeiteter Umgang mit APM's (Sofort, Giro, EPS)
  Bestellung wird nun (wieder) in XT gespeichert, bevor zum Zahlungsdienstleister geleitet wird
- Änderungen betreffend den Umgang mit Beständen bei APM-Zahlungen (eingeführt mit 3.2.6)
- Admin Detailansicht Bestellung, Übersicht Bestellstatus-Änderungen  
  für den Trigger order_process nun auch Anzeige des Status der Transaktion (Zahlung)  
  
  vorher  
    order 4KK311737K603063G status COMPLETED. payment transaction 8PJ73808SN474922B  
  jetzt  
    order 4KK311737K603063G status COMPLETED. payment transaction 8PJ73808SN474922B COMPLETED  
  oder auch
    order 4KK311737K603063G status COMPLETED. payment transaction 8PJ73808SN474922B DENIED  
  DENIED, also Zahlung abgelehnt. Daher empfehlen wir in den Einstellungen der Zahlungsweisen  
  den Trigger order_process einem Status wie 'In Bearbeitung' zuzuordnen, nicht 'Zahlung erhalten' oä  
  
  wird im Bestellprozess (order_process) für den Zahlungsstatus etwas anders als COMPLETED ermittelt,  
  wird dies an der Bestellung geloggt und der Kunde zur Auswahl der Zahlungsweise zurückgeleitet
    
- FIX Express Checkout berücksichtigt für Versandarten gesperrte Zahlungsweisen (idF also xt_paypal_checkout_paypal)
- FIX für Versandarten gesperrte Zahlungsweisen konnten dazu führen, dass Express-Checkout nicht angezeigt wird
- FIX Fehler im Installer in sofort.xml
- FIX Für Standard-Kreditkarte (zb in CH, Belgien, ...) muss die Button-Farbe auf weiß oder schwarz gesetzt werden  
  Advanced KK nur in bestimmten Ländern https://developer.paypal.com/docs/checkout/advanced/


## [3.3.3]
- weiteres Mapping an der Zahlungsweisen
  für den Bestellstatus-Auslöser (Trigger) *order_process* kann nun ein separater Wert gewählt werden
- IPN CHECKOUT.ORDER.APPROVED wird für PayPal-Express nicht verarbeitet
  für APM und PUI wird die IPN verarbeitet
  aber es wird kein xt-Statusupdate durchgeführt, sondern lediglich ein Log-Eintrag gesetzt

## [3.3.2]
- FIX Bestellkommentar wird im Express Checkout nicht gespeichert

## [3.3.1]
- FIX IPN für APM's (Sofort etc) und PUI (Rechnungskauf) werden ignoriert 

## [3.3.0]
- Unterstützung Kommastellen im Warenkorb  
  zB xt-Warenkorb 'Stoff Anzahl 3,5 Meter' wird an Paypal übertragen als '1 mal 3,5 Meter Stoff'

## [3.2.6]
- Checkout Flow geändert, um Probleme mit Lagerbeständen zu vermeiden  
  ORDER_COMPLETE_ON_PAYMENT_APPROVAL Bestellungen (APM's und PUI) werden nun nicht innerhalb des Flows angelegt und dann aktualisiert,  
  sondern nur noch einmalig angelegt
  Bestellungen mit ORDER_COMPLETE_ON_PAYMENT_APPROVAL haben in PayPal daher keine xt-Bestellnummer

## [3.2.5]
- FIX  *The specified processing_instruction is not supported for the given payment_source*  
  bei Verwendung Kreditkarte
- javascript angepasst zur Erkennung von checkout-Formularen 
  (keine Bestellung nach Klick auf *Zahlen* im Paypal-Fenster)

## [3.2.4]
- angepasste Kreditkarten-Zahlung für Käufer aus AT/CH
  für Käufer aus AT/CH wird der Standard-KK-Button verwendet

## [3.2.3]
- FIX  Telefonnummern mit Leer- oder Sonderzeichen liefern den Fehler INVALID_PARAMETER_SYNTAX

## [3.2.2]
- FIX  Land wird nicht übergeben bei Express

## [3.2.1]
- FIX  *Kauf auf Rechnung* und *Später bezahlen* NO_PAYMENT_SOURCE_PROVIDED
- Anpassung für IPN bei *Kauf auf Rechnung*

## [3.2.0]
- angepasste Kreditkarten-Zahlung für Händler aus AT/CH  
  für xt-Händler aus AT/CH wird der Standard-KK-Button verwendet
- FIX KK-3DS-Auswertung
- FIX APM's werden nicht im Checkout angezeigt (zB Giropay, Sofort)
- FIX bei Express-Bestellung fehlt die Artikelliste in PayPal
- FIX nach Express-Bestellung und nachfolgendem Standard-Checkout ist Änderung der Lieferadresse nicht möglich
- Express-Checkout: Änderung der Rechnungsadresse nun möglich auf xt-Bestätigungsseite
- Backend Bestellübersicht: Express-Bestellungen sind mit dem PayPal-Symbol und einem kleinen x gekennzeichnet

## [3.1.7]
- Js-Ladevorgang/ Templates überarbeitet  
  PayPal SDK sollte nun nur einmal geladen werden  
  nach DomcContentLoaded, sollte den Seitenaufbau nicht aufhalten  
  wird in display.php_content_head.php geladen  
  dort gibt es den Hook display.php:content_head:render_ppcp_sdk_code  
  per Hook kann die boolean Variable *$render_ppcp_sdk_code* true/false gesetzt werden  
  oder per Funktion *do_render_ppcp_sdk_code* in ppcp_config.php
- Unterstützung für PHP 8.2 / 8.1 / 7.4

## [3.1.6]
- Zahlungseinstellung *New order status* hinzugefügt
- In der Bestellung wird *Express* ausgewiesen, wenn über Expresskauf bestellt
- hookpoints in buildPurchaseUnit  return $purchase_units;  
  xt_paypal_checkout:buildPurchaseUnit_top  
  xt_paypal_checkout:buildPurchaseUnit_bottom

## [3.1.5]
- ppcp_config.php / PPCP_DONT_LOAD_SDK_PAGES / PPCP_DONT_LOAD_SDK_COMPONENT_MESSAGES
  https://xtcommerce.atlassian.net/wiki/spaces/MANUAL/pages/2860482567
- Anpassungen im Javascript (Shop)

## [3.1.4]
- FIX im Express Checkout confirmation wird uU fälschlicherweise ein Fehler angezeigt  
  PATCH_VALUE_REQUIRED: Please specify a 'value' to for the field that is being patched.  
  wenn in PayPal ein Land ohne Versand gewählt und dann aber korrigiert wurde
- Hinweistexte an den Plugin-Einstellungen, dass Client-ID etc durch den Signup gesetzt werden
- intern IUR-266-99870 

## [3.1.3]
- Anpassung für xt_product_options 6.2.2 (Express-Checkout am Artikel)

## [3.1.2]
- IPN für Rückzahlungen löst nun wieder Statusupdate im Shop aus  
  (war absichtlich deaktiviert um nicht die WAWI's zu triggern)

## [3.1.1]
- FIX Versandkosten für Händler Inland

## [3.1.0]
- Anpassung im IPN-Log  
  bisher mit *capture* gekennzeichnete Transaction-IDs, werden nun mit *payment* gekennzeichnet
- Umgang mit DUPLICATE_INVOICE_ID überarbeitet, Anzeige für Kunden im Shop und im xtAdmin  
  https://xtcommerce.atlassian.net/wiki/spaces/MANUAL/pages/2851995649
- FIX Onboarding öffnete im Tab statt Minibrowser

## [3.0.5]
- Keine Kunden-Email bei Fehlern im Checkout Prozess
- Installer Update Zahlungsgebühren
  Updater 3.0.1 wird nochmals ausgeführt
- Mitteilung im Admin an der Bestellung, wenn Kreditkarten 3ds nicht klappte

## [3.0.4]
- FIX MISSING_REQUIRED_PARAMETER
  idF trat der Fehler auf, wenn an der 126sten Stelle des Artikelnamens ein Sonderzeichen stand

## [3.0.2 / 3.0.3]
- Anpassungen für xt_orders_invoices (Ausgabe Abtretungshinweise)
- 30 Tage Zahlungsziel für Rechnungskauf
- Übernahme Telefonnummer von PayPal überarbeitet
  https://xtcommerce.atlassian.net/wiki/spaces/MANUAL/pages/2847047681/Telefonnummern
- Template-Fixes für Express Checkout
- Kreditkartenprüfung überarbeitet

Wichtig: Wennn Sie das Plugin xt_orders_invoice einsetzen, müssen Sie die Änderungen aus der Vorlage xt_orders_invoices/installer/tpl/tpl_invoice.html der neuen Version 6.0.17 übernehmen!

## [3.0.1]
- Updater korrigiert ab-Werte der Zahlungskosten von 0.00 auf 0.01

## [3.0.0]
- im Express-Checkout werden Lieferkosten in PayPal ausgewählt
- Auto-Login (wenn bestehendes Kundenkonto)
- Vermeiden doppeltes Mapping des Status COMPLETED
- Shop-Frontend  Abhängigkeit bootstrap/jquery entfernt in paypal-checkout-shipping.html
- Eingabe Kreditkartendaten für kleine Displays angepasst

## [2.2.3]
- Fallback-Link im Signup, wenn Rechnungskauf nicht vorhanden in den capabilities

## [2.2.2]
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
