## [5.1.4]
- FIX Anpassung an neuen Konstruktor getProductSQL_query

## [5.1.2]
- FIX wenn Anzeige-Limit unterschritten wird werden keine Artikel angezeigt

## [5.1.1]
- cleanup 5.1.0

## [5.1.0] 
- Vorläufiger Performance-Fix

## [5.0.6]
- FIX Class 'auto_cross_sell' not found

## [5.0.5]
- FIX Bei der Ermittlung der  auto-cross-sells wurde nicht gleich zu Beginn auf evtl mittlerweilen deaktivierte Artikel geprüft\
  dadurch konnte es teilweise passieren, dass zB trotz Konfiguration '4 Artikel anzeigen' nur 2 angezeigt wurden, obwohl 20 vorhanden
- Reihenfolge für Hooks im Artikel und Warenkorb hochgesetzt auf 10\
  dadurch wird gewährleistet, dass im Standard xt_cross_sell (Zubehör) vor xt_auto_cross_sell angezeigt wird\
  wird es in Ihrem Shop anders gewünscht, kann die Reihenfolge der Hooks angepasst werden: installierte Plugins > Hookpoints (icon)
- dev: die Ermittlung der Artikel findet nun komplett auf der DB statt, in einer Abfrage die gleich die Artikeldaten lädt  