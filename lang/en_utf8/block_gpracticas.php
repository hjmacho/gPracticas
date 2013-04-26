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

   $string['blockname'] = 'gPracticas';


   $string['yes'] = 'SÍ';
   $string['no'] = 'NO';
   $string['save_changes'] = 'Guardar cambios';
   $string['delete'] = 'Borrar';
   $string['mark'] = 'Nota';
   $string['marks'] = 'Notas';
   $string['comments'] = 'Comentarios';
   $string['user_id'] = 'ID usuario';
   $string['user'] = 'Usuario';
   $string['user_name'] = 'Nombre';
   $string['student_comments'] = 'Comentarios para el alumno';
   $string['no_configured'] = 'Es necesario configurar el bloque antes de poder seguir utilizándolo.';
   $string['update_bd_ok'] = 'La base de datos se ha actualizado correctamente.';


   $string['practice'] = 'Práctica';
   $string['practices'] = 'Prácticas';
   $string['newpractice'] = 'Nueva práctica';
   $string['addpractice'] = 'Añadir práctica';
   $string['practice_name'] = 'Nombre de la práctica';
   $string['startingdate'] = 'Fecha inicio';
   $string['endingdate'] = 'Fecha fin';
   $string['description'] = 'Descripción';
   $string['flexible_description'] = 'Permitir entrega fuera de plazo';
   $string['visible_description'] = 'Visible para los alumnos';
   $string['no_visible'] = 'No visible para los alumnos';
   $string['unzip_description'] = 'Descomprimir automáticamente ficheros subidos por los alumnos';
   $string['reopen_description'] = 'Permitir modificar la entrega una vez cerrada';
   $string['no_auth_add_practice'] = 'No está autorizado a añadir nuevas prácticas a este curso.';
   $string['del_all_practices'] = 'Borrar todas las prácticas';
   $string['practices_del_ok'] = 'Las prácticas se borraron correctamente.';
   $string['no_auth_del_practice'] = 'No está autorizado para borrar prácticas de este curso.';
   $string['no_auth_edit_practices'] = 'No está autorizado a editar las prácticas de este curso.';
   $string['practice_del_ok'] = 'La práctica se ha borrado correctamente.';
   $string['confirm_delete'] = 'Confirmar borrado';
   $string['ask_confirm_delete'] = '¿Desea borrar las prácticas correspondientes a';
   $string['del_practice'] = 'Borrar práctica';
   $string['del_practice_ok'] = 'La práctica se ha borrado correctamente';
   $string['not_practices_yet'] = 'Aún no se han definido prácticas correspondientes a este bloque.';
   $string['editpractice'] = 'Editar práctica';
   $string['info_del_practice'] = 'Al borrar la práctica se borrará <u>toda</u> la información referente a ella, incluida la información referente a las entregas por parte de los alumnos de dicha práctica (nota, fecha de entrega, ...). ¿Está seguro de que desea borrar esta práctica?.';
   $string['add_practice_ok'] = 'La práctica se ha añadido correctamente a la base de datos.';
   $string['end_date_delivery'] = 'Fin plazo entrega práctica';
   $string['info_script'] = 'Si lo desea puede subir un script de shell para que se ejecute cada vez que el alumno entregue un fichero. Dicho script debera llamarse <i>script.sh</i> y al ejecutarse recibirá como argumentos la ruta relativa del directorio donde se encuentran los ficheros subidos por el alumno y el nombre de usuario del alumno.<BR>';
   $string['info_files_not_del'] = 'Nota: </b><i>Los ficheros subidos por los alumnos no se borrarán al realizar esta acción y en caso de querer borrarlos será necesario hacerlo posteriormente de forma manual.';



   $string['deliveries_of_practice'] = 'Entregas correspondientes a la práctica:';
   $string['deliveries'] = 'Entregas';
   $string['closedelivery'] = 'Cerrar entrega';
   $string['reopendelivery'] = 'Reabrir entrega';
   $string['deletedelivery'] = 'Borrar entrega';
   $string['delivery_date'] = 'Fecha de entrega';
   $string['in_closing_date'] = 'Entregada dentro de plazo';
   $string['no_auth_del_delivery'] = 'No está autorizado a borrar la entrega.';
   $string['no_auth_see_delivery'] = 'No está autorizado para ver las entregas correspondientes a esta práctica.';
   $string['no_deliveries'] = 'Ningún alumno ha entregado la práctica.';
   $string['see_delivery'] = 'Ver entrega';
   $string['no_auth_edit_delivery'] = 'No estás autorizado para ver/modificar esta entrega.';
   $string['deliver_practice'] = 'Entregar práctica';
   $string['delivered'] = 'Entregada';
   $string['add_marks_ok'] = 'Las notas se han añadido correctamente a la base de datos.';
   $string['no_auth_see_marks'] = 'No estás autorizado para ver las notas.';
   $string['no_auth_mark_delivery'] = 'No está autorizado para modificar las notas de esta práctica.';
   $string['no_auth_edit_practice'] = 'No está autorizado a editar esta práctica.';
   $string['script_ok'] = 'El script se ha ejecutado correctamente.';
   $string['script_yet'] = 'El script aún no se ha ejecutado.';
   $string['script_invalid'] = 'El script no es válido.';
   $string['no_auth_delivery'] = 'No está autorizado a realizar operaciones respecto a esta entrega.';
   $string['no_auth_reopen_delivery'] = 'No está autorizado a reabrir la entrega de la práctica.';
   $string['info_del_delivery'] = 'Al borrar la entrega se borraran todos los ficheros subidos y la práctica constará como no entregada. ¿Está seguro de que quiere continuar?';



   $string['uploadfile'] = 'Subir fichero';
   $string['file_doesnt_exist'] = 'El fichero no existe o no es legible.';
   $string['no_auth_del_file'] = 'No está autorizado para borrar el fichero.';
   $string['no_auth_up_file'] = 'No está autorizado para subir ficheros.';
   $string['see_files'] = 'Ver ficheros';
   $string['download_file'] = 'Descargar fichero';
   $string['delete_file'] = 'Borrar fichero';
   $string['info_file_name'] = 'El nombre del fichero sólo puede contener caracteres alfanuméricos, guiones, guiones bajos o puntos (Tamaño máximo permitido: ';
   $string['files_delivered'] = 'Ficheros entregados';
   $string['last_modified'] = 'Última modificación';
   $string['actions'] = 'Acciones';
   $string['file_name'] = 'Nombre fichero';
   $string['downloading'] = 'Descargando';
   $string['no_auth_down_file'] = 'No está autorizado para descargar este fichero.';
   $string['no_exists_file'] = 'El fichero solicitado no se encuentra o no es legible.';
   $string['ask_confirm_del_file'] = '¿Está seguro de que desea borrar el fichero: ';

   $string['err_read_db'] = 'Error al leer la base de datos.';
   $string['err_extract_files'] = 'No se han podido extraer los ficheros.';
   $string['err_up_file'] = 'Error al subir el fichero';
   $string['err_max_size_exceed'] = 'Error: tamaño máximo permitido por el servidor excedido.'; 
   $string['err_add_practice'] = 'Error al insertar la práctica en la base de datos.';
   $string['err_update_practice'] = 'Error al actualizar la práctica.';
   $string['err_add_delivery'] = 'Error al insertar la entrega en la base de datos.';
   $string['err_update_delivery'] = 'Error al actualizar la entrega.';
   $string['err_del_event_db'] = 'Error al borrar el evento de la base de datos.';
   $string['err_del_deliveries_db'] = 'Error al borrar las entregas de la base de datos.';
   $string['err_del_practice_db'] = 'Error al borrar la práctica de la base de datos.';
   $string['err_file_name'] = 'Nombre de fichero no válido.';
   $string['err_add_event_bd'] = 'Error al añadir el evento al calendario.';
   $string['err_update_event_bd'] = 'Error al actualizar el evento.';
?>
