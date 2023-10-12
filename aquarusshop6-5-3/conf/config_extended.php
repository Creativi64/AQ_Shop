<?php
/*
 #########################################################################
 #                       xt:Commerce Shopsoftware
 # ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 #
 # Copyright 2021 xt:Commerce GmbH All Rights Reserved.
 # This file may not be redistributed in whole or significant part.
 # Content of this file is Protected By International Copyright Laws.
 #
 # ~~~~~~ xt:Commerce Shopsoftware IS NOT FREE SOFTWARE ~~~~~~~
 #
 # https://www.xt-commerce.com
 #
 # ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 #
 # @copyright xt:Commerce GmbH, www.xt-commerce.com
 #
 # ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 #
 # xt:Commerce GmbH, Maximilianstrasse 9, 6020 Innsbruck
 #
 # office@xt-commerce.com
 #
 #########################################################################
 */

defined('_VALID_CALL') or die('Direct Access is not allowed.');

/**
 *  beim Speichern im backend auf doppelte artikelnr prüfen
 */
define('CHECK_DUPLICATE_PRODUCTS_MODEL',true);

/**
 *   themes editor
 */
// themes.xml wird gegen themes.xsd validiert
define('THEMES_VALIDATE_XML', false);
// Haupt-Css (Template.css) komprimieren bei Erzeugung
define('THEMES_COMPRESS_CSS', false);

/**
 *   Sortierung und Sortierrichtung in Zuweisung Artikel > Kategorie
 *
 *   mgl Sortierung
 *   categories_name | categories_id | sort_order
 *
 *   mgl Sortierrichtung
 *   asc | desc
 */
define('PRODUCT_TO_CAT_SORT_KEY', 'categories_name');
define('PRODUCT_TO_CAT_SORT_ORDER', 'asc');

/**
 *   Sortierung in Zuweisung Artikel > Artikeleigenschaften > Varianten
 *   in Zuweisung Artikel > Artikeleigenschaften immer nach Name (text)
 *
 *   mgl Sortierung
 *   text | sort_order | model
 */
define('BACKEND_ATTRIBUTES_SORT_1', 'text');
