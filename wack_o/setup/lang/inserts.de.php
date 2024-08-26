<?php
$lng = "de";

if ($config["language"]==$lng)
{
 InsertPage($config["root_page"], "((http://wackowiki.com/images/wacko4.gif WackoWiki))\n**Willkommen zu Deiner ((WackoWiki:WackoWiki WackoWiki)) Installation!**\n\nKlicke unten auf den Punkt \"Editieren\" um zu beginnen.\n\nDokumentation ist unter WackoWiki:WackoDocDeutsch zu finden.\n\nNützliche Seiten: ((WackoWiki:WackoDocDeutsch/Formatierung Formatierung)), VerwaisteSeiten, OffeneSeiten, TextSuche, MeineSeiten, MeineAenderungen.\n\n", $lng);
 InsertPage('OffeneSeiten', '{{WantedPages}}', $lng); 
 InsertPage('VerwaisteSeiten', '{{OrphanedPages}}', $lng); 
 InsertPage('MeineSeiten', '{{MyPages}}', $lng); 
 InsertPage('MeineAenderungen', '{{MyChanges}}', $lng); 
}

InsertPage('LetzteAenderungen', '{{RecentChanges}}', $lng); 
InsertPage('LetzteKommentare', '{{RecentlyCommented}}', $lng); 
InsertPage('SeitenIndex', '{{PageIndex}}', $lng); 
InsertPage('Benutzer', '{{LastUsers}}', $lng); 
InsertPage('Registrierung', '{{Registration}}', $lng); 

InsertPage('Password', '{{ChangePassword}}', $lng); 
InsertPage('TextSuche', '{{Search}}', $lng); 
InsertPage('Anmeldung', '{{Login}}', $lng); 
InsertPage('Einstellungen', '{{UserSettings}}', $lng); 

InsertPage('InterWiki', '{{InterWikiList}}', $lng); 

?>