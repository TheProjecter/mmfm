<?php


require_once 'header.php';
require_once 'libs/char_lib.php';
require_once 'libs/mail_lib.php';
require_once 'libs/item_lib.php';
valid_login($action_permission['read']);

//########################################################################################################################
// SHOW CHARACTERS ACHIEVEMENTS
//########################################################################################################################
function char_friends(&$sqlr, &$sqlc)
{
  global $output, $lang_global, $lang_char,
    $realm_id, $realm_db, $mmfpm_db, $characters_db,
    $action_permission, $user_lvl, $user_name,
    $item_datasite;

  if (empty($_GET['id']))
    error($lang_global['empty_fields']);

  // this is multi realm support, as of writing still under development
  //  this page is already implementing it
  if (empty($_GET['realm']))
    $realmid = $realm_id;
  else
  {
    $realmid = $sqlr->quote_smart($_GET['realm']);
    if (is_numeric($realmid))
      $sqlc->connect($characters_db[$realmid]['addr'], $characters_db[$realmid]['user'], $characters_db[$realmid]['pass'], $characters_db[$realmid]['name']);
    else
      $realmid = $realm_id;
  }

  //==========================$_GET and SECURE========================
  $id = $sqlc->quote_smart($_GET['id']);
  if (is_numeric($id)); else $id = 0;

  $order_by = (isset($_GET['order_by'])) ? $sqlc->quote_smart($_GET['order_by']) : 'name';
  if (preg_match('/^[[:lower:]]{1,6}$/', $order_by)); else $order_by = 'name';

  $dir = (isset($_GET['dir'])) ? $sqlc->quote_smart($_GET['dir']) : 1;
  if (preg_match('/^[01]{1}$/', $dir)); else $dir = 1;

  $order_dir = ($dir) ? 'ASC' : 'DESC';
  $dir = ($dir) ? 0 : 1;
  //==========================$_GET and SECURE end========================

  // getting character data from database
  $result = $sqlc->query('SELECT account, name, race, class, level, gender
    FROM characters WHERE guid = '.$id.' LIMIT 1');

  if ($sqlc->num_rows($result))
  {
    $char = $sqlc->fetch_assoc($result);

    // we get user permissions first
    $owner_acc_id = $sqlc->result($result, 0, 'account');
    $result = $sqlr->query('SELECT gmlevel, username FROM account WHERE id = '.$char['account'].'');
    $owner_gmlvl = $sqlr->result($result, 0, 'gmlevel');
    $owner_name = $sqlr->result($result, 0, 'username');

    if (($user_lvl > $owner_gmlvl)||($owner_name === $user_name))
    {
      //------------------------Character Tabs---------------------------------
      // we start with a lead of 10 spaces,
      //  because last line of header is an opening tag with 8 spaces
      //  keep html indent in sync, so debuging from browser source would be easy to read
      $output .= '
          <center>
            <script type="text/javascript">
              // <![CDATA[
                function wrap()
                {
                  if (getBrowserWidth() > 1024)
                  document.write(\'</table></td><td><table class="lined" style="width: 1%;">\');
                }
              // ]]>
            </script>
            <div id="tab">
              <ul>
                <li><a href="char.php?id='.$id.'&amp;realm='.$realmid.'">'.$lang_char['char_sheet'].'</a></li>
                <li><a href="char_inv.php?id='.$id.'&amp;realm='.$realmid.'">'.$lang_char['inventory'].'</a></li>
                <li><a href="char_talent.php?id='.$id.'&amp;realm='.$realmid.'">'.$lang_char['talents'].'</a></li>
                <li><a href="char_achieve.php?id='.$id.'&amp;realm='.$realmid.'">'.$lang_char['achievements'].'</a></li>
                <li><a href="char_quest.php?id='.$id.'&amp;realm='.$realmid.'">'.$lang_char['quests'].'</a></li>
                <li id="selected"><a href="char_friends.php?id='.$id.'&amp;realm='.$realmid.'">'.$lang_char['friends'].'</a></li>
              </ul>
            </div>
            <div id="tab_content">
              <font class="bold">
                '.htmlentities($char['name']).' -
                <img src="img/c_icons/'.$char['race'].'-'.$char['gender'].'.gif"
                  onmousemove="toolTip(\''.char_get_race_name($char['race']).'\', \'item_tooltip\')" onmouseout="toolTip()" alt="" />
                <img src="img/c_icons/'.$char['class'].'.gif"
                  onmousemove="toolTip(\''.char_get_class_name($char['class']).'\', \'item_tooltip\')" onmouseout="toolTip()" alt="" /> - lvl '.char_get_level_color($char['level']).'
              </font>
              <br /><br />
              <table class="hidden"  style="width: 1%;">
                <tr valign="top">';
      //---------------Page Specific Starts Ends here----------------------------
      $query = $sqlc->query('SELECT a.messageType as messagetype, a.sender as sender,
        a.subject as subject, a.itemTextId as itemtextid, a.has_items as hasitems, a.money as money, a.cod as cod, a.checked as checked,
        b.item_template as itemtemplate
        FROM mail a INNER JOIN mail_items b ON a.id = b.mail_id where a.receiver = '.$id .'');

      $output .= '
                <td>
                  <table class="lined">
                    <tr>
                      <th width="5%">Mail Type</th>
                      <th width="10%">Sender</th>
                      <th width="15%">Subject</th>
                      <th width="5%">Has Items</th>
                      <th width="25%">Text</th>
                      <th width="20%">Money</th>
                      <th width="5%">Checked</th>
                    </tr>';
      while ($mail = $sqlc->fetch_assoc($query))
      {
        $output .= '
                    <tr valign=top>
                      <td>'.get_mail_source($mail['messagetype']).'</td>
                      <td><a href="char.php?id='.$mail['sender'].'">'.get_char_name($mail['sender']).'</a></td>
                      <td>'.$mail['subject'].'</td>
                      <td>
                        <a style="padding:2px;" href="'.$item_datasite.$mail['itemtemplate'].'" target="_blank">
                          <img class="bag_icon" src="'.get_item_icon($mail['itemtemplate'], $sqlm).'" alt="" />
                        </a>
                      </td>
                      <td>'.get_mail_text($mail['itemtextid']).'</td>
                      <td>
                        '.substr($mail['money'],  0, -4).'<img src="img/gold.gif" alt="" align="middle" />
                        '.substr($mail['money'], -4,  2).'<img src="img/silver.gif" alt="" align="middle" />
                        '.substr($mail['money'], -2).'<img src="img/copper.gif" alt="" align="middle" />
                      </td>
                      <td>'.get_check_state($mail['checked']).'</td>
                    </tr>';
      }
      $output .= '
                  </table>
                </td>';
      //---------------Page Specific Data Ends here----------------------------
      //---------------Character Tabs Footer-----------------------------------
      $output .= '
                </tr>
              </table>
            </div>
            <br />
            <table class="hidden">
              <tr>
                <td>';
                  // button to user account page, user account page has own security
                  makebutton($lang_char['chars_acc'], 'user.php?action=edit_user&amp;id='.$owner_acc_id.'', 130);
      $output .= '
                </td>
                <td>';

      // only higher level GM with delete access can edit character
      //  character edit allows removal of character items, so delete permission is needed
      if ( ($user_lvl > $owner_gmlvl) && ($user_lvl >= $action_permission['delete']) )
      {
                  makebutton($lang_char['edit_button'], 'char_edit.php?id='.$id.'&amp;realm='.$realmid.'', 130);
        $output .= '
                </td>
                <td>';
      }
      // only higher level GM with delete access, or character owner can delete character
      if ( ( ($user_lvl > $owner_gmlvl) && ($user_lvl >= $action_permission['delete']) ) || ($owner_name === $user_name) )
      {
                  makebutton($lang_char['del_char'], 'char_list.php?action=del_char_form&amp;check%5B%5D='.$id.'" type="wrn', 130);
        $output .= '
                </td>
                <td>';
      }
      // only GM with update permission can send mail, mail can send items, so update permission is needed
      if ($user_lvl >= $action_permission['update'])
      {
                  makebutton($lang_char['send_mail'], 'mail.php?type=ingame_mail&amp;to='.$char['name'].'', 130);
        $output .= '
                </td>
                <td>';
      }
                  makebutton($lang_global['back'], 'javascript:window.history.back()" type="def', 130);
      $output .= '
                </td>
              </tr>
            </table>
            <br />
          </center>
          <!-- end of char_friends.php -->';
    }
    else
      error($lang_char['no_permission']);
  }
  else
    error($lang_char['no_char_found']);

}


//########################################################################################################################
// MAIN
//########################################################################################################################

//$action = (isset($_GET['action'])) ? $_GET['action'] : NULL;

$lang_char = lang_char();

char_friends($sqlr, $sqlc);

//unset($action);
unset($action_permission);
unset($lang_char);

require_once 'footer.php';


?>
