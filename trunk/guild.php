<?php
/*
 * Project Name: MiniManager for Mangos Server
 * Date: 17.10.2006 inital version (0.0.1a)
 * Author: Q.SA
 * Copyright: Q.SA
 * Email: *****
 * License: GNU General Public License v2(GPL)
 */

require_once("header.php");
valid_login(0);
require_once("scripts/id_tab.php");
//########################################################################################################################
// BROWSE GUILDS
//########################################################################################################################
function browse_guilds() {
 global $lang_guild, $lang_global, $output, $characters_db, $realm_id, $itemperpage, $realm_db;

 $sql = new SQL;
 $sql->connect($characters_db[$realm_id]['addr'], $characters_db[$realm_id]['user'], $characters_db[$realm_id]['pass'], $characters_db[$realm_id]['name']);

 $start = (isset($_GET['start'])) ? $sql->quote_smart($_GET['start']) : 0;
 $order_by = (isset($_GET['order_by'])) ? $sql->quote_smart($_GET['order_by']) : "gid";

 $dir = (isset($_GET['dir'])) ? $sql->quote_smart($_GET['dir']) : 1;
 $order_dir = ($dir) ? "ASC" : "DESC";
 $dir = ($dir) ? 0 : 1;

 $query_1 = $sql->query("SELECT count(*) FROM guild");
 $all_record = $sql->result($query_1,0);

 $query = $sql->query("SELECT guild.guildid AS gid, guild.name AS name,guild.leaderguid AS lguid,SUBSTRING_INDEX(guild.MOTD,' ',6), guild.createdate,
						(SELECT name FROM `characters` WHERE guid = lguid) AS l_name,(SELECT COUNT(*) FROM guild_member WHERE guildid = gid) AS tot_chars
						FROM guild ORDER BY $order_by $order_dir LIMIT $start, $itemperpage");
 $this_page = $sql->num_rows($query);

//==========================top tage navigaion starts here========================
 $output .="<center><table class=\"top_hidden\">
          <tr><td>
			<table class=\"hidden\">
				<tr><td>
			<form action=\"guild.php\" method=\"get\" name=\"form\">
			<input type=\"hidden\" name=\"action\" value=\"search\" />
			<input type=\"hidden\" name=\"error\" value=\"4\" />
			<input type=\"text\" size=\"45\" name=\"search_value\" />
			<select name=\"search_by\">
				<option value=\"name\">{$lang_guild['by_name']}</option>
				<option value=\"leaderguid\">{$lang_guild['by_guild_leader']}</option>
				<option value=\"createdate\">{$lang_guild['by_create_date']}</option>
				<option value=\"guildid\">{$lang_guild['by_id']}</option>
			</select></form></td><td>";
		makebutton($lang_global['search'], "javascript:do_submit()",80);
 $output .= "</td></tr></table>
			<td align=\"right\">";
 $output .= generate_pagination("guild.php?action=brows_guilds&amp;order_by=$order_by&amp;dir=".!$dir, $all_record, $itemperpage, $start);
 $output .= "</td></tr></table>";
//==========================top tage navigaion ENDS here ========================

 $output .= "<table class=\"lined\">
   <tr>
	<th width=\"5%\"><a href=\"guild.php?order_by=gid&amp;start=$start&amp;dir=$dir\">".($order_by=='gid' ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" /> " : "")."{$lang_guild['id']}</a></th>
	<th width=\"23%\"><a href=\"guild.php?order_by=name&amp;start=$start&amp;dir=$dir\">".($order_by=='name' ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" /> " : "")."{$lang_guild['guild_name']}</a></th>
	<th width=\"5%\"><a href=\"guild.php?order_by=tot_chars&amp;start=$start&amp;dir=$dir\">".($order_by=='tot_chars' ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" /> " : "")."{$lang_guild['tot_members']}</a></th>
	<th width=\"5%\">Online Members</th>
	<th width=\"15%\"><a href=\"guild.php?order_by=leaderguid&amp;start=$start&amp;dir=$dir\">".($order_by=='leaderguid' ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" /> " : "")."{$lang_guild['guild_leader']}</a></th>
	<th width=\"32%\">{$lang_guild['guild_motd']}</th>
	<th width=\"15%\"><a href=\"guild.php?order_by=createdate&amp;start=$start&amp;dir=$dir\">".($order_by=='createdate' ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" /> " : "")."{$lang_guild['create_date']}</a></th>
   </tr>";

while ($data = $sql->fetch_row($query))	{

 $gonline = $sql->query("SELECT count(*) AS GCNT  FROM `guild_member`, `characters`, `guild` WHERE guild.guildid = ".$data[0]." AND guild_member.guildid = guild.guildid AND guild_member.guid = characters.guid AND characters.online = 1;");
  $guild_online = $sql->result($gonline,"GCNT");

   	$output .= "<tr>
			 <td>$data[0]</td>
			 <td><a href=\"guild.php?action=view_guild&amp;error=3&amp;id=$data[0]\">$data[1]</a></td>
			 <td>$data[6]</td>
			 <td>$guild_online</td>
			 <td><a href=\"char.php?id=$data[2]\">$data[5]</a></td>
			 <td>$data[3] ...</td>
			 <td class=\"small\">$data[4]</td>
            </tr>";
}

 $output .= "<tr><td colspan=\"6\" class=\"hidden\" align=\"right\">{$lang_guild['tot_guilds']} : $all_record</td></tr>
   </table></center>";

 $sql->close();
}


//########################################################################################################################
//  SEARCH
//########################################################################################################################
function search() {
 global $lang_guild, $lang_global, $output, $characters_db, $realm_id, $sql_search_limit;

 if(!isset($_GET['search_value']) || !isset($_GET['search_by'])) redirect("guild.php?error=2");

 $sql = new SQL;
 $sql->connect($characters_db[$realm_id]['addr'], $characters_db[$realm_id]['user'], $characters_db[$realm_id]['pass'], $characters_db[$realm_id]['name']);

 $search_value = $sql->quote_smart($_GET['search_value']);
 $search_by = $sql->quote_smart($_GET['search_by']);

 if(isset($_GET['order_by'])) $order_by = $sql->quote_smart($_GET['order_by']);
	else $order_by = "guildid";

 $dir = (isset($_GET['dir'])) ? $sql->quote_smart($_GET['dir']) : 1;
 $order_dir = ($dir) ? "ASC" : "DESC";
 $dir = ($dir) ? 0 : 1;

if ($search_by == "leaderguid"){
	$temp = $sql->query("SELECT guid FROM `characters` WHERE name ='$search_value'");
	$search_value = $sql->result($temp, 0, 'guid');
}

 $query = $sql->query("SELECT guild.guildid AS gid, guild.name AS name,guild.leaderguid AS lguid,SUBSTRING_INDEX(guild.MOTD,' ',6), guild.createdate,
						(SELECT name FROM `characters` WHERE guid = lguid) AS l_name, (SELECT COUNT(*) FROM guild_member WHERE guildid = gid) AS tot_chars
						FROM guild WHERE $search_by LIKE '%$search_value%' ORDER BY $order_by $order_dir LIMIT $sql_search_limit");
 $total_found = $sql->num_rows($query);

//==========================top tage navigaion starts here========================
 $output .="<center><table class=\"top_hidden\">
			<tr><td>";
			makebutton($lang_guild['guilds'], "guild.php", 120);
			makebutton($lang_global['back'], "javascript:window.history.back()", 120);
  $output .= "<form action=\"guild.php\" method=\"get\" name=\"form\">
			<input type=\"hidden\" name=\"action\" value=\"search\" />
			<input type=\"hidden\" name=\"error\" value=\"4\" />
			<input type=\"text\" size=\"30\" name=\"search_value\" />
			<select name=\"search_by\">
				<option value=\"name\">{$lang_guild['by_name']}</option>
				<option value=\"leaderguid\">{$lang_guild['by_guild_leader']}</option>
				<option value=\"createdate\">{$lang_guild['by_create_date']}</option>
				<option value=\"guildid\">{$lang_guild['by_id']}</option>
			</select>
			</form></td><td>";
			makebutton($lang_global['search'], "javascript:do_submit()",90);
$output .= "</td></tr></table>";
//==========================top tage navigaion ENDS here ========================

 $output .= "<table class=\"lined\">
   <tr>
	<th width=\"5%\"><a href=\"guild.php?action=search&amp;error=4&amp;order_by=guildid&amp;search_by=$search_by&amp;search_value=$search_value&amp;dir=$dir\">".($order_by=='guildid' ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" /> " : "")."{$lang_guild['id']}</a></th>
	<th width=\"25%\"><a href=\"guild.php?action=search&amp;error=4&amp;order_by=name&amp;search_by=$search_by&amp;search_value=$search_value&amp;dir=$dir\">".($order_by=='name' ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" /> " : "")."{$lang_guild['guild_name']}</a></th>
	<th width=\"5%\"><a href=\"guild.php?action=search&amp;error=4&amp;order_by=tot_chars&amp;search_by=$search_by&amp;search_value=$search_value&amp;dir=$dir\">".($order_by=='tot_chars' ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" /> " : "")."{$lang_guild['tot_members']}</a></th>
	<th width=\"15%\"><a href=\"guild.php?action=search&amp;error=4&amp;order_by=leaderguid&amp;search_by=$search_by&amp;search_value=$search_value&amp;dir=$dir\">".($order_by=='leaderguid' ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" /> " : "")."{$lang_guild['guild_leader']}</a></th>
	<th width=\"35%\">{$lang_guild['guild_motd']}</th>
	<th width=\"15%\"><a href=\"guild.php?action=search&amp;error=4&amp;order_by=createdate&amp;search_by=$search_by&amp;search_value=$search_value&amp;dir=$dir\">".($order_by=='createdate' ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" /> " : "")."{$lang_guild['create_date']}</a></th>
   </tr>";

 while ($data =$sql->fetch_row($query)){
	$output .= "<tr>
			 <td>$data[0]</td>
			 <td><a href=\"guild.php?action=view_guild&amp;error=3&amp;id=$data[0]\">$data[1]</a></td>
			 <td>$data[6]</td>
			 <td><a href=\"char.php?id=$data[2]\">$data[5]</a></td>
			 <td>$data[3] ...</td>
			 <td class=\"small\">$data[4]</td>
            </tr>";
}

 $output .= "<tr>
      <td colspan=\"6\" class=\"hidden\" align=\"right\">{$lang_guild['tot_found']} : $total_found {$lang_global['limit']} : $sql_search_limit</td>
    </tr>
   </table></center>";

 $sql->close();
}

function count_days( $a, $b ) {
	$gd_a = getdate( $a );
	$gd_b = getdate( $b );
	$a_new = mktime( 12, 0, 0, $gd_a['mon'], $gd_a['mday'], $gd_a['year'] );
	$b_new = mktime( 12, 0, 0, $gd_b['mon'], $gd_b['mday'], $gd_b['year'] );
	return round( abs( $a_new - $b_new ) / 86400 );
}

//########################################################################################################################
// VIEW GUILD
//########################################################################################################################
function view_guild() {
 global $lang_guild, $lang_global, $output, $characters_db, $realm_id, $user_lvl;

 if(!isset($_GET['id'])) redirect("guild.php?error=1");

 $sql = new SQL;
 $sql->connect($characters_db[$realm_id]['addr'], $characters_db[$realm_id]['user'], $characters_db[$realm_id]['pass'], $characters_db[$realm_id]['name']);

 $guild_id = $sql->quote_smart($_GET['id']);

 $query = $sql->query("SELECT guildid, name, info, MOTD, createdate FROM guild WHERE guildid = '$guild_id'");
 $guild_data = $sql->fetch_row($query);

 $members = $sql->query("SELECT guild_member.guid, guild_member.rank AS mrank,
						`characters`.name, SUBSTRING_INDEX(SUBSTRING_INDEX(`characters`.`data`, ' ', 35), ' ', -1) AS level,
						(SELECT rname FROM guild_rank WHERE guildid ='$guild_id' AND rid = mrank+1) AS rname,
						guild_member.Pnote, guild_member.OFFnote
						FROM guild_member,`characters`
						LEFT JOIN guild_member k1 ON k1.`guid`=`characters`.`guid`
						WHERE guild_member.guildid = '$guild_id' AND guild_member.guid=`characters`.guid
						ORDER BY mrank");



 $total_members = $sql->num_rows($members);

 if (!$guild_data[2]) $guild_data[2] = $lang_global['none'];

 $output .= "<script type=\"text/javascript\">
	answerbox.btn_ok='{$lang_global['yes_low']}';
	answerbox.btn_cancel='{$lang_global['no']}';
 </script>
 <center>
 <fieldset style=\"width: 950px;\">
	<legend>{$lang_guild['guild']}</legend>
 <table class=\"lined\" style=\"width: 910px;\">
  <tr class=\"bold\">
    <td colspan=\"11\">$guild_data[1]</td>
  </tr>
  <tr>
    <td colspan=\"11\">{$lang_guild['create_date']}: $guild_data[4]</td>
  </tr>
  <tr>
    <td colspan=\"11\">{$lang_guild['info']}: $guild_data[2]</td>
  </tr>
  <tr>
    <td colspan=\"11\">{$lang_guild['motd']}: $guild_data[3]</td>
  </tr>
  <tr>
    <td colspan=\"11\">{$lang_guild['tot_members']}: $total_members</td>
  </tr>
  <tr>";
    if ($user_lvl > 2){
    $output .= " <th width=\"3%\">{$lang_guild['remove']}</th>";
       }
    $output .= "
    <th width=\"21%\">{$lang_guild['name']}</th>
    <th width=\"3%\">Race</th>
    <th width=\"3%\">Class</th>
	<th width=\"3%\">{$lang_guild['level']}</th>
	<th width=\"21%\">{$lang_guild['rank']}</th>
	<th width=\"14%\">Player Note</th>
	<th width=\"14%\">Office Note</th>
	<th width=\"15%\">Last Login (Days)</th>
	<th width=\"3%\">Online</th>
  </tr>";

 while ($member = $sql->fetch_row($members)){

	$query = $sql->query("SELECT `race`,`class`,`online`, `account`, `logout_time`, SUBSTRING_INDEX(SUBSTRING_INDEX(`characters`.`data`, ' ', 35), ' ', -1) AS level, mid(lpad( hex( CAST(substring_index(substring_index(data,' ',".(36+1)."),' ',-1) as unsigned) ),8,'0'),4,1) as gender FROM `characters` WHERE `name` = '$member[2]';");

	$online = $sql->fetch_row($query);
	$accid = $online[3];
	$llogin = count_days($online[4], time());

 $level = $online[5];

			if($level > 0)
		{
			$lev = '<font color="#FFFFFF">'.$level.'</font>';
		}
		if($level > 9)
		{
			$lev = '<font color="#858585">'.$level.'</font>';
		}
		if($level > 19)
		{
			$lev = '<font color="#339900">'.$level.'</font>';
		}
		if($level > 29)
		{
			$lev = '<font color="#3300CC">'.$level.'</font>';
		}
		if($level > 39)
		{
			$lev = '<font color="#5552FF">'.$level.'</font>';
		}
		if($level > 49)
		{
			$lev = '<font color="#FF8000">'.$level.'</font>';
		}
		if($level > 59)
		{
			$lev = '<font color="#FF0000">'.$level.'</font>';
		}
		if($level > 69)
		{
			$lev = '<font color="#FF00CC">'.$level.'</font>';
		}
		if($level > 70)
		{
			$lev = '<font color="#FFF000">'.$level.'</font>';
		}


	if($llogin < 1)
	{
		$lastlogin = '<font color="#009900">'.$llogin.'</font>';
	}
	if($llogin >= 1)
	{
		$lastlogin = '<font color="#0000CC">'.$llogin.'</font>';
	}
	if($llogin > 5)
	{
		$lastlogin = '<font color="#FFFF00">'.$llogin.'</font>';
	}
	if($llogin > 15)
	{
		$lastlogin = '<font color="#FF8000">'.$llogin.'</font>';
	}
	if($llogin > 30)
	{
		$lastlogin = '<font color="#FF0000">'.$llogin.'</font>';
	}
	if($llogin > 60)
	{
		$lastlogin = '<font color="#FF00FF">'.$llogin.'</font>';
	}
	if($llogin > 90)
	{
		$lastlogin = '<font color="#8000FF">'.$llogin.'</font>';
	}

   	$output .= " <tr>";
   	if ($user_lvl > 2){
		$output .= " <td><img src=\"img/aff_cross.png\" alt=\"\" onclick=\"answerBox('{$lang_global['delete']}: <font color=white>{$member[2]}</font><br />{$lang_global['are_you_sure']}', 'guild.php?action=rem_char_from_guild&amp;id=$member[0]&amp;guld_id=$guild_id');\" style=\"cursor:pointer;\" /></td>";
	}
	$output .= " <td><a href=\"char.php?id=$member[0]\">$member[2]</a></td>
	<td><img src='img/c_icons/{$online[0]}-{$online[6]}.gif' onmousemove='toolTip(\"".get_player_race($online[0])."\",\"item_tooltip\")' onmouseout='toolTip()' /></td>
	<td><img src='img/c_icons/{$online[1]}.gif' onmousemove='toolTip(\"".get_player_class($online[1])."\",\"item_tooltip\")' onmouseout='toolTip()' /></td>
	<td>$lev</td>
	<td>$member[4] ($member[1])</td>
	<td>$member[5]</td>
	<td>$member[6]</td>
	<td>$lastlogin</td>
	<td>".(($online[2]) ? "<img src=\"img/up.gif\" alt=\"\" />" : "-")."</td>
	</tr>";
}


 $output .= "</table><br />";
  $sql->close();

 $output .= "<table class=\"hidden\">
          <tr><td>";
				makebutton($lang_guild['guilds'], "guild.php", 272);
 $output .= "</td>
			<td>";
 if ($user_lvl > 2){
		makebutton($lang_guild['del_guild'], "guild.php?action=del_guild&amp;id=$guild_id", 272);
		$output .= "</td></tr>
					<tr><td colspan=\"2\">";
		makebutton($lang_global['back'], "javascript:window.history.back()",556);
		$output .= "</td></tr>";
	} else {
		makebutton($lang_global['back'], "javascript:window.history.back()",272);
		$output .= "</td></tr>";
}

$output .= "</table>
</fieldset></center><br />";
}

//########################################################################################################################
// ARE YOU SURE  YOU WOULD LIKE TO OPEN YOUR AIRBAG?
//########################################################################################################################
function del_guild() {
 global $lang_guild, $lang_global, $output;
 if(isset($_GET['id'])) $id = $_GET['id'];
	else redirect("guild.php?error=1");

 $output .= "<center><h1><font class=\"error\">{$lang_global['are_you_sure']}</font></h1><br />
			<font class=\"bold\">{$lang_guild['guild_id']}: $id {$lang_global['will_be_erased']}</font><br /><br />
			<form action=\"cleanup.php?action=docleanup\" method=\"post\" name=\"form\">
			<input type=\"hidden\" name=\"type\" value=\"guild\" />
			<input type=\"hidden\" name=\"check\" value=\"-$id\" />
		 <table class=\"hidden\">
          <tr><td>";
				makebutton($lang_global['yes'], "javascript:do_submit()",120);
				makebutton($lang_global['no'], "guild.php?action=view_guild&amp;id=$id",120);
 $output .= "</td></tr>
        </table>
		</form></center><br />";
}


//##########################################################################################
//REMOVE CHAR FROM GUILD
function rem_char_from_guild(){
	global $characters_db, $realm_id, $user_lvl;

	require_once("scripts/defines.php");

	if(isset($_GET['id'])) $guid = $_GET['id'];
		else redirect("guild.php?error=1");
	if(isset($_GET['guld_id'])) $guld_id = $_GET['guld_id'];
		else redirect("guild.php?error=1");

	$sql = new SQL;
	$sql->connect($characters_db[$realm_id]['addr'], $characters_db[$realm_id]['user'], $characters_db[$realm_id]['pass'], $characters_db[$realm_id]['name']);

	$char_data = $sql->query("SELECT data FROM `characters` WHERE guid = '$guid' LIMIT 1");
	$data = $sql->result($char_data, 0, 'data');
	$data = explode(' ',$data);
	$data[CHAR_DATA_OFFSET_GUILD_ID] = 0;
	$data[CHAR_DATA_OFFSET_GUILD_RANK] = 0;
	$data = implode(' ',$data);
	$sql->query("UPDATE `characters` SET data = '$data' WHERE guid = '$guid'");
	$sql->query("DELETE FROM guild_member WHERE guid = '$guid'");

	$sql->close();
	redirect("guild.php?action=view_guild&id=$guld_id");
}

//########################################################################################################################
// MAIN
//########################################################################################################################
$err = (isset($_GET['error'])) ? $_GET['error'] : NULL;

$output .= "<div class=\"top\">";
switch ($err) {
case 1:
   $output .= "<h1><font class=\"error\">{$lang_global['empty_fields']}</font></h1>";
   break;
case 2:
   $output .= "<h1><font class=\"error\">{$lang_global['err_no_search_passed']}</font></h1>";
   break;
case 3: //keep blank
   break;
case 4:
   $output .= "<h1>{$lang_guild['guild_search_result']}:</h1>";
   break;
default: //no error
    $output .= "<h1>{$lang_guild['browse_guilds']}</h1>";
}
$output .= "</div>";

$action = (isset($_GET['action'])) ? $_GET['action'] : NULL;

switch ($action) {
case "browse_guilds":
   browse_guilds();
   break;
case "search":
   search();
   break;
case "view_guild":
   view_guild();
   break;
case "del_guild":
   del_guild();
   break;
case "rem_char_from_guild":
   rem_char_from_guild();
   break;
default:
    browse_guilds();
}

require_once("footer.php");
?>
