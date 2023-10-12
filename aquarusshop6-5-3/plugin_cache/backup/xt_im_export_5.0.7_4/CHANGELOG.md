## [5.0.7]
- NEU Export/Import 'weitere Suchbegriffe' products_keywords

## [5.0.6]
- FIX #207 csv-Header werden nicht vollständig geschrieben, Sprachen/Bilder fehlen

## [5.0.5]
- Artikel-IMPORT-UPDATE
  - FIX für: Preise werden auf 0 gesetzt, wenn in UPDATE-Datei kein Preis-Spalte (products_price) angegeben ist
  - FIX für: Import-Einstellung 'Brutto-Preis' wird nicht berücksichtigt, wenn Artikel vorhanden und in UPDATE-Datei keine Steuerklasse (products_tax_class_id) angegeben ist
- Artikel-IMPORT-IMPORT
  - wenn in der IMPORT-Datei keine Steuerklasse (products_tax_class_id) angegeben ist, werden die Artikel nicht importiert !

## [5.0.4]
- Header wird wieder exportiert