<?php
// author: IvanRudev 22 dec 2004.
// action usergroups 

if (!$cols) $cols = 4; //number of table columns
else $cols = intval($cols);

if (is_array($this->config['aliases'])) 
{
  if (!$nomark)
    print('<fieldset><legend>'.$this->GetResourceValue("UserGroups").':</legend>');
  print ('<table border="0" cellspacing="5" cellpadding="5"><tr>');
  
  $i = 1;
  
  foreach ($this->config['aliases'] as $gname => $gusers) 
  {
    if ($i == $cols+1) {print '</tr><tr>';$i = 1;}
    print '<td valign="top" bgcolor="#f0f0f0">';
    
    $arr = explode("\n",$gusers);
    foreach ($arr as $k=>$v) 
      $arr[$k] = $this->Link("/".$v,"",$v);
    
    $gusers = implode("<br />",$arr);
    print "<strong>$gname</strong>:<br />".str_replace("\n","<br />",$gusers)."<br />";
    print "</td>";
    
    $i++;
  }
  
  print ('</tr></table>');
  if (!$nomark)
    print ('</fieldset>');
}
?>