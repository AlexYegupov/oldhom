<?php
/*
   {{TOC page|for="!/SubTag" from="h2" to="h4" numerate="0|1|..." }}
*/
// 1. check for first param (for what TOC is built)
if ($for) $page=$for;
if ($page) 
{
  $page = $this->UnwrapLink($page);
  $ppage = "/".$page;
  $context = $page;
  $_page = $this->LoadPage($page);
  if (!$title) $title = $page;
  $link = $this->Href("",$_page["tag"]);
}
else 
{
  $page = ""; $ppage="";
  $context = $this->tag;
  $_page = $this->page;
  $link = "";
}

if (!$from) $from = "h2";
if (!$to)   $to   = "h9";

$start_depth = $from{1};
$end_depth   = $to{1};

// 3. output
print "<fieldset>";
print "<legend><strong> ".$this->GetResourceValue("TOCTitle")." ".$this->Link($ppage, "", $title)."  </strong></legend>";
if (!$this->HasAccess("read",$_page["tag"])) 
 print $this->GetResourceValue("ReadAccessDenied"); 
else
{
  $toc = $this->BuildToc( $context, $start_depth, $end_depth, $numerate, $link );
  { // ---------------------- toc numeration ------------------------
    // вы€сн€ем, какие номера где сто€т
    $toc_len = sizeof($toc);
    $numbers = array(); $depth = 0;
    for($i=0;$i<$toc_len;$i++)
     if ($toc[$i][2] < 66666)
     { // нормировали глубину погружени€
       $toc[$i][4] = $toc[$i][2]-$start_depth+1;
       if ($numerate)
       {
         // если погрузились глубже, обнулили счЄтчик дл€ новой глубины
         if ($toc[$i][2] > $depth) $numbers[ $toc[$i][2] ] =0; 
         // если вышли уровнем меньше, ничего делать не надо.
         // запоминаем глубину и увеличиваем счЄтчик пункта
         $depth = $toc[$i][2];
         $numbers[ $depth ]++;
         // собираем нумерацию по массиву $numbers от начала до текущей глубины, пропуска€ нулевые
         $num="";
         for($j=1;$j<=$depth; $j++)
          if ($numbers[$j] > 0) $num.=$numbers[$j].".";
         // правим содержимое TOC
         $toc[$i][1] = $num." ".$toc[$i][1];
       }
     }
     // неплохо б в кэш записать подобновлЄнную версию
     $this->tocs[ $context ] = &$toc;
     // теперь надо поставить флажок о том, что неплохо бы искурочить в пост-ваке 
     // исходник странички, добавив туда цыфирки
     if (!$ppage) { $this->post_wacko_toc = &$toc; $this->post_wacko_action["toc"] = 1; }
  } // --------------------------------------------------------------
 // отображаем!
  foreach( $toc as $v )
  if ($v[4])
  {
    echo '<div class="toc'.$v[4].'">';
     echo '<a href="'.$v[3].'#'.$v[0].'">'.strip_tags($v[1]).'</a>';
    echo '</div>';
  }
 //$this->tocRecursion( ($ppage?$this->Href("",$ppage):""), $toc_body, 2 );
}
print "</fieldset>";



?>
