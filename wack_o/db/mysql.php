<?php

//Primitive DBAL for mysql

function quote($string) 
{
 return mysql_escape_string($string);
}

//All DBALs (mysql excluded) must replace LIMIT with some other instruction.
function query($query, $dblink="")
{
  $result = mysql_query($query, $dblink);
  if (mysql_errno())
  {
    ob_end_clean();
    die("Query failed: ".$query." (".mysql_errno().": ".mysql_error().")");
  }
  return $result;
}

function fetch_assoc($rs) 
{
 return mysql_fetch_assoc($rs);
}

function free_result($rs) 
{
 return mysql_free_result($rs);
}

function connect($host, $user, $passw, $db, $collation = false) 
{
 if(!extension_loaded("mysql")) dl("mysql.so");
 $dblink = mysql_connect($host, $user, $passw);
 mysql_select_db($db, $dblink);
 if ($collation)  mysql_query("SET NAMES '".$collation."'", $dblink);
 return $dblink;
}



?>