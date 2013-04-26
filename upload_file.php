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
require_once("$CFG->dirroot/blocks/gpracticas/bin/Tar.php");
require_once("$CFG->dirroot/lib/filelib.php");

global $CFG;
global $USER;


function is_extension_targz ($file) {
    $extension = substr($file,-6);
    if ($extension == 'tar.gz') {
        return true;
    } else {
        $extension = substr($file,-3);
        if ($extension == 'tgz') {
            return true;
        } else {
            return false;
        }
    }
}

/*si la suma de bytes de los datos del formulario, incluidos los bytes del fichero, exceden el post_max_size definido en el php.ini
se produciría un fallo del cual PHP no nos informa, por lo que tenemos que comprobar que dicho fallo no se ha producido*/
if ($_SERVER['CONTENT_LENGTH'] > return_bytes(ini_get('post_max_size'))) {
    print_header_simple(get_string('err_up_file','block_gpracticas'),'',get_string('err_up_file','block_gpracticas'),'','',false);
    echo get_string('err_max_size_exceed','block_gpracticas').'<BR>';
    print_footer();
    exit;
}

/*pese a que ambos parámetros deberían ser parámetros requeridos es necesario definirlos como opcionales, ya que en caso de que se
exceda el post_max_size la información de ambas variables podría no llegar, lo que arrojaría un mensaje de error que no se corresponde con la situación real*/
$courseid = optional_param('courseid',0,PARAM_INT);
$practiceid = optional_param('practiceid',0,PARAM_INT);

require_login($courseid);
$auxpractice = get_record('gpracticas_practices', 'id', $practiceid) or die(get_string('err_read_db','block_gpracticas'));
$practice = new Practice($auxpractice);
$course = get_record('course', 'id', $courseid) or die(get_string('err_read_db','block_gpracticas'));
$context = get_context_instance(CONTEXT_COURSE,$courseid);
//comprobamos si es un alumno, si la práctica corresponde al curso y si la sesskey es válida
if (has_capability('block/gpracticas:student', $context) && ($courseid == $practice->get_subject()) && confirm_sesskey()) {
    $userfile = 'UID'.$USER->id.'CID'.$courseid;
    $filename = basename( $_FILES[$userfile]['name']);       
    if ($_FILES[$userfile]['error'] > 0){
        print_header_simple($practice->get_name(),'',$practice->get_name(),'','',false);
        echo get_string('no_auth_up_file','block_gpracticas').'<BR>';
    } elseif ($filename != clean_filename($filename)) {
        $url= $CFG->wwwroot.'/blocks/gpracticas/delivery.php?courseid='.$courseid.'&practiceid='.$practiceid.'&sesskey='.sesskey().'&errorname=1';
        header ("Location: $url");
    } else {
        $dir = get_route($courseid,$practiceid,$USER->id);
        check_dir_exists($dir,true);
        $dir .= '/'.$filename;
        move_uploaded_file($_FILES[$userfile]['tmp_name'],$dir);
        if ((mimeinfo('type',$dir) == 'application/zip') && ($practice->get_autounzip() == 1)) { 
            unzip_file($dir,'',false);
            unlink($dir);
        }
        if ((mimeinfo('type',$dir) == 'application/g-zip')&& (is_extension_targz($filename)) && ($practice->get_autounzip())) {
            $tar = new Archive_Tar("$dir");
            $tar->extract(get_route($courseid,$practiceid,$USER->id)) or die(get_string('err_extract_files','block_gpracticas'));
            unlink($dir);
        }
        $url= $CFG->wwwroot.'/blocks/gpracticas/delivery.php?courseid='.$courseid.'&practiceid='.$practiceid.'&sesskey='.sesskey();
        header ("Location: $url");      
    }       
} else {
    print_header_simple($practice->get_name(),'',$practice->get_name(),'','',false);
    echo get_string('err_up_file','block_gpracticas');
}
print_footer($course);

?>
