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

global $CFG;
require_once("$CFG->dirroot/blocks/gpracticas/lib.php");


class block_gpracticas extends block_base {

    function init() {
        $this->title = get_string('blockname','block_gpracticas');
        $this->version = 2012040602;
    }
    
    function get_content() {
        global $COURSE;
        global $USER;

        if ($this->content !== NULL) {
            return $this->content;
        }

        if ($this->title == get_string('blockname','block_gpracticas')) {
            $this->content = new stdClass;
            $this->content->text =  get_string('no_configured','block_gpracticas');
            $this->content->footer = '';
            return $this->content;
        }
        $context = get_context_instance(CONTEXT_COURSE,$COURSE->id);
        if (has_capability('block/gpracticas:teacher', $context)) { 
            $this->content = new stdClass;
            $this->content->text =  show_practices_teacher($this->config->examinationsession, $this->config->academiccourse);
            $this->content->footer = teacher_footer($this->config->examinationsession,$this->config->academiccourse);
        }
        else if (has_capability('block/gpracticas:student', $context)){
            $this->content = new stdClass;
            $this->content->text =  show_practices_student($this->config->examinationsession, $this->config->academiccourse);
            $this->content->footer = student_footer($this->config->examinationsession,$this->config->academiccourse);
        }
       
        return $this->content;
       
    }

    function specialization() {
        if (isset($this->config->academiccourse)) {
            $this->title = get_string('practices','block_gpracticas').' '.$this->config->examinationsession.' '.$this->config->academiccourse;
        }
    }
    
    function instance_allow_multiple() {
        return true;
    }
    
}

?>
