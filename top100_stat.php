<?php


require_once 'header.php';
require_once 'libs/char_lib.php';

function top100($realmid, &$sqlr, &$sqlc)
{
  global $output, $lang_top,
    $realm_db, $characters_db, $server,
    $itemperpage, $developer_test_mode, $multi_realm_mode;

  $realm_id = $realmid;

  $sqlc->connect($characters_db[$realm_id]['addr'], $characters_db[$realm_id]['user'], $characters_db[$realm_id]['pass'], $characters_db[$realm_id]['name']);

  //==========================$_GET and SECURE========================
  $start = (isset($_GET['start'])) ? $sqlc->quote_smart($_GET['start']) : 0;
  if (is_numeric($start)); else $start=0;

  $order_by = (isset($_GET['order_by'])) ? $sqlc->quote_smart($_GET['order_by']) : 'level';
  if (preg_match('/^[_[:lower:]]{1,10}$/', $order_by)); else $order_by = 'level';

  $dir = (isset($_GET['dir'])) ? $sqlc->quote_smart($_GET['dir']) : 1;
  if (preg_match('/^[01]{1}$/', $dir)); else $dir=1;

  $order_dir = ($dir) ? 'DESC' : 'DESC';
  $dir = ($dir) ? 0 : 1;
  //==========================$_GET and SECURE end========================

  $result = $sqlc->query('SELECT count(*) FROM characters');
  $all_record = $sqlc->result($result, 0);
  $all_record = (($all_record < 100) ? $all_record : 100);

  $result = $sqlc->query('SELECT guid, name, race, class, gender, level,
    CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(data, " ", '.(CHAR_DATA_OFFSET_MAX_HEALTH+1).'), " ", -1) AS UNSIGNED) AS health,
    CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(data, " ", '.(CHAR_DATA_OFFSET_MAX_MANA+1).'),   " ", -1) AS UNSIGNED) AS mana,
    CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(data, " ", '.(CHAR_DATA_OFFSET_STR+1).'), " ", -1) AS UNSIGNED) AS str,
	CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(data, " ", '.(CHAR_DATA_OFFSET_AGI+1).'), " ", -1) AS UNSIGNED) AS agi,
	CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(data, " ", '.(CHAR_DATA_OFFSET_STA+1).'),   " ", -1) AS UNSIGNED) AS sta,
	CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(data, " ", '.(CHAR_DATA_OFFSET_INT+1).'),   " ", -1) AS UNSIGNED) AS intel,
	CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(data, " ", '.(CHAR_DATA_OFFSET_SPI+1).'),   " ", -1) AS UNSIGNED) AS spi
    FROM characters ORDER BY '.$order_by.' '.$order_dir.' LIMIT '.$start.', '.$itemperpage.'');

  //==========================top tage navigaion starts here========================
$output .= "
  <center>
    <div id=\"tab\">
      <ul>
        <li>
          <a href=\"top100.php\">
            {$lang_top['misc']}
          </a>
        </li>
        <li id=\"selected\">
          <a href=\"top100_stat.php\">
            {$lang_top['stats']}
          </a>
        </li>
        <li>
          <a href=\"top100_pvp.php\">
            {$lang_top['pvp']}
          </a>
        </li>
      </ul>
    </div>
    <div id=\"tab_content\">
";
  
  $output .= '
          <script type="text/javascript" src="js/check.js"></script>
          <center>
            <table class="top_hidden">';
  if($developer_test_mode && $multi_realm_mode)
  {
    $realms = $sqlr->query('SELECT count(*) FROM realmlist');
    $tot_realms = $sqlr->result($realms, 0);
    if (1 < $tot_realms && 1 < count($server))
    {
      $output .= '
              <tr>
                <td colspan="2" align="left">';
                  makebutton('View', 'javascript:do_submit(\'form'.$realm_id.'\',0)', 130);
      $output .= '
                  <form action="top100.php" method="get" name="form'.$realm_id.'">
                    Number of Realms :
                    <input type="hidden" name="action" value="realms" />
                    <select name="n_realms">';
      for($i=1;$i<=$tot_realms;++$i)
        $output .= '
                      <option value="'.$i.'">'.htmlentities($i).'</option>';
      $output .= '
                    </select>
                  </form>
                </td>
              </tr>';
    }
  }
  $output .= '
              <tr>
                <td align="right">Total: '.$all_record.'</td>
                <td align="right" width="25%">';
  $output .= generate_pagination('top100.php?order_by='.$order_by.'&amp;dir='.(($dir) ? 0 : 1).'', $all_record, $itemperpage, $start);
  $output .= '
                </td>
              </tr>
            </table>';
  //==========================top tage navigaion ENDS here ========================

  $output .= '
            <table class="lined">
              <tr>
                <th width="1%">'.$lang_top['name'].'</th>
                <th width="1%">'.$lang_top['race'].'</th>
                <th width="1%">'.$lang_top['class'].'</th>
                <th width="1%"><a href="top100.php?order_by=level&amp;start='.$start.'&amp;dir='.$dir.'"'.($order_by=='level' ? ' class="'.$order_dir.'"' : '').'>'.$lang_top['level'].'</a></th>
				<th width="1%"><a href="top100_stat.php?order_by=health&amp;start='.$start.'&amp;dir='.$dir.'"'.($order_by=='health' ? ' class="'.$order_dir.'"' : '').'>'.$lang_top['health'].'</a></th>
				<th width="1%"><a href="top100_stat.php?order_by=mana&amp;start='.$start.'&amp;dir='.$dir.'"'.($order_by=='mana' ? ' class="'.$order_dir.'"' : '').'>'.$lang_top['mana'].'</a></th>
				<th width="1%"><a href="top100_stat.php?order_by=str&amp;start='.$start.'&amp;dir='.$dir.'"'.($order_by=='str' ? ' class="'.$order_dir.'"' : '').'>'.$lang_top['str'].'</a></th>
				<th width="1%"><a href="top100_stat.php?order_by=agi&amp;start='.$start.'&amp;dir='.$dir.'"'.($order_by=='agi' ? ' class="'.$order_dir.'"' : '').'>'.$lang_top['agi'].'</a></th>
				<th width="1%"><a href="top100_stat.php?order_by=sta&amp;start='.$start.'&amp;dir='.$dir.'"'.($order_by=='sta' ? ' class="'.$order_dir.'"' : '').'>'.$lang_top['sta'].'</a></th>
				<th width="1%"><a href="top100_stat.php?order_by=intel&amp;start='.$start.'&amp;dir='.$dir.'"'.($order_by=='intel' ? ' class="'.$order_dir.'"' : '').'>'.$lang_top['intel'].'</a></th>
				<th width="1%"><a href="top100_stat.php?order_by=spi&amp;start='.$start.'&amp;dir='.$dir.'"'.($order_by=='spi' ? ' class="'.$order_dir.'"' : '').'>'.$lang_top['spi'].'</a></th>
              </tr>';
  for ($i=0; $i<$itemperpage; ++$i)
  {
    $char = $sqlc->fetch_assoc($result);

    $output .= '
              <tr valign="top">
                <td><a href="char.php?id='.$char['guid'].'&amp;realm='.$realm_id.'">'.htmlentities($char['name']).'</a></td>
                <td><img src="img/c_icons/'.$char['race'].'-'.$char['gender'].'.gif" alt="'.char_get_race_name($char['race']).'" onmousemove="toolTip(\''.char_get_race_name($char['race']).'\', \'item_tooltip\')" onmouseout="toolTip()" /></td>
                <td><img src="img/c_icons/'.$char['class'].'.gif" alt="'.char_get_class_name($char['class']).'" onmousemove="toolTip(\''.char_get_class_name($char['class']).'\', \'item_tooltip\')" onmouseout="toolTip()" /></td>
                <td>'.char_get_level_color($char['level']).'</td>
				<td>'.$char['health'].'</td>
                <td>'.$char['mana'].'</td>
                <td>'.$char['str'].'</td>
				<td>'.$char['agi'].'</td>
				<td>'.$char['sta'].'</td>
				<td>'.$char['intel'].'</td>
				<td>'.$char['spi'].'</td>
              </tr>';
  }
  $output .= '
              <tr>
                <td colspan="12" class="hidden" align="right" width="25%">';
  $output .= generate_pagination('top100.php?order_by='.$order_by.'&amp;dir='.(($dir) ? 0 : 1).'', $all_record, $itemperpage, $start);
  unset($all_record);
  $output .= '
                </td>
              </tr>
            </table>
            <br />
          </center>';


}

//#############################################################################
// MAIN
//#############################################################################

//$err = (isset($_GET['error'])) ? $_GET['error'] : NULL;

//$output .= '
//          <div class="top">';

$lang_top = lang_top();

//if(1 == $err);
//else
//  $output .= "
//            <h1>'.$lang_top['top100'].'</h1>;

//unset($err);

//$output .= '
//          </div>';

$action = (isset($_GET['action'])) ? $_GET['action'] : NULL;

if ('realms' == $action)
{
  if (isset($_GET['n_realms']))
  {
    $n_realms = $_GET['n_realms'];

    $realms = $sqlr->query('SELECT id, name FROM realmlist LIMIT 10');

    if (1 < $sqlr->num_rows($realms) && 1 < (count($server)))
    {
      for($i=1;$i<=$n_realms;++$i)
      {
        $realm = $sqlr->fetch_assoc($realms);
        if(isset($server[$realm['id']]))
        {
          $output .= '
          <div class="top"><h1>Top 100 of '.$realm['name'].'</h1></div>';
          top100($realm['id'], $sqlr, $sqlc);
        }
      }
    }
    else
    {
      $output .= '
          <div class="top"><h1>'.$lang_top['top100'].'</h1></div>';
      top100($realm_id, $sqlr, $sqlc);
    }
  }
  else
  {
    $output .= '
          <div class="top"><h1>'.$lang_top['top100'].'</h1></div>';
    top100($realm_id, $sqlr, $sqlc);
  }
}
else
{
  $output .= '
          <div class="top"><h1>'.$lang_top['top100'].'</h1></div>';
  top100($realm_id, $sqlr, $sqlc);
}


unset($action);
unset($action_permission);
unset($lang_top);

require_once 'footer.php';


?>
