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

global $USER;

$courseid=required_param('courseid',PARAM_INT);
$practiceid=required_param('practiceid',PARAM_INT);
$filename=required_param('filename',PARAM_TEXT);
$userid=required_param('userid',PARAM_TEXT);

//comprobamos que el usuario está identificado en el sistema y pertenece al curso
require_login($courseid);
$auxpractice = get_record('gpracticas_practices', 'id', $practiceid) or die(get_string('err_read_db','block_gpracticas'));
$practice = new Practice($auxpractice);
$course = get_record('course', 'id', $courseid) or die(get_string('err_read_db','block_gpracticas'));
$context = get_context_instance(CONTEXT_COURSE,$courseid);
//Es un alumno, el archivo le pertenece y sesskey es correcta
if ((has_capability('block/gpracticas:student', $context)) && confirm_sesskey() && ($USER->id==$userid)){ 
    $file=(get_route($courseid,$practiceid,$USER->id)).'/'.$filename;
    if (is_file($file)) {
        unlink($file);
        //si el directorio donde se encontraba el fichero está vacío lo borramos también
        $dir=substr($file,0,'-'.strlen(strrchr($file, '/')));
        rmdir($dir);
        //hacemos lo mismo con el directorio raíz de la entrega del alumno
        rmdir(get_route($courseid,$practiceid,$USER->id));
        $url= $CFG->wwwroot.'/blocks/gpracticas/delivery.php?courseid='.$courseid.'&practiceid='.$practiceid.'&sesskey='.sesskey();
        header ("Location: $url"); 
    } else {
        echo get_string('file_doesnt_exist','block_gpracticas');
    }
} else {
    print_header_simple($practice->get_name(),'',$practice->get_name(),'','',false);
    echo get_string('no_auth_del_file','block_gpracticas');
    print_footer($course);
}

?>
