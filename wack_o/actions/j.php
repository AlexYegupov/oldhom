<?php

if(isset($permalink)) {

  $jSep = '|';
  $jString = explode($jSep, $permalink);
  if(is_array($jString)) {
    $userName = $jString['1'];
    $itemId   = $jString['2'];

    $jTitle1    = "Запись в журнале $userName";
    $jLinkText  = "lj://$userName/$itemId";

    if(isset($title)) {
      // echo($title.'<br />');
      $jTitle1    = $title;
      $title      = $this->Format($title);
      $jLinkText  = "$userName: $title";
    }

    $jTitle2 = "$jTitle1 (без оформления)";

    $jLink = "<a href=\"http://www.livejournal.com/users/$userName/$itemId.html?usescheme=lynx\" title=\"$jTitle2\">&#0149;</a>&nbsp;<a href=\"http://www.livejournal.com/users/$userName/$itemId.html\" title=\"$jTitle1\">$jLinkText</a>";
    echo($jLink);
  }
}
?> 