<?php


require_once 'header.php';
require_once 'libs/telnet_lib.php';
//valid_login($action_permission['insert']);

function main()
{
  global $output, $lang_global;

  $output .= '
          <div class="top"><h1>Send System Message/Global Notification</h1></div>
          <center>
            <form action="message.php?action=send" method="post" name="form">
              <table class="top_hidden">
                <tr>
                  <td align="center">
                    Send :
                    <select name="type">
                      <option value="1" selected="selected">Announcement</option>
                      <option value="2">Notification</option>
                      <option value="3">Both</option>
                    </select>
                  </td>
                </tr>
                <tr>
                  <td colspan="2" align="center">
                    <textarea id="msg" name="msg" rows="26" cols="80"></textarea>
                  </td>
                </tr>
                <tr>
                  <td align="center">
                    <table align="center" class="hidden"
                      <tr>
                        <td>';
                          makebutton('Send', 'javascript:do_submit()" type="wrn', 130);
  $output .= '
                        </td>
                        <td>';
                          makebutton($lang_global['back'], 'javascript:window.history.back()" type="def', 130);
  $output .= '
                        </td>
                      </tr>
                    </table>
                  </td>
                </tr>
              </table>
            </form>
          </center>';
}


function check()
{
  global $output, $realm_id, $server;

  $telnet = new telnet_lib();
  $result = $telnet->Connect($server[$realm_id]['addr'], $server[$realm_id]['telnet_port'], $server[$realm_id]['telnet_user'], $server[$realm_id]['telnet_pass']);
  if (0 == $result)
  {
    $telnet->Disconnect();
    redirect('message.php?action=main');
  }
  elseif (1 == $result)
    $mess_str = 'Connect failed: Unable to open network connection, please check your config.';
  elseif (2 == $result)
    $mess_str = 'Connect failed: Unknown host, please check your config.';
  elseif (3 == $result)
    $mess_str = 'Connect failed: Login failed, please check your config.';
  elseif (4 == $result)
    $mess_str = 'Connect failed: Your PHP version does not support PHP Telnet, please check your config.';

  unset($result);
  unset($telnet);

  redirect('message.php?action=result&mess='.$mess_str.'');
}


function send(&$sqlc)
{
  global $realm_id, $server;

  if (empty($_POST['msg'])) redirect('message.php?action=result&mess=Empty Fields');

  $type = (isset($_POST['type'])) ? $sqlc->quote_smart($_POST['type']) : 3;
  if (is_numeric($type)); else $type = 3;

  $msg = $sqlc->quote_smart($_POST['msg']);
  if (4096 < strlen($msg))
    redirect('message.php?action=result&mess=Message too long');

  $telnet = new telnet_lib();
  $result = $telnet->Connect($server[$realm_id]['addr'], $server[$realm_id]['telnet_port'], $server[$realm_id]['telnet_user'], $server[$realm_id]['telnet_pass']);
  if (0 == $result)
  {
    $mess_str = '';
    if ( 2 == $type);
    else
    {
      $telnet->DoCommand('announce '.$msg, $result);
      $mess_str .= 'System Message: "'.$msg.'" sent.';
    }
    if ( 3 == $type)
      $mess_str .= '<br /><br />';
    if ( 1 == $type);
    else
    {
      $telnet->DoCommand('notify '.$msg, $result);
      $mess_str .= 'Global Notify: "'.$msg.'" sent.';
    }
    $telnet->Disconnect();
  }
  elseif (1 == $result)
    $mess_str = 'Connect failed: Unable to open network connection, please check your config.';
  elseif (2 == $result)
    $mess_str = 'Connect failed: Unknown host, please check your config.';
  elseif (3 == $result)
    $mess_str = 'Connect failed: Login failed, please check your config.';
  elseif (4 == $result)
    $mess_str = 'Connect failed: Your PHP version does not support PHP Telnet, please check your config.';

  unset($result);
  unset($telnet);
  unset($type);
  unset($msg);

  redirect('message.php?action=result&mess='.$mess_str.'');
}


function result()
{
  global $lang_global, $output;
  $mess = (isset($_GET['mess'])) ? $_GET['mess'] : NULL;

  $output .= '
        <div class="top"><h1>Message Result</h1></div>
        <center>
          <table class="top_hidden" width="400">
            <tr>
              <td align="center">
                <br />'.$mess.'<br /><br />';
  unset($mess);
  $output .= '
              </td>
            </tr>
            <tr>
              <td align="center">
                <table align="center" class="hidden">
                  <tr>
                    <td>';
                      makebutton($lang_global['back'], 'javascript:window.history.back()', 130);
  $output .= '
                    </td>
                  </tr>
                </table>
              </td>
            </tr>
          </table>
        </center>';
}

$action = (isset($_GET['action'])) ? $_GET['action'] : NULL;

//$lang_message = lang_message();

if ('send' == $action)
  send($sqlc);
elseif ('result' == $action)
  result();
elseif ('main' == $action)
  main();
else
  check();

unset($action);
unset($action_permission);
//unset($lang_message);

require_once 'footer.php';


?>
