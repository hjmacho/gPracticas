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

global $CFG;
$courseid = required_param('courseid',PARAM_INT);
$practiceid=required_param('practiceid',PARAM_INT);

//crea la ruta del directorio de entrega por parte del alumno para que el profesor acceda a él mediante el gestor de ficheros de Moodle
function create_route($student,$practiceid) {
    global $COURSE;
    global $CFG;
    $auxpractice=get_record('gpracticas_practices', 'id', $practiceid) or die(get_string('err_read_db','block_gpracticas'));
    $practice=new Practice($auxpractice);
    $route=$CFG->wwwroot.'/files/index.php?id='.$COURSE->id.'&wdir=/'.clean_filename($practice->get_examinationsession()).'_'.clean_filename($practice->get_academiccourse()).'/'.$practiceid.'_'.$practice->get_name().'/'.create_dir_delivery($student->id,$student->username,$practice->get_security_key()).'&choose=';
    return $route;
}
//comprobamos que el usuario está identificado en el sistema y pertenece al curso
require_login($courseid);
$auxpractice = get_record('gpracticas_practices', 'id', $practiceid) or die(get_string('err_read_db','block_gpracticas'));
$practice = new Practice($auxpractice);
$course = get_record("course", "id", $courseid) or die(get_string('err_read_db','block_gpracticas'));
print_header_simple(get_string('deliveries','block_gpracticas').' '.$practice->get_name(),'',get_string('deliveries','block_gpracticas').' '.$practice->get_name(),'','',false);
$context = get_context_instance(CONTEXT_COURSE,$courseid);
//comprobamos si es un profesor, si la práctica corresponde al curso y si la sesskey es válida
if (has_capability('block/gpracticas:teacher', $context) && ($courseid == $practice->get_subject()) && confirm_sesskey()) {
    $data=get_records('gpracticas_deliveries','practice',$practiceid);
    if (empty($data)) {
        echo get_string('no_deliveries','block_gpracticas');
    } else {
        $numdata=count($data);
        echo '<H2>'.get_string('deliveries_of_practice','block_gpracticas').' '.$practice->get_name().'</H2><BR>
               <FORM name="grade_practice" ACTION="grade_practice.php" method = "POST">
               <INPUT type="hidden" name="courseid" value="'.$courseid.'">
               <INPUT type="hidden" name="practiceid" value="'.$practiceid.'">
               <INPUT type="hidden" name="sesskey" value="'.sesskey().'">
               <TABLE>
               <TR>
                  <TD align=center valign=top><b>'.get_string('user_id','block_gpracticas').'</b></TD><TD></TD><TD></TD>
                  <TD align=center valign=top><b>'.get_string('user','block_gpracticas').'</b></TD><TD></TD><TD></TD>
                  <TD align=center valign=top><b>'.get_string('user_name','block_gpracticas').'</b></TD><TD></TD><TD></TD>
                  <TD align=center valign=top><b>'.get_string('delivery_date','block_gpracticas').'</b></TD><TD></TD><TD></TD>
                  <TD align=center valign=top><b>'.get_string('in_closing_date','block_gpracticas').'</b></TD><TD></TD><TD></TD>
                  <TD align=center valign=top><b>'.get_string('comments','block_gpracticas').'</b></TD><TD></TD><TD></TD>
                  <TD align=center valign=top><b>'.get_string('student_comments','block_gpracticas').'</b></TD><TD></TD><TD></TD>
                  <TD align=center valign=top><b>'.get_string('mark','block_gpracticas').'</b></TD></TD><TD></TD><TD></TD>
                  <TD align=center valign=top><b>'.get_string('see_delivery','block_gpracticas').'</b></TD>
               </TR>';
        $i=1;
        foreach ($data as $value) {
            $delivery = new Delivery($value);
            $student=get_record('user','id',$delivery->get_student()); 
            echo '<INPUT type="hidden" name="id'.$i.'" value="'.$delivery->get_id().'">
                  <INPUT type="hidden" name="oldmark'.$i.'" value="'.$delivery->get_mark().'">
                  <INPUT type="hidden" name="oldcomentary'.$i.'" value="'.$delivery->get_comments().'">
                  <INPUT type="hidden" name="oldcomentarystudent'.$i.'" value="'.$delivery->get_student_comments().'">
                  <TR>
                  <TD align=center valign=top>'.$student->id.'</TD><TD></TD><TD></TD>
                  <TD align=center valign=top>'.$student->username.'</TD><TD></TD><TD></TD>
                  <TD align=center valign=top>'.$student->firstname.' '.$student->lastname.'</TD><TD></TD><TD></TD>
                  <TD align=center valign=top>'.date("d/m/y H:i",strtotime($delivery->get_delivery_date())).'</TD><TD></TD><TD></TD>';
            if ($delivery->get_in_closing_date()==1) {
                echo '<TD align=center valign=top>'.get_string('yes','block_gpracticas').'</TD><TD></TD><TD></TD>';
            } else {
                echo '<TD align=center valign=top>'.get_string('no','block_gpracticas').'</TD><TD></TD><TD></TD>';
            }
                echo '<TD align=left valign=top><TEXTAREA NAME="comments'.$i.'" COLS="30" ROWS="2">'.$delivery->get_comments().'</TEXTAREA></TD></TD><TD></TD><TD></TD><TD align=left valign=top><TEXTAREA NAME="student_comments'.$i.'" COLS="30" ROWS="2">'.$delivery->get_student_comments().'</TEXTAREA></TD></TD><TD></TD><TD></TD><TD align=center valign=top><INPUT type=text name="mark'.$i.'" value="'.$delivery->get_mark().'"></TD></TD><TD></TD><TD></TD><TD align=center valign=top><a title="'.get_string('see_files','block_gpracticas').'" href="'.create_route($student,$practiceid).'"><img src="'.$CFG->wwwroot.'/pix/i/files.gif" alt="Ver ficheros" ></a></TD></TR>';
            $i++;  
        }
        echo '<TR>
               <INPUT type="hidden" name="numrows" value="'.$i.'">
               <TD colspan=16 align=center valign=top>
                 <BR><BR><INPUT  type="submit" value="'.get_string('save_changes','block_gpracticas').'">
               </TD>
               </TR>               
               </TABLE></FORM>';
         
    }
} else {
    echo get_string('no_auth_see_delivery','block_gpracticas');
}
print_footer($course);
?>
