<?php

//if (file_exists('scripts/config.php'))
//{
//  if (file_exists('scripts/config.dist.php'))
    require_once 'scripts/config.dist.php';
//  else
//    exit('<center><br><code>\'scripts/config.dist.php\'</code> not found,<br>
//          please restore <code>\'scripts/config.dist.php\'</code></center>');
  require_once 'scripts/config.php';
//}
//else
//  exit('<center><br><code>\'scripts/config.php\'</code> not found,<br>
//        please copy <code>\'scripts/config.dist.php\'</code> to
//        <code>\'scripts/config.php\'</code> and make appropriate changes.');

require_once 'libs/db_lib.php';
require_once 'libs/global_lib.php';
require_once 'libs/archieve_lib.php';

//header('Expires: Tue, 01 Jan 2000 00:00:00 GMT');
//header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
//header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
//header('Cache-Control: post-check=0, pre-check=0', false);
//header('Pragma: no-cache');

//wowhead_tt();
//echo $output;

$sqlm = new SQL;
$sqlm->connect($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass'], $mmfpm_db['name']);

$result = $sqlm->query('select id from dbc_achievement order by id ASC');
$i=0;
while($entry = $sqlm->fetch_assoc($result))
{
  if(achieve_get_icon($entry['id'], $sqlm) == 'img/INV/INV_blank_32.gif')
  {
    //  echo '<a href="'.$achievement_datasite.$entry['id'].'"><img src="'.achieve_get_icon($entry['id'], $sqlm).'" /></a>';
    achieve_get_icon($entry['id'], $sqlm);
    echo $entry['id'].' ';
  }
  else ++$i;
}
echo $i;


?>
