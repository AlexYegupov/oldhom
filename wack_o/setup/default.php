<?php

if (!$wakkaConfig["wakka_version"])
{
?>
<script language="JavaScript" type="text/javascript">
function check() {
var f = document.forms.form1;
var re;
re = new RegExp("^[A-Z][a-z]+[A-Z0-9][A-Za-z0-9]*$");
if (f.elements["config[admin_name]"].value.search(re)==-1) {
 alert('<?php echo $lang["incorrect wikiname"];?>');
 return false;
}
re = new RegExp("[a-zA-Z0-9_\-]+@[a-zA-Z0-9_\-]+\.[a-zA-Z]+", "i");
if (f.elements["config[admin_email]"].value.search(re)==-1) {
 alert('<?php echo $lang["incorrect email"];?>');
 return false;
}
if (f.elements["password"].value.length<5) {
 alert('<?php echo $lang["password too short"];?>');
 return false;
}
if (f.elements["password"].value!=f.elements["password2"].value) {
 alert('<?php echo $lang["passwords don't match"];?>');
 return false;
}

if (f.elements["config[base_url]"].value.indexOf( "?" ) != -1)
  if (f.elements["config[rewrite_mode]"].value != 0)
    if (!confirm('<?php echo addcslashes($lang["RewriteModeAlert"],"\n"); ?>'))
    return false;

return true;
}
</script>
<?php } else {?>
<script language="JavaScript">
function check() {
 return true;
}

</script>
<?php } ?>
<form action="<?php echo myLocation() ?>?installAction=install" name="form1" method="post">
<input type="hidden" name="config[language]" value="<?php echo $config["language"];?>" />
<table>

 <tr><td></td><td><strong><?php echo $lang["title"];?></strong></td></tr>

 <?php
 if ($wakkaConfig["wakka_version"])
 {
         print("<tr><td></td><td>".$lang["installed"].($wakkaConfig["wacko_version"]?$wakkaConfig["wacko_version"]:$wakkaConfig["wakka_version"]).". ".$lang["toUpgrade"].WACKO_VERSION.". ".$lang["review"]."</td></tr>\n");
         print("<tr><td></td><td class='warning'>".$lang["PleaseBackup"]."</td></tr>\n");
 }
 else
 {
         print("<tr><td></td><td>".$lang["fresh"].WACKO_VERSION.". ".$lang["pleaseConfigure"]."</td></tr>\n");
 }
 ?>

 <tr><td></td><td><br /><?php echo $lang["note"];?></td></tr>

<?php
 if (!$wakkaConfig["wakka_version"])
 {
?>
 <tr><td></td><td><br /><strong><?php echo $lang["databaseConf"];?></strong></td></tr>
 <tr><td></td><td><?php echo $lang["mysqlHostDesc"];?></td></tr>
 <tr><td align="right" nowrap><?php echo $lang["mysqlHost"];?>:</td><td><input type="text" size="50" name="config[mysql_host]" value="<?php echo $wakkaConfig["mysql_host"] ?>" /></td></tr>
 <tr><td></td><td><?php echo $lang["dbDesc"];?></td></tr>
 <tr><td align="right" nowrap><?php echo $lang["db"];?>:</td><td><input type="text" size="50" name="config[mysql_database]" value="<?php echo $wakkaConfig["mysql_database"] ?>" /></td></tr>
 <tr><td></td><td><?php echo $lang["mysqlPasswDesc"];?></td></tr>
 <tr><td align="right" nowrap><?php echo $lang["mysqlUser"];?>:</td><td><input type="text" size="50" name="config[mysql_user]" value="<?php echo $wakkaConfig["mysql_user"] ?>" /></td></tr>
 <tr><td align="right" nowrap><?php echo $lang["mysqlPassw"];?>:</td><td><input type="password" size="50" name="config[mysql_password]" value="<?php echo $wakkaConfig["mysql_password"] ?>" /></td></tr>
 <tr><td></td><td><?php echo $lang["prefixDesc"];?></td></tr>
 <tr><td align="right" nowrap><?php echo $lang["prefix"];?>:</td><td><input type="text" size="50" name="config[table_prefix]" value="<?php echo $wakkaConfig["table_prefix"] ?>" /></td></tr>
<?php
 }
?>

 <tr><td></td><td><br /><strong><?php echo $lang["SiteConf"];?></strong></td></tr>

 <tr><td></td><td><?php echo $lang["nameDesc"];?></td></tr>
 <tr><td align="right" nowrap><?php echo $lang["name"];?>:</td><td><input type="text" size="50" name="config[wakka_name]" value="<?php echo $wakkaConfig["wakka_name"] ?>" /></td></tr>

 <tr><td></td><td><?php echo $lang["homeDesc"];?></td></tr>
 <tr><td align="right" nowrap><?php echo $lang["home"];?>:</td><td><input type="text" size="50" name="config[root_page]" value="<?php echo $wakkaConfig["root_page"] ?>" /></td></tr>

<?php
/*
 <tr><td></td><td><?php echo $lang["metaDesc"];?></td></tr>
 <tr><td align="right" nowrap><?php echo $lang["meta1"];?>:</td><td><input type="text" size="50" name="config[meta_keywords]" value="<?php echo $wakkaConfig["meta_keywords"] ?>" /></td></tr>
 <tr><td align="right" nowrap><?php echo $lang["meta2"];?>:</td><td><input type="text" size="50" name="config[meta_description]" value="<?php echo $wakkaConfig["meta_description"] ?>" /></td></tr>
*/
 if (!$wakkaConfig["wakka_version"])
 {
?>
 <tr><td></td><td><?php echo $lang["multilangDesc"];?></td></tr>
 <tr><td align="right" nowrap><?php echo $lang["multilang"];?>:</td><td><input type="hidden" name="config[multilanguage]" value="0" /><input type="checkbox" name="config[multilanguage]" value="1" <?php echo $wakkaConfig["multilanguage"] ? "checked" : "" ?> /> <?php echo $lang["enabled"];?></td></tr>

 <tr><td></td><td><br /><strong><?php echo $lang["AdminConf"];?></strong></td></tr>

 <tr><td></td><td><?php echo $lang["adminDesc"];?></td></tr>
 <tr><td align="right" nowrap><?php echo $lang["admin"];?>:</td><td><input type="text" size="50" name="config[admin_name]" value="<?php echo $wakkaConfig["admin_name"] ?>" /></td></tr>

 <tr><td></td><td><?php echo $lang["passwDesc"];?></td></tr>
 <tr><td align="right" nowrap><?php echo $lang["password"];?>:</td><td><input type="password" size="50" name="password" value="" /></td></tr>
 <tr><td align="right" nowrap><?php echo $lang["password2"];?>:</td><td><input type="password" size="50" name="password2" value="" /></td></tr>

 <tr><td></td><td><?php echo $lang["mailDesc"];?></td></tr>
 <tr><td align="right" nowrap><?php echo $lang["mail"];?>:</td><td><input type="text" size="50" name="config[admin_email]" value="<?php echo $wakkaConfig["admin_email"] ?>" /></td></tr>
<?php
 }
?>

 <tr><td></td><td><br /><strong><?php echo $lang["UrlConf"];?></strong>
 <?php echo $wakkaConfig["wakka_version"] ? "" : "<br />".$lang["newinstall"] ?></td></tr>

 <tr><td></td><td><?php echo $lang["baseDesc"];?></td></tr>
 <tr><td align="right" nowrap><?php echo $lang["base"];?>:</td><td><input type="text" size="50" name="config[base_url]" value="<?php echo $wakkaConfig["base_url"] ?>" /></td></tr>

 <tr><td></td><td><?php echo $lang["rewriteDesc"];?></td></tr>
 <tr><td align="right" nowrap><?php echo $lang["rewrite"];?>:</td><td><input type="hidden" name="config[rewrite_mode]" value="0" /><input type="checkbox" name="config[rewrite_mode]" value="1" <?php echo $wakkaConfig["rewrite_mode"] ? "checked" : "" ?> /> <?php echo $lang["enabled"];?></td></tr>


 <tr><td></td><td><input type="submit" value="<?php echo $lang["Continue"];?>" onclick="return check();" /></td></tr>
</table>
</form>