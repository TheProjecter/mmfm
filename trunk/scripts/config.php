<?php
/*
 * Project Name: MiniManager for Mangos Server
 * Date: 17.10.2006 inital version (0.0.1a)
 * Author: Q.SA
 * Copyright: Q.SA
 * Email: *****
 * License: GNU General Public License v2(GPL)
 *
 * Updated by Shnappie to work with 3 databases
 * instead of 2 supported by version of Q.SA
 */

$version = "0.11";

//---- SQL Configuration ----

/* SQL server type  :
*  "MySQL" - Mysql
*  "PgSQL" - PostgreSQL
*  "MySQLi" - MySQLi
*  "SQLLite" - SQLLite
*/
$db_type = "MySQL";

$realm_db = Array(
	'addr' => "127.0.0.1:3306",	//SQL server IP:port this realmd located on
	'user' => "root",			//SQL server login this realmd located on
	'pass' => "",			//SQL server pass this realmd located on
	'name' => "realmd",			//realmd DB name
	'encoding' => "utf8" 		//SQL connection encoding
	);
$mangos_db = Array(
	1 => array(		//position in array must represent realmd ID
			'id' => 1,					//Realm ID
			'addr' => "127.0.0.1:3306",	//SQL server IP:port this DB located on
			'user' => "root",			//SQL server login this DB located on
			'pass' => "",			//SQL server pass this DB located on
			'name' => "mangos",			//World Database name
			'encoding' => "utf8" 		//SQL connection encoding
			),
);

$characters_db = Array(
	1 => array(		//position in array must represent realmd ID
			'id' => 1,					//Realm ID
			'addr' => "127.0.0.1:3306",	//SQL server IP:port this DB located on
			'user' => "root",			//SQL server login this DB located on
			'pass' => "",			//SQL server pass this DB located on
			'name' => "characters",			//Character Database name
			'encoding' => "utf8", 		//SQL connection encoding
			),          //NOTE: THIS USER MUST HAVE AT LEAST READ ACCESS ON THE WORLD DATABASE
);

//---- Game Server Configuration ----
$server = Array(	//if more than one realm used, even if they are on same system new subarray MUST be added.
	1 => array(		//position in array must represent realmd ID, same as in $mangos_db
			'addr' => "127.0.0.1",		//Game Server IP - Must be external address
			'game_port' => 8085,		//Game Server port
			'term_type' => "SSH",		//Terminal type - ("SSH"/"Telnet")
			'term_port' => 22,			//Terminal port
			'rev' => "rev.5297 SD269",		//Mangos rev. used
			'both_factions' => true	//Allow to see opponent faction characters. Affects only players.
			),
);
$sql_search_limit = 100; //limit number of maximum search results

//---- Mail configuration ----
$admin_mail = "mail@mail.com";	//mail used for bug reports and other user contact

$mailer_type = "smtp"; 			// type of mailer to be used("mail", "sendmail", "smtp")
$from_mail = "mail@mail.com"; 	//all emails will be sent from this email
//smtp server config
$smtp_cfg = array(
			'host' => "smtp.mail.com",	//smtp server
			'port' => 25,				//port
			'user' => "",				//username - use only if auth. required
			'pass' => ""				//pass
			);

//---- New account creation Options ----
$disable_acc_creation = false; 	//true = Do not allow new accounts to be created
$send_mail_on_creation = false; //true = send mail at account creation.
$create_acc_locked = 0; 		//if set to '1' newly created accounts will be made locked to 0.0.0.0 IP disallowing user to login.
$validate_mail_host = false;  	//actualy make sure the mail host provided in email is valid/accessible host.
$limit_acc_per_ip = false; 		//true = limit to one account per IP
/* this option will limit account creation to users from selected net range(s).
*  allow all => empty array
* e.g: "120-122.55.255-0.255-0", */
$valid_ip_mask = array(
				//"255-0.255-0.255-0.255-0",
				);

//---- Layout configuration ----
$title = "MiniManager for MaNgOs srv.";
$itemperpage = 25;

$css_template = "Sulfur"; 		//file/folder name of css tamplate to use from templates directory by default
$language = "enUS"; 			//default site language
$site_encoding = "iso-8859-1"; 	//default encoding

//---- IRC Options ------
$irc_cfg = array(
			'server' => "mangos.cjb.net",	//irc server
			'port' => 6667,					//port
			'channel' => "minimanager"				//channel
			);

//---- External Links ----
$item_datasite = "http://www.wowhead.com/?item=";
$quest_datasite = "http://www.wowhead.com/?quest=";
$creature_datasite = "http://www.wowhead.com/?npc=";
$spell_datasite = "http://www.wowhead.com/?spell=";
$skill_datasite = "http://www.wowhead.com/?spells=";
$talent_datasite = "http://wowhead.com/?spell=";
$talent_calculator_datasite = "http://www.worldofwarcraft.com/info/classes";
$go_datasite = "http://www.wowhead.com/?object=";
$get_icons_from_web = true; //wherever to get icons from the web in case they are missing in /img/INV dir.

//---- Backup configuration ----
$backup_dir = "./backup";  //make sure webserver have the permission to write/read it!

//---- HTTP Proxy Configuration ----
$proxy_cfg = Array(
	'addr' => "", //configure only if requierd
	'port' => 80,
	'user' => "",
	'pass' => ""
	);

//menu content by user level
$menu_array = Array(
	5 => array("SysOP" ,array(
				array("index.php", 'main', array()),
				array("#", 'users', array(
							array("user.php", 'accounts'),
							array("char_list.php", 'characters'),
							array("guild.php", 'guilds'),
							array("arenateam.php", 'arena_teams'),
							array("honor.php", 'honor'),
							array("banned.php", 'banned_list'),
							array("cleanup.php", 'cleanup'),
							array("stat.php", 'statistics'),
							array("stat_on.php", 'statistics_on'),
							array("javascript:void(0);\" onclick=\"window.open('./pomm/pomm.php', 'pomm', 'Toolbar=0, Location=0, Directories=0, Status=0, Menubar=0, Scrollbar=0, Resizable=0, Copyhistory=1, Width=784, Height=525')", 'player_map'),
							),
						),
				array("#", 'tools', array(
							array("ssh.php", 'ssh_line'),
							array("run_patch.php", 'run_sql_patch'),
							array("ticket.php", 'tickets'),
							array("ahstats.php", 'auctionhouse'),
							array("events.php", 'events'),
							array("realm.php", 'realm'),
							array("motd.php", 'add_motd'),
							array("mail.php", 'mail'),
							array("irc.php", 'irc'),
							array("instances.php", 'instances'),
							),
						),
				array("#", 'db', array(
							array("item.php", 'items'),
							array("creature.php", 'creatures'),
							array("game_object.php", 'game_object'),
							array("tele.php", 'teleports'),
							array("command.php", 'command'),
							array("backup.php", 'backup'),
							array("repair.php", 'repair'),
							),
						),
				array("#", 'forums', array(
							array("forum.php", 'forums'),
							array("javascript:void(0);\" onclick=\"window.open('./forum.html', 'forum')", 'forums'),
							),
						),
				)
		),
	4 => array("Admin" ,array(
				array("index.php", 'main', array()),
				array("#", 'users', array(
							array("user.php", 'accounts'),
							array("char_list.php", 'characters'),
							array("guild.php", 'guilds'),
							array("arenateam.php", 'arena_teams'),
							array("honor.php", 'honor'),
							array("banned.php", 'banned_list'),
							array("cleanup.php", 'cleanup'),
							array("stat.php", 'statistics'),
							array("stat_on.php", 'statistics_on'),
							array("javascript:void(0);\" onclick=\"window.open('./pomm/pomm.php', 'pomm', 'Toolbar=0, Location=0, Directories=0, Status=0, Menubar=0, Scrollbar=0, Resizable=0, Copyhistory=1, Width=784, Height=525')", 'player_map'),
							),
						),
				array("#", 'tools', array(
							array("ssh.php", 'ssh_line'),
							array("run_patch.php", 'run_sql_patch'),
							array("ticket.php", 'tickets'),
							array("ahstats.php", 'auctionhouse'),
							array("events.php", 'events'),
							array("realm.php", 'realm'),
							array("motd.php", 'add_motd'),
							array("mail.php", 'mail'),
							array("irc.php", 'irc'),
							array("instances.php", 'instances'),
							),
						),
				array("#", 'db', array(
							array("item.php", 'items'),
							array("creature.php", 'creatures'),
							array("game_object.php", 'game_object'),
							array("tele.php", 'teleports'),
							array("command.php", 'command'),
							array("backup.php", 'backup'),
							array("repair.php", 'repair'),
							),
						),
				array("#", 'forums', array(
							array("forum.php", 'forums'),
							array("javascript:void(0);\" onclick=\"window.open('./forum.html', 'forum')", 'forums'),
							),
						),
				)
		),
	3 => array("BugTracker" ,array(
				array("index.php", 'main', array()),
				array("#", 'users', array(
							array("user.php", 'accounts'),
							array("char_list.php", 'characters'),
							array("guild.php", 'guilds'),
							array("arenateam.php", 'arena_teams'),
							array("honor.php", 'honor'),
							array("banned.php", 'banned_list'),
							array("cleanup.php", 'cleanup'),
							array("stat.php", 'statistics'),
							array("stat_on.php", 'statistics_on'),
							array("javascript:void(0);\" onclick=\"window.open('./pomm/pomm.php', 'pomm', 'Toolbar=0, Location=0, Directories=0, Status=0, Menubar=0, Scrollbar=0, Resizable=0, Copyhistory=1, Width=784, Height=525')", 'player_map'),
							),
						),
				array("#", 'tools', array(
							array("ssh.php", 'ssh_line'),
							array("run_patch.php", 'run_sql_patch'),
							array("ticket.php", 'tickets'),
							array("ahstats.php", 'auctionhouse'),
							array("events.php", 'events'),
							array("realm.php", 'realm'),
							array("motd.php", 'add_motd'),
							array("mail.php", 'mail'),
							array("irc.php", 'irc'),
							array("instances.php", 'instances'),
							),
						),
				array("#", 'db', array(
							array("item.php", 'items'),
							array("creature.php", 'creatures'),
							array("game_object.php", 'game_object'),
							array("tele.php", 'teleports'),
							array("command.php", 'command'),
							array("backup.php", 'backup'),
							array("repair.php", 'repair'),
							),
						),
				array("#", 'forums', array(
							array("forum.php", 'forums'),
							array("javascript:void(0);\" onclick=\"window.open('./forum.html', 'forum')", 'forums'),
							),
						),
				)
		),
	2 => array("GameMaster" ,array(
				array("index.php", 'main', array()),
				array("#", 'users', array(
							array("user.php", 'accounts'),
							array("char_list.php", 'characters'),
							array("guild.php", 'guilds'),
							array("arenateam.php", 'arena_teams'),
							array("honor.php", 'honor'),
							array("banned.php", 'banned_list'),
							array("stat.php", 'statistics'),
							array("stat_on.php", 'statistics_on'),
							array("javascript:void(0);\" onclick=\"window.open('./pomm/pomm.php', 'pomm', 'Toolbar=0, Location=0, Directories=0, Status=0, Menubar=0, Scrollbar=0, Resizable=0, Copyhistory=1, Width=784, Height=525')", 'player_map'),
							),
						),
				array("#", 'tools', array(
							array("ticket.php", 'tickets'),
							array("ahstats.php", 'auctionhouse'),
							array("events.php", 'events'),
							array("motd.php", 'add_motd'),
							array("mail.php", 'mail'),
							array("irc.php", 'irc'),
							array("instances.php", 'instances'),
							),
						),
				array("#", 'db', array(
							array("item.php", 'items'),
							array("creature.php", 'creatures'),
							array("game_object.php", 'game_object'),
							array("tele.php", 'teleports'),
							array("command.php", 'command'),
							),
						),
				array("#", 'forums', array(
							array("forum.php", 'forums'),
							array("javascript:void(0);\" onclick=\"window.open('./forum.html', 'forum')", 'forums'),
							),
						),
			)
		),
	1 => array("Moderator" ,array(
				array("index.php", 'main', array()),
				array("#", 'users', array(
							array("user.php", 'accounts'),
							array("char_list.php", 'characters'),
							array("guild.php", 'guilds'),
							array("arenateam.php", 'arena_teams'),
							array("honor.php", 'honor'),
							array("banned.php", 'banned_list'),
							array("stat.php", 'statistics'),
							array("stat_on.php", 'statistics_on'),
							array("javascript:void(0);\" onclick=\"window.open('./pomm/pomm.php', 'pomm', 'Toolbar=0, Location=0, Directories=0, Status=0, Menubar=0, Scrollbar=0, Resizable=0, Copyhistory=1, Width=784, Height=525')", 'player_map'),
							),
						),
				array("#", 'tools', array(
							array("ticket.php", 'tickets'),
							array("ahstats.php", 'auctionhouse'),
							array("events.php", 'events'),
							array("motd.php", 'add_motd'),
							array("irc.php", 'irc'),
							array("instances.php", 'instances'),
							),
					),
				array("#", 'db', array(
							array("item.php", 'items'),
							array("creature.php", 'creatures'),
							array("game_object.php", 'game_object'),
							array("tele.php", 'teleports'),
							array("command.php", 'command'),
							),
					),
				array("#", 'forums', array(
							array("forum.php", 'forums'),
							array("javascript:void(0);\" onclick=\"window.open('./forum.html', 'forum')", 'forums'),
							),
						),
			)
		),
	0 => array("Player" ,array(
				array("index.php", 'main', array()),
				array("#", 'users', array(
							array("guild.php", 'guilds'),
							array("arenateam.php", 'arena_teams'),
							array("honor.php", 'honor'),
							array("stat.php", 'statistics'),
							array("stat_on.php", 'statistics_on'),
							array("javascript:void(0);\" onclick=\"window.open('./pomm/pomm.php', 'pomm', 'Toolbar=0, Location=0, Directories=0, Status=0, Menubar=0, Scrollbar=0, Resizable=0, Copyhistory=1, Width=784, Height=525')", 'player_map'),
							),
						),
				array("#", 'tools', array(
							array("ahstats.php", 'auctionhouse'),
							array("command.php", 'command'),
							array("events.php", 'events'),
							array("irc.php", 'irc'),
							array("instances.php", 'instances'),
							),
					),
				array("#", 'forums', array(
							array("forum.php", 'forums'),
							array("javascript:void(0);\" onclick=\"window.open('./forum.html', 'forum')", 'forums'),
							),
						),
			)
		),

);

$debug = false; //set to true if full php debugging requierd.
?>