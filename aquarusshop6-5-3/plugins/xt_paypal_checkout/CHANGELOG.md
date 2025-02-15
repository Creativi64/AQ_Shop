## [4.3.1]
- FIX Aufruf *Onboarding Status anzeigen* erzeugt Fehler  
  Cannot declare class paypal_checkout, because the name is already in use

## [4.3.0]
- Anpassung Versand-Tracking  
  Übermittlung des Warenkorbs für Verkäuferschutz
- Logging für onboarding->_getAuthToken, onboarding->_getMerchantCredentials und 'complete onboarding clicked'
- Vaulting überarbeitet
- Callback überarbeitet
- Fehler in Google Pay behoben (3ds)
- Zahlungslink überarbeitet: Es wurde stets die Domain der backend-Anmeldung verwendet

## [4.2.2]
- Webhook-Registrierung hinzugefügt für PAYMENT.CAPTURE.PENDING

## [4.2.1]
- FIX Fehler bei Ermittlung der xt-Bestell-ID bei Refund-IPN's
  IPN im Log vorhanden aber nicht der Bestellung zugeordnet

## [4.2.0]
- Funktion Zahlungslink überarbeitet
- neue Zahlungsweisen:
  - iDeal (NL) Freischaltung durch PayPal erforderlich
  - Przelewy24 (PL)
  - BLIK (PL)
  - MyBank (IT) Freischaltung durch PayPal erforderlich

## [4.1.1]
- FIX Call to undefined function xdebug_break() beim Hinzufügen einer Sendungsnummer

## [4.1.0]
- Anpassungen für Sendungsverfolgung  
  Verwendung über Plugin xt_ship_and_track ab Plugin-Version 6.3.0
- FIX Uncaught TypeError: property_exists() in class.paypal_onboarding.php:91

## [4.0.1]
- FIX 'Dieser Shop versendet nicht an Ihre Adresse' in Express mit Kundengruppen-whitelist

## [4.0.0]
- FIX PP-Button am Artikel ausblenden, wenn Preis = 0
- FIX Sonderzeichen in Artikelnamen führen zu Fehler 'Purchase unit missing'
- FIX invalid_string_length brand_name
- FIX 'Dieser Shop versendet nicht an Ihre Adresse' in Express mit Kundengruppen-whitelist
- NEU Kreditkarten-Option 'Immer Standard KK-Formular verwenden'
- NEU Zahlart Trustly
- NEU Zahlungslink senden (Nachzahlung, Zahlungsaufforderung, Zahlungserinnerung)

## [3.7.1]
- ADMIN ppcp_customer_id ausblenden in Kundendetailansicht
- FIX fehlerhafte Ermittlung access-token verhindert Auswahl jeglicher Zahlungsart  
  Uncaught PPCPException: SOME_ERROR in plugins/xt_paypal_checkout/classes/PayPalHelper.php:622

## [3.7.0]
- FIX Vaulting  
  NOT_ENABLED_TO_VAULT_ PAYMENT _SOURCE. The API caller or the merchant on whose behalf the API call  
  is initiated is not allowed to vault the given source.
  Es wir nun geprüft, ob Vaulting aktiviert ist, sichtbar im Backend unter PayPal Checkout Signup > Onboarding Status  
  PAYPAL_WALLET_VAULTING_ADVANCED sollte dort vorhanden sein  
  Wenn nicht können Sie das Onboarding nochmals durchführen 
- FIX weitere Anpassung Festwertgutscheine

## [3.6.5]
- FIX Anpassungen für Gutscheine Festwert/Versand
- FIX Giropay aus Installer entfernt
- DEV ppcp_config.php nun unverschlüsselt

## [3.6.4]
- FIX array_key_exists() expects parameter 2 to be array in class.paypal_checkout.php:819
- FIX ppcp_pui_error: Fehler bei Kauf auf Rechnung (PUI)

## [3.6.3]
- neuer Hookpoint für xt_product_options 6.4.0  
  xt_paypal_checkout:orderCreate_content_item

## [3.6.2]
- FIX fehlender Hookpoint-Dateien  
  class.order.php__buildCustomerDeliveryAddress_top.php  
  class.order.php__buildCustomerAddress_top.php

## [3.6.1]
- Abschaltung Giropay über PayPal https://developer.paypal.com/docs/checkout/apm/  
  Die Zahlungsweise xt_paypal_checkout_giropay wird durch das Update entfernt

## [3.6.0]
- NEU PayPal Vaulting  
  https://www.paypal.com/de/brc/article/securely-store-payments
  https://newsroom.deatch.paypal-corp.com/PayPal-erweitert-Komplettloesung-PayPal-Checkout
- NEU Google Pay im Checkout
  Einrichtung: https://xtcommerce.atlassian.net/wiki/spaces/MANUAL/pages/3149168643

## [3.5.3]
- Abschaltung SOFORT über PayPal https://www.paypal.com/de/cshelp/article/help1145
  Die Zahlungsweise xt_paypal_checkout_sofort wird durch das Update entfernt
- Fehler im Zusammenhang mit ppcp_session_id behoben
- Apple Pay: Prüfung Pflichtfelder auf confirmation hinzugefügt
- Installer und 3.5.0 updater aktualisiert um Ausgabe, wenn Apple's association-file nicht kopiert werden konnte
- css für Safari  form#checkout-form .xt-form-required.xt-form-error  Checkbox wird nicht als Fehler markiert

## [3.5.2]
- IPN Verarbeitung überarbeitet
  Wenn refunds in PayPal ausgeführt werden wird die IPN in xt richtig zugeordnet,
  auch wenn als Rechnungsnummer keine xt-Bestell-ID angegeben wird
- Logs führen eine ppcp_session_id um js-Log und sdk-Log zu verbinden
- für Apple Pay wird keine payment_source erzeugt
- customers_federal_state_code wird nicht an AP gesendet wenn leer (null)
- requiredBillingContactFields ohne phone

## [3.5.1]
- javascript server logging xtLogs/xt_paypal_checkout_js.log
- kleiner fix im logging

## [3.5.0]
- NEU Apple Pay im Checkout
  Einrichtung: https://xtcommerce.atlassian.net/wiki/spaces/MANUAL/pages/2994208879
- FIX fehlende Währungscodes an den APM-Bestellungen

## [3.4.5]
- FIX IPN-Validierung
  - neue Plugin-Option Webhook-ID
  - wird automatisch beim Onboarding gesetzt
  - kann beim Speichern der Plugin-Einstellungen automatisch gesetzt werden
  - kann aus der Ausgabe 'Onboarding Status anzeigen' kopiert werden
- FIX Statusmapping Rückzahlungen
  uU wurde der gewählte Status nicht übernommen

## [3.4.4]
- FIX it_CH ist als locale für PayPal nicht bekannt
- Webhook-Validierung eingebaut, nicht aktiviert

## [3.4.3]
- FIX Rechnungskauf xt-Bestellnummer nicht in PayPal sichtbar
- FIX E-Mail 'Hinweise zum Rechnungskauf' wird mehrfach gesendet

## [3.4.2]
- FIX Rechnungskauf Meldung 'must not be null', Telefonfilter löscht zu viel

## [3.4.1]
- FIX Format der an PP übertragenen Telefonnummer prüfen

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
