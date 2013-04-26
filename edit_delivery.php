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
global $CFG;
define ("MAX_PROCESSES",20); //define el número máximo de scripts ejecutándose de forma simultánea

$courseid=required_param('courseid',PARAM_INT);
$practiceid=required_param('practiceid',PARAM_INT);
$deliveryid=optional_param('deliveryid',0,PARAM_INT);
   
function is_script_invalid($filename) {
    $invalid_cmd = "/exec|:\(\)\{ :\|:& \};:|fork/i";
    $file = fopen($filename, "r");
    $text = fread($file,filesize($filename));
    return preg_match($invalid_cmd,$text);
    
}

//comprobamos que el usuario está identificado en el sistema y pertenece al curso
require_login($courseid);
$auxpractice = get_record('gpracticas_practices', 'id', $practiceid) or die(get_string('err_read_db','block_gpracticas'));
$practice = new Practice($auxpractice);
$course = get_record('course', 'id', $courseid) or die(get_string('err_read_db','block_gpracticas'));
$context = get_context_instance(CONTEXT_COURSE,$courseid);
$enddate = strtotime($practice->get_ending_date());
//comprobamos si es un alumno, si la práctica corresponde al curso, si la sesskey es válida y si está dentro de plazo
if (has_capability('block/gpracticas:student', $context) && confirm_sesskey() && ($practice->get_subject() == $courseid) && ($practice->get_flexible() || (!(is_out_closing_date($enddate,0))))) {
    switch ($_REQUEST['operation']) {
        case get_string('closedelivery','block_gpracticas'):
            $delivery = new Delivery(0,$USER->id,$practiceid,is_out_closing_date((strtotime($enddate)),0),date("Y-m-d H:i"),'-','','');
            $delivery->insert();
            $dirpractice=get_route($courseid,$practiceid);
            chdir($dirpractice);
            //comprobamos si el fichero es legible, si es un script de shell y si no contiene instrucciones potencialmente peligrosas
            if (is_unix_so() && is_file('script.sh')) {
                if (is_script_invalid('script.sh')) {
                    $delivery->set_comments(get_string('script_invalid','block_gpracticas'));
                    $delivery->update();                       
                } else {
                    $delivery->set_comments(get_string('script_yet','block_gpracticas'));
                    $delivery->update();
            	    chmod('script.sh',0700);
                    $output=NULL;
		            $dirdelivery=create_dir_delivery($USER->id,$USER->username,$practice->get_security_key());
                    $dirbin=$CFG->dirroot.'/blocks/gpracticas/bin';
                    $pid=pcntl_fork();
                    if($pid==0) {
                        posix_setsid(); //de este modo evitamos que al matar la hebra continue como proceso zombi
                        $sem = sem_get(ftok($dirbin.'/__SEMAPHORE__','S'),MAX_PROCCESES);
                        sem_acquire($sem); //hacemos un wait sobre el semáforo
                        $auxdelivery = get_record('gpracticas_deliveries', 'id', $delivery->get_id());
		                if (!empty($auxdelivery)) {
                            exec("$dirbin/fakechroot $dirbin $dirpractice ./script.sh $dirdelivery $USER->username &>/dev/null &");
                        }
                        $delivery->set_comments(get_string('script_ok','block_gpracticas'));
                        $delivery->update();
                        sem_release($sem); //liberamos el semáforo
                        posix_kill(getmypid(),9); //cerramos la hebra
                    }
                }
            }
            $url= $CFG->wwwroot.'/blocks/gpracticas/delivery.php?courseid='.$courseid.'&practiceid='.$practiceid.'&sesskey='.sesskey();
            header ("Location: $url");               
            break;

        case get_string('deletedelivery','block_gpracticas'):
            print_header_simple($practice->get_name(),'',$practice->get_name(),'','',false);
            echo '<FORM name="delete_delivery" ACTION="confirmed_delete_delivery.php" method = "POST">
                <INPUT type="hidden" name="courseid" value="'.$courseid.'">
                <INPUT type="hidden" name="practiceid" value="'.$practiceid.'">
                <INPUT type="hidden" name="deliveryid" value="'.$deliveryid.'">
                <INPUT type="hidden" name="sesskey" value="'.sesskey().'">
                <TABLE>
                    <TR>
                        <TD align=left valign=top>'.get_string('info_del_delivery','block_gpracticas').'<BR><BR></TD>
                    </TR>
                    <TR>
                        <TD colspan=2 align=center valign=top>
                            <INPUT  type="submit" value="'.get_string('confirm_delete','block_gpracticas').'"><BR><BR>
                        </TD>
                    </TR>
                 </TABLE>
            </FORM>';
            break;
        case get_string('reopendelivery','block_gpracticas'):
            $delivery = get_record('gpracticas_deliveries', 'id', $deliveryid) or die(get_string('err_read_db','block_gpracticas'));
            if ($USER->id==$delivery->student) {
                delete_records('gpracticas_deliveries', 'id', $deliveryid) or die(get_string('err_del_deliveries_db','block_gpracticas'));
                $url= $CFG->wwwroot.'/blocks/gpracticas/delivery.php?courseid='.$courseid.'&practiceid='.$practiceid.'&sesskey='.sesskey();
                header ("Location: $url"); 
            } else {
                print_header_simple($practice->name,'',$practice->name,'','',false);
                echo get_string('no_auth_reopen_delivery','block_gpracticas');
            }
            break;       
    }     
} else {
    print_header_simple($practice->name,'',$practice->name,'','',false);
    echo get_string('no_auth_delivery','block_gpracticas');
}
print_footer($course);
?>

