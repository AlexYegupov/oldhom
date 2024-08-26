<?php
// $Id: wakka.php 495 2005-03-27 12:49:11Z kukutz $
// $Date: 2005-03-27 16:49:11 +0400 (Sun, 27 Mar 2005) $                        $Revision: 1.139 $

// app start
error_reporting (E_ALL ^ E_NOTICE);

if (ini_get("zlib.output_compression"))
  ob_start();
else
  ob_start("ob_gzhandler");

// do not change this two lines, PLEASE-PLEASE. In fact, don't change anything! Ever!
define("WAKKA_VERSION", "0.1.2");
define("WACKO_VERSION", "R4.2");
define('XML_HTMLSAX3', dirname(__FILE__)."/lib/HTMLSax3/");

// stupid version check
if (!isset($_REQUEST)) die('$_REQUEST[] not found. WackoWiki requires PHP 4.1.0 or higher!');

// workaround for the amazingly annoying magic quotes.
function magicQuotesSuck(&$a)
{
  if (is_array($a))
  {
    foreach ($a as $k => $v)
    {
      if (is_array($v))
        magicQuotesSuck($a[$k]);
      else
        $a[$k] = stripslashes($v);
    }
  }
}
set_magic_quotes_runtime(0);
if (get_magic_quotes_gpc())
{
  magicQuotesSuck($_POST);
  magicQuotesSuck($_GET);
  magicQuotesSuck($_COOKIE);
  magicQuotesSuck($_SERVER);
  magicQuotesSuck($_REQUEST);
}

if (strstr($_SERVER["SERVER_SOFTWARE"], "IIS")) $_SERVER["REQUEST_URI"] = $_SERVER["PATH_INFO"];

// default configuration values
$wakkaDefaultConfig = array(
  "mysql_host"      => "localhost",
  "mysql_database"    => "wakka",
  "mysql_user"      => "wakka",
  "table_prefix"      => "wakka_",
  "cookie_prefix"      => "wakka_",

  "root_page"       => "HomePage",
  "wakka_name"      => "MyWackoSite",
  "base_url"        => "http://".$_SERVER["SERVER_NAME"].
                       ($_SERVER["SERVER_PORT"] != 80 ? ":".$_SERVER["SERVER_PORT"] : "").
                       preg_replace("/(\?|&)installAction=default/","",$_SERVER["REQUEST_URI"]).
                       (preg_match("/wakka\.php/", $_SERVER["REQUEST_URI"]) ? "?wakka=" : ""),
  "rewrite_mode"      => (preg_match("/wakka\.php/", $_SERVER["REQUEST_URI"]) ? "0" : "1"),

  "action_path"     => "actions",
  "handler_path"      => "handlers",

  "language"     => "en",
  "theme"      => "default",

  "header_action"     => "header",
  "footer_action"     => "footer",

  "show_datetime"     => "Y",
  "show_spaces"     => "Y",

//  "site_bookmarks" => "PageIndex / RecentChanges / RecentlyCommented",
//  "default_bookmarks" => "PageIndex\nRecentChanges\nRecentlyCommented\n((Registration))",
  "default_typografica" => 1,
  "default_showdatetime" => 1,
  "paragrafica" => 1,

  "referrers_purge_time"  => 1,
  "pages_purge_time"    => 0,

  "hide_comments"     => 0,
  "debug"     => 0,
  "youarehere_text"     => "",
  "hide_locked"     => 1,
  "allow_rawhtml" => 1,
  "disable_safehtml" => 0,
  "urls_underscores" => 0,

  "allrecentchanges_page" => "", 
  "allpageindex_page" => "",

  "default_write_acl"   => "*",
  "default_read_acl"    => "*",
  "default_comment_acl" => "*",
  "default_rename_redirect" => 1,
  "owners_can_remove_comments" => 1,
  "allow_registration" => 1,

  "standart_handlers" => "acls|addcomment|claim|diff|edit|msword|print|referrers|referrers_sites|remove|rename|revisions|revisions\.xml|show|watch|settings",

  "edit_table_based" => 0,
  "revisions_hide_cancel" => 0,
  "footer_comments" => 1,
  "footer_files" => 1,

  "disable_tikilinks" => 0,
  "remove_onlyadmins" => 0,

  "upload"             => "admins",
  "upload_images_only" => 0,
  "upload_max_size"    => 190,
  "upload_max_per_user" => 100,
  "upload_path"           => "files",
  "upload_path_per_page"  => "files/perpage",
  "upload_banned_exts" => "php|cgi|js|php|php3|php4|php5|pl|ssi|jsp|phtm|phtml|shtm|shtml|xhtm|xht|asp|aspx|htw|ida|idq|cer|cdx|asa|htr|idc|stm|printer|asax|ascx|ashx|asmx|axd|vdisco|rem|soap|config|cs|csproj|vb|vbproj|webinfo|licx|resx|resources",

  "outlook_workaround" => 1,
  "disable_autosubscribe" => 0,
  "allow_gethostbyaddr" => 1,

  "multilanguage" => 1,

  "cache" => 0,
  "cache_dir" => "_cache/",
  "cache_ttl" => 600,

  "db_collation" => 0,
  "rename_globalacl" => "Admins",

  );
$wakkaDefaultConfig['aliases'] = array('Admins' => "",);


// load config
if (!$configfile = GetEnv("WAKKA_CONFIG")) $configfile = "wakka.config.php";
if (@file_exists($configfile)) include($configfile);
$wakkaConfigLocation = $configfile;
$wakkaConfig = array_merge($wakkaDefaultConfig, (array)$wakkaConfig);

// check for locking
if (@file_exists("locked")) {
  // read password from lockfile
  $lines = file("locked");
  $lockpw = trim($lines[0]);

  // is authentification given?
  if (isset($_SERVER["PHP_AUTH_USER"])) {
    if (!(($_SERVER["PHP_AUTH_USER"] == "admin") && ($_SERVER["PHP_AUTH_PW"] == $lockpw))) {
      $ask = 1;
    }
  } else {
    $ask = 1;
  }

  if ($ask) {
    header("WWW-Authenticate: Basic realm=\"".$wakkaConfig["wakka_name"]." Install/Upgrade Interface\"");
    header("HTTP/1.0 401 Unauthorized");
    print("This site is currently being upgraded. Please try again later.");
    exit;
    }
}

// compare versions, start installer if necessary
if ($wakkaConfig["wacko_version"] != WACKO_VERSION)
{
  if (!$_REQUEST["installAction"] && !strstr($_SERVER["SERVER_SOFTWARE"], "IIS")) 
  {
   $req = $_SERVER["REQUEST_URI"];
   if ($req{strlen($req)-1}!="/" && strstr($req, ".php")!=".php") {
    header("Location: http://".$_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"]."/");
    exit;
   }
  }
  // start installer
  if (!$installAction = trim($_REQUEST["installAction"])) $installAction = "lang";
  include("setup/header.php");
  if (@file_exists("setup/".$installAction.".php")) include("setup/".$installAction.".php"); else print("<em>Invalid action</em>");
  include("setup/footer.php");
  exit;
}

// set root_url & theme_url
if (!isset($wakkaConfig["root_url"])) $wakkaConfig["root_url"]=preg_replace("#/[^/]*$#","/",$wakkaConfig["base_url"]);
$wakkaConfig["theme_url"]=$wakkaConfig["root_url"]."themes/".$wakkaConfig["theme"]."/";

//user-table
if (!isset($wakkaConfig["user_table"]) && !$wakkaConfig["user_table"]) $wakkaConfig["user_table"] = $wakkaConfig["table_prefix"]."users";

// fetch wakka location
if (isset($_SERVER["PATH_INFO"]) && function_exists("virtual")) $request = $_SERVER["PATH_INFO"];
else $request = @$_REQUEST["wakka"];

// fix win32 apache 1 bug
if (stristr($_SERVER["SERVER_SOFTWARE"], "Apache/1") && stristr($_SERVER["SERVER_SOFTWARE"], "Win32") && $wakkaConfig["rewrite_mode"])
{
 $dir = str_replace("http://".$_SERVER["SERVER_NAME"].($_SERVER["SERVER_PORT"] != 80 ? ":".$_SERVER["SERVER_PORT"] : ""),"",$wakkaConfig["base_url"]);
 $request = preg_replace("+^".preg_quote(rtrim($dir,"/"))."+i","",$_SERVER["REDIRECT_URL"]);//$request);
} 

// remove leading slash
$request = preg_replace("/^\//", "", $request);
$method = '';

// split into page/method
$p = strrpos($request, "/");
if ($p === false) { 
 $page = $request;
} else {
 $page = substr($request, 0, $p);
 $m1 = $method = strtolower(substr($request, $p-strlen($request)+1));
 if (!@file_exists($wakkaConfig["handler_path"]."/page/".$method.".php")) 
 {
  $page = $request;
  $method = "";
 } else if (preg_match( '/^(.*?)\/('.$wakkaConfig["standart_handlers"].')($|\/(.*)$)/i', $page, $match ))
 {//translit case
  $page = $match[1];
  $method = $match[2];
 }
}

// load dbal
if (!isset( $wakkaConfig["db_layer"] )) $wakkaConfig["db_layer"] = "mysql";
$dbfile = "db/".$wakkaConfig["db_layer"].".php";
if (@file_exists($dbfile)) include($dbfile);
else die("Error loading DBAL.");

// cache!
require("classes/cache.php");
$cache = &new Cache($wakkaConfig["cache_dir"], $wakkaConfig["cache_ttl"]);

$iscache = null;
if ($wakkaConfig["cache"] &&  $_SERVER["REQUEST_METHOD"]!="POST" && $method!="edit" && $method!="watch")
{
 // anonymous
 if (!$_COOKIE[$wakkaConfig["cookie_prefix"]."name"])
 {
   $iscache = $cache->CheckHttpRequest($page, $method);
 }
}

// start session
session_start();

// create wacko object
require("classes/wacko.php");
$wacko = &new Wacko($wakkaConfig);
$wacko->headerCount = 0;
$cache->wacko = &$wacko;
$wacko->cache = &$cache;
//$cache->Log("Before Run wacko=".$wacko);
if ($method && $method != "show") unset($wacko->config["youarehere_text"]); 

// go!
$pg = $wacko->Run($page, $method);

if ($iscache) 
{
 $data = ob_get_contents();
 $cache->StoreToCache($data);
}

// how much time script take
$ddd = $wacko->GetMicroTime();
if ($wacko->GetConfigValue("debug")>=1 && strpos($method,".xml")===false && $method!="print") 
{
 echo ("<div style='margin:5px 20px; color:#999999'><small>".$wacko->GetResourceValue("MeasuredTime").": ".(number_format(($ddd-$wacko->timer),3))." s<br />");
 if ($mem = @memory_get_usage()) echo ($wacko->GetResourceValue("MeasuredMemory").": ".(number_format(($mem/(1024*1024)),3))." Mb");
 if ($wacko->GetConfigValue("debug")>=2)
 {
  $sql_time = 0;
  foreach($wacko->queryLog as $q)
    $sql_time += $q["time"];
  echo (" &nbsp; SQL time: ".$sql_time);
 }
 echo "</small></div>";
}
if (strpos($method,".xml")===false) 
 echo "</body></html>";

//spesta - del in release
//$s_addurl="stat/";
//@include ($s_addurl."counter.php"); 


?>
