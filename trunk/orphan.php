<?php

  require_once 'header.php';
  require_once 'libs/tab_lib.php';

  if($server_type)
    $table = $tab_del_user_characters_trinity;
  else
    $table = $tab_del_user_characters;

  foreach ($table as $db => $value)
  {
    $result = $sqlc->query('select distinct '.$value[1].' from '.$value[0].' where '.$value[1].' not in (select guid from characters)');

    if ($sqlc->num_rows($result))
      $output .= '<br />'.$value[0].'<br />';

    while($orphan = $sqlc->fetch_assoc($result))
    {
      foreach($orphan as $data => $guid)
      {
        $output .= $data.' '.$guid.'<br />';
      }
    }
  }

  $result = $sqlc->query('select id from item_text where id not in (select itemTextID from mail)');
  if ($sqlc->num_rows($result))
    $output .= '<br />item_text<br />';

  while($orphan = $sqlc->fetch_assoc($result))
  {
    foreach($orphan as $data => $guid)
    {
      $output .= $data.' '.$guid.'<br />';
    }
  }

  $result = $sqlc->query('select mail_id from mail_items where mail_id not in (select id from mail)');
  if ($sqlc->num_rows($result))
    $output .= '<br />mail_items<br />';

  while($orphan = $sqlc->fetch_assoc($result))
  {
    foreach($orphan as $data => $guid)
    {
      $output .= $data.' '.$guid.'<br />';
    }
  }




/*
  $guid = 58;
  $realm = 1;

  $sqlr = new SQL;
  $sqlc = new SQL;
  $sqlr->connect($realm_db['addr'], $realm_db['user'], $realm_db['pass'], $realm_db['name']);
  $sqlc->connect($characters_db[$realm]['addr'], $characters_db[$realm]['user'], $characters_db[$realm]['pass'], $characters_db[$realm]['name']);

  $sqlc->query('
    DELETE FROM pet_aura WHERE guid IN
    (SELECT id FROM character_pet WHERE owner IN
    (SELECT guid FROM characters WHERE guid = '.$guid.'))');
  $sqlc->query('
    DELETE FROM pet_spell WHERE guid IN
    (SELECT id FROM character_pet WHERE owner IN
    (SELECT guid FROM characters WHERE guid = '.$guid.'))');
  $sqlc->query('
    DELETE FROM pet_spell_cooldown WHERE guid IN
    (SELECT id FROM character_pet WHERE owner IN
    (SELECT guid FROM characters WHERE guid = '.$guid.'))');
  $sqlc->query('
    DELETE FROM item_text WHERE id IN
    (SELECT itemTextId FROM mail WHERE receiver IN
    (SELECT guid FROM characters WHERE guid = '.$guid.'))');
  foreach ($tab_del_user_characters as $value)
    $sqlc->query('DELETE FROM '.$value[0].' WHERE '.$value[1].' = '.$guid.'');
*/

  require_once 'footer.php';

?>
