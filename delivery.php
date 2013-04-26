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

$courseid = required_param('courseid',PARAM_INT);
$practiceid = required_param('practiceid',PARAM_INT);
$errorname = optional_param('errorname',0,PARAM_INT);

//se encarga de generar el código HTML con la lista de ficheros que se encuentran en el directorio de entrega del alumno
function list_files($dir,$route,$courseid,$userid,$practiceid,$delivery,$colour='') {
global $CFG;
define('COLOUR','#E6E6E6');
    if ($handle = opendir($dir)) {
        while (false !== ($file = readdir($handle))) {
            if ($file != "." && $file != "..") {
                if (is_dir($dir.'/'.$file)) {
                    $colour=list_files($dir.'/'.$file,$route.$file.'/',$courseid,$userid,$practiceid,$delivery,$colour);  
                } else {
                    $date=date("d-m-y H:i:s",filemtime($dir.'/'.$file));
                    echo '<TR bgcolor='.$colour.'><TD>'.$route.$file.'</TD><TD width="10px"></TD><TD align="center">'.$date.'</TD><TD width="10px"></TD><TD align="right"><a title="'.get_string('download_file','block_gpracticas').'" href="'.$CFG->wwwroot.'/blocks/gpracticas/download_file.php?courseid='.$courseid.'&sesskey='.sesskey().'&userid='.$userid.'&filename='.$route.$file.'&practiceid='.$practiceid.'"><img src="'.$CFG->wwwroot.'/blocks/gpracticas/icons/descargar_practica.gif" alt="'.get_string('download_file','block_gpracticas').'" ></a>';
                    if ($delivery) {
                        echo '&nbsp;&nbsp;<a title="'.get_string('delete_file','block_gpracticas').'" href="'.$CFG->wwwroot.'/blocks/gpracticas/delete_file.php?courseid='.$courseid.'&sesskey='.sesskey().'&userid='.$userid.'&filename='.$route.$file.'&practiceid='.$practiceid.'"><img src="'.$CFG->wwwroot.'/pix/i/cross_red_big.gif" alt="'.get_string('delete_file','block_gpracticas').'" ></a>';
                    }
                    echo '</TD></TR>';
                    if (empty($colour)) {
                        $colour=COLOUR;
                    } else {
                        $colour='';
                    }    
                }
            }
        }
        closedir($handle);
        return $colour;
    }   
}



//comprobamos que el usuario está identificado en el sistema y pertenece al curso
require_login($courseid);
$auxpractice = get_record('gpracticas_practices', 'id', $practiceid) or die(get_string('err_read_db','block_gpracticas'));
$practice = new Practice($auxpractice);
$course = get_record('course', 'id', $courseid) or die(get_string('err_read_db','block_gpracticas'));
print_header_simple($practice->get_name(),'',$practice->get_name(),'','',false);
$context = get_context_instance(CONTEXT_COURSE,$courseid);
$enddate = strtotime($practice->get_ending_date());
if (return_bytes(ini_get('post_max_size')) > (return_bytes(ini_get('upload_max_filesize'))+592)) {
    $maxsizefile = return_bytes(ini_get('upload_max_filesize'));
} else {
    $maxsizefile = return_bytes(ini_get('post_max_size'))-592;//la información contenida en las variables que pasamos a trabes de $POST pesan 592bytes, por lo que el tamaño de fichero permitido es el post_max_size menos 592 bytes
}

//comprobamos si es un alumno, si la práctica corresponde al curso, si la sesskey es válida y si está dentro de plazo
if (has_capability('block/gpracticas:student', $context) && confirm_sesskey() && ($practice->get_subject() == $courseid) && ($practice->get_flexible() || (!(is_out_closing_date($enddate,0))))) {
    $auxdelivery = get_record('gpracticas_deliveries', 'practice', $practiceid, 'student', $USER->id);
    $userfile='UID'.$USER->id.'CID'.$courseid;
    $student=get_record('user','id',$USER->id) or die(mysql_error());
    $files=get_route($courseid,$practiceid,$USER->id);
    echo '<H2>'.$practice->get_name().'</H2>';
    $description=$practice->get_description();
    if (!empty($description)) {
        echo get_string('description','block_gpracticas').': '.$practice->get_description().'<BR>';
    }
    if (!empty($auxdelivery)) {
        $delivery=new Delivery($auxdelivery);
        echo get_string('delivered','block_gpracticas').': '.get_string('yes','block_gpracticas').'<BR>';
        echo get_string('delivery_date','block_gpracticas').': '.date("d/m/y H:i",strtotime($delivery->get_delivery_date())).'<BR>';
        echo get_string('in_closing_date','block_gpracticas').': ';
        if ($delivery->get_in_closing_date()) {
            echo get_string('yes','block_gpracticas');
        } else {
            echo get_string('no','block_gpracticas');
        }             
        echo '<BR>'.get_string('mark','block_gpracticas').': '.$delivery->get_mark().'<BR>';
        $comments=$delivery->get_student_comments();
        if ($comments!='')  {
            echo get_string('comments','block_gpracticas').': '.$comments.'<BR>';
        }
		if ($practice->get_reopen()) {
        	echo '<BR><FORM name="edit_delivery" ACTION="edit_delivery.php" method="POST">
                <INPUT type="hidden" name="courseid" value="'.$courseid.'">
                <INPUT type="hidden" name="practiceid" value="'.$practiceid.'">
                <INPUT type="hidden" name="deliveryid" value="'.$delivery->get_id().'">
                <INPUT type="hidden" name="sesskey" value="'.sesskey().'">
                <INPUT type="submit" name="operation" value="'.get_string('reopendelivery','block_gpracticas').'" />
                <INPUT type="submit" name="operation" value="'.get_string('deletedelivery','block_gpracticas').'" />
                </FORM>';
		}
    } else {
        echo get_string('delivered','block_gpracticas').': '.get_string('no','block_gpracticas').'<BR>';
        echo get_string('reopendelivery','block_gpracticas').': -<BR>';
        if (file_exists($files)) {
            echo '<BR><FORM name="edit_delivery" ACTION="edit_delivery.php" method="POST">
                <INPUT type="hidden" name="courseid" value="'.$courseid.'">
                <INPUT type="hidden" name="practiceid" value="'.$practiceid.'">
                <INPUT type="hidden" name="sesskey" value="'.sesskey().'">
                <INPUT type="submit" name="operation" value="'.get_string('closedelivery','block_gpracticas').'" />
                <INPUT type="submit" name="operation" value="'.get_string('deletedelivery','block_gpracticas').'" />
                </FORM><BR>';
			if (!($practice->get_reopen())) {
		    	echo '<BR><b><font color="red">Una vez cerrada la entrega no se permitirá modificarla.</font></b><BR>';
			}
        }
        echo '<BR><H2>'.get_string('deliver_practice','block_gpracticas').':</H2>';
        echo '<FORM enctype="multipart/form-data" action="upload_file.php" method="POST">
                <INPUT type="hidden" name="courseid" value="'.$courseid.'">
                <INPUT type="hidden" name="practiceid" value="'.$practiceid.'">
                <INPUT type="hidden" name="sesskey" value="'.sesskey().'">
                <INPUT name="'.$userfile.'" type="file" /><BR>
                <i>'.get_string('info_file_name','block_gpracticas').$maxsizefile.' bytes)</i><BR><BR>
                <INPUT type="submit" value="'.get_string('uploadfile','block_gpracticas').'" /></FORM>';
        if ($errorname) {
            echo '<BR><BR><b><font color="red">'.get_string('err_file_name','block_gpracticas').'</font></b><BR>';
        }
    }
    if (file_exists($files)) {  
        echo '<BR><H2>'.get_string('files_delivered','block_gpracticas').'</H2>';
        echo '<TABLE><TR><TH>'.get_string('file_name','block_gpracticas').'</TH><TD></TD><TH>'.get_string('last_modified','block_gpracticas').'</TH><TD></TD><TH>'.get_string('actions','block_gpracticas').'</TH></TR>';
        list_files(get_route($courseid,$practiceid,$USER->id),'',$courseid,$USER->id,$practiceid,empty($delivery));
        echo '</TABLE>';     
    }

} else {
    echo get_string('no_auth_edit_delivery','block_gpracticas');
}
print_footer($course);

?>
