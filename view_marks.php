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

global $USER;

require_once('../../config.php');
require_once("$CFG->dirroot/blocks/gpracticas/lib.php");
   
define('COLOUR','#E6E6E6');
$courseid = required_param('courseid',PARAM_INT);
$examinationsession = required_param('examinationsession',PARAM_CLEAN);
$academiccourse= required_param('academiccourse',PARAM_CLEAN);

//comprobamos que el usuario está identificado en el sistema y pertenece al curso
require_login($courseid);
print_header_simple(get_string('marks','block_gpracticas').' '.$examinationsession.' '.$academiccourse,'',get_string('marks','block_gpracticas').' '.$examinationsession.' '.$academiccourse,'','',false);
$context = get_context_instance(CONTEXT_COURSE,$courseid);
$course = get_record('course', 'id', $courseid) or die(get_string('err_read_db','block_gpracticas'));
//comprobamos si es un alumno, si la práctica corresponde al curso y si la sesskey es válida
if (has_capability('block/gpracticas:student', $context) && confirm_sesskey()) {
    $select = "subject=$courseid AND examinationsession='$examinationsession' AND academiccourse=$academiccourse";
    $data = get_records_select('gpracticas_practices',$select,'id ASC');
    echo '<H2>'.get_string('marks','block_gpracticas').' '.$examinationsession.' '.$academiccourse.'</H2>';
    $colour='';
    if (empty($data)) {
        echo get_string('not_practices_yet','block_gpracticas');      
    } else {
        echo '<TABLE>
            <TR>
            <TD align=center valign=top><b>'.get_string('practice','block_gpracticas').'</b></TD><TD width="10px"></TD>
            <TD align=center valign=top><b>'.get_string('mark','block_gpracticas').'</b></TD><TD></TD>
            <TD align=center valign=top><b>'.get_string('comments','block_gpracticas').'</b></TD></TR>';
        foreach ($data as $value) {
            $practice=new Practice($value);
            $auxdelivery=get_record('gpracticas_deliveries', 'practice', $practice->get_id(), 'student', $USER->id);
            echo '<TR  bgcolor='.$colour.'><TD>'.$practice->get_name().'</TD><TD></TD>';
            if (empty($auxdelivery)) {
                echo '<TD align=center valign=top> - </TD>';
                echo '<TD width="10px"></TD><TD align=center valign=top></TD></TR>';
            } else {
                $delivery=new Delivery($auxdelivery);
                echo '<TD align=center valign=top>'.$delivery->get_mark().'</TD>';
                echo '<TD width="10px"></TD><TD align=center valign=top>'.$delivery->get_student_comments().'</TD></TR>';
            }           
            if (empty($colour)) {
                $colour=COLOUR;
            } else {
                $colour='';
            }
        }
        echo '</TABLE>';
    }
} else {
    echo get_string('no_auth_see_marks','block_gpracticas');
}
print_footer($course);
?>
