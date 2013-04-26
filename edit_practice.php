<?php
#  Copyright (C) 2012  Héctor J. Macho Pedroso <hjmacho@outlook.com>
#  This file is part of gPracticas.
#
#  gPracticas is free software: you can redistribute it and/or modify
#  it under the terms of the GNU General Public License as published by
#  the Free Software Foundation, either version 3 of the License, or
#  (at your option) any later version.
#
#  gPracticas is distributed in the hope that it will be useful,
#  but WITHOUT ANY WARRANTY; without even the implied warranty of
#  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#  GNU General Public License for more details.
#
#  You should have received a copy of the GNU General Public License
#  along with gPracticas.  If not, see <http://www.gnu.org/licenses/>

require_once('../../config.php');
require_once("$CFG->dirroot/blocks/gpracticas/lib.php");

global $USER;

//recuperamos los datos
$courseid = required_param('courseid',PARAM_INT);
$examinationsession = optional_param('examinationsession',PARAM_TEXT);
$academiccourse = optional_param('academiccourse',PARAM_TEXT);
$action = required_param('action',PARAM_TEXT);
$practiceid=optional_param('practiceid',0,PARAM_INT);

function print_form($text,$courseid,$examinationsession,$academiccourse,$practice,$practiceid){
    echo' <H2>'.get_string($text,'block_gpracticas').'</H2><BR>
          <FORM name="'.$text.'" ACTION="update_practice.php" method="POST">
              <INPUT type="hidden" name="courseid" value="'.$courseid.'">
              <INPUT type="hidden" name="practiceid" value="'.$practiceid.'">
              <INPUT type="hidden" name="examinationsession" value="'.$examinationsession.'">
              <INPUT type="hidden" name="academiccourse" value="'.$academiccourse.'">
              <INPUT type="hidden" name="sesskey" value="'.sesskey().'">
              <INPUT type="hidden" name="idcalendar" value="'.$practice->idcalendar.'">
              <TABLE>
                  <TR><TD align=left valign=top>'.get_string('practice_name','block_gpracticas').':</TD>
                      <TD align=left valign=top><INPUT type=text name="name" value="'.$practice->name.'"></TD>
                  </TR>
                  <TR>
                      <TD align=left valign=top>'.get_string('description','block_gpracticas').':</TD>
                      <TD align=left valign=top>';
    print_textarea (true, 10, 50, 0, 0, 'description', $practice->description);
    echo '</TD>
      </TR>
          <TD align=left valign=top>'.get_string('endingdate','block_gpracticas').':</TD>
          <TD align=left valign=top>';
    if (empty($practice->ending_date)) {
        echo print_date_selector('day','month','year', date(),true).print_time_selector('hour','minute', date(),1,true);
        $practice->flexible = 0;
        $practice->visible = 1;
        $practice->autounzip = 1;
        $practice->reopen = 1;
    } else {
        echo print_date_selector('day','month','year', strtotime($practice->ending_date),true).print_time_selector('hour','minute', strtotime($practice->ending_date),1,true);
    }
    echo '</TD>
        </TR>
        <TR>
            <TD align=left valign=top>'.get_string('flexible_description','block_gpracticas').'</TD>
            <TD align=left valign=top><INPUT type="checkbox" name="flexible" value="1" '.int_to_check($practice->flexible).'></TD>
        </TR>
        <TR>
            <TD align=left valign=top>'.get_string('visible_description','block_gpracticas').'</TD>
            <TD align=left valign=top><INPUT type="checkbox" name="visible" value="1" '.int_to_check($practice->visible).'></TD>
        </TR>
        <TR>
            <TD align=left valign=top>'.get_string('unzip_description','block_gpracticas').'</TD>
            <TD align=left valign=top><INPUT type="checkbox" name="autounzip" value="1" '.int_to_check($practice->autounzip).'></TD>
        </TR>
		<TR>
            <TD align=left valign=top>'.get_string('reopen_description','block_gpracticas').'</TD>
            <TD align=left valign=top><INPUT type="checkbox" name="reopen" value="1" '.int_to_check($practice->reopen).'></TD>
        </TR>
        <TR>
            <TD colspan=2 align=center valign=top>
                <INPUT  type="submit" value="'.get_string('save_changes','block_gpracticas').'">
            </TD>
        </TR>
        </TABLE></FORM>';
    use_html_editor(); 

}


//comprobamos que el usuario está identificado en el sistema y pertenece al curso
require_login($COURSE->id);
$course = get_record("course", "id", $courseid) or die(get_string('err_read_db','block_gpracticas'));
if ($practiceid != 0) {
    $practice = get_record('gpracticas_practices', 'id', $practiceid) or die(get_string('err_read_db','block_gpracticas'));
    $text = $practice->name;
} else {
    $text = get_string('newpractice','block_gpracticas').' ('.$examinationsession.' '.$academiccourse.')';
}
print_header_simple($text,'',$text,'','',false);
$context = get_context_instance(CONTEXT_COURSE,$courseid);
//comprobamos si es un profesor y si la sesskey es válida
if (has_capability('block/gpracticas:teacher', $context) && confirm_sesskey()) {
    if ($action == 'addpractice') {
        print_form ($action,$courseid,$examinationsession,$academiccourse,'',$practiceid);
    } else {
//comprobamos si la práctica corresponde al curso
        if ($courseid == $practice->subject) {
            print_form ($action,$courseid,$practice->examinationsession,$practice->academiccourse,$practice,$practiceid);
        } else {
            echo get_string('no_auth_edit_practice','block_gpracticas');
        }
    }
} else {
    if ($action == 'addpractice') {
        echo get_string('no_auth_add_practice','block_gpracticas');
    } else {
        echo get_string('no_auth_edit_practice','block_gpracticas');
    }
}
print_footer($course);

?> 
