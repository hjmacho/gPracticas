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

$courseid = required_param('courseid',PARAM_INT);
$practiceid=required_param('practiceid',PARAM_INT);
//comprobamos que el usuario está identificado en el sistema y pertenece al curso
require_login($courseid);
$course = get_record("course", "id", $courseid) or die(get_string('err_read_db','block_gpracticas'));
$auxpractice = get_record('gpracticas_practices', 'id', $practiceid) or die(get_string('err_read_db','block_gpracticas'));
$practice = new Practice($auxpractice);
$course = get_record("course", "id", $courseid) or die(get_string('err_read_db','block_gpracticas'));
print_header_simple($practice->get_name(),'',$practice->get_name(),'','',false);
$context = get_context_instance(CONTEXT_COURSE,$courseid);
//comprobamos si es un profesor, si la práctica corresponde al curso y si la sesskey es válida
if (has_capability('block/gpracticas:teacher', $context) && ($courseid == $practice->get_subject()) && confirm_sesskey()) {
    $numrows=$_POST['numrows'];
    for ($i=1; $i<$numrows; $i++) {
        if (($_POST['oldmark'.$i]!=$_POST['mark'.$i]) OR ($_POST['oldcomentary'.$i]!=$_POST['comments'.$i]) OR ($_POST['oldcomentarystudent'.$i]!=$_POST['student_comments'.$i])) {
            $data=get_record('gpracticas_deliveries','id',$_POST['id'.$i]) or die(get_string('err_read_db','block_gpracticas'));
            $delivery = new Delivery($data);
            $delivery->set_mark($_POST['mark'.$i]);
            $delivery->set_comments($_POST['comments'.$i]);
            $delivery->set_student_comments($_POST['student_comments'.$i]);
            $delivery->update();
        }       
    }
    echo get_string('add_marks_ok','block_gpracticas');
} else {
    echo get_string('no_auth_mark_delivery','block_gpracticas');
}
print_footer($course);
?>
