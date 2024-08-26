<div class="pageBefore"><img src="<?php echo $this->GetConfigValue("root_url"); ?>images/z.gif" width="1" height="1" border="0" alt="" style="display:block" align="top" /></div><div class="page">
<?php

if ($this->UserIsOwner())
{
  if ($_POST)
  {
   $pages = $this->LoadAll("select ".$this->pages_meta." from ".
            $this->config["table_prefix"]."pages where (supertag = '".quote($this->tag)."'".
            " OR supertag like '".quote($this->tag."/%")."')".
            " AND owner='".quote($this->GetUserName())."'".
            " and comment_on = ''");
   foreach ($pages as $num=>$page)
   {
    // store lists
    $this->SaveAcl($page["tag"], "read", $_POST["read_acl"]);
    $this->SaveAcl($page["tag"], "write", $_POST["write_acl"]);
    $this->SaveAcl($page["tag"], "comment", $_POST["comment_acl"]);
    // change owner?
    if ($newowner = $_POST["newowner"])
      $this->SetPageOwner($page["tag"], $newowner);
   }
   $message = $this->GetResourceValue("ACLUpdated");
   if ($newowner = $_POST["newowner"])
     $message .= $this->GetResourceValue("ACLGaveOwnership").$newowner;

   // redirect back to page
   $this->SetMessage($message."!");
   $this->Redirect($this->Href());
  }
  else
  {
    // load acls
    $readACL = $this->LoadAcl($this->GetPageTag(), "read");
    $writeACL = $this->LoadAcl($this->GetPageTag(), "write");
    $commentACL = $this->LoadAcl($this->GetPageTag(), "comment");

    // show form
    ?>
    <h3><?php echo str_replace("%1",$this->Link("/".$this->GetPageTag()),$this->GetResourceValue("ACLForCluster")); ?></h3>
    <br />
    
    <?php echo $this->FormOpen("massacls") ?>
    <table border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td valign="top" style="padding-right: 20px">
          <?php echo $this->GetResourceValue("ACLRead"); ?><br />
          <textarea name="read_acl" rows="4" cols="20"><?php echo $readACL["list"] ?></textarea>
        <td>
        <td valign="top" style="padding-right: 20px">
          <?php echo $this->GetResourceValue("ACLWrite"); ?><br />
          <textarea name="write_acl" rows="4" cols="20"><?php echo $writeACL["list"] ?></textarea>
        <td>
        <td valign="top" style="padding-right: 20px">
          <?php echo $this->GetResourceValue("ACLComment"); ?><br />
          <textarea name="comment_acl" rows="4" cols="20"><?php echo $commentACL["list"] ?></textarea>
        <td>
      </tr>
      <tr>
        <td colspan="3">
          <?php echo $this->GetResourceValue("SetOwner"); ?><br />
          <select name="newowner">
            <option value=""><?php echo $this->GetResourceValue("OwnerDon'tChange"); ?></option>
            <?php
            if ($users = $this->LoadUsers())
            {
              foreach($users as $user)
              {
                print("<option value=\"".htmlspecialchars($user["name"])."\">".$user["name"]."</option>\n");
              }
            }
            ?>
          </select>
        <td>
      </tr>
      <tr>
        <td colspan="3">
          <br />
          <input class="OkBtn" onmouseover='this.className="OkBtn_";' 
             onmouseout ='this.className="OkBtn";' onclick='return confirm("<?php echo $this->GetResourceValue("ACLAreYouSure"); ?>")'
                 type="submit" align="top" value="<?php echo $this->GetResourceValue("ACLStoreButton"); ?>" style="width: 120px" />
          <img src="<?php echo $this->GetConfigValue("root_url");?>images/z.gif" width="100" height="1" alt="" border="0" />
          <input class="CancelBtn" onmouseover='this.className="CancelBtn_";' 
                                     onmouseout ='this.className="CancelBtn";' 
                 type="button" align="top" value="<?php echo $this->GetResourceValue("ACLCancelButton"); ?>" onclick="history.back();" style="width: 120px" />
        </td>
      </tr>
    </table>
    <?php
    print($this->FormClose());
  }
}
else
{
  print($this->GetResourceValue("ACLAccessDenied"));
}

?>
</div>
