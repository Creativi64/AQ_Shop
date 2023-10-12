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

// Suchbegriffe einzeln verarbeiten, ehemals Einstellungen => Konfiguration => Performance => Suchanfragen trennen
define('_SYSTEM_SEARCH_SPLIT', true);

// Zeichenketten kürzer als SEARCH_MIN_LENGTH werden von der Suche prinzipiell ausgeschlossen
// mit _SYSTEM_SEARCH_SPLIT = true werden Einzelzeichenketten aus der Suche entfernt
// mit _SYSTEM_SEARCH_SPLIT = false wird die Gesamtlänge bewertet
define('SEARCH_MIN_LENGTH', 3);

// Sortierung der Ergebnisse
define('SEARCH_SORT_ID',    1);
define('SEARCH_SORT_ADDED', 2);
define('SEARCH_SORT_NAME',  3);
define('SEARCH_SORT_ORDER', 4);
// SEARCH_SORT_ID       nach ID des Artikel, höchste zuerst
// SEARCH_SORT_DATE     nach Datum 'hinzugefügt' Artikel-DB-Spalte 'date_added', neueste zuerst
// SEARCH_SORT_NAME     nach Name des Artikels, beginnend mit A
// SEARCH_SORT_ORDER    nach Gesamtbestellmenge Artikel-DB-Spalte 'products_ordered', höchste zuerst
define('SEARCH_SORT', SEARCH_SORT_ID);

// Sortierrichtung   'ASC' = aufsteigend,  'DESC' = absteigend
define('SEARCH_SORT_DIRECTION', 'DESC');

// wie sollen die einzelnen Suchbegriffe verknüpft werden?
// bezieht sich auf die Suche in Keywords, Artikelnamen, -beschreibung und -kurzbeschreibung
// OR  > irgendeiner der Suchbegriffe soll gefunden werden > grosse Ergebnissmenge
// AND > alle Suchbegriffe müssen enthalten sein           > kleinere Ergebnissmenge
define('SEARCH_CONDITION_CONNECTOR', ' AND ');

// soll in Kategorien gesucht werden
define('SEARCH_USE_CATEGORIES', true);
// nur Kategorien der ersten Ebene anzeigen (Suche-Seite lädt schneller bei grossen Kategoriebäumen)
// empfohlen
// wenn true, wird automtisch die Checkbox 'Suche in Unterkategorien' angehakt
define('SEARCH_USE_CATEGORIES_FIRST_LEVEL_ONLY', true);

// soll in Herstellern gesucht werden können
define('SEARCH_USE_MANUFACTURERS', true);

// soll in Keywords gesucht werden
define('SEARCH_USE_KEYWORDS', true);

// soll in EAN gesucht werden und welche Mindestlänge hat eine EAN
define('SEARCH_USE_EAN', false);
define('SEARCH_EAN_MIN_LENGTH', 13);

// soll in der Artikelnummer gesucht werden und welche Mindestlänge hat eine Artikelnummer
define('SEARCH_USE_MODEL', true);
define('SEARCH_MODEL_MIN_LENGTH', 5);

// nur Artikel mit Bestand > 0 ?
define('SEARCH_CHECK_STOCK', false);

// Such-Box: Beschreibung durchsuchen
define('SEARCH_BOX_SEARCH_IN_DESC', false);

// Such-Box: Kurzbeschreibung durchsuchen
define('SEARCH_BOX_SEARCH_IN_SDESC', false);


