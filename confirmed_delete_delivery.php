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
   
require_once("../../config.php");
require_once("$CFG->dirroot/blocks/gpracticas/lib.php");
require_once("$CFG->dirroot/lib/filelib.php");

 
$courseid=required_param('courseid',PARAM_INT);
$practiceid=required_param('practiceid',PARAM_INT);
$deliveryid=required_param('deliveryid',PARAM_INT);
global $USER;

//comprobamos que el usuario está identificado en el sistema y pertenece al curso
require_login($courseid);
$auxpractice = get_record('gpracticas_practices', 'id', $practiceid) or die(get_string('err_read_db','block_gpracticas'));
$practice = new Practice($auxpractice);
$course = get_record('course', 'id', $courseid) or die(get_string('err_read_db','block_gpracticas'));
$context = get_context_instance(CONTEXT_COURSE,$courseid);
//comprobamos si es un alumno, si la práctica corresponde al curso y si la sesskey es válida
if (has_capability('block/gpracticas:student', $context) && confirm_sesskey() && ($courseid == $practice->get_subject())) {         
    if ($deliveryid==0) { //si la práctica no figura como entregada/cerrada
        fulldelete(get_route($courseid,$practiceid,$USER->id));
        $url= $CFG->wwwroot.'/blocks/gpracticas/delivery.php?courseid='.$courseid.'&practiceid='.$practiceid.'&sesskey='.sesskey();
        header ("Location: $url");
    } else { //si la práctica figura como entregada/cerrada
        $auxdelivery = get_record('gpracticas_deliveries', 'id', $deliveryid) or die(get_string('err_read_db','block_gpracticas'));
        $delivery = new Delivery($auxdelivery);
        $userid=$delivery->get_student();
        if ($USER->id==$userid) {    
            fulldelete(get_route($courseid,$practiceid,$USER->id));
            delete_records('gpracticas_deliveries', 'id', $deliveryid) or die(mysql_error());
            $url= $CFG->wwwroot.'/blocks/gpracticas/delivery.php?courseid='.$courseid.'&practiceid='.$practiceid.'&sesskey='.sesskey();
            header ("Location: $url");
        }
    }
}

print_header_simple($practice->get_name(),'',$practice->get_name(),'','',false);
echo get_string('no_auth_del_delivery','block_gpracticas');

print_footer($course);

?>
