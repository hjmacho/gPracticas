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

class Delivery {

    private $id;
    private $student;
    private $practice;
    private $in_closing_date;
    private $delivery_date;
    private $mark;
    private $comments;
    private $student_comments;

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
        $this->student = $var[0]->student;
        $this->practice = $var[0]->practice;
        $this->in_closing_date = $var[0]->in_closing_date;
        $this->delivery_date = $var[0]->delivery_date;
        $this->mark = $var[0]->mark;
        $this->comments = $var[0]->comments;
        $this->student_comments = $var[0]->student_comments;
    }

    function __construct2 ($var) {
        $this->id = $var[0];
        $this->student = addslashes($var[1]);
        $this->practice = $var[2];
        $this->in_closing_date = $var[3];
        $this->delivery_date = $var[4];
        $this->mark = $var[5];
        $this->comments = addslashes($var[6]);
        $this->student_comments = addslashes($var[7]);
    }

//getters
    
    public function get_id() {
        return $this->id;
    }

    public function get_student() {
        return $this->student;
    }

    public function get_practice() {
        return $this->practice;
    }

    public function get_in_closing_date() {
        return $this->in_closing_date;
    } 

    public function get_delivery_date() {
        return $this->delivery_date;
    } 

    public function get_mark() {
        return $this->mark;
    } 

    public function get_comments() {
        return $this->comments;
    } 

    public function get_student_comments() {
        return $this->student_comments;
    } 


//setters

    public function set_id($aux) {
        $this->id = $aux;
    }

    public function set_student($aux) {
        $this->student = addslashes($aux);
    }

    public function set_practice($aux) {
        $this->practice = $aux;
    }

    public function set_in_closing_date($aux) {
        $this->in_closing_date = $aux;
    } 

    public function set_delivery_date($aux) {
        $this->delivery_date = addslashes($aux);
    } 

    public function set_mark($aux) {
        $this->mark = $aux;
    } 

    public function set_comments($aux) {
        $this->comments = addslashes($aux);
    } 

    public function set_student_comments($aux) {
        $this->student_comments = addslashes($aux);
    } 

//nos permite convertir el objeto en un objeto sin variables privadas para poder insertarlo en la BD
    private function privtopublic() {
        $aux = new stdClass;
        $aux->id = $this->id;
        $aux->student = $this->student;
        $aux->practice = $this->practice;
        $aux->in_closing_date = $this->in_closing_date;
        $aux->delivery_date = $this->delivery_date;
        $aux->mark = $this->mark;
        $aux->comments = $this->comments;
        $aux->student_comments = $this->student_comments;
        return $aux;
    }

//inserta el objeto en la BD
    public function insert() {
        $aux = $this->privtopublic();
        $this->id=insert_record("gpracticas_deliveries", $aux) or die(get_string('err_add_delivery','block_gpracticas'));
    }
	
//actualiza el objeto en la BD
    public function update() {
        $aux = $this->privtopublic();
        update_record("gpracticas_deliveries", $aux)  or die(get_string('err_update_delivery','block_gpracticas'));
    }
}

?>
