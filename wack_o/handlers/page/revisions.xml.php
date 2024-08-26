<?php
header("Content-type: text/xml");

$xml = "<?xml version=\"1.0\" encoding=\"".$this->GetCharset()."\"?>\n";
$xml .= "<rss version=\"2.0\">\n";
$xml .= "<channel>\n";
$xml .= "<title>".$this->GetConfigValue("wakka_name")." - ".$this->tag."</title>\n";
$xml .= "<link>".$this->GetConfigValue("base_url").$this->tag."</link>\n";
$xml .= "<description>History/revisions of ".$this->GetConfigValue("wakka_name")."/".$this->tag."</description>\n";
$xml .= "<language>en-us</language>\n";

if ($this->HasAccess("read"))
{
 // load revisions for this page
 if ($pages = $this->LoadRevisions($this->tag))
 {
  $max = 10;

  $c = 0;
  $_GET["b"] = -1;
  $_GET["fastdiff"] = 1;
  foreach ($pages as $page)
  {
   $c++;
   if (($c <= $max) && $c>1)
   {
    $xml .= "<item>\n";
    $xml .= "<title>".$page["time"]."</title>\n";
    $xml .= "<link>".$this->href("show").($this->GetConfigValue("rewrite_mode")?"?":"&amp;")."time=".urlencode($page["time"])."</link>\n";
    $_GET["a"] = $_GET["b"];
    $_GET["b"] = $page["id"];
    $diff = $this->IncludeBuffered("handlers/page/diff.php", "oops");

    $xml .= "<description>".str_replace("<", "&lt;", str_replace("&", "&amp;", $diff))."</description>\n";
    $xml .= "</item>\n";
   }
  }
 }
}
else
{
 $xml .= "<item>\n";
 $xml .= "<title>Error</title>\n";
 $xml .= "<link>".$this->href("show")."</link>\n";
 $xml .= "<description>You're not allowed to access this information.</description>\n";
 $xml .= "</item>\n";
}

$xml .= "</channel>\n";
$xml .= "</rss>\n";

print($xml);

?>
