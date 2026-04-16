/*
	Llenado de las tablas, con inserts.
    
    Recomiendo NO BORRAR los datos de la tabla Estado (1er insert), 
    ya que así lo manejamos en los documentos, además van ligados directamente
    al uso del tablero tipo Kanban.
*/
USE gestor_tareas_v2;

INSERT INTO estado VALUES
	(1, 'asignada'),
	(2, 'en proceso'),
	(3, 'terminada');

INSERT INTO usuario VALUES
	('1', 'Diana Rico', 'diana@correo.com', '1234'),
	('2', 'Carlos Lopez Hernandez', 'carlos@correo.com', 'abcd');

INSERT INTO usuario (nombre_usuario, correo, contrasena) VALUES
	('Oscar Ramirez', 'oscar@correo.com', '1234');

INSERT INTO materia (id_usuario, nombre_materia, dificultad, color, activo) VALUES
	('1', 'Bases de Datos', 9, '#8EC5FF', TRUE),
	('1', 'Inteligencia Artificial', 8, '#C82909', TRUE),
	('2', 'Programacion', 8, '#BA5FFC', TRUE),
	('2', 'Probabilidad', 10, '#FCCEE8', FALSE); -- Caso de materia inactiva

INSERT INTO materia (id_usuario, nombre_materia, dificultad, color, activo) VALUES
	((SELECT id_usuario FROM usuario WHERE correo='oscar@correo.com'), 'Probabilidad', 10, '#BBF451', TRUE);

INSERT INTO tarea VALUES
	('1', 1, 'Proyecto BD Diseñar base de datos', 4, '2026-04-10 23:59:00', NOW(), 1),
	('2', 1, 'Normalizacion', 3, '2026-04-05 20:00:00', NOW(), 2),
	('3', 2, 'Modelo IA', 5, '2026-04-03 18:00:00', NOW(), 1),
    ('4', 3, 'Algoritmos Voraces', 5, '2026-03-30 13:30:00', NOW(), 2),
	('5', 4, 'Ejercicios de esperanza', 2, '2026-03-20 18:00:00', NOW(), 3);

INSERT INTO tarea (id_materia, descripcion, dificultad, fecha_entrega, fecha_creacion, id_estado) VALUES
	(5, 'Ejercicios de varianza', 3, '2026-04-20 23:00:00', NOW(), 2);

INSERT INTO priorizacion (id_tarea, puntaje_final, nivel_prioridad, fecha_calculo) VALUES
	('1', 85.50, 'alta', NOW()),
	('2', 70.00, 'media', NOW()),
	('3', 95.00, 'muy alta', NOW()),
    ('4', 70.00, 'alta', NOW()),
    ('5', 70.00, 'baja', NOW());

INSERT INTO priorizacion (id_tarea, puntaje_final, nivel_prioridad) VALUES
    ((SELECT id_tarea FROM tarea WHERE  descripcion= 'Ejercicios de varianza'), 30.00, 'baja');

