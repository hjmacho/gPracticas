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

$examinationsession = required_param('examinationsession',PARAM_TEXT);
$academiccourse =required_param('academiccourse',PARAM_TEXT);
$courseid = required_param('courseid',PARAM_INT);

//comprobamos que el usuario está identificado en el sistema y pertenece al curso
require_login($courseid);
$course = get_record("course", "id", $courseid) or die(get_string('err_read_db','block_gpracticas'));
print_header_simple(get_string('del_all_practices','block_gpracticas').": $examinationsession $academiccourse",'',get_string('del_all_practices','block_gpracticas').": $examinationsession $academiccourse",'','',false);
$context = get_context_instance(CONTEXT_COURSE,$courseid);
//comprobamos si es un profesor y si la sesskey es válida
if (has_capability('block/gpracticas:teacher', $context) && confirm_sesskey()) {
    echo '<H2>'.get_string('del_practice','block_gpracticas').'</H2><BR>
        <FORM name="delete_all_practices" ACTION="confirmed_delete_all_practices.php" method = "POST">
        <INPUT type="hidden" name="courseid" value="'.$courseid.'">
        <INPUT type="hidden" name="examinationsession" value="'.$examinationsession.'">
        <INPUT type="hidden" name="academiccourse" value="'.$academiccourse.'">
        <INPUT type="hidden" name="sesskey" value="'.sesskey().'">
        <TABLE>
            <TR>
                <TD align=left valign=top>'.get_string('ask_confirm_delete','block_gpracticas').': <b>'.$examinationsession.' '.$academiccourse.'</b>?.<BR><BR></TD>
            </TR>
            <TR>
            <TD colspan=2 align=center valign=top>
                <INPUT  type="submit" value="'.get_string('confirm_delete','block_gpracticas').'"><BR><BR>
            </TD>
            </TR>
            <TR>
            </TABLE></FORM>';

   } else {
    echo get_string('no_auth_del_practice','block_gpracticas');
}
print_footer($course);

?>
