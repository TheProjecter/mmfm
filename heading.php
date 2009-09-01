<?php
  global $lang_edit, $lang_global, $output, $realm_db, $characters_db, $realm_id, $mmfpm_db, $user_name, $user_id, $lang_id_tab, $gm_level_arr, $ren_char, $total_points, $tp_cost, $ch_gend_cost, $is_online, $move_char_cost, $item_cost, $vip_ext, $vip_cost, $rename_cost;
  require_once("header.php");
  valid_login($action_permission['read']);
  mysql_connect($realm_db['addr'], $realm_db['user'], $realm_db['pass']) or die(mysql_error());
$datetime = date("Y-m-d H:i:s");
?>