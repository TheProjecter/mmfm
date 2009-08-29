<?php

require_once 'header.php';

function read()
{
  global $output, $debug;

  $output .= '
          <center>
            <form method="post" action="test.php?action=save" name="form">
              <table>
                <tr>
                  <td>
                    debug
                  </td>
                  <td>
                    <select name="setting[$debug]">
                      <option value="0" ';
                        if(0 == $debug) $output .= 'selected="selected"';
                        $output .= '>0</option>
                      <option value="1" ';
                        if(1 == $debug) $output .= 'selected="selected"';
                        $output .= '>1</option>
                      <option value="2" ';
                        if(2 == $debug) $output .= 'selected="selected"';
                        $output .= '>2</option>
                      <option value="3" ';
                        if(3 == $debug) $output .= 'selected="selected"';
                        $output .= '>3</option>
                      <option value="4" ';
                        if(4 == $debug) $output .= 'selected="selected"';
                        $output .= '>4</option>
                    </select>
                  </td>
                  <td>
                    <select name="setting[$debug1]">
                      <option value="0" ';
                        if(0 == $debug) $output .= 'selected="selected"';
                        $output .= '>0</option>
                      <option value="1" ';
                        if(1 == $debug) $output .= 'selected="selected"';
                        $output .= '>1</option>
                      <option value="2" ';
                        if(2 == $debug) $output .= 'selected="selected"';
                        $output .= '>2</option>
                      <option value="3" ';
                        if(3 == $debug) $output .= 'selected="selected"';
                        $output .= '>3</option>
                      <option value="4" ';
                        if(4 == $debug) $output .= 'selected="selected"';
                        $output .= '>4</option>
                    </select>
                  </td>
                </tr>
                <tr>
                  <td>';
                  makebutton('save', 'javascript:do_submit()', 130);
  $output .= '
                  </td>
                </tr>
              </table>
            </form>
          </center>';

}



function save(&$sqlr)
{
  global $output, $debug1;

  //if (empty($_POST['setting']))
  //  redirect('error.php?err=test');

  $setting =  $sqlr->quote_smart($_POST['setting']);

  foreach($setting as $a => $b)
  {
    $output .= $a.' '.$b.'<br />';
  }
}


$action = (isset($_GET['action'])) ? $_GET['action'] : NULL;

if ('save' == $action)
  save($sqlr);
else
  read();

unset($action);

require_once 'footer.php';


?>
