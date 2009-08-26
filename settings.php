<?php

  require_once 'header.php';
  require_once 'libs/config_lib.php';

  $sqlm = new SQL;
  $sqlm->connect($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass'], $mmfpm_db['name']);

  $query = $sqlm->query('SELECT setting, value FROM mm_settings');

  $output .= '
          <center>
            <table style="width: 550px; text-align: left;" class="lined">';
  while($data = $sqlm->fetch_assoc($query))
  {
    $output .= '
              <tr>
                <td>
                  '.$data['setting'].'
                </td>
                <td>
                  '.$data['value'].'
                </td>';
  }

  $output .= '
            </table>
          </center>';



  require_once 'footer.php';

?>
