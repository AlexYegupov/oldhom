<?php
//  action 'pageindex'
//  modified by YaroslavSyuzev 02 Fev 2005

$offset = ( isset($_GET["offset"]) ) ? (int)$_GET["offset"] : 0;
$limit = 100;

$sql = "select count(*) as n from ".$this->config["table_prefix"]."pages where ".
       "latest = 'Y' and LEFT(supertag,7)!='comment'";
$all_pages_count = $this->LoadSingle($sql);
$total = $all_pages_count["n"];

if ($limit >= $total) $no_arr = true;

//  collect data

$cnt = 0;
$pages_to_display = array();
$last_page = "";

while ($cnt < $limit)
{
  $sql = "select ".$this->pages_meta." from ".$this->config["table_prefix"]."pages where ".
        "latest = 'Y' and LEFT(supertag,7)!='comment' order by BINARY tag limit ".$offset.",".(2 * $limit);

  if ($pages = $this->LoadAll($sql))
  {
    foreach ($pages as $page)
    {
      $offset++;

      if ($this->config["hide_locked"]) $access = $this->HasAccess("read",$page["tag"]);
      else $access = true;
      if ($access)
      {
          $pages_to_display[$page["tag"]] = $page;
          $cnt++;
      }

      if ($cnt >= $limit) break 2;
    }
  }
  else break;

  if ($offset >= $total) break;
}


//  display collected data

foreach ($pages_to_display as $page)
{
  
  $firstChar = strtoupper($page["tag"][0]);
  if (!preg_match("/".$this->language["ALPHA"]."/", $firstChar)) { $firstChar = "#"; }

  if ($firstChar != $curChar) {
    if ($curChar) print("<br />\n");
    print("<strong>$firstChar</strong><br />\n");
    $curChar = $firstChar;
  }

  print($this->Link("/".$page["tag"],"",$page["tag"])."<br />\n");
}

//$last_page = array_pop($pages_to_display);

  
//  display navigation

if ( $offset < $total )
  $next_page_link = '<a href="?offset='.$offset.'">'.$this->GetResourceValue("Next").' &gt;</a>';
else
  $next_page_link = $this->GetResourceValue("Next")." &gt;";

if ($offset > $limit )
  $prev_page_link = '<a href="?offset='.(($offset-2*$limit) > 0 ? $offset-2*$limit : 0).'">&lt; '.$this->GetResourceValue("Prev").'</a>';
else
  $prev_page_link = "&lt; ".$this->GetResourceValue("Prev");
  

if ($pages_to_display)
{
  if (!$no_arr)
  {
    print "<p class='logBtn'>$prev_page_link $next_page_link</p>\n";
  }
}  
else
  echo $this->GetResourceValue("NoPagesFound");

?>