## [5.2.0]
- addCart/updateCarte/deleteCart beschleunigen

## [5.1.9]
- FIX Wert 'New' für Rabatt an Kundengruppe wird falsch verarbeitet IZT-812-81722
  Uncaught TypeError: Unsupported operand types: string / int in plugins/xt_customersdiscount/hooks/class.product.php__getPrice_price.php:33

## [5.1.8]
- FIX Fehler im Backend beim Hinzufügen eines Staffelpreis-Artikels zu einer Bestellung

## [5.1.7]
- FIX Kombination aus Kundengruppen-Rabat und Staffelpreis erfordert Neuladen der Seite nach Hinzufügen (RSF-622-55219)

## [5.1.6]
- FIX beim Entfernen eines Artikels aus dem Warenkorb wird der discount nicht sofort zurückgesetzt, sondern erfordert Neuladen der Seite