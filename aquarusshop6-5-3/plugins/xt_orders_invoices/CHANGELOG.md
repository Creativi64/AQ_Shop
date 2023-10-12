## [6.0.19]
- Template in plugins/xt_orders_invoices/installer/tpl/tpl_invoice.html angepasst für xt_product_options  
  muss manuell übernommen werden, 3 Zeilen ab Zeile 167

## [6.0.18]
- FIX PHP Fatal error:  Uncaught TypeError: Cannot access offset of type string on string in /plugins/xt_product_options/classes/class.xt_product_options.php:478  
  Anpassung für xt_product_options
- Template in plugins/xt_orders_invoices/installer/tpl/tpl_invoice.html angepasst für xt_product_options

## [6.0.17]
- Rechnungsmail nicht versenden, wenn Trigger-Status bereits gesetzt wurde 

## [6.0.16]
- Anpassungen für xt_paypal_checkout  
  Wichtig: Sie müssen die Änderungen aus der Vorlage /installer/tpl/tpl_invoice.html übernehmen!

## [6.0.15]
- neue Hookpoints

## [6.0.14]
- ORDER_INVOICES_PDF_DEBUG-Konstante eingeführt  
  wenn true wird das html der letzten PDF in den xtLogs-Ordner geschrieben

## [6.0.13]
- FIX PHP 8 Anpassung
- FIX uU sql-Fehler bei Neuanlage von Vorlagen
- FIX uU Neuanlage nicht möglich

## [6.0.12]
- Hookpoint für Entwickler class.order.php:_updateOrderStatusHistory_data nun per require statt require_once

## [6.0.11]
- neuer Hookpoint für Entwickler class.xt_orders_invoices.php:_getPdfContent_assign_smarty_data

## [6.0.10]
- FIX Shop-Url auf Rechung bei manueller Erstellung zeigt fälschlicherweise Backend-Anmelde-Domain und nicht die der Bestellung
- FIX Logo-Verwendung in 6.0.4 - eigene Logos wurden nicht übernommen

## [6.0.9]
- FIX Mysql-Fehler template_type/template_body cannot be NULL beim Kopieren/Speichern von PDF-Vorlagen
- Pflichtfelder definiert beim Anlegen/Speichern von PDF-Vorlagen
- FIX Pdf-Anzeige nicht möglich (Grund konnte nicht ermittelt werden, Hosting/3t plg?)

## [6.0.8]
- FIX Rabattanzeige auf Rechnung #118 #112
- FIX Blättern (Paging) im Backend optimiert (Speicherverbrauch gesenkt)
- Lieferschein: im Standard werden auch die Versandkosten/Gewicht angezeigt
- Anpassungen für aktualisert Bibliothek dompdf 0.8.6 in xt 6.3
- Anzeige Seriennummer auf Rechung (benötigt xt_serials min 6.0.2)
 
## [6.0.7]
- Installer typo Fix 'invoice_fistname' vs 'invocie_firstname'

## [6.0.6]
- Installer / Updater Fix
- firstname / lastname NULL erlaubt
- neue DB-Spalte invoice_company

## [6.0.5]
- FIX Automatischer Versand direkt bei Bestellung: Bei Zahlarten mit eigener Bankverbindung (zB Paypal Rechnung) wurde nicht die tatsächliche bankverbindung sondern die des Shops angezeigt/gedruckt

## [6.0.4]
- Logo-Verwendung überarbeitet https://xtcommerce.atlassian.net/wiki/spaces/MANUAL/pages/70844427/PDF-Templates
- FIX im dummy wird kein Logo angezeigt

## [6.0.3]
- Anpassungen für `products_shippingtime_nostock` (xt 6.2)
- FIX csrf-Warnings

## [6.0.2]
- FIX PHP-Warnings