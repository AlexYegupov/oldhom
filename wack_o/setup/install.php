<?php
@set_time_limit(0);
@ignore_user_abort(true);

// test configuration
print("<strong>".$lang["Testing Configuration"]."</strong><br />\n");
test($lang["TestSql"], $dblink = @mysql_connect($config2["mysql_host"], $config2["mysql_user"], $config2["mysql_password"]));
test($lang["Looking for database..."], @mysql_select_db($config2["mysql_database"], $dblink), $lang["DBError"]);
print("<br />\n");

// do installation stuff
if (!$version = trim($wakkaConfig["wakka_version"])) $version = "0";
if (trim($wakkaConfig["wacko_version"])) $version = trim($wakkaConfig["wacko_version"]);
switch ($version)
{
// new installation
case "0":
  print("<strong>".$lang["Installing Stuff"]."</strong><br />\n");
  test(str_replace("%1","page",$lang["Creating table..."]),
    @mysql_query(
      "CREATE TABLE ".$config2["table_prefix"]."pages (".
        "id int(10) unsigned NOT NULL auto_increment,".
        "tag varchar(250) binary NOT NULL default '',".
        "supertag varchar(250) NOT NULL default '',".
        "time datetime NOT NULL default '0000-00-00 00:00:00',".
        "body text NOT NULL,".
        "body_r text NOT NULL,".
        "body_toc text NOT NULL,".
        "owner varchar(50) NOT NULL default '',".
        "user varchar(50) NOT NULL default '',".
        "latest enum('Y','N') NOT NULL default 'N',".
        "handler varchar(30) NOT NULL default 'page', ".
        "comment_on varchar(250) binary NOT NULL default '', ".
        "super_comment_on varchar(250) NOT NULL default '', ".
        "hits int(11) NOT NULL default '0', ".
        "lang varchar(20) NOT NULL default '', ".
        "description varchar(250) NOT NULL default '',".
        "keywords varchar(250) binary NOT NULL default '',".
        "PRIMARY KEY  (id),".
        "FULLTEXT KEY body (body),".
        "UNIQUE KEY idx_tag (tag),".
        "KEY idx_supertag (supertag),".
        "KEY idx_time (time),".
        "KEY idx_latest (latest),".
        "KEY idx_comment_on (comment_on), ".
        "KEY idx_super_comment_on (super_comment_on) ".
      ") TYPE=MyISAM;", $dblink), $lang["Already exists?"], 0);
  test(str_replace("%1","revision",$lang["Creating table..."]),
    @mysql_query(
      "CREATE TABLE ".$config2["table_prefix"]."revisions (".
        "id int(10) unsigned NOT NULL auto_increment,".
        "tag varchar(250) binary NOT NULL default '',".
        "supertag varchar(250) binary NOT NULL default '',".
        "time datetime NOT NULL default '0000-00-00 00:00:00',".
        "body text NOT NULL,".
        "body_r text NOT NULL,".
        "owner varchar(50) NOT NULL default '',".
        "user varchar(50) NOT NULL default '',".
        "latest enum('Y','N') NOT NULL default 'N',".
        "handler varchar(30) NOT NULL default 'page',".
        "comment_on varchar(250) binary NOT NULL default '',".
        "super_comment_on varchar(250) NOT NULL default '', ".
        "lang varchar(20) NOT NULL default '', ".
        "description varchar(250) NOT NULL default '',".
        "keywords varchar(250) binary NOT NULL default '',".
        "PRIMARY KEY  (id), ".
        "KEY idx_tag (tag), ".
        "KEY idx_supertag (supertag), ".
        "KEY idx_time (time), ".
        "KEY idx_latest (latest), ".
        "KEY idx_comment_on (comment_on) ".
      ") TYPE=MyISAM;", $dblink), $lang["Already exists?"], 0);
  test(str_replace("%1","ACL",$lang["Creating table..."]),
    @mysql_query(
      "CREATE TABLE ".$config2["table_prefix"]."acls (".
        "page_tag varchar(250) binary NOT NULL default '',".
        "supertag varchar(250) NOT NULL default '',".
        "privilege varchar(20) NOT NULL default '',".
        "list text NOT NULL,".
        "PRIMARY KEY  (page_tag,privilege),".
        "KEY supertag (supertag)".
        ") TYPE=MyISAM", $dblink), $lang["Already exists?"], 0);
  test(str_replace("%1","link tracking",$lang["Creating table..."]),
    @mysql_query(
      "CREATE TABLE ".$config2["table_prefix"]."links (".
        "from_tag varchar(250) binary NOT NULL default '',".
        "to_tag varchar(250) binary NOT NULL default '',".
        "to_supertag VARCHAR(250) NOT NULL, ".
        "KEY from_tag (from_tag,to_tag),".
        "KEY idx_from (from_tag),".
        "KEY idx_to (to_tag)".
      ") TYPE=MyISAM", $dblink), $lang["Already exists?"], 0);
  test(str_replace("%1","referrer",$lang["Creating table..."]),
    @mysql_query(
      "CREATE TABLE ".$config2["table_prefix"]."referrers (".
        "page_tag char(250) binary NOT NULL default '',".
        "referrer char(150) NOT NULL default '',".
        "time datetime NOT NULL default '0000-00-00 00:00:00',".
        "KEY idx_page_tag (page_tag),".
        "KEY idx_time (time)".
      ") TYPE=MyISAM", $dblink), $lang["Already exists?"], 0);
  test(str_replace("%1","user",$lang["Creating table..."]),
    @mysql_query(
      "CREATE TABLE ".$config2["table_prefix"]."users (".
        "name varchar(80) NOT NULL default '',".
        "password varchar(32) NOT NULL default '',".
        "email varchar(50) NOT NULL default '',".
        "motto text NOT NULL,".
        "revisioncount int(10) unsigned NOT NULL default '20',".
        "changescount int(10) unsigned NOT NULL default '50',".
        "doubleclickedit enum('Y','N') NOT NULL default 'Y',".
        "signuptime datetime NOT NULL default '0000-00-00 00:00:00',".
        "show_comments enum('Y','N') NOT NULL default 'N',".
        "bookmarks text NOT NULL,".
        "lang varchar(20) NOT NULL default '',".
        "show_spaces enum('Y','N') NOT NULL default 'Y',".
        "showdatetime enum('Y','N') NOT NULL default 'Y',".
        "typografica enum('Y','N') NOT NULL default 'Y',".
        "more text NOT NULL,".
        "changepassword VARCHAR(100) NOT NULL,".
        "email_confirm varchar(40) NOT NULL default '',".
        "PRIMARY KEY  (name),".
        "KEY idx_name (name),".
        "KEY idx_signuptime (signuptime)".
      ") TYPE=MyISAM", $dblink), $lang["Already exists?"], 0);
  test(str_replace("%1","watches",$lang["Creating table..."]),
    @mysql_query("CREATE TABLE ".$config2["table_prefix"]."pagewatches (".
        "id int(10) NOT NULL auto_increment, ".
        "user varchar(80) NOT NULL default '', ".
        "tag varchar(250) binary NOT NULL default '', ".
        "time timestamp(14) NOT NULL, ".
        "PRIMARY KEY  (id)) TYPE=MyISAM", $dblink), $lang["Already exists?"], 0);
  test(str_replace("%1","upload",$lang["Creating table..."]),
    @mysql_query("CREATE TABLE ".$config2["table_prefix"]."upload (".
        "id int(11) NOT NULL auto_increment, ".
        "page_id int(11) NOT NULL default '0', ".
        "filename varchar(250) NOT NULL default '', ".
        "description varchar(250) NOT NULL default '', ".
        "uploaded_dt datetime NOT NULL default '0000-00-00 00:00:00', ".
        "filesize int(11) NOT NULL default '0', ".
        "picture_w int(11) NOT NULL default '0', ".
        "picture_h int(11) NOT NULL default '0', ".
        "file_ext varchar(10) NOT NULL default '', ".
        "user varchar(80) NOT NULL default '0', ".
        "PRIMARY KEY  (id), ".
        "KEY page_id (page_id,filename), ".
        "KEY page_id_2 (page_id,uploaded_dt), ".
        "KEY user_id (user,page_id)) TYPE=MyISAM", $dblink), $lang["Already exists?"], 0);
  test(str_replace("%1","cache",$lang["Creating table..."]),
    @mysql_query("CREATE TABLE ".$config2["table_prefix"]."cache (".
        "name VARCHAR( 32 ) NOT NULL, ".
        "method VARCHAR( 20 ) NOT NULL, ".
        "query VARCHAR( 100 ) NOT NULL, ".
        "INDEX (name)) TYPE=MyISAM", $dblink), $lang["Already exists?"], 0);
        
  test($lang["adding admin"], 
    @mysql_query("insert into ".$config2["table_prefix"]."users set name = '".$config["admin_name"]."', password = md5('".mysql_escape_string($_POST["password"])."'), email = '".$config["admin_email"]."', signuptime = now()", $dblink), $lang["Hmm!"], 0);

  test($lang["Adding some pages..."], 1);
  require("setup/inserts.php");
  break;

// The funny upgrading stuff. Make sure these are in order! //
// And yes, there are no break;s here. This is on purpose.  //

// from 0.1 to 0.1.1
case "0.1":
  print("<strong>0.1 ".$lang["to"]." 0.1.1</strong><br />\n");
  test($lang["pages alter"], 
    @mysql_query("alter table ".$config2["table_prefix"]."pages add body_r text not null default '' after body", $dblink), $lang["Hmm!"], 0);

// from 0.1.1 to 0.1.2
case "0.1.1":
  print("<strong>0.1.1 ".$lang["to"]."o 0.1.2</strong><br />\n");
  test($lang["0.1.1"], 1);

// from Wakka 0.1.2 or Wacko R1
case "0.1.2":
case "0.1.3-dev":
  print("<strong>0.1.2 ".$lang["to"]." Wacko R2</strong><br />\n");
  test($lang["useralter"], 
    @mysql_query("alter table ".$config2["table_prefix"]."users add bookmarks text not null default '', add lang varchar(20) NOT NULL default '', add show_spaces enum('Y','N') NOT NULL default 'Y'", $dblink), $lang["Hmm!"], 0);
  test(str_replace("%1","watches",$lang["Creating table..."]),
    @mysql_query("CREATE TABLE ".$config2["table_prefix"]."pagewatches (id int(10) NOT NULL auto_increment, user varchar(80) NOT NULL default '', tag varchar(50) NOT NULL default '', time timestamp(14) NOT NULL, PRIMARY KEY  (id)) TYPE=MyISAM", $dblink), $lang["Already exists?"], 0);
  test($lang["Claiming all your base..."], 1);

// from Wacko R2
case "R2":
  print("<strong>Wacko R2 ".$lang["to"]." R3</strong><br />\n");
  test($lang["useralter"], 
    @mysql_query("alter table ".$config2["table_prefix"]."users add showdatetime enum('Y','N') NOT NULL default 'Y', add typografica enum('Y','N') NOT NULL default 'Y'", $dblink), $lang["Hmm!"], 0);
  test(str_replace("%1","pages",$lang["And table..."]),
    @mysql_query("alter table ".$config2["table_prefix"]."pages add supertag VARCHAR(250) NOT NULL default '' after tag, CHANGE tag tag VARCHAR(250) NOT NULL, ADD INDEX supertag (supertag)", $dblink), $lang["Hmm!"], 0);
  test(str_replace("%1","acls",$lang["And table..."]),
    @mysql_query("alter table ".$config2["table_prefix"]."acls add supertag VARCHAR(250) NOT NULL default '', CHANGE page_tag page_tag VARCHAR(250) NOT NULL, ADD INDEX(supertag)", $dblink), $lang["Hmm!"], 0);
  test(str_replace("%1","links",$lang["And table..."]),
    @mysql_query("alter table ".$config2["table_prefix"]."links CHANGE from_tag from_tag VARCHAR(250) NOT NULL, CHANGE to_tag to_tag VARCHAR(250) NOT NULL", $dblink), $lang["Hmm!"], 0);
  test(str_replace("%1","referrers",$lang["And table..."]),
    @mysql_query("alter table ".$config2["table_prefix"]."referrers CHANGE page_tag page_tag VARCHAR(250) NOT NULL", $dblink), $lang["Hmm!"], 0);
  test(str_replace("%1","watches",$lang["And table..."]),
    @mysql_query("alter table ".$config2["table_prefix"]."pagewatches CHANGE tag tag VARCHAR(250) NOT NULL", $dblink), $lang["Hmm!"], 0);
  test(str_replace("%1","revision",$lang["Creating table..."]),
    @mysql_query(
      "CREATE TABLE ".$config2["table_prefix"]."revisions (".
        "id int(10) unsigned NOT NULL auto_increment,".
        "tag varchar(250) NOT NULL default '',".
        "supertag varchar(250) NOT NULL default '',".
        "time datetime NOT NULL default '0000-00-00 00:00:00',".
        "body text NOT NULL,".
        "body_r text NOT NULL,".
        "owner varchar(50) NOT NULL default '',".
        "user varchar(50) NOT NULL default '',".
        "latest enum('Y','N') NOT NULL default 'N',".
        "handler varchar(30) NOT NULL default 'page',".
        "comment_on varchar(50) NOT NULL default '',".
        "PRIMARY KEY  (id),".
        "KEY idx_tag (tag),".
        "KEY idx_supertag (supertag),".
        "KEY idx_time (time),".
        "KEY idx_latest (latest),".
        "KEY idx_comment_on (comment_on),".
        "KEY supertag (supertag)".
      ") TYPE=MyISAM;", $dblink), $lang["Already exists?"], 0);
  test($lang["Moving data to revisions table..."],
    @mysql_query("INSERT INTO ".$config2["table_prefix"]."revisions ( id, tag, supertag, time, body, body_r, owner, user, latest, handler, comment_on ) SELECT id, tag, supertag, time, body, body_r, owner, user, latest, handler, comment_on FROM ".$config2["table_prefix"]."pages WHERE latest='N';", $dblink), $lang["VeryBad"], 0);
  test($lang["pages alter"], 
    @mysql_query("alter table ".$config2["table_prefix"]."pages DROP INDEX idx_tag, ADD UNIQUE idx_tag (tag)", $dblink), $lang["Doubles"], 0);

  test($lang["Adding some pages..."], 1);
  require("setup/inserts.php");

// from Wacko R3
case "R3":
  print("<strong>Wacko R3/3.5 ".$lang["to"]." R4</strong><br />\n");
  test($lang["useralter"], 
    @mysql_query("ALTER TABLE ".$config2["table_prefix"]."users ADD more TEXT NOT NULL", $dblink), $lang["Hmm!"], 0);
  test($lang["useralter"], 
    @mysql_query("ALTER TABLE ".$config2["table_prefix"]."users ADD changepassword VARCHAR(100) NOT NULL", $dblink), $lang["Hmm!"], 0);
  test($lang["useralter"], 
    @mysql_query("ALTER TABLE ".$config2["table_prefix"]."users ADD email_confirm VARCHAR(100) NOT NULL", $dblink), $lang["Hmm!"], 0);

  test(str_replace("%1","revisions",$lang["And table..."]),
    @mysql_query("ALTER TABLE ".$config2["table_prefix"]."revisions CHANGE tag tag VARCHAR(250) BINARY NOT NULL", $dblink), $lang["Hmm!"], 0);
  test(str_replace("%1","revisions",$lang["And table..."]),
    @mysql_query("ALTER TABLE ".$config2["table_prefix"]."revisions CHANGE comment_on comment_on VARCHAR(250) BINARY NOT NULL", $dblink), $lang["Hmm!"], 0);
  test(str_replace("%1","revisions",$lang["And table..."]),
    @mysql_query("ALTER TABLE ".$config2["table_prefix"]."revisions ADD super_comment_on VARCHAR(250) NOT NULL AFTER comment_on", $dblink), $lang["Hmm!"], 0);
  test(str_replace("%1","revisions",$lang["And table..."]),
    @mysql_query("ALTER TABLE ".$config2["table_prefix"]."revisions ADD lang VARCHAR(10) NOT NULL", $dblink), $lang["Hmm!"], 0);
  test(str_replace("%1","revisions",$lang["And table..."]),
    @mysql_query("ALTER TABLE ".$config2["table_prefix"]."revisions ADD description varchar(250) NOT NULL default ''", $dblink), $lang["Hmm!"], 0);
  test(str_replace("%1","revisions",$lang["And table..."]),
    @mysql_query("ALTER TABLE ".$config2["table_prefix"]."revisions ADD keywords varchar(250) binary NOT NULL default ''", $dblink), $lang["Hmm!"], 0);

  test(str_replace("%1","links",$lang["And table..."]),
    @mysql_query("ALTER TABLE ".$config2["table_prefix"]."links CHANGE from_tag from_tag CHAR(250) BINARY NOT NULL", $dblink), $lang["Hmm!"], 0);
  test(str_replace("%1","links",$lang["And table..."]),
    @mysql_query("ALTER TABLE ".$config2["table_prefix"]."links CHANGE to_tag to_tag CHAR(250) BINARY NOT NULL", $dblink), $lang["Hmm!"], 0);
  test(str_replace("%1","links",$lang["And table..."]),
    @mysql_query("ALTER TABLE ".$config2["table_prefix"]."links ADD to_supertag VARCHAR(250) NOT NULL", $dblink), $lang["Hmm!"], 0);

  test(str_replace("%1","acls",$lang["And table..."]),
    @mysql_query("ALTER TABLE ".$config2["table_prefix"]."acls CHANGE page_tag page_tag VARCHAR(250) BINARY NOT NULL", $dblink), $lang["Hmm!"], 0);

  test(str_replace("%1","pagewatches",$lang["And table..."]),
    @mysql_query("ALTER TABLE ".$config2["table_prefix"]."pagewatches CHANGE tag tag VARCHAR(250) BINARY NOT NULL", $dblink), $lang["Hmm!"], 0);

  test(str_replace("%1","referrers",$lang["And table..."]),
    @mysql_query("ALTER TABLE ".$config2["table_prefix"]."referrers CHANGE page_tag page_tag CHAR(250) BINARY NOT NULL", $dblink), $lang["Hmm!"], 0);

  @mysql_query("ALTER TABLE ".$config2["table_prefix"]."pages DROP INDEX fts", $dblink);
  @mysql_query("ALTER TABLE ".$config2["table_prefix"]."pages DROP INDEX body", $dblink);
  @mysql_query("ALTER TABLE ".$config2["table_prefix"]."pages DROP INDEX tag", $dblink);

  test(str_replace("%1","pages",$lang["And table..."]),
    @mysql_query("ALTER TABLE ".$config2["table_prefix"]."pages ADD FULLTEXT (body)", $dblink), $lang["Hmm!"], 0);
  test(str_replace("%1","pages",$lang["And table..."]),
    @mysql_query("ALTER TABLE ".$config2["table_prefix"]."pages CHANGE tag tag VARCHAR(250) BINARY NOT NULL", $dblink), $lang["Hmm!"], 0);
  test(str_replace("%1","pages",$lang["And table..."]),
    @mysql_query("ALTER TABLE ".$config2["table_prefix"]."pages CHANGE comment_on comment_on VARCHAR(250) BINARY NOT NULL", $dblink), $lang["Hmm!"], 0);
  test(str_replace("%1","pages",$lang["And table..."]),
    @mysql_query("ALTER TABLE ".$config2["table_prefix"]."pages ADD hits INT DEFAULT '0' NOT NULL", $dblink), $lang["Hmm!"], 0);
  test(str_replace("%1","pages",$lang["And table..."]),
    @mysql_query("ALTER TABLE ".$config2["table_prefix"]."pages ADD super_comment_on VARCHAR(250) NOT NULL AFTER comment_on", $dblink), $lang["Hmm!"], 0);
  test(str_replace("%1","pages",$lang["And table..."]),
    @mysql_query("ALTER TABLE ".$config2["table_prefix"]."pages ADD lang VARCHAR(10) NOT NULL", $dblink), $lang["Hmm!"], 0);
  test(str_replace("%1","pages",$lang["And table..."]),
    @mysql_query("ALTER TABLE ".$config2["table_prefix"]."pages ADD description varchar(250) NOT NULL default ''", $dblink), $lang["Hmm!"], 0);
  test(str_replace("%1","pages",$lang["And table..."]),
    @mysql_query("ALTER TABLE ".$config2["table_prefix"]."pages ADD keywords varchar(250) binary NOT NULL default ''", $dblink), $lang["Hmm!"], 0);
  test(str_replace("%1","pages",$lang["And table..."]),
    @mysql_query("alter table ".$config2["table_prefix"]."pages add body_toc text not null default '' after body_r", $dblink), $lang["Hmm!"], 0);

  test(str_replace("%1","upload",$lang["Creating table..."]),
    @mysql_query("CREATE TABLE ".$config2["table_prefix"]."upload (".
        "id int(11) NOT NULL auto_increment, ".
        "page_id int(11) NOT NULL default '0', ".
        "filename varchar(250) NOT NULL default '', ".
        "description varchar(250) NOT NULL default '', ".
        "uploaded_dt datetime NOT NULL default '0000-00-00 00:00:00', ".
        "filesize int(11) NOT NULL default '0', ".
        "picture_w int(11) NOT NULL default '0', ".
        "picture_h int(11) NOT NULL default '0', ".
        "file_ext varchar(10) NOT NULL default '', ".
        "user varchar(80) NOT NULL default '0', ".
        "PRIMARY KEY  (id), ".
        "KEY page_id (page_id,filename), ".
        "KEY page_id_2 (page_id,uploaded_dt), ".
        "KEY user_id (user,page_id)) TYPE=MyISAM", $dblink), $lang["Already exists?"], 0);

  test("",
    @mysql_query("UPDATE ".$config2["table_prefix"]."pages SET body_r=''", $dblink), "", 0);

  test("",
    @mysql_query("UPDATE ".$config2["table_prefix"]."pages SET body_toc=''", $dblink), "", 0);

//from R4 beta
case "R4":
  print("<strong>Wacko R4 beta ".$lang["to"]." R4 RC1</strong><br />\n");
  test(str_replace("%1","cache",$lang["Creating table..."]),
    @mysql_query("CREATE TABLE ".$config2["table_prefix"]."cache (".
        "name VARCHAR( 32 ) NOT NULL, ".
        "method VARCHAR( 20 ) NOT NULL, ".
        "query VARCHAR( 100 ) NOT NULL, ".
        "INDEX (name)) TYPE=MyISAM", $dblink), $lang["Already exists?"], 0);

//from R4 release candidat
case "R4 RC1":
  print("<strong>Wacko R4 RC1 ".$lang["to"]." R4.0</strong><br />\n");

//from R4.0 to R4.2
case "R4.0":
  print("<strong>Wacko R4.0 ".$lang["to"]." R4.2</strong><br />\n");
  
}

?>

<p>
<?php echo $lang["NextStep"];?> <tt><?php echo $wakkaConfigLocation ?></tt>.
<?php echo $lang["MakeWrite"];?>.
<?php echo $lang["ForDetailsSee"];?>.
</p>

<form action="<?php echo myLocation(); ?>?installAction=writeconfig" method="post">
<input type="hidden" name="config_s" value="<?php echo htmlspecialchars(serialize($config)) ?>" />
<input type="hidden" name="config[language]" value="<?php echo $config["language"]; ?>" />
<input type="submit" value="<?php echo $lang["Continue"];?>" />
</form>
