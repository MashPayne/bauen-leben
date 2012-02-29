<?php

$GLOBALS['TL_LANG']['tl_webShop_article'] = array(
    'title' => array('Artikelname', 'Bitte geben Sie einen Artikelnamen ein.'),
    'alias' => array('Artikelalias', 'Bitte geben Sie einen Artikelalias ein. Lassen Sie dieses Feld leer, um den Alias automatisch zu erzeugen.'),
    'type' => array('Typ', 'Bitte wählen Sie den Artikeltyp aus.'),
    'types' => array(
      'article' => 'Artikel',
      'articleVariants' => 'Artikel mit Varianten',
      'forward' => 'Weiterleitung zu einem anderen Artikel',
      'auction' => 'Auktion',
      'download' => 'Download Artikel'
    ),
    'description' => array('Artikelbeschreibung', 'Bitte geben Sie eine Beschreibung für diesen Artikel ein.'),
    'singlePrice' => array('Einzelpreis', 'Bitte geben Sie den Einzelpreis dieses Artikels ein.'),
    'taxid' => array('Besteuerung', 'Bitte wählen Sie aus wie dieser Artikel besteuert wird.'),
    'taxtype' => array('Umsatzsteuer ist im Preis:', 'Bitte wählen Sie ob die Umsatzsteuer bereits im Preis enthalten ist oder nicht.'),
    'taxtypes' => array(
      'included' => 'enthalten',
      'excluded' => 'nicht enthalten'
    ),
    'teaser' => array('Teasertext', 'Bitte geben Sie einen Teasertext für diesen Artikel ein.'),
    'keywords' => array('SEO - Keywords', 'Bitte geben Sie die Keywords für die Suchmaschinen ein, sie werden in den Metatags eingetragen.'),
    'seoDescription' => array('SEO - Description', 'Geben Sie hier einen Beschreibungstext für die Suchmaschinenoptimierung ein.'),
    'addImage' => array('Ein Bild hinzufügen', 'Soll diesem Artikel ein Bild hinzugefügt werden?'),
    'addGallery' => array('Eine Galerie hinzufügen', 'Soll diesem Artikel eine Galerie hinzugefügt werden?'),
    'singleSRC' => array('Bilddatei', 'Bitte wählen Sie eine Bilddatei aus.'),
    'multiSRC' => array('Bilddateien', 'Bitte wählen Sie Bilder für die Galerie aus.'),
    'template' => array('Template Datei', 'Bitte wählen Sie die Templatedatei aus, die für die Anzeige dieses Artikels verwendet werden soll.'),
    'start' => array('Anzeigen ab', 'Ab wann soll dieser Artikel angezeigt werden?'),
    'stop' => array('Anzeigen bis', 'Bis wann soll dieser Artikel angezeigt werden?'),
    'published' => array('Veröffentlicht', 'Dieser Artikel wird nur angezeigt, wenn er veröffentlicht ist.'),
    'attributes' => array('Artikelattribute', 'Bitte wählen Sie die Artikelattribute aus.'),
    'groupPrices' => array('Sonderpreise', 'Bitte geben Sie die Sonderpreise für bestimmte Benutzergruppen ein.'),
    'linkTarget' => array('Weiterleiten zu', 'Bitte wählen Sie den Artikel aus, zu dem Sie verlinken möchten.'),
    'productid' => array('Artikelnummer', 'Bitte geben Sie eine Artikelnummer ein.'),
    'addStock' => array('Artikel mit Lagerbestand', 'Ist dies ein Artikel mit begrenztem Lagerbestand?'),
    'stock' => array('Lagerbestand', 'Wieviele Artikel haben Sie noch auf Lager?'),
    'hideIfEmpty' => array('Bei leerem Lagerbestand ausblenden', 'Soll dieser Artikel ausgeblendet werden wenn der Lagerbestand null erreicht hat?'),
    'startPrice' => array('Startgebot', 'Bitte geben Sie das Startgebot für diese Auktion ein.'),
    'raisePrice' => array('Mindesterhöhung', 'Bitte geben Sie die Mindesterhöhung für diese Auktion ein.'),
    'auctionStart' => array('Auktionsbegin', 'Bitte geben Sie die Startzeit für diese Auktion ein'),
    'auctionEnd' => array('Auktionsende', 'Bitte geben Sie an, wann diese Auktion enden soll.'),
    'new' => array('Neuer Artikel', 'Einen neuen Artikel anlegen'),
    'variants' => array('Varianten bearbeiten', 'Varianten für den Artikel mit der ID %s bearbeiten'),
    'variantGrouping' => array('Varianten Gruppierung', 'Bitte wählen Sie eine Attributgruppe für die Sortierung der Varianten aus.'),
    'weight' => array('Gewicht', 'Bitte geben Sie das Gewicht dieses Artikels ein. Wichtig für die Berechnung der Versandkosten.'),
    'added' => array('Eingetragen am', 'Bitte geben Sie das Datum ein, an dem dieser Artikel in den Bestand aufgenommen wurde.<br/>Wichtig für die Anzeige der neuesten Artikel.'),
    'recommendet' => array('Empfohlene Artikel', 'Wählen Sie weitere Artikel aus die sie zu diesem empfehlen möchten.'),
    'isnew' => array('Als NEU markieren', 'Soll dieser Artikel als NEU im Shop angezeigt werden?'),
    'specialoffer' => array('Als ANGEBOT markieren', 'Soll dieser Artikel als ANGEBOT im Shop angezeigt werden?'),
    'specialprice' => array('Sonderpreis', 'Geben Sie einen Sonderpreis ein.'),
    'specialprice_start' => array('Sonderpreis gültig ab', 'Geben Sie ein Datum ein, ab wann der Sonderpreis gültig sein soll.'),
    'specialprice_stop' => array('Sonderpreis gültig bis', 'Geben Sie ein Datum ein, bis wann der Sonderpreis gültig sein soll.'),
    'options' => array('Artikeloptionen', 'Wählen Sie die Optionen aus, die bei diesem Artikel zur Auswahl stehen sollen.'),
    'seo_legend' => 'Suchmaschinen Optimierung',
    'image_legend' => 'Bilder zu diesem Artikel',
    'deliveryTime' => array('Lieferzeit', 'Bitte geben Sie an, wie schnell Sie den Artikel verschicken können (z.B. Versandfertig innerhalb 24h).'),
    'showvpe' => array('Grundpreis anzeigen', 'Zeigt automatisch den Grundpreis dieses Artikels an (z.B. pro Liter, pro Kilo etc)'),
    'vpeid' => array('Grundpreis Einheit', 'Bitte wählen Sie die Einheit aus.'),
    'vpefactor' => array('Grundpreis Faktor', 'Bitte geben Sie den Faktor für die Berechnung ein. (Bei 100g Verpackungen wäre der Faktor 0.1 für eine Kilo Angabe).'),
    'productgroup' => array('Produktgruppe', 'Bitte wählen Sie die Produktgruppe aus.'),
    'lbl_type' => 'Titel und Typ',
    'lbl_description' => 'Beschreibung',
    'lbl_prices' => 'Preise',
    'lbl_specialprices' => 'Sonderpreise',
    'lbl_images' => 'Bilder und Galerien',
    'lbl_image' => 'Bild',
    'lbl_seo' => 'Suchmaschinenoptimierung',
    'lbl_details' => 'Artikeldetails',
    'lbl_stock' => 'Lagerbestand',
    'lbl_attributes' => 'Artikelattribute',
    'lbl_recommendet' => 'Empfehlungen',
    'lbl_display' => 'Anzeige',
    
    'edit' => array('Bearbeiten', 'Datensatz #%s bearbeiten'),
    'cut' => array('Ausschneiden', 'Datensatz #%s ausschneiden'),
    'delete' => array('Löschen', 'Datensatz #%s löschen'),
    'show' => array('Anzeigen', 'Datensatz #%s anzeigen'),
    'copy' => array('Kopieren', 'Datensatz #%s kopieren')
    
  );

?>