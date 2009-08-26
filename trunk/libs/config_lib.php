<?php


function new(&$sqlm)
{
  $result  = $sqlm->query('CHECK TABLE `mm_settings` FAST QUICK');
  if('Error' == $sqlm->result($result, 0, 'Msg_type'))
  {
    $output .= 'not found';
    if($sqlm->query('
      CREATE TABLE `mm_settings`
      (
        `id` int(10) NOT NULL auto_increment,
        `cat` int(10) NOT NULL,
        `parent` int(10) NOT NULL default "-1",
        `setting` tinytext,
        `value` tinytext,
        PRIMARY KEY  (`id`),
        UNIQUE KEY `id` (`id`)
      )
      ENGINE=MyISAM DEFAULT CHARSET=utf8'))
    $output .= 'table created';
    developer_test_mode($sqlm);

  }
  elseif('status' == $sqlm->result($result, 0, 'Msg_type'))
  {
    $output .= 'found<br />'.developer_test_mode($sqlm);
    if(developer_test_mode($sqlm)) $output .= 'done';
  }
  else
    $output .= 'unknown';
}


function developer_test_mode(&$sqlm)
{
  $result=$sqlm->result($sqlm->query('select value from mm_settings where setting = \'developer_test_mode\''), 0);
  if($result)
    return $result;
  else $sqlm->query('INSERT INTO mm_settings (id, cat, parent, setting, value)
    VALUES (NULL, "1", "-1", "developer_test_mode" ,"false")');
    return 'false';
}


?>
