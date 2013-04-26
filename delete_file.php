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
$userid=required_param('userid',PARAM_INT);     

//Comprobamos que el alumno está logueado y pertenece a este curso
require_login($courseid);
$course = get_record('course', 'id', $courseid) or die(get_string('err_read_db','block_gpracticas'));
$context = get_context_instance(CONTEXT_COURSE,$courseid);
print_header_simple(get_string('delete','block_gpracticas').' '.$filename,'',get_string('delete','block_gpracticas').' '.$filename,'','',false);
//Es un alumno,el archivo le pertenece y sesskey es correcta
if (has_capability('block/gpracticas:student', $context) && ($userid==$USER->id) && confirm_sesskey()){ 
    echo '<FORM name="delete_file" ACTION="confirmed_delete_file.php" method = "POST">
        <INPUT type="hidden" name="courseid" value="'.$courseid.'">
        <INPUT type="hidden" name="practiceid" value="'.$practiceid.'">
        <INPUT type="hidden" name="userid" value="'.$userid.'">
        <INPUT type="hidden" name="filename" value="'.$filename.'">
        <INPUT type="hidden" name="sesskey" value="'.sesskey().'">
        <TABLE>
            <TR>
                <TD align=left valign=top>'.get_string('ask_confirm_del_file','block_gpracticas').$filename.'?<BR><BR></TD>
            </TR>
            <TR>
                <TD colspan=2 align=center valign=top>
                    <INPUT  type="submit" value="'.get_string('confirm_delete','block_gpracticas').'"><BR><BR>
                </TD>
            </TR>
        </TABLE>
        </FORM>';
} else {
    echo get_string('no_auth_del_file','block_gpracticas');
}
print_footer($course);

?>