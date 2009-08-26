<?php

require_once 'header.php';
require_once 'libs/bbcode_lib.php';
function main(&$sqlr, &$sqlc)
{
  global $output, $lang_login,
  $characters_db, $server,
  $remember_me_checked, $motd_display_poster;

  $output .= '
          <center>
            <script type="text/javascript" src="libs/js/sha1.js"></script>
            <script type="text/javascript">
              // <![CDATA[
                function dologin ()
                {
                  document.form.pass.value = hex_sha1(document.form.user.value.toUpperCase()+":"+document.form.login_pass.value.toUpperCase());
                  document.form.login_pass.value = "0";
                  do_submit();
                }
              // ]]>
            </script>
            <table class="hidden" style="width: 100%">
              <tr>
                <td valign="top" width="25%">
                  <table  class="lined" style="width: 100%">
                      <th align="left" width="40%">
                          <div id="divlogin" onclick="expand(\'login\', this);">[-]</div>
                      </th>
                      <th align="left">
                        Login
                      </th>
                    </tr>
                    <tr>
                      <td colspan="2"valign="top">
                          <form method="post" action="login.php?action=dologin" name="form" onsubmit="return dologin()">
                            <input type="hidden" name="pass" value="" maxlength="256" />
                            <table id="login" style="width: 100%; display: table">
                              <tr align="right">
                                <td>'.$lang_login['username'].' : <input type="text" name="user" size="12" maxlength="16" /></td>
                              </tr>
                              <tr align="right">
                                <td>'.$lang_login['password'].' : <input type="password" name="login_pass" size="12" maxlength="40" /></td>
                              </tr>';
  $result = $sqlr->query('SELECT id, name FROM realmlist LIMIT 10');

  if ($sqlr->num_rows($result) > 1 && (count($server) > 1) && (count($characters_db) > 1))
  {
    $output .= '
                              <tr align="right">
                                <td>'.$lang_login['select_realm'].' :
                                  <select name="realm">';
    while ($realm = $sqlr->fetch_assoc($result))
    if(isset($server[$realm['id']]))
      $output .= '
                                    <option value="'.$realm['id'].'">'.htmlentities($realm['name']).'</option>';
    $output .= '
                                  </select>
                                </td>
                              </tr>';
  }
  else
    $output .= '
                              <input type="hidden" name="realm" value="'.$sqlr->result($result, 0, 'id').'" />';
  $output .= '
                              <tr align="right">
                                <td>'.$lang_login['remember_me'].' : <input type="checkbox" name="remember" value="1"';
  if ($remember_me_checked)
    $output .= ' checked="checked"';
  $output .= ' /></td>
                              </tr>
                              <input type="submit" value="" style="display: none" />
                              <tr align="right">
                                <td>';
                                  makebutton($lang_login['not_registrated'], 'register.php" type="wrn', 80);
                                  makebutton($lang_login['login'], 'javascript:dologin()" type="def', 80);
  $output .= '
                                </td>
                              </tr>
                              <tr align="right">
                                <td>
                                  <a href="register.php?action=pass_recovery">'.$lang_login['pass_recovery'].'</a>
                                </td>
                              </tr>
                            </table>
                            <script type="text/javascript">
                              // <![CDATA[
                                document.form.user.focus();
                              // ]]>
                            </script>
                          </form>
                      </td>
                    <tr>
                  </table>
                </td>
                <td>
                </td>
                <td valign="top" width="50%">
                  <table class="lined" style="width: 100%">
                      <th align="left" width="40%">
                          <div id="divMOTD" onclick="expand(\'MOTD\', this);">[-]</div>
                      </th>
                      <th align="left">
                        MOTD
                      </th>
                    </tr>
                    <tr>
                      <td colspan="2"valign="top">';
  $all_record_m = $sqlc->result($sqlc->query('SELECT count(*) FROM bugreport'), 0);
  $output .= '
                        <table id="MOTD" class="lined" style="width: 100%; display: table">';
  if($all_record_m)
  {
    $result = $sqlc->query('SELECT id, type, content FROM bugreport ORDER BY id DESC LIMIT 0, 3');
    while($post = $sqlc->fetch_assoc($result))
    {
      $output .= '
                          <tr>
                            <td align="left" class="large">
                              <blockquote>'.bbcode_bbc2html($post['content']).'</blockquote>
                            </td>
                          </tr>
                          <tr>
                            <td align="right">';
      ($motd_display_poster) ? $output .= $post['type'] : '';
      $output .= '
                            </td>
                          </tr>
                          <tr>
                            <td class="hidden"></td>
                          </tr>';
    }
  }
  $output .= '
                        </table>
                      </td>
                    <tr>
                  </table>
                </td>
                <td>
                </td>
                <td valign="top" width="25%">
                  <table class="lined" style="width: 100%">
                    <tr>
                      <th align="left" width="40%">
                          <div id="divstats" onclick="expand(\'stats\', this);">[-]</div>
                      </th>
                      <th align="left">
                        Stats
                      </th>
                    </tr>
                    <tr>
                      <td colspan="2"valign="top">
                        <table id="stats" class="lined" style="width: 100%; display: table">
                          <tr>
                            <td align="right">
                              Total Accounts:
                            </td>
                            <td align="left">
                              '.$sqlr->result($sqlr->query('SELECT count(*) FROM account'), 0).'
                            </td>
                          </tr>
                          <tr>
                            <td align="right">
                              Total GMs:
                            </td>
                            <td align="left">
                              '.$sqlr->result($sqlr->query('SELECT count(*) FROM account WHERE gmlevel > 0'), 0).'
                            </td>
                          </tr>';
  $realms = $sqlr->query('SELECT id, name FROM realmlist');
  if  ( 1 < $sqlr->num_rows($realms) && (1 < count($server)) && (1 < count($characters_db)) )
  {
    while ($realm = $sqlr->fetch_assoc($realms))
    {
      $sqlc->connect($characters_db[$realm['id']]['addr'], $characters_db[$realm['id']]['user'], $characters_db[$realm['id']]['pass'], $characters_db[$realm['id']]['name']);
      $output .= '
                          <tr>
                            <th colspan="2" align="left">
                              Realm '.$realm['name'].'
                            </th>
                          </tr>
                          <tr>
                            <td align="right">
                              Total Chars:
                            </td>
                            <td align="left">'.$sqlc->result($sqlc->query('SELECT count(*) FROM characters'), 0).'</td>
                          </tr>
                          <tr>
                            <td align="right">
                              Total Online:
                            </td>
                            <td align="left">'.$sqlc->result($sqlc->query('SELECT count(*) FROM characters where online = \'1\''), 0).'</td>
                          </tr>';
    }
    unset($realm);
  }
  else
  {
    $realm = $sqlr->fetch_assoc($realms);
    $output .= '
                          <tr>
                            <th colspan="2" align="left">
                              Realm '.$realm['name'].'
                            </th>
                          </tr>
                          <tr>
                            <td align="right">
                              Total Chars:
                            </td>
                            <td align="left">'.$sqlc->result($sqlc->query('SELECT count(*) FROM characters'), 0).'</td>
                          </tr>
                          <tr>
                            <td align="right">
                              Total Online:
                            </td>
                            <td align="left">'.$sqlc->result($sqlc->query('SELECT count(*) FROM characters where online = \'1\''), 0).'</td>
                          </tr>';
  }
  $output .= '
                        </table>
                      </td>
                    <tr>
                  </table>
                </td>
              <tr>
            </table>
          </center>';
}

$lang_login = lang_login();

main($sqlr, $sqlc);

unset($lang_login);

require_once 'footer.php';


?>
