## [1.2.5]
- FIX "drehende Räder/Pfeile" auf Seite Zahlungsweise, Klarna-Widget werden uU nicht geladen
- FIX Backend Übersicht Bestellungen, letzte Zeile nicht sichtbar

## [1.2.4]
- FIX Uncaught Error: Attempt to assign property "expires_at" on null, PHP8
- FIX Uncaught TypeError: in_array(): Argument #2 ($haystack) must be of type array, string given, PHP8
- FIX js Fehler im Backend
- FIX fehlende Icons im Backend

## [1.2.3]
- FIX Fehler in Bezeichnung eines hookpoints

## [1.2.2]
- Anpassung 'Anrede', Wert wird nicht an Klarna übertragen, wenn nicht [female,male,company]

## [1.2.1]
- FIX es wurden falsche Werte für Artikelanzahl/preis und Versandkosten an Klarna übertragen  
  wenn nach Auswahl der Zahlungsweise der Warenkorb verändert wurde

## [1.2.0]
- NEU unter Trigger können mehrere Werte gewählt werden

## [1.1.8]
- FIX  javascript-Fehler 'jquery is not defined'

## [1.1.7]
- FIX doppelte Anzeige 'Testmodus...' im Checkout (anders al in 1.1.6)
- mehrfaches Erzeugen der Klarna-Session vermeiden auf checkout/payment

## [1.1.6]
- FIX js für xt6.3
- FIX BAD_VALUE: Bad value: order_lines[1].total_tax_amount, weil tax immer mit 0 angegeben, wenn unbekannter orderline.type
  bei Verwendung von unbekannten Typen für orderline.type > Standard: surcharge 
  Neuer Hookpoint in setCartSubcontent um ggf orderline.type und tax anzupassen
- FIX doppelte Anzeige 'Testmodus...' im Checkout

## [1.1.5]
- FIX Fehler durch falsche Angabe zu autoload

## [1.1.4]
- FIX Hinweis anzeigen, wennn Land der Zahlungsadresse von Klarna nicht akzeptiert wird

## [1.1.3]
- FIX Fehlende php-Funktion für 'Aktualisieren' an der Bestellung

## [1.1.2]
- FIX teilweise doppelte Bestellung durch js-Fehler QCK-520-61997

## [1.1.1]
- fix csrf warnings

## [1.1.0]
- Anmeldedaten getrennt von Klarna Checkout (KCO)
- Möglichkeit Kundengruppe zu definieren die immer im Testmodus arbeitet  
- Im Testmodus erstellte Bestellungen sind in Übersicht und Detailansicht markiert mit 'Testmodus'  
  Die Markierung greift nur für Bestellungen die mit Plg-Version >= 1.1.0 angelegt wurden
- Option Icon-Auswahl entfernt  
  Icon in Box Zahlungsweisen entsprechend Klarna-Vorgaben angepasst  
  benötigt für korrekte Darstellung box_payment_logos.html aus xt 6.1.2)  
- Anzeige 'Nicht erfasste Beträge' berücksichtigt nur noch Livebestellungen
- FIX csrf-Warnungen bei Aufruf der KP-Shop-Einstellngen  
- FIX Bestellungen Listenübersicht: Filter 'Klarna: nicht vollständig erfasst' korregiert
- FIX Installer für xtWizard