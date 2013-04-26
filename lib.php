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

//en caso de problemas derivados de la mala configuración horaria del servidor descomentar la siguiente línea
//date_default_timezone_set('Europe/Madrid');
require_once("$CFG->dirroot/blocks/gpracticas/class_practice.php");
require_once("$CFG->dirroot/blocks/gpracticas/class_delivery.php");


//*Genera el contenido del bloque tal y como lo vería un profesor
function show_practices_teacher($examinationsession,$academiccourse) {
    global $CFG;
    global $COURSE;
    global $USER;

    $select = "subject=$COURSE->id AND examinationsession='$examinationsession' AND academiccourse='$academiccourse'";
    $data = get_records_select('gpracticas_practices',$select,'ending_date ASC');
    if (empty($data)) {
        $result = get_string('not_practices_yet','block_gpracticas');      
        return $result;
    }
    $result = '';
    
    foreach ($data as $value) {
        $practice = new Practice($value);
        $result .= '<b><u>'.$practice->get_name().'</u></b>';
        if ($practice->get_visible()) { //si visible=1 entonces la entrega es visible, en caso de valga 0 no
            $result .= "&nbsp;&nbsp;<img title=\"".get_string('visible_description','block_gpracticas')."\" src=\"";
            $result .= $CFG->wwwroot.'/pix/t/hide.gif';
            $result .= "\" alt=\"".get_string('visible_description','block_gpracticas')."\" ><br>";
        } else {
            $result .= "&nbsp;&nbsp;<img title=\"".get_string('no_visible','block_gpracticas')."\" src=\"";
            $result .= $CFG->wwwroot.'/pix/t/show.gif';
            $result .= "\" alt=\"".get_string('no_visible','block_gpracticas')."\" ><br>";
        }
        $result .= get_string('endingdate','block_gpracticas').': <b>'.date("d/m/y H:i",strtotime($practice->get_ending_date())).'</b><br>';
        $result .= 'Entrega fuera de plazo: ';
        if ($practice->get_flexible()) { //si flexible=1 entonces la entrega es flexible, en caso de valga 0 no
            $result .= get_string('yes','block_gpracticas');
        } else {
            $result .= get_string('no','block_gpracticas'); 
        }
        $result .= '<BR>Auto-descomprimir: ';
        if ($practice->get_autounzip()) { //si autounzip=1 entonces los ficheros se autodescomprimen, en caso de valga 0 no
            $result .= get_string('yes','block_gpracticas');
        } else {
            $result .= get_string('no','block_gpracticas'); 
        }
		$result .= '<BR>Modificar entrega: ';
        if ($practice->get_reopen()) { //si reopen=1 entonces la entrega puede modificarse, en caso de valga 0 no
            $result .= get_string('yes','block_gpracticas');
        } else {
            $result .= get_string('no','block_gpracticas'); 
        }
        $result .= '<BR><a title="'.get_string('editpractice','block_gpracticas').'" href="'.$CFG->wwwroot.'/blocks/gpracticas/edit_practice.php?courseid='.$COURSE->id.'&practiceid='.$practice->get_id().'&sesskey='.sesskey().'&action=editpractice"><img src="';
        $result .= $CFG->wwwroot.'/pix/t/edit.gif';
        $result .= '" alt="'.get_string('editpractice','block_gpracticas').'" ></a>&nbsp;&nbsp;<a title="'.get_string('del_practice','block_gpracticas').'" href="'.$CFG->wwwroot.'/blocks/gpracticas/delete_practice.php?courseid='.$COURSE->id.'&practiceid='.$practice->get_id().'&sesskey='.sesskey().'"><img src="';
        $result .= $CFG->wwwroot.'/pix/t/delete.gif';
        $result .= '" alt="'.get_string('del_practice','block_gpracticas').'" ></a>&nbsp;&nbsp;<a title="Ver entregas/Calificar" href="'.$CFG->wwwroot.'/blocks/gpracticas/view_practice.php?courseid='.$COURSE->id.'&practiceid='.$practice->get_id().'&sesskey='.sesskey().'"><img src="';
        $result .= $CFG->wwwroot.'/pix/i/report.gif';
        $result .= '" alt="Ver entregas/Calificar" ></a><br><br>';
    }
    return $result; 
}


//Genera el pie del bloque tal y como lo vería un alumno
function teacher_footer($examinationsession,$academiccourse) {
    global $CFG;
    global $COURSE;
    global $USER;
    $select = "subject=$COURSE->id AND examinationsession='$examinationsession' AND academiccourse='$academiccourse'";
    $data = get_records_select('gpracticas_practices',$select,'ending_date ASC');

    $result .= '<a title="'.get_string('addpractice','block_gpracticas').'" href="'.$CFG->wwwroot.'/blocks/gpracticas/edit_practice.php?courseid='.$COURSE->id.'&sesskey='.sesskey().'&examinationsession='.$examinationsession.'&academiccourse='.$academiccourse.'&action=addpractice"><img src="';
    $result .= $CFG->wwwroot.'/blocks/gpracticas/icons/anadir_practica.gif';
    $result .= '" alt="'.get_string('addpractice','block_gpracticas').'" ></a>&nbsp;&nbsp;<a title="'.get_string('see_files','block_gpracticas').'" href="'.$CFG->wwwroot.'/files/index.php?id='.$COURSE->id.'&wdir='.clean_filename($examinationsession).'_'.clean_filename($academiccourse).'"><img src="'.$CFG->wwwroot.'/pix/i/files.gif" alt="'.get_string('see_files','block_gpracticas').'" ></a>';
    if (!empty($data)) {
        $result .= '&nbsp;&nbsp;<a title="'.get_string('del_all_practices','block_gpracticas').'" href="'.$CFG->wwwroot.'/blocks/gpracticas/delete_all_practices.php?courseid='.$COURSE->id.'&examinationsession='.$examinationsession.'&academiccourse='.$academiccourse.'&sesskey='.sesskey().'"><img src="'.$CFG->wwwroot.'/pix/i/cross_red_big.gif" alt="'.get_string('del_all_practices','block_gpracticas').'" ></a>';
    }
    return $result;
}


//Genera el contenido del bloque tal y como lo vería un alumno
function show_practices_student($examinationsession,$academiccourse) {
    global $CFG;
    global $COURSE;
    global $USER;
    $select = "subject='$COURSE->id' AND examinationsession='$examinationsession' AND academiccourse='$academiccourse' AND visible=1";
    $data = get_records_select('gpracticas_practices',$select,'ending_date ASC');
    if (empty($data)) {
        $result = 'Aún no se han definido prácticas correspondientes a este bloque.';      
        return $result;
    }
    $result = '';
    foreach ($data as $value) {
        $practice = new Practice($value);
        $delivery = get_record('gpracticas_deliveries', 'practice', $practice->get_id(), 'student', $USER->id);
        $result .= '<b><u>'.$practice->get_name().'</u></b>&nbsp;&nbsp;';
        $ending_date = strtotime($practice->get_ending_date());
        if (!(is_out_closing_date($ending_date,0))) {
            $delivery_permitida = true;
            $result .= "<img title=\"Dentro de plazo\" src=\"";
            $result .= $CFG->wwwroot.'/blocks/gpracticas/icons/dentro_plazo.gif';
            $result .= "\" alt=\"Dentro de plazo\" >";
        } else {
            if ($practice->get_flexible()) {
                $delivery_permitida = true;
                $result .= "<img title=\"Plazo expirado. Se permite entrega\" src=\"";
                $result .= $CFG->wwwroot.'/blocks/gpracticas/icons/entrega_flexible.gif';
                $result .= "\" alt=\"Plazo expirado. Se permite entrega\" >";
            } else {
                $delivery_permitida = false;
                $result .= "<img title=\"Plazo expirado. No se permite entrega\" src=\"";
                $result .= $CFG->wwwroot.'/blocks/gpracticas/icons/fuera_plazo.gif';
                $result .= "\" alt=\"Plazo expirado. No se permite entrega\" >";
            }
        }
         
        $result .= '<br>';
        $result .= get_string('endingdate','block_gpracticas').': <b>'.date("d/m/y H:i",$ending_date).'</b><br>';
        if (!empty($delivery)) {
            $aux = new Delivery($delivery);
            $result .= 'Entregada: '.get_string('yes','block_gpracticas').'<br>';
            $result .= 'Fecha entrega: '.date("d/m/y H:i",strtotime($aux->get_delivery_date())).'<br>';
            $result .= 'Entregada dentro plazo: ';
            if ($aux->get_in_closing_date()) {
                $result .= get_string('yes','block_gpracticas').'<br>';
            } else {
                $result .= get_string('no','block_gpracticas').'<br>';
            }
            $result .= 'Nota: '.$aux->get_mark().'<br>';
            $comments = $aux->get_student_comments();
            if (!empty($comments)) {
               $result .= 'Comentarios: '.$comments.'<BR>';
            }
         } else {
            $result .= 'Entregada: '.get_string('no','block_gpracticas').'<br>';
            $result .= 'Nota: -<br>';
         }
         if ($delivery_permitida) {
            $result .= '<a title="Entregar práctica" href="'.$CFG->wwwroot.'/blocks/gpracticas/delivery.php?courseid='.$COURSE->id.'&practiceid='.$practice->get_id().'&sesskey='.sesskey().'"><img src="';
            $result .= $CFG->wwwroot.'/blocks/gpracticas/icons/entregar_practica.gif';
            $result .= '" alt="Entregar práctica" ></a><br>';
         }
         $result .= '<br>';
    }
    return $result;
}



//Genera el pie del bloque tal y como lo vería un alumno
function student_footer ($examinationsession,$academiccourse) {
    global $CFG;
    global $COURSE;
    global $USER;
    $result .= '<a title="Ver notas" href="'.$CFG->wwwroot.'/blocks/gpracticas/view_marks.php?courseid='.$COURSE->id.'&sesskey='.sesskey().'&examinationsession='.$examinationsession.'&academiccourse='.$academiccourse.'"><img src="';
    $result .= $CFG->wwwroot.'/pix/i/report.gif';
    $result .= '" alt="Ver notas" ></a>';
    return $result;
}


//comprueba si la la práctica se encuentra fuera de plazo
function is_out_closing_date($date1,$date2) {
    if ($date2==0) {
        $actualdate=strtotime(date("Y-m-d H:i"));
        return ($actualdate>$date1); 
    } else {   
        return ($date2>$date1);
    } 
}


//crea la ruta del directorio de una práctica
function create_dir_delivery($id,$name,$security_key) {
    $dir=$id.'-'.md5($name.$security_key);
    return $dir;
}


//crea la ruta del directorio de entrega de una práctica por parte de un alumno
function get_route($courseid,$practiceid,$userid='') {
    global $CFG;
   
    $practice=get_record('gpracticas_practices','id',$practiceid);
    if (empty($userid)) {
        $dir=$CFG->dataroot.'/'.$courseid.'/'.clean_filename($practice->examinationsession).'_'.clean_filename($practice->academiccourse).'/'.$practiceid.'_'.clean_filename($practice->name);
    } else {
        $student=get_record('user','id',$userid);
        $dir=$CFG->dataroot.'/'.$courseid.'/'.clean_filename($practice->examinationsession).'_'.clean_filename($practice->academiccourse).'/'.$practiceid.'_'.clean_filename($practice->name).'/'.create_dir_delivery($userid,$student->username.$practice->security_key);
    }
    return $dir;
}

//convierte 0 en unchecked y 1 en checked
function int_to_check ($value) {
    if ($value) {
        return "checked";
    } else {
        return "unchecked";
    }
}

//convierte a bytes
function return_bytes($val) {
    $val = trim($val);
    $last = strtolower($val[strlen($val)-1]);
    switch($last) {
        case 'g':
            $val *= 1024;
        case 'm':
            $val *= 1024;
        case 'k':
            $val *= 1024;
    }
    return $val;
}
//comprueba si es un sistema UNIX
function is_unix_so() {
    $pattern = "/linux/i";
    return preg_match($pattern,php_uname());
}

?>
