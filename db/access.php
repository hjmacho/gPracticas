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

$block_gpracticas_capabilities = array (
    'block/gpracticas:teacher' => array (
        'captype'      => 'read',
        'contextlevel' => CONTEXT_COURSE,
        'legacy' => array (
            'guest'          => CAP_PREVENT,
            'student'        => CAP_PREVENT,
            'teacher'        => CAP_ALLOW,
            'editingteacher' => CAP_ALLOW,
            'coursecreator'  => CAP_ALLOW,
            'admin'          => CAP_ALLOW
        )
    ),
    'block/gpracticas:student' => array (
        'captype'      => 'read',
        'contextlevel' => CONTEXT_COURSE,
        'legacy' => array (
            'guest'          => CAP_PREVENT,
            'student'        => CAP_ALLOW,
            'teacher'        => CAP_PREVENT,
            'editingteacher' => CAP_PREVENT,
            'coursecreator'  => CAP_PREVENT,
            'admin'          => CAP_PREVENT
        ) 
    )
);
?>
