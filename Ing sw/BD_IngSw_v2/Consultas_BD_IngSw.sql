
USE gestor_tareas_v2;

-- Consulta de las tablas completas
SELECT * FROM usuario;
SELECT * FROM materia;
SELECT * FROM tarea;
SELECT * FROM estado;
SELECT * FROM priorizacion;

-- Consultas especificas 

SELECT m.nombre_materia, t.fecha_entrega, e.nombre_estado FROM tarea t
	JOIN materia m ON t.id_materia = m.id_materia
    JOIN estado e ON t.id_estado = e.id_estado;

SELECT u.nombre_usuario, m.nombre_materia, t.fecha_entrega FROM tarea t
	JOIN materia m ON t.id_materia = m.id_materia
	JOIN usuario u ON u.id_usuario = m.id_usuario
	WHERE m.activo = TRUE 							-- Quitar si quieren ver las vencidas
	ORDER BY t.fecha_entrega ASC;
    
SELECT m.nombre_materia, AVG(t.dificultad) AS promedio_dificultad FROM tarea t
	JOIN materia m ON t.id_materia = m.id_materia
	GROUP BY m.nombre_materia;
    
SELECT u.id_usuario, t.id_tarea, m.id_materia, m.color, t.descripcion, t.dificultad, t.fecha_creacion, t.fecha_entrega, t.id_estado FROM tarea t
	JOIN materia m ON t.id_materia = m.id_materia
	JOIN usuario u ON u.id_usuario = m.id_usuario
	