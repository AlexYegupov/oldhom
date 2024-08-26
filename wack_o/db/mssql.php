<?php

// Non-working MSSQL driver.

function quote($string) 
{
  $s = mysql_escape_string($string);
  $s = str_replace('\\\\','\\',$s);
 return str_replace("\\'","''",$s);
}

//All DBALs (mysql excluded) must replace LIMIT with some other instruction.
function query($query, $dblink="")
{
  if (strpos($query,"limit"))
  {
    //1. get no
    preg_match("/(limit\s+([0-9])+[\s;]*$)/i", $query, $m);
    $no = 1*$m[2];
    //2. delete limit
    $query = str_replace( $m[1], "", $query );
    //3. insert top
    if (strpos($query, "select") === 0)
     $query = str_replace( "select", "select top ".$no, $query );
  }
  $query = str_replace("now()", "getdate()", $query);
  if (!$result = mssql_query($query, $dblink)) 
  {
    ob_end_clean();
    die("Query failed: ".$query." (".mssql_get_last_message().")");
  }
  return $result;
}

function fetch_assoc($rs) 
{
 return mssql_fetch_assoc($rs);
}

function free_result($rs) 
{
 return mssql_free_result($rs);
}

function connect($host, $user, $passw, $db, $collation = false) 
{
 $dblink = mssql_connect($host, $user, $passw);
 mssql_select_db($db, $dblink);
 return $dblink;
}



?>