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
require_once 'libs/item_lib.php';

//header('Expires: Tue, 01 Jan 2000 00:00:00 GMT');
//header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
//header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
//header('Cache-Control: post-check=0, pre-check=0', false);
//header('Pragma: no-cache');

//wowhead_tt();
//echo $output;

$sqlm = new SQL;
$sqlm->connect($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass'], $mmfpm_db['name']);

$realm_id = 1;
$sqlw = new SQL;
$sqlw->connect($world_db[$realm_id]['addr'], $world_db[$realm_id]['user'], $world_db[$realm_id]['pass'], $world_db[$realm_id]['name']);

$result = $sqlw->query('select entry from item_template order by entry ASC');
$i=0;
while($entry = $sqlw->fetch_assoc($result))
{
  if(get_item_icon($entry['entry'], $sqlm, $sqlw) == 'img/INV/INV_blank_32.gif')
  {
    //echo '<a href="'.$item_datasite.$entry['entry'].'"><img src="'.get_item_icon($entry['entry'], $sqlm, $sqlw).'" /></a>';
    get_item_icon($entry['entry']);
    echo $entry['entry'].' ';
  }
  else ++$i;
}
echo $i;


?>
