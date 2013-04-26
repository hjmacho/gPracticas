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
require_once("lib.php");

global $USER;

$courseid=required_param('courseid',PARAM_INT);
$practiceid=required_param('practiceid',PARAM_INT);
$filename=required_param('filename',PARAM_TEXT);
$userid=required_param('userid',PARAM_INT);
      

require_login($courseid);
//Comprobamos que el alumno está identificado en el sistema y pertenece a este curso
$course = get_record('course', 'id', $courseid) or die(get_string('err_read_db','block_gpracticas'));
$context = get_context_instance(CONTEXT_COURSE,$courseid);
//Es un alumno,el archivo le pertenece y sesskey es correcta
if (has_capability('block/gpracticas:student', $context) && ($userid==$USER->id) && confirm_sesskey()){ 
    $file= get_route($courseid, $practiceid, $userid).'/'.$filename;
    if (is_file($file)) {
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename='.basename($file));
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file));
        ob_clean();
        flush();
        readfile($file);
        exit;
    } else {
        print_header_simple(get_string('downloading','block_gpracticas').' '.$filename,'',get_string('downloading','block_gpracticas').' '.$filename,'','',false);
        echo get_string('no_exists_file','block_gpracticas');
        print_footer($course);
    }   
} else {
    print_header_simple(get_string('downloading','block_gpracticas').' '.$filename,'',get_string('downloading','block_gpracticas').' '.$filename,'','',false);
    echo get_string('no_auth_down_file','block_gpracticas');
    print_footer($course);
}

?>
