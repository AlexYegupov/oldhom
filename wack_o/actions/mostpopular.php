<?php

if (!$max)  $max = 25;
if ($max>500) $max = 500;

$pages = $this->LoadAll("select ".$this->pages_meta.",hits from ".$this->config["table_prefix"]."pages order by hits desc limit ".(2*(int)$max));
$num = 0;

print("<table>");
foreach ($pages as $page)
{
  if ($num<$max)
  {
   if ($this->config["hide_locked"]) $access = $this->HasAccess("read",$page["tag"]);
   else $access = true;
   if ($access)
   {
     // print entry
     $num++;
     print("<tr><td>&nbsp;&nbsp;".$num.".&nbsp;".$this->Link($page["tag"])."</td><td>".
           $this->GetResourceValue("Shown")."</td><td>".
           $page["hits"]."</td></tr>\n");
   }
  }
}
print("</table>");

?>