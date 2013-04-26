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

$examinationsession = required_param('examinationsession',PARAM_TEXT);
$academiccourse =required_param('academiccourse',PARAM_TEXT);
$courseid = required_param('courseid',PARAM_INT);

//comprobamos que el usuario está identificado en el sistema y pertenece al curso
require_login($courseid);
$course = get_record("course", "id", $courseid) or die(mysql_error());
print_header_simple(get_string('del_all_practices','block_gpracticas').": $examinationsession $academiccourse",'',get_string('del_all_practices','block_gpracticas').": $examinationsession $academiccourse",'','',false);
$context = get_context_instance(CONTEXT_COURSE,$courseid);

//comprobamos si es un profesor y si la sesskey es válida
if (has_capability('block/gpracticas:teacher', $context) && confirm_sesskey()) {
    $select = "subject=$courseid AND examinationsession='$examinationsession' AND academiccourse='$academiccourse'";
    $practices = get_records_select('gpracticas_practices',$select) or die(get_string('err_read_db','block_gpracticas'));
    foreach ($practices as $data) {
        $practice = new Practice($data);
        if ($practice->get_subject() == $courseid) { //comprobamos si la práctica pertenece a ese curso
            $dir=get_route($courseid,$practice->get_id());
            delete_records('event','id',$practice->get_idcalendar()) or die(get_string('err_del_event_db','block_gpracticas'));
            delete_records('gpracticas_deliveries','practice',$practice->get_id()) or die(get_string('err_del_deliveries_db','block_gpracticas'));
            delete_records('gpracticas_practices','id',$practice->get_id()) or die(get_string('err_del_practices_db','block_gpracticas'));
            rmdir($dir);//borramos los directorios de las prácticas en caso de que estén vacíos
        } else {
            echo get_string('no_auth_del_practice','block_gpracticas').': '.$practice->get_name();
        }
    }
    rmdir($CFG->dataroot.'/'.$courseid.'/'.clean_filename($examinationsession).'_'.clean_filename($academiccourse));//borramos el directorio raíz de las prácticas en caso de que esté vacío
    echo get_string('practices_del_ok','block_gpracticas'); 
} else {
    echo get_string('no_auth_del_practice','block_gpracticas');
}
print_footer($course);

?>
