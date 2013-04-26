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

class Practice {

    private $id;
    private $subject;
    private $name;
    private $academiccourse;
    private $examinationsession;
    private $flexible;
    private $ending_date;
    private $visible;
    private $description;
    private $idcalendar;
    private $security_key;
    private $autounzip;
    private $reopen;

//constructores
    function __construct () {
        $args = func_get_args();
        $numargs = func_num_args();
        if ($numargs == 1) {
            self::__construct1($args);
        } else {
            self::__construct2($args);
        }
    }

    function __construct1 ($var) {
        $this->id = $var[0]->id;
        $this->subject = $var[0]->subject;
        $this->name = $var[0]->name;
        $this->academiccourse = $var[0]->academiccourse;
        $this->examinationsession = $var[0]->examinationsession;
        $this->flexible = $var[0]->flexible;
        $this->ending_date = $var[0]->ending_date;
        $this->visible = $var[0]->visible;
        $this->description = $var[0]->description;
        $this->idcalendar = $var[0]->idcalendar;
        $this->security_key = $var[0]->security_key;
        $this->autounzip = $var[0]->autounzip;
        $this->reopen = $var[0]->reopen;
    }

    function __construct2 ($var) {
        $this->id = $var[0];
        $this->subject = $var[1];
        $this->name = addslashes($var[2]);
        $this->academiccourse = addslashes($var[3]);
        $this->examinationsession = addslashes($var[4]);
        $this->flexible = $var[5];
        $this->ending_date = $var[6];
        $this->visible = $var[7];
        $this->description = addslashes($var[8]);
        $this->idcalendar = $var[9];
        $this->security_key = $var[10];
        $this->autounzip = $var[11];
        $this->reopen = $var[12];
    }

//getters
    
    public function get_id() {
        return $this->id;
    }

    public function get_subject() {
        return $this->subject;
    }

    public function get_name() {
        return $this->name;
    }

    public function get_academiccourse() {
        return $this->academiccourse;
    } 

    public function get_examinationsession() {
        return $this->examinationsession;
    } 

    public function get_flexible() {
        return $this->flexible;
    } 

    public function get_ending_date() {
        return $this->ending_date;
    } 

    public function get_visible() {
        return $this->visible;
    } 

    public function get_description() {
        return $this->description;
    }

    public function get_idcalendar() {
        return $this->idcalendar;
    }

    public function get_security_key() {
        return $this->security_key;
    }

    public function get_autounzip() {
        return $this->autounzip;
    }

    public function get_reopen() {
        return $this->reopen;
    }

//setters

    public function set_id($aux) {
        $this->id = $aux;
    }

    public function set_subject($aux) {
        $this->subject = $aux;
    }

    public function set_name($aux) {
        $this->name = addslashes($aux);
    }

    public function set_academiccourse($aux) {
        $this->academiccourse = addslashes($aux);
    } 

    public function set_examinationsession($aux) {
        $this->examinationsession = addslashes($aux);
    } 

    public function set_flexible($aux) {
        $this->flexible = $aux;
    } 

    public function set_ending_date($aux) {
        $this->ending_date = $aux;
    } 

    public function set_visible($aux) {
        $this->visible = $aux;
    } 

    public function set_description($aux) {
        $this->description = addslashes($aux);
    }

    public function set_idcalendar($aux) {
        $this->idcalendar = $aux;
    }

    public function set_security_key($aux) {
        $this->security_key = $aux;
    }

    public function set_autounzip($aux) {
        $this->autounzip = $aux;
    }

    public function set_reopen($aux) {
        $this->reopen = $aux;
    }


//nos permite convertir el objeto en un objeto sin variables privadas para poder insertarlo en la BD
    private function privtopublic() {
        $aux = new stdClass;
        $aux->id = $this->id;
        $aux->subject = $this->subject;
        $aux->name = $this->name;
        $aux->academiccourse = $this->academiccourse;
        $aux->examinationsession = $this->examinationsession;
        $aux->flexible = $this->flexible;
        $aux->ending_date = $this->ending_date;
        $aux->visible = $this->visible;
        $aux->description = $this->description;
        $aux->idcalendar = $this->idcalendar;
        $aux->security_key = $this->security_key;
        $aux->autounzip = $this->autounzip;
        $aux->reopen = $this->reopen;
        return $aux;
    }

//inserta el objeto en la BD
    public function insert_practice() {
        $aux = $this->privtopublic();
        $practiceid = insert_record('gpracticas_practices',$aux,'true','id') or die(get_string('err_add_practice','block_gpracticas'));
        $this->id=$practiceid;
        return $practiceid;
    }
	
//actualiza el objeto en la BD
    public function update_practice() {
        $aux = $this->privtopublic();
        update_record('gpracticas_practices', $aux)  or die(get_string('err_update_practice','block_gpracticas'));
    }
}

?>
