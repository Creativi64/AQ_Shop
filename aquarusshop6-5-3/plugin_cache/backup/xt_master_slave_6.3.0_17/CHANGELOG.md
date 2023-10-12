## [6.3.0]
- FIX SQL-Fehler beim Aufruf Variantenartikel durch falschen DB_Spaltennamen / Installer-Fix
- NEU 'Menge der Varianten summieren für Staffelpreis' lässt sich auch am Hauptartikel konfigurieren  
  abweichend von der Einstellung am Plugin

## [6.2.3]
- FIX SQL-Fehler bei Suche nach Attributen in der Ansicht 'Artikelvarianten'

## [6.2.2]
- FIX weitere Unterscheidung für 'Varianten werden nicht richtig ermittelt bei fehlerhafter Konfiguration (keine Eigenschaften festgelegt)'  
  

## [6.2.1]
- FIX Varianten werden nicht richtig ermittelt bei fehlerhafter Konfiguration (keine Eigenschaften festgelegt)
- FIX php-Fehler called getProductList() on null in product_info_tpl_list.php

## [6.2.0]
- NEU varianten können Bilder des Hauptartikels laden  
  es wird erst die Einstellung an der Variante gelesen, dann am Hauptartikel, dann am Plugin
- Hauptbilder nicht laden
- Einstellung vom Hauptartikel übernehmen (bzw am Hauptartikel vom Plugin übernehmen)
- Bilder vor den Variantenbilder einreihen
- NEU oben genannte Einstellungen sind eben so für das Hauptbild möglich 
- Bilder nach den Variantenbildern anfügen

## [6.1.2]
- FIX Dateianhänge öffnen erst nach x Klicks
- FIX unnötiger/falscher Link bei Darstellung der Varianten

## [6.1.1]
- FIX #201 Kein Update der attributes_parent_id in xt_plg_products_to_attributes  
  beim Verschieben eines Attributes in anderes Eltern-Attribute
- FIX Paging Artikel-Eigenschaften im Backend  

## [6.1.0]
- FIX DB-Fehler, wenn anderer DB-Prefix als xt_ verwendet wird
- Sortierung im Backend unter Artikelvarianten nun nach Parent-Attribut und dann Modell  
  Sortierung mgl nach Name, Model, Reihenfolge
- FIX 'Items pro Seite' wurde nicht übernommen im Backend unter Artikelvarianten  
- NEU  Ansicht Zuweisung Eigenschaften zu Artikel: Sortierung der Eigenschaften nach Name   
  einstellbar, zZ php-verdrahtet in plugins/xt_master_slave/classes/config.php, siehe dort
 

## [6.0.2]
- FIX keine Bilder/Beschreibung in Variantenliste wenn nur eine Variante vorhanden/aktiv

## [6.0.1]
- Bearbeitung der Produkteigenschaften der Artikelvarianten in der Übersichtsliste #191
- FIX fehlendes Icon Bilder Upload in der Übersichtsliste

## [6.0.0]
- Änderung der Bezeichnugen
    - Master > Hauptartikel / Artikel mit Varianten / Varianten-Artikel
    - Slave  > Artikelvariante / Artikel-Variante
    
- Generator und manuelle Zuordnung der Eigenschaften
    - übersichtlicher durch weniger Icons
    - Hauptknoten 'Artikel Eigenschaften' öffnet automatisch
    - Weiter-Button 'scrollt' nicht mehr weg, sondern bleibt immer im Sichtbereich

- manuelle Zuordnung der Eigenschaften
    - es ist nicht mehr möglich fälschlicherweise mehrere Werte einer Eigenschaft auszuwählen
    - es ist möglich die nicht verwendeten Eigenschaften auszublenden
    
- Funktion 'Artikel-Eigenschaften' für Hauptprodukte deaktiviert \
  ACHTUNG der Updater löscht in der Datenbank die falsch zugeordenten Eigenschaften für Hauptprodukte    
  
- Funktionen Variantenliste und Generator lassen sich auch über eine Variante aufrufen

- An einem Hauptartikel kann der Preis für alle Varianten übernommen werden   

- einige Plugin-Optionen/Einstellungen lassen sich an den Varianten-Artikel überschreiben
    - Auswahl: Plugin-Einstellungen / ja / nein
    
- einige Einstellungen der Varianten-Artikel lassen sich an den Artikelvarianten überschreiben
    - Auswahl: Konfiguration Hauptprodukt / ja / nein /    

- Neue Option 'Slave-Liste ausblenden bei konkretem Artikel'

- FIX $ not defined. Anpassungen für jquery-Load im footer (xt 6.3)
    
- Datenbankänderungen für MySQL 8 

- Datenbank wird bereinigt bei Plg-Update und jeder Erzeugung von Varianten \
   xt_tmp_product und xt_tmp_plg_products_to_attributes \
   Bsp-Kunde 300 MB
   
- FIX für PHP 8   

## [5.2.3]
- FIX  Slave-Sortierung greift nicht, wenn Plg-Option 'Slave-Liste Filtern nach Optionsauswahl' nicht aktiv ist
- php warning

## [5.2.2]
- FIX  Installer/Updater. Fehlende Spalte products_master_slave_order in Tabelle xt_tmp_products

## [5.2.1]
- FIX  Artikel ändert sich nicht bei Auswahl einer Eigenschaft/Attributs, wenn der Artikel nur 1 Attribut hat

## [5.2.0]
- FIX Slave-Generator: Artikelname wurde in allen Sprachen immer in der Sprache des Backends angelegt\
  aber 'nur', wenn im Generator Änderung wie Preis gemacht wurden und gespeichert wurde\
  Hinweis mehrsprachige Shops: Der Artikelname kann im Generator nicht mehr geändert werden\
  Änderung des Artikelnamen im Generator ist nur in einsprachigen Shops möglich
- NEW Slave-Generator: Angabe der Slave-Sortierung möglich
- FIX: Sortierung drr Slaves in der Varianten-Liste behoben   

## [5.1.9]
- Template-Fix in ms_default.html\

bekannte Fehler:
- html-Fehler 'attribut anchor not allowed' benötigt den fix in class.form.php aus xt6.2

## [5.1.8]
- FIX es wird angezeigt 'xy leider nicht verfügbar', obwohl mit Bestand. Fehler tritt bei zb einem Farb-Artikel auf, wenn bereits ein Artikel mit Farbe im Warenkorb liegt

## [5.1.7]
- php warning/notice/deprecated

## [5.1.6]
- FIX *Zu viele Umleitungen* aus Warenkorb bei falsch konfigurierten Slaves (ohne Optionen)

## [5.1.4]
- Updater prüft ob die Indizes products_model und products_master_model existieren
- Unnötigen redirect verhindern

## [5.1.3]
- FIX: Master-Artikel dürfen keinen Staffelpreis haben, wenn die Slaves keine Artikel-Eigenschaft besitzen ('falsch' konfigurierte Slaves)
- Abwärtskompatibilität: Darstellung von Slaves ohne Eigenschaften 

## [5.1.2]
- hook plugins/xt_master_slave/hooks/class.product.php_buildData_top.php wird nur ausgeführt, wenn $this->data['products_option_master_price']!='mp' 
- Code-Cleanup für master_model-Ermittlung in plugins/xt_master_slave/hooks/class.product.php_BuildData_bottom.php

## [5.1.1]
- FIX: keine fehlerhafte Doppelauswahl bei nicht verfügbarem Slave, stattdessen wieder Anzeige 'Nicht verfügbar ...'
- Hookpoint deaktiviert
- FIX: Template HTML 

## [5.1.0]
- xt:Commerce 5.1 Anpassungen
- Liste der ms-Attribute zeigt jetzt Namen der übergeordeneten Attributs; Filter für übergeordente Attribute
- Teilweise fehlerhafter redirect wenn Cross-Selling-Artikel die gleiche oberste Eigenschaft hat, error_log Wanings behoben
- FIX: kein 302 mehr bei Direktaufruf eines Slaves bei nur einem Slave

## [5.0.10]
- Fix: Einstellung ’Produkte in der Shop Suche’ wirkt sich fälschlicherweise auf Listings (zB Kategorie) aus

## [5.0.9]
- Bugfixes

## [5.0.8]
- Bugfixes

## [5.0.7]
- Fix: fehlerhafter Redirect
- Fix: Problem bei Attribut-Auswahl

## [5.0.6]
- Performance-verbessrung Warenkorb mit Master-Slave-Artikeln
- Fix: Kein Warenkorb-Button in Listings für Master-Artikel, wenn nur ein Slave aktiv

## [5.0.5]
- Fix: Warenkorb-Button nur bei Slaves
- Fix für: kein Attribut-Auswahl wenn einzelne Slaves deaktiviert

## [5.0.4]
- Fix: 'unvorhersehbare' Slave-Auswahl im Shop-Frontend

## [5.0.3]
- Fix: Redirect-Problem im Produktlisting, wenn kein Slave vorhanden
- Fix: Problem bei nicht erzeugten Slaves bzw. nicht vollständig konfiguriertem Master

## [5.0.2]
- Fix: Artikel lassen sich nun direkt aus dem Slave-Listing auf der Produktseite zum Warenkorb hinzufügen

## [5.0.1]
- Fix: XTC4PMS-15 - Bei Master-Artikeln im Listing fehlt Preisanzeige
- Fix: Tabellenname hardcoded

## [5.0.0]
- xt:Commerce 5 Anpassungen
