<?php
  if ($this->GetConfigValue("debug")>=2)
  {
    print("<span style=\"font-size: 11px; color: #888888\">");
    print("<strong>Query log:</strong><br />\n");
    foreach ($this->queryLog as $query)
    {
      print($query["query"]." (".$query["time"].")<br />\n");
      $zz++;
    }
    print("<b>total: $zz</b>");
    print("</span>");
  }

//don't place final </body></html> here. Wacko closes HTML automatically.
?>

<!-- wrapper -->
		</td>
	</tr>
</table>

<table class="bottom" align="center" border="0" cellpadding="0" cellspacing="0" width="100%">
<tr>
 <td class="copyright">
  		Powered by <?php echo $this->Link("WackoWiki:WackoWiki", "", "WackoWiki ".$this->GetWackoVersion()) ?>
   </td>
</tr>

</table>
