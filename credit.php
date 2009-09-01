 <?php

 
/////////////////////////////////////////////////////////////
///DEFINITIONS
////////////////////////////////////////////////////////////
$rename_cost = (int)6;
$vip_cost = (int)20;
$vip_ext = (int)20;
$item_cost = (int)20;
$move_char_cost = (int)7;
$is_online = (int)1;
$ch_gend_cost = (int)6;
$tp_cost = (int)1;

///////////////////////////////////////////////////////////////
/// Teleport
//////////////////////////////////////////////////////////////
function tplayer()
{
  require_once("heading.php");
$telename = $_POST['tchar'];
$tplace = $_POST['tplace'];

    mysql_select_db($characters_db[$realm_id]['addr'], $characters_db[$realm_id]['user'], $characters_db[$realm_id]['pass'], $characters_db[$realm_id]['name']); ;
    $old_userid = (int)0;
    $old_userid = mysql_fetch_row(mysql_query("SELECT `account` FROM characters WHERE `name` = '$telename';"));
    $old_userid = $old_userid[0];
    mysql_select_db($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass']) ;
        $total_points = mysql_fetch_row(mysql_query("SELECT `points` FROM point_system WHERE `accountid` = '$user_id';"));
        $total_points = $total_points[0];
        if($total_points <= 0)
          $total_points = (int)0;
if($total_points > ($tp_cost-1))
{
  if($old_userid == $user_id)
    {
    require_once "scripts/PHPTelnet.php";
    $telnet = new PHPTelnet();
    $result = $telnet->Connect($server[$realm_id]['addr'],$server[$realm_id]['game_acc'],$server[$realm_id]['game_pass']);
        if ($result == 0)
        {
        mysql_select_db($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass']) ;
        mysql_query("INSERT INTO point_system_requests (`username`, `request`, `date`, `code`, `treated`) VALUES ('$user_name', 'Teleport to $tplace', '$datetime', '$telename', 'Yes');");
        mysql_query("UPDATE point_system SET `points` = `points` - $tp_cost WHERE `accountid` = '$user_id';");
        $telnet->DoCommand("tele name $telename $tplace");
        $telnet->Disconnect();

        $output .= "<div class=\"top\"><h1>$telename Has been teleported to $tplace!</h1></div>";
        }
        else
        {
        mysql_select_db($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass']) ;
        mysql_query("INSERT INTO point_system_requests (`username`, `request`, `date`, `code`, `treated`) VALUES ('$user_name', 'TP Error to $tplace', '$datetime', '$telename', 'Yes');");
        $output .= "<div class=\"top\"><h1>$telename, There was a connection error, try again later!</h1></div>";
        }
    }
  else     $output .= "<div class=\"top\"><h1>$telename Is not your player, you cannot teleport him to $tplace!</h1></div>";
}
else $output .= "<div class=\"top\"><h1>$telename You do not have enough points to teleport to $tplace!</h1></div>";    
require_once("footer.php");

}
///////////////////////////////////////////////////////////////
/// Generate Invite Points
//////////////////////////////////////////////////////////////
function GenerateInvites()
{

  require_once("heading.php");
     mysql_select_db($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass']) ;
     $InviteSystem = mysql_fetch_row(mysql_query("SELECT * FROM point_system_invites WHERE `InviterAccount` = '$user_name';"));
        while ($Invites = $sql->fetch_array($InviteSystem)){
        $output .= "<tr>
        <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'---></td>
        <td>I Invited $InviteSystem[1]</td>
        </tr>";
    }

}


///////////////////////////////////////////////////////////////
/// RENAME CHAR
//////////////////////////////////////////////////////////////
function renchar($ren_char)
{
  require_once("heading.php");
     mysql_select_db($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass']) ;
     $total_points = mysql_fetch_row(mysql_query("SELECT `points` FROM point_system WHERE `accountid` = '$user_id';"));
     $total_points = $total_points[0];

       if($total_points <= 0)
         $total_points = (int)0;
    mysql_select_db($characters_db[$realm_id]['addr'], $characters_db[$realm_id]['user'], $characters_db[$realm_id]['pass'], $characters_db[$realm_id]['name']); ;
    $atlogin = mysql_fetch_row(mysql_query("SELECT `at_login` FROM characters WHERE `name` = '$ren_char';"));
        $atlogin = $atlogin[0];
if($total_points > ($rename_cost-1))
{
     mysql_select_db($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass']) ;
     $total_points = mysql_fetch_row(mysql_query("SELECT `points` FROM point_system WHERE `accountid` = '$user_id';"));
     $total_points = $total_points[0];
       if($total_points <= 0)
         $total_points = (int)0;
    mysql_select_db($characters_db[$realm_id]['addr'], $characters_db[$realm_id]['user'], $characters_db[$realm_id]['pass'], $characters_db[$realm_id]['name']); ;
    $old_userid = (int)0;
    $old_userid = mysql_fetch_row(mysql_query("SELECT `account` FROM characters WHERE `name` = '$ren_char';"));
    $old_userid = $old_userid[0];
    mysql_select_db($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass']) ;
    if($user_id == $old_userid)
    {
    if($atlogin !=1)
    {
    require_once "scripts/PHPTelnet.php";
    $telnet = new PHPTelnet();
    $result = $telnet->Connect($server[$realm_id]['addr'],$server[$realm_id]['game_acc'],$server[$realm_id]['game_pass']);
        if ($result == 0)
        {
        mysql_query("UPDATE point_system SET `points` = `points` - $rename_cost WHERE `accountid` = '$user_id';");
        $telnet->DoCommand("rename $ren_char");
        $telnet->Disconnect();
 	    mysql_select_db($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass']) ;
        mysql_query("INSERT INTO point_system_requests (`username`, `request`, `date`, `code`, `treated`) VALUES ('$user_name', 'Rename', '$datetime', '$ren_char', 'Yes');");
        $output .= "<div class=\"top\"><h1>$ren_char has been makred for rename</h1></div><center>";
        }
        else
        {
 	    mysql_select_db($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass']) ;
        mysql_query("INSERT INTO point_system_requests (`username`, `request`, `date`, `code`, `treated`) VALUES ('$user_name', 'Rename Error', '$datetime', '$ren_char', 'Yes');");
        $output .= "<div class=\"top\"><h1>$ren_char, There was a connection error, try again later!</h1></div>";
        }

}    
else $output .= "<div class=\"top\"><h1>$ren_char Is already flagged for rename!</h1></div><center>";
    
    
     }
    else $output .= "<div class=\"top\"><h1>$ren_char Is not your char, you cannot rename it!</h1></div><center>";
}
else $output .= "<div class=\"top\"><h1>$ren_char you do not have enough points to rename you characters!</h1></div><center>";
require_once("footer.php");
}


///////////////////////////////////////////////////////////////
/// VIP
//////////////////////////////////////////////////////////////

function getvip()
{
  require_once("heading.php");
     mysql_select_db($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass']) ;
     $total_points = mysql_fetch_row(mysql_query("SELECT `points` FROM point_system WHERE `accountid` = '$user_id';"));
     $total_points = $total_points[0];
       if($total_points <= 0)
         $total_points = (int)0;
     mysql_select_db($realm_db['addr'], $realm_db['user'], $realm_db['pass'], $realm_db['name']) ;
    $old_userid = (int)0;
    $old_userid = mysql_fetch_row(mysql_query("SELECT `id` FROM account WHERE `username` = '$user_name';"));
    $old_userid = $old_userid[0];
    $vip_level = mysql_fetch_row(mysql_query("SELECT `gmlevel` FROM account WHERE `username` = '$user_name';"));
        $vip_level = $vip_level[0];    
    mysql_select_db($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass']) ;
    
if($total_points > ($vip_cost-1))
    {
    if($vip_level < 3)
        {
        $vip_level = $vip_level + 1;
            if($user_name != NULL)
              mysql_query("INSERT INTO point_system_requests (`username`, `request`, `date`, `code`, `treated`) VALUES ('$user_name', 'VIP$vip_level', '$datetime', '$user_name', 'Yes');");
                    else mysql_query("INSERT INTO point_system_requests (`username`, `request`, `date`, `code`, `treated`) VALUES ('$user_name', 'VIP$vip_level', '$datetime', 'Error!!', 'No');");
        mysql_query("UPDATE point_system SET `points` = `points` - $vip_cost WHERE `accountid` = '$user_id';");
     mysql_select_db($realm_db['addr'], $realm_db['user'], $realm_db['pass'], $realm_db['name']) ;
        mysql_query("UPDATE account SET `gmlevel` = $vip_level WHERE username = '$user_name';");
	    mysql_select_db($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass']) ;
        $output .= "<div class=\"top\"><h1>$user_name VIP Granted!</h1></div><center>";
        }
    else $output .= "<div class=\"top\"><h1>$user_name You are already Max VIP!</h1></div><center>";
     }
else $output .= "<div class=\"top\"><h1>$user_name you do not have enough points upgrade to VIP!</h1></div><center>";
require_once("footer.php");
}


///////////////////////////////////////////////////////////////
/// EXT VIP
//////////////////////////////////////////////////////////////

function extvip()
{
  require_once("heading.php");
     mysql_select_db($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass']) ;
     $total_points = mysql_fetch_row(mysql_query("SELECT `points` FROM point_system WHERE `accountid` = '$user_id';"));
     $total_points = $total_points[0];
       if($total_points <= 0)
         $total_points = (int)0;
     mysql_select_db($realm_db['addr'], $realm_db['user'], $realm_db['pass'], $realm_db['name']) ;
    $old_userid = (int)0;
    $old_userid = mysql_fetch_row(mysql_query("SELECT `id` FROM account WHERE `username` = '$user_name';"));
    $old_userid = $old_userid[0];
    $vip_level = mysql_fetch_row(mysql_query("SELECT `gmlevel` FROM account WHERE `username` = '$user_name';"));
        $vip_level = $vip_level[0];    
    mysql_select_db($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass']) ;
    
if($total_points > ($vip_ext-1))
    {
    if($vip_level < 3)
        {
        $vip_level = $vip_level + 1;
            if($user_name != NULL)
              mysql_query("INSERT INTO point_system_requests (`username`, `request`, `date`, `code`, `treated`) VALUES ('$user_name', 'VIP$vip_level', '$datetime', '$user_name', 'Yes');");
                    else mysql_query("INSERT INTO point_system_requests (`username`, `request`, `date`, `code`, `treated`) VALUES ('$user_name', 'VIP$vip_level', '$datetime', 'Error!!', 'No');");
        mysql_query("UPDATE point_system SET `points` = `points` - $vip_ext WHERE `accountid` = '$user_id';");
     mysql_select_db($realm_db['addr'], $realm_db['user'], $realm_db['pass'], $realm_db['name']) ;
        mysql_query("UPDATE account SET `gmlevel` = $vip_level WHERE username = '$user_name';");
        $output .= "<div class=\"top\"><h1>$user_name VIP Granted!</h1></div><center>";
        }
    else {
  	    mysql_select_db($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass']) ;
            if($user_name != NULL)
              mysql_query("INSERT INTO point_system_requests (`username`, `request`, `date`, `code`, `treated`) VALUES ('$user_name', 'Extend VIP$vip_level', '$datetime', '$user_name', 'No');");
                    else mysql_query("INSERT INTO point_system_requests (`username`, `request`, `date`, `code`, `treated`) VALUES ('$user_name', 'Extend VIP$vip_level', '$datetime', 'Error!!', 'No');");
        mysql_query("UPDATE point_system SET `points` = `points` - $vip_ext WHERE `accountid` = '$user_id';");

        $output .= "<div class=\"top\"><h1>$user_name Extended VIP for 2 more months.</h1></div><center>";
         }

     }
else $output .= "<div class=\"top\"><h1>$user_name you do not have enough points extend your VIP!</h1></div><center>";
require_once("footer.php");
}




///////////////////////////////////////////////////////////////
/// Get Item Cost
//////////////////////////////////////////////////////////////

function getitemcost($item_cost)
{
  require_once("heading.php");
$item = $_POST["items"];
$player = $_POST["character"];
if ($item == 'Bag')
  $item_cost = (int)20;
if ($item == 'Phoenix')
  $item_cost = (int)20;
if ($item == 'Raven')
  $item_cost = (int)15;
if ($item == 'PrimalNether')
  $item_cost = (int)5;
if ($item == 'NetherVortex')
  $item_cost = (int)8;
if ($item == 'AVMark')
  $item_cost = (int)5;
if ($item == 'MercilessD')
  $item_cost = (int)25;
if ($item == 'Murloc')
  $item_cost = (int)5;
if ($item == 'Tiger60')
  $item_cost = (int)20;
if ($item == 'Tiger30')
  $item_cost = (int)15;
if ($item == 'Ogre')
  $item_cost = (int)5;
if ($item == 'BattleBear')
  $item_cost = (int)15;
if ($item == 'FlyingBroom')
  $item_cost = (int)20;
if ($item == 'XRocket')
  $item_cost = (int)25;

return $item_cost;
}
///////////////////////////////////////////////////////////////
/// Get Item ID
//////////////////////////////////////////////////////////////
function getitemid($item_id)
{
  require_once("heading.php");
$item = $_POST["items"];
$player = $_POST["character"];
if ($item == 'Bag')
  $item_id = (int)23162;
if ($item == 'Phoenix')
  $item_id = (int)32458;
if ($item == 'Raven')
  $item_id = (int)32768;
if ($item == 'PrimalNether')
  $item_id = (int)23572;  
if ($item == 'NetherVortex')
  $item_id = (int)30183;
if ($item == 'AVMark')
  $item_id = (int)20560;
if ($item == 'MercilessD')
  $item_id = (int)34092;
if ($item == 'Murloc')
  $item_id = (int)33079;
if ($item == 'Tiger60')
  $item_id = (int)33225;
if ($item == 'Tiger30')
  $item_id = (int)33224;
if ($item == 'Ogre')
  $item_id = (int)23716;
if ($item == 'BattleBear')
  $item_id = (int)38576;
if ($item == 'FlyingBroom')
  $item_id = (int)33182;
if ($item == 'XRocket')
  $item_id = (int)35226;

return $item_id;
}
///////////////////////////////////////////////////////////////
/// Get Item
//////////////////////////////////////////////////////////////

function getitem()
{
  require_once("heading.php");
$item = $_POST["items"];
$player = $_POST["character"];

  if ($item == "error")
  {
          $output .= "<div class=\"top\"><h1>Wrong item selected!</h1></div><center>";    
  }
  
  if (($player == "Character Name") || (trim($player) == ""))
  {
          $output .= "<div class=\"top\"><h1>Please type valid character name!</h1></div><center>";
  }
  else
  {
     mysql_select_db($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass']) ;
     $total_points = mysql_fetch_row(mysql_query("SELECT `points` FROM point_system WHERE `accountid` = '$user_id';"));
     $total_points = $total_points[0];
       if($total_points <= 0)
         $total_points = (int)0;
    $item_cost = getitemcost();
    $item_id = getitemid();
if($total_points > ($item_cost-1))
{
    require_once "scripts/PHPTelnet.php";
    $telnet = new PHPTelnet();
    $result = $telnet->Connect($server[$realm_id]['addr'],$server[$realm_id]['game_acc'],$server[$realm_id]['game_pass']);
        if ($result == 0)
        {
        mysql_query("UPDATE point_system SET `points` = `points` - $item_cost WHERE `accountid` = '$user_id';");
        $telnet->DoCommand("senditems $player \"Credit item\" \"Thanks for your donation! your item is attached to this mail\" $item_id");
        $telnet->Disconnect();
  	    mysql_select_db($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass']) ;
        mysql_query("INSERT INTO point_system_requests (`username`, `request`, `date`, `code`, `treated`) VALUES ('$user_name', '$player', '$datetime', '$item', 'Yes');");
        $output .= "<div class=\"top\"><h1>$item has been sent to your mailbox $player</h1></div><center>";
        }
        else
        {
  	    mysql_select_db($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass']) ;
        mysql_query("INSERT INTO point_system_requests (`username`, `request`, `date`, `code`, `treated`) VALUES ('$user_name', '$item Item Error', '$datetime', '$player', 'Yes');");
        $output .= "<div class=\"top\"><h1>$ren_char, There was a connection error, try again later!</h1></div>";
        }
         }
else $output .= "<div class=\"top\"><h1>$user_name, you do not have enough points to request a $item!</h1></div><center>";
}
  require_once("footer.php");
}


///////////////////////////////////////////////////////////////
/// Move Char
//////////////////////////////////////////////////////////////

function movechar()
{
  require_once("heading.php");
     mysql_select_db($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass']) ;
     $total_points = mysql_fetch_row(mysql_query("SELECT `points` FROM point_system WHERE `accountid` = '$user_id';"));
     $total_points = $total_points[0];
       if($total_points <= 0)
         $total_points = (int)0;
  $move_char = $_GET["char"];

if($total_points > ($move_char_cost-1))
  {
     mysql_select_db($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass']) ;
            if($user_name != NULL)
                    mysql_query("INSERT INTO point_system_requests (`username`, `request`, `date`, `code`, `treated`) VALUES ('$user_name', 'Move Char', '$datetime', '$move_char', 'No');");
               else mysql_query("INSERT INTO point_system_requests (`username`, `request`, `date`, `code`, `treated`) VALUES ('$user_name', '$move_char', '$datetime', 'Error!!', 'No');");
        mysql_query("UPDATE point_system SET `points` = `points` - $move_char_cost WHERE `accountid` = '$user_id';");
        $output .= "<div class=\"top\"><h1>$user_name, Your char has been flagged for account move, Contact a GM ingame.</h1></div><center>";
  }
else $output .= "<div class=\"top\"><h1>$user_name, you do not have enough points to move $move_char!</h1></div><center>";

  require_once("footer.php");
}

///////////////////////////////////////////////////////////////
/// Change Gender
//////////////////////////////////////////////////////////////

function gen_char()
{
  require_once("heading.php");
     mysql_select_db($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass']) ;
     $total_points = mysql_fetch_row(mysql_query("SELECT `points` FROM point_system WHERE `accountid` = '$user_id';"));
     $total_points = $total_points[0];
       if($total_points <= 0)
         $total_points = (int)0;
  $gend_char = $_GET["gend_char"];
    mysql_select_db($characters_db[$realm_id]['addr'], $characters_db[$realm_id]['user'], $characters_db[$realm_id]['pass'], $characters_db[$realm_id]['name']); ;
    $old_userid = (int)0;
    $old_userid = mysql_fetch_row(mysql_query("SELECT `account` FROM characters WHERE `name` = '$gend_char';"));
    $old_userid = $old_userid[0];
     mysql_select_db($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass']) ;
    if($user_id == $old_userid)
    {
if($total_points > ($ch_gend_cost-1))
  {
     mysql_select_db($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass']) ;
            if($user_name != NULL)
                    mysql_query("INSERT INTO point_system_requests (`username`, `request`, `date`, `code`, `treated`) VALUES ('$user_name', 'Change Gender', '$datetime', '$gend_char', 'No');");
               else mysql_query("INSERT INTO point_system_requests (`username`, `request`, `date`, `code`, `treated`) VALUES ('$user_name', '$gend_char', '$datetime', 'Error!!', 'No');");
        mysql_query("UPDATE point_system SET `points` = `points` - $ch_gend_cost WHERE `accountid` = '$user_id';");
        $output .= "<div class=\"top\"><h1>$user_name, Your char has been flagged for gender change, Contact a GM ingame.</h1></div><center>";
  }
else $output .= "<div class=\"top\"><h1>$user_name, you do not have enough points to change $move_char 's gender!</h1></div><center>";
}
else $output .= "<div class=\"top\"><h1>$user_name, $gend_char Is not your char!</h1></div><center>";

  require_once("footer.php");
}
///////////////////////////////////////////////////////////////
/// Move Points
//////////////////////////////////////////////////////////////

function movepoints()
{
  require_once("heading.php");
   $tpoints = $_POST["tpoints"];
   $tchar = $_POST["tcharacter"];
    mysql_select_db($characters_db[$realm_id]['addr'], $characters_db[$realm_id]['user'], $characters_db[$realm_id]['pass'], $characters_db[$realm_id]['name']); ;
         $tacc_id = mysql_fetch_row(mysql_query("SELECT `account` FROM characters WHERE `name` = '$tchar';"));
         $tacc_id = $tacc_id[0];
	     mysql_select_db($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass']) ;
         $thave_points = mysql_fetch_row(mysql_query("SELECT `points` FROM point_system WHERE `accountid` = '$tacc_id';"));
         $thave_points = $thave_points[0];
        if($thave_points == NULL)
            $thave_points = -1;
 mysql_select_db($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass']) ;
     $total_points = mysql_fetch_row(mysql_query("SELECT `points` FROM point_system WHERE `accountid` = '$user_id';"));
     $total_points = $total_points[0];
       if($total_points <= 0)
         $total_points = (int)0;

if($tpoints > 0)
  {
   if (($tchar == "Character Name") || (trim($tchar) == ""))
    $output .= "<div class=\"top\"><h1>$user_name, you did not write the currect players name!</h1></div><center>";
   else
    {
      if( ($total_points - $tpoints) >= 0)
      {
     mysql_select_db($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass']) ;
         mysql_query("UPDATE point_system SET `points` = ($total_points - $tpoints) WHERE `accountid` = '$user_id';");
          if($thave_points >= 0)
            {    
   			  mysql_select_db($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass']) ;
              mysql_query("INSERT INTO point_system_requests (`username`, `request`, `date`, `code`, `treated`) VALUES ('$user_name', 'Move $tpoints Points', '$datetime', '$tchar @ $tacc_id', 'Yes');");
              $told_points = mysql_fetch_row(mysql_query("SELECT `points` FROM point_system WHERE `accountid` = '$tacc_id';"));
              $told_points = $told_points[0];
              mysql_query("UPDATE point_system SET `points` = $told_points + $tpoints WHERE `accountid` = '$tacc_id';");
          $output .= "<div class=\"top\"><h1>$user_name, Success moving $tpoints points to $tchar!</h1></div><center>";
            }
          else
           {
   			 mysql_select_db($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass']) ;
             mysql_query("INSERT INTO point_system (`accountid`, `points`) VALUES ('$tacc_id', '$tpoints');");
             mysql_query("INSERT INTO point_system_requests (`username`, `request`, `date`, `code`, `treated`) VALUES ('$user_name', 'Create $tpoints Points', '$datetime', '$tchar @ $tacc_id', 'Yes');");
         $output .= "<div class=\"top\"><h1>$user_name, Success moving $tpoints points to $tchar!</h1></div><center>";
           }
      }
      else  $output .= "<div class=\"top\"><h1>$user_name, You have $total_points Credit, you tried to send $tpoints Credits</h1></div><center>";
} }
  else $output .= "<div class=\"top\"><h1>$user_name, you did not write how much points to give $tchar!</h1></div><center>";


  require_once("footer.php");
}
//////////////////////////////////////////////////////////////


if ($_GET["action"] == "getvip")
{
getvip();
}
else
{
if ($_GET["action"] == "movepoints")
{
movepoints();
}
else
{
if ($_GET["action"] == "gen_char")
{
gen_char();
}
else
{
if ($_GET["action"] == "movechar")
{
movechar();
}
else
{
if ($_GET["action"] == "getitem")
{
getitem();
}
else
{
if ($_GET["action"] == "tplayer")
{
tplayer();
}
else
{
if ($_GET["action"] == "extvip")
{
extvip();
}
else
{
if ($_GET["action"] == "rename")
{
renchar($ren_char);
}

else
{
require_once("header.php");

valid_login($action_permission['read']);


//################################################################################
##############################
// EDIT USER
//################################################################################
##############################
function edit_user() {
global $lang_edit, $lang_global, $output, $realm_db, $characters_db, $realm_id, $mmfpm_db, $user_name, $user_id,
        $lang_id_tab, $gm_level_arr, $ren_char, $total_points;

mysql_connect($realm_db['addr'], $realm_db['user'], $realm_db['pass']) ;
mysql_select_db($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass']) ;
$referred_by = mysql_fetch_row(mysql_query("SELECT `InvitedBy` FROM point_system_invites WHERE `PlayersAccount` = '$user_name';"));
$referred_by = $referred_by[0];
$total_points = mysql_fetch_row(mysql_query("SELECT `points` FROM point_system WHERE `accountid` = '$user_id';"));
$total_points = $total_points[0];
if($total_points <= 0)
  $total_points = (int)0;
  $datetime = date("Y-m-d H:i:s");
//################################################################################
##############################
// INVITE SYSTEM
//################################################################################
##############################
$invite_points = 2;
$write_invited = 1;
mysql_select_db($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass']) ;
$rewarded = mysql_fetch_row(mysql_query("SELECT `Rewarded` FROM point_system_invites WHERE `PlayersAccount` = '$user_name';"));
$rewarded = $rewarded[0];
if ($rewarded != NULL)
{
if ($rewarded == 0)
{
if($referred_by != NULL)
{
 		    mysql_select_db($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass']) ;
            $total_points = mysql_fetch_row(mysql_query("SELECT `points` FROM point_system WHERE `accountid` = '$user_id';"));
            $total_points = $total_points[0];
          if($total_points == NULL)
        $total_points = -1;
if($total_points >= 0)
  {
    mysql_select_db($characters_db[$realm_id]['addr'], $characters_db[$realm_id]['user'], $characters_db[$realm_id]['pass'], $characters_db[$realm_id]['name']); ;
        $RightLevel =  mysql_fetch_row(mysql_query("SELECT CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(`data`, ' ', 35), ' ', -1) AS UNSIGNED) AS `lvl` FROM `characters` WHERE account='$user_id' AND (SELECT CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(`data`, ' ', 35), ' ', -1) AS UNSIGNED)) >= '45' ORDER BY `lvl` DESC LIMIT 1;"));
       if($RightLevel[0] != NULL)
    {
     mysql_select_db($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass']) ;
         mysql_query("UPDATE point_system SET `points` = ($total_points + $write_invited) WHERE `accountid` = '$user_id';");
         mysql_query("INSERT INTO point_system_requests (`username`, `request`, `date`, `code`, `treated`) VALUES ('$user_name', 'Got $write_invited Points', '$datetime', 'For Writing a Reffer', 'Yes');");
     mysql_query("UPDATE point_system_invites SET `Rewarded` = '1' WHERE `PlayersAccount` = '$user_name';");
     $output .= "You Received $write_invited Points for Writing who invited you!<br>";
}
  }      
if($total_points == -1)
{
    mysql_select_db($characters_db[$realm_id]['addr'], $characters_db[$realm_id]['user'], $characters_db[$realm_id]['pass'], $characters_db[$realm_id]['name']); ;
        $RightLevel =  mysql_fetch_row(mysql_query("SELECT CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(`data`, ' ', 35), ' ', -1) AS UNSIGNED) AS `lvl` FROM `characters` WHERE account='$user_id' AND (SELECT CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(`data`, ' ', 35), ' ', -1) AS UNSIGNED)) >= '45' ORDER BY `lvl` DESC LIMIT 1;"));
       if($RightLevel[0] != NULL)
           {
	     mysql_select_db($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass']) ;
             mysql_query("INSERT INTO point_system (`accountid`, `points`) VALUES ('$user_id', '$write_invited');");
             mysql_query("INSERT INTO point_system_requests (`username`, `request`, `date`, `code`, `treated`) VALUES ('$user_name', 'Created $write_invited Points', '$datetime', 'For Writing a Reffer', 'Yes');");
         mysql_query("UPDATE point_system_invites SET `Rewarded` = '1' WHERE `PlayersAccount` = '$user_name';");
         $output .= "You Received $write_invited Points for Writing who invited you! (NEW)<br>";
           }
}
}
}
}
mysql_select_db($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass']) ;
$HasPoints = mysql_fetch_row(mysql_query("SELECT `PlayersAccount`,`Treated` FROM point_system_invites WHERE `InviterAccount` = '$user_name' AND `Treated` = 0 LIMIT 1;"));
if($HasPoints != NULL)
{
  $HasPoint = $HasPoints[1];
  $PlayersAccount = $HasPoints[0];
     mysql_select_db($realm_db['addr'], $realm_db['user'], $realm_db['pass'], $realm_db['name']) ;
$iIP =  mysql_fetch_row(mysql_query("SELECT `last_ip` FROM account WHERE `username` = '$PlayersAccount';"));
$pIP =  mysql_fetch_row(mysql_query("SELECT `last_ip` FROM account WHERE `username` = '$user_name';"));
if($HasPoint != 1)
{
   if($iIP[0] != $pIP[0])
    {
     mysql_select_db($realm_db['addr'], $realm_db['user'], $realm_db['pass'], $realm_db['name']) ;
    $PlayersAccountID = mysql_fetch_row(mysql_query("SELECT `id` FROM account WHERE `username` = '$PlayersAccount';"));
    $PlayersAccountID = $PlayersAccountID[0];
    mysql_select_db($characters_db[$realm_id]['addr'], $characters_db[$realm_id]['user'], $characters_db[$realm_id]['pass'], $characters_db[$realm_id]['name']); ;
        $RightLevel =  mysql_fetch_row(mysql_query("SELECT CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(`data`, ' ', 35), ' ', -1) AS UNSIGNED) AS `lvl` FROM `characters` WHERE account='$PlayersAccountID' AND (SELECT CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(`data`, ' ', 35), ' ', -1) AS UNSIGNED)) >= '45' ORDER BY `lvl` DESC LIMIT 1;"));
       if($user_id < $PlayersAccountID)
        {
       if($RightLevel[0] != NULL)
        {
        $output .= "You received points for account $PlayersAccount who has a player level $RightLevel[0]<br>";
 		    mysql_select_db($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass']) ;
            $total_points = mysql_fetch_row(mysql_query("SELECT `points` FROM point_system WHERE `accountid` = '$user_id';"));
            $total_points = $total_points[0];
          if($total_points == NULL)
        $total_points = -1;
if($total_points >= 0)
  {
     mysql_select_db($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass']) ;
         mysql_query("UPDATE point_system SET `points` = ($total_points + $invite_points) WHERE `accountid` = '$user_id';");
         mysql_query("INSERT INTO point_system_requests (`username`, `request`, `date`, `code`, `treated`) VALUES ('$user_name', 'Added $invite_points Points', '$datetime', 'Invited $PlayersAccount', 'Yes');");
     mysql_query("UPDATE point_system_invites SET `Treated` = '1' WHERE `PlayersAccount` = '$PlayersAccount';");
     $output .= "You Received $invite_points Points for Inviting a Friend, Good JOB!";
  }      
if($total_points == -1)
           {
  		 mysql_select_db($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass']) ;
             mysql_query("INSERT INTO point_system (`accountid`, `points`) VALUES ('$user_id', '$invite_points');");
             mysql_query("INSERT INTO point_system_requests (`username`, `request`, `date`, `code`, `treated`) VALUES ('$user_name', 'Created $invite_points Points', '$datetime', 'Invited $PlayersAccount', 'Yes');");
         mysql_query("UPDATE point_system_invites SET `Treated` = '1' WHERE `PlayersAccount` = '$PlayersAccount';");
         $output .= "You Received $invite_points Points for Inviting a Friend, Good JOB! (NEW)";
           }

      

        }
            else
         {
    mysql_select_db($characters_db[$realm_id]['addr'], $characters_db[$realm_id]['user'], $characters_db[$realm_id]['pass'], $characters_db[$realm_id]['name']); ;
              $RightLevel =  mysql_fetch_row(mysql_query("SELECT CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(`data`, ' ', 35), ' ', -1) AS UNSIGNED) AS `lvl` FROM `characters` WHERE account='$PlayersAccountID' AND (SELECT CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(`data`, ' ', 35), ' ', -1) AS UNSIGNED)) >= '45' ORDER BY `lvl` DESC LIMIT 1;"));
 		  mysql_select_db($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass']) ;
          mysql_query("UPDATE point_system_invites SET `Treated` = '1' WHERE `PlayersAccount` = '$PlayersAccount';");
          $output .= "Players you invited did not reach correct level for points";
          if($RightLevel != NULL)
            mysql_query("UPDATE point_system_invites SET `Treated` = '0' WHERE `PlayersAccount` = '$PlayersAccount';");
         }
        
        } else {
         $output .= "Inviter is older than you";
 	     mysql_select_db($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass']) ;
         mysql_query("UPDATE point_system_invites SET `Treated` = '1' WHERE `PlayersAccount` = '$PlayersAccount';");
        }

    } else $output .= "Same comp Same IP";


} else $output .= "All invite points has been treated";



} else $output .= "No New Points to add";
//################################################################################
##############################
// PRINT
//################################################################################
##############################

$sql = new SQL;
$sql->connect($realm_db['addr'], $realm_db['user'], $realm_db['pass'], $realm_db['name']);

$result = $sql->query("SELECT email,gmlevel,joindate,expansion FROM account WHERE username ='$user_name'");

if ($acc = $sql->fetch_row($result)) {
  require_once("scripts/id_tab.php");

  $output .= "<center>
  <script type=\"text/javascript\" src=\"js/sha1.js\"></script>
  <script type=\"text/javascript\">
        function do_submit_data () {
            document.form.pass.value = hex_sha1('".strtoupper($user_name).":'+document.form.user_pass.value.toUpperCase());
            document.form.user_pass.value = '0';
            do_submit();
        }



</script>
  <fieldset style=\"width: 600px;\">
    <legend>Credit Panel</legend>
    <form method=\"post\" action=\"credit.php?action=getitem\" name=\"form\">
    <input type=\"hidden\" name=\"pass\" value=\"\" maxlength=\"256\" />
    <table class=\"flat\">
    <tr>
    <td>Your Credits:</td>
    <td>$total_points</td>
      <tr>
        <td>VIP Level</td>
        <td>".get_gm_level($acc[1])." ( $acc[1] )</td>";
    if($acc[1] == 0)
    $output .= "<td><a href=\"credit.php?action=getvip\">Upgrade(20)</td>";
    if($acc[1] != 0)
        if($acc[1] >= 3)
          $output .= "<td><a href=\"credit.php?action=extvip\">Extend VIP(20)</td>";
      else $output .= "<td><a href=\"credit.php?action=getvip\">Upgrade</td><td><a href=\"credit.php?action=extvip\">Extend VIP(20)</td>";
                    

       $output .= "</tr>
    <td>Request Item:</td></tr><tr>
    <td>
    <select name=\"items\">  
    <option value=\"error\">Please select an item</option>
    <option value=\"Phoenix\">Phoenix(20)</option>
    <option value=\"Bag\">36 Slot Bag(20)</option>
    <option value=\"Raven\">Raven Lord(15)</option>
    <option value=\"PrimalNether\">Primal Nether(5)</option>
    <option value=\"NetherVortex\">Nether Vortex(8)</option>
    <option value=\"MercilessD\">Merciless Nether Drake(25)</option>
    <option value=\"Murloc\">Murloc Costume(5)</option>
    <option value=\"Tiger60\">Swift Spectral Tiger For lvl 60(20)</option>
    <option value=\"Tiger30\">Swift Spectral Tiger For lvl 30(15)</option>
    <option value=\"Ogre\">Carved Ogre Idol(5)</option>
    <option value=\"FlyingBroom\">Swift Flying Broom(20)</option>
    <option value=\"BattleBear\">Big Battle Bear(15)</option>
    <option value=\"XRocket\">X-51 Nether-Rocket X-TREME(25)</option>
    </select>
    </td>
    <td><input name=\"character\" type=\"text\" value=\"Character Name\"></input></td>
    <td>
      <input type=\"submit\" value=\"Send item\">
    </td></tr>
        <tr><td>Your chars</td>
      </tr>";
    $result = $sql->query("SELECT SUM(numchars) FROM realmcharacters WHERE acctid = '$user_id'");
    

    $sql->connect($characters_db[$realm_id]['addr'], $characters_db[$realm_id]['user'], $characters_db[$realm_id]['pass'], $characters_db[$realm_id]['name']);
    $result = $sql->query("SELECT guid,name,race,class,SUBSTRING_INDEX(SUBSTRING_INDEX(`data`, ' ', 35), ' ', -1) FROM `characters` WHERE account = $user_id");




    while ($char = $sql->fetch_array($result)){
        $ren_char = $char[1];
        $output .= "<tr>
        <td>$char[1]  - ".get_player_race($char[2])." ".get_player_class($char[3])." | lvl $char[4]</td>
        </tr>
<tr><td><a href=\"credit.php?action=rename&ren_char=$ren_char\">Rename(6)</a></td><td><a href=\"credit.php?action=gen_char&gend_char=$ren_char\">Change Gender(6)</a></td><td><a href=\"credit.php?action=movechar&char=$ren_char\">Move Account(7)</a></td>";
    }
$output .= "</form>    <tr>
     <form method=\"post\" action=\"credit.php?action=movepoints\" name=\"form\">
    <input type=\"hidden\" name=\"pass\" value=\"\" maxlength=\"256\" />
    <td>Transfer points to other players:</td></tr><tr>
    <td><input name=\"tcharacter\" type=\"text\" value=\"Character Name\"></input></td><td><input name=\"tpoints\" type=\"text\" value=\"Points\"></input></td>
    <td>
      <input type=\"submit\" value=\"Transfer\">
    </td></tr></form>
    <form method=\"post\" action=\"credit.php?action=tplayer\" name=\"form\">
    <tr><td>Teleport Player</td></tr><tr>
    <td><input name=\"tchar\" type=\"text\" value=\"Character Name\"></input></td>
    <td>
    <select name=\"tplace\">  
    <option value=\"error\">Please select a place</option>
    <option value=\"Shattrath\">Shattrath(1)</option>
    <option value=\"Stormwind\">Stormwind(1)</option>
    <option value=\"Orgrimmar\">Orgrimmar(1)</option>
    </select>
    </td>
    <td><input type=\"submit\" value=\"Teleport\"></td></tr>";
        $output .= "</table>
    </fieldset>
    <br />

    <br /></center>";
} else error($lang_global['err_no_records_found']);

$sql->close();
}






//################################################################################
###############################
// MAIN
//################################################################################
###############################
$err = (isset($_GET['error'])) ? $_GET['error'] : NULL;

$output .= "<div class=\"top\">";
switch ($err) {
case 1:
   $output .= "<h1><font class=\"error\">{$lang_global['empty_fields']}</font></h1>";
   break;
case 2:
   $output .= "<h1><font class=\"error\">{$lang_edit['use_valid_email']}</font></h1>";
   break;
case 3:
   $output .= "<h1><font class=\"error\">{$lang_edit['data_updated']}</font></h1>";
   break;
case 4:
   $output .= "<h1><font class=\"error\">{$lang_edit['error_updating']}</font></h1>";
   break;
default: //no error
   $output .= "<h1>Credits Panel</h1>";
}
$output .= "</div>";

$action = (isset($_GET['action'])) ? $_GET['action'] : NULL;

switch ($action) {
case "doedit_user":
    doedit_user();
    break;
case "lang_set":
    lang_set();
    break;
case "template_set":
    template_set();
    break;
default:
    edit_user();
}

require_once("footer.php");
}
}
}
}
}
}
}
}
?>