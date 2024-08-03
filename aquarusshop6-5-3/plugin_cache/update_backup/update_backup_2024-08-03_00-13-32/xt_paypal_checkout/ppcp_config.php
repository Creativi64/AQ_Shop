<?php

// bei Bedarf kopieren nach conf/ppcp_config.php und dort bearbeiten
// wird automatisch eingebunden, wenn in vorhanden in conf/

defined('_VALID_CALL') or die('Direct Access is not allowed.');

// auf diesen Seiten die PayPal SDK nicht laden, array
// bezieht sich auf global $page->name
define('PPCP_DONT_LOAD_SDK_PAGES',  ['categorie','content','manufacturers','index','404','manufacturers_categories','inquiry']);

// hier kann eigene Logik untergebracht werden für ob/wo/wann die PayPal js SDK geladen werden soll
/**
 * @return bool
 */
/*
function do_render_ppcp_sdk_code($render_ppcp_sdk_code)
{
    return $render_ppcp_sdk_code;
}
*/

// 'Später Zahlen' Ratenrechner ausblenden (reduziert Größe der zu ladenden SDK)
//define('PPCP_DONT_LOAD_SDK_COMPONENT_MESSAGES', false);

// für diesen Seiten wird kein snapshot auf der index.php gesetzt
// fest definiert und immer berücksichtigt sind['PAYPAL_CHECKOUT_GET_SHIPPING_OPTIONS', 'PAYPAL_CHECKOUT_ORDER_CREATE']
//define('PPCP_NO_SNAPSHOT_PAGES', false);

// Validierung der Webhooks aktivieren
// define('PPCP_VERIFY_WEBHOOK', true);


/**
 *  folgende Einstellungen nur für Entwicklung und Tests
 */

// overcapture testen
// führt dazu, dass nach Express der autorisierte Betrag erhöht
// damit ist ein neue Autorisierung erforderlich
//define('PPCP_TEST_OVER_CAPTURE', false);

// Standard-KK vs hosted-fields
// serverseitig wird über die ppcp capabilities geprüft ob CUSTOM_CARD_PROCESSING enabled ist
// allerdings liefert pp für Kunden aus AT/CH keine caps, siehe Backend > Einstellung > PayPal Checkout Signup > Onboarding Status anzeigen
// hosted-fields für einfaches Kreditkarten-Formular nicht mgl für AT/CH, Standard-KK wird angezeigt
// für Tests auf true setzen, um Standard-KK anzuzeigen, ohne hosted-fields Komponente
//define('PPCP_HOSTED_FIELDS_NOT_AVAILABLE', false);

// Land für Standard-KK
// mit welchen Länderteil der locale soll die PP-js-SDK geladen werden, wenn Sprache de/fr/it
// ergibt zb fr_CH oder de_CH
// mit dem CH-Teil wird bestimmt welches Land vorausgewählt ist bei Standard-KK
//define('PPCP_LOCALES_COUNTRY', 'CH');

// Webhook-Daten werden geloggt in xt_paypal_checkout.log
// define('PPCP_LOG_WEBHOOK', true);

// Webhooks nicht verarbeiten
// define('PPCP_DO_NOT_PROCESS_WEBHOOK', false);
