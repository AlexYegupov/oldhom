<div class="pageBefore"><img src="<?php echo $this->GetConfigValue("root_url"); ?>images/z.gif" width="1" height="1" border="0" alt="" style="display:block" align="top" /></div><div class="page">
<?php

if ($this->UserIsOwner() || $this->IsAdmin())
{
  if ($_POST)
  {
    // store lists
    $this->SaveAcl($this->GetPageTag(), "read", $_POST["read_acl"]);
    $this->SaveAcl($this->GetPageTag(), "write", $_POST["write_acl"]);
    $this->SaveAcl($this->GetPageTag(), "comment", $_POST["comment_acl"]);
    $message = $this->GetResourceValue("ACLUpdated");
    
    // change owner?
    if ($newowner = $_POST["newowner"])
    {
      $this->SetPageOwner($this->GetPageTag(), $newowner);
      $message .= $this->GetResourceValue("ACLGaveOwnership").$newowner;
    }

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
    <h3><?php echo str_replace("%1",$this->Link("/".$this->GetPageTag()),$this->GetResourceValue("ACLFor")); ?></h3>
    <br />
    
    <?php echo $this->FormOpen("acls") ?>
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
                                 onmouseout ='this.className="OkBtn";' 
                 type="submit" align="top" value="<?php echo $this->GetResourceValue("ACLStoreButton"); ?>" style="width: 120px" accesskey="s" />
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
