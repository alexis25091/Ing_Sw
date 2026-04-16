/*
	Aquí se encuentra el cascaron de la BD, creando las tablas y definiendo su contenido
    tal cual el diagrama normalizado.
*/

CREATE DATABASE gestor_tareas_v2;
USE gestor_tareas_v2;

CREATE TABLE usuario (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    nombre_usuario VARCHAR(50) NOT NULL,
    correo VARCHAR(100) NOT NULL UNIQUE,
    contrasena VARCHAR(255) NOT NULL
);

CREATE TABLE materia (
    id_materia INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    nombre_materia VARCHAR(100) NOT NULL,
    dificultad INT NOT NULL,
    color VARCHAR(100) NOT NULL,
    activo BOOLEAN DEFAULT TRUE, -- Agregado para que no se elimine y se mantenga en el historial
    FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario)
);

CREATE TABLE estado (
    id_estado INT PRIMARY KEY,
    nombre_estado VARCHAR(20) NOT NULL
);

CREATE TABLE tarea (
    id_tarea INT AUTO_INCREMENT PRIMARY KEY,
    id_materia INT NOT NULL,
    -- titulo VARCHAR(100) NOT NULL, 
    descripcion TEXT,
    dificultad INT NOT NULL CHECK (dificultad BETWEEN 1 AND 10),
    fecha_entrega DATETIME NOT NULL,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP, -- AGREGADO
    id_estado INT NOT NULL,
    FOREIGN KEY (id_materia) REFERENCES materia(id_materia)
		ON DELETE CASCADE,
    FOREIGN KEY (id_estado) REFERENCES estado(id_estado)
);

CREATE TABLE priorizacion (
    id_priorizacion INT AUTO_INCREMENT PRIMARY KEY,
    id_tarea INT NOT NULL,
    puntaje_final DECIMAL(5,2),
    nivel_prioridad ENUM('baja','media','alta','muy alta'),
	fecha_calculo TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_tarea) REFERENCES tarea(id_tarea)
        ON DELETE CASCADE
);