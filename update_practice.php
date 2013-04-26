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
//se encarga de convertir las fechas de un dígito en 2 dígitos
function convert_date($date) {
    if (($date >= 1) && ($date <= 9)) {
        $result = '0'.$date;
        return $result;
    } else {
        return $date;
    }
}
//genera la ruta para acceder al directorio de la práctica por parte del profesor usando el gesto de ficheros de Moodle
function practice_route($practice) {
global $CFG;
    $dir = $CFG->wwwroot.'/files/index.php?id='.$practice->get_subject().'&wdir='.clean_filename($practice->get_examinationsession()).'_'.clean_filename($practice->get_academiccourse()).'/'.$practice->get_id().'_'.clean_filename($practice->get_name());
    return $dir;
}

function edit_event($id,$name,$courseid,$userid,$timeend,$visible) {
    $event = new stdClass;
    $event->id = $id;
    $event->name = addslashes(get_string('end_date_delivery','block_gpracticas').' '.$name);
    $event->description = '';
    $event->format = 1;
    $event->courseid = $courseid;
    $event->groupid = 0;
    $event->userid = $userid;
    $event->repeatid = 0;
    $event->modulename = '';
    $event->instance = 0;
    $event->eventtype = '';
    $event->timestart = strtotime($timeend);
    $event->timeduration = 0;
    $event->visible = $visible;
    $event->uuid = '';
    $event->sequence = 1;
    $event->timemodified = time();
    return $event;
}

//recuperamos los datos
$courseid = required_param('courseid',PARAM_INT);
$practiceid = optional_param('practiceid',0,PARAM_INT);
$idcalendar = optional_param('idcalendar',0,PARAM_INT);
$year = required_param('year',PARAM_INT);
//si los meses y los días no están en formato de 2 dígitos falla al ordenar las prácticas por fecha, por lo que hay que usar la función convert_date
$month = convert_date(required_param('month',PARAM_INT));
$day = convert_date(required_param('day',PARAM_INT));
$hour = required_param('hour',PARAM_INT);
$minute = required_param('minute',PARAM_INT);
$visible = optional_param('visible',0,PARAM_INT);
$flexible = optional_param('flexible',0,PARAM_INT);
$name = required_param('name',PARAM_TEXT);
$description = required_param('description',PARAM_TEXT);
$academiccourse = required_param('academiccourse',PARAM_TEXT);
$examinationsession = required_param('examinationsession',PARAM_TEXT);
$autounzip = optional_param('autounzip',0,PARAM_INT);
$reopen = optional_param('reopen',0,PARAM_INT);

//comprobamos que el usuario está identificado en el sistema y pertenece al curso
require_login($courseid);
$course = get_record('course', 'id', $courseid) or die(get_string('err_read_db','block_gpracticas'));
print_header_simple($name,'',$name,'','',false);
$context = get_context_instance(CONTEXT_COURSE,$courseid);
//comprobamos si es un profesor y si la sesskey es válida
if (has_capability('block/gpracticas:teacher',$context) && confirm_sesskey()) {
    if ($practiceid != 0) {
        $ending_date = $year.'-'.$month.'-'.$day.' '.$hour.':'.$minute; 
        $event = edit_event($idcalendar, $name, $courseid, $USER->id, $ending_date, $visible);
        $security_key = get_field('gpracticas_practices','security_key','id',$practiceid) or die(get_string('err_read_db','block_gpracticas'));
        $practice = new Practice($practiceid,$courseid,stripslashes($name),stripslashes($academiccourse),stripslashes($examinationsession),$flexible,$ending_date,$visible,stripslashes($description),$idcalendar,$security_key,$autounzip,$reopen); 
        update_record('event', $event)  or die(get_string('err_update_event_db','block_gpracticas'));
        $practice->update_practice();
        echo get_string('update_bd_ok','block_gpracticas');
    } else {
        $ending_date = $year.'-'.$month.'-'.$day.' '.$hour.':'.$minute;
        $event = edit_event(0, $name, $courseid, $USER->id, $ending_date, $visible);
        $idcalendar = insert_record('event',$event,'true','id') or die(get_string('err_add_event_db','block_gpracticas'));
        $security_key = substr(md5(rand()),0,8);
        $practice = new Practice($practiceid,$courseid,$name,$academiccourse,$examinationsession,$flexible,$ending_date,$visible,$description,$idcalendar,$security_key,$autounzip,$reopen);  
        $practiceid = $practice->insert_practice();
        $route = get_route($courseid,$practiceid);
        check_dir_exists($route,true,true);
        echo get_string('add_practice_ok','block_gpracticas').'<BR>';
        if (is_unix_so()) {
            echo get_string('info_script','block_gpracticas');
            echo '<FORM name="upload_script" ACTION="'.practice_route($practice).'" method="POST"><INPUT type="submit" value="Subir script.sh" /></FORM><BR>';
        }
    }
} else {
    echo get_string('no_auth_edit_practices','block_gpracticas');
}
print_footer($course);

?>
