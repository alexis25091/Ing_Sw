/*
	Aquí se encuentra el cascaron de la BD, creando las tablas y definiendo su contenido
    tal cual el diagrama normalizado.
*/

CREATE DATABASE NextTask;
USE NextTask;

CREATE TABLE usuarios (
    id INT NOT NULL AUTO_INCREMENT,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    password VARCHAR(100) NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY (email)
);

CREATE TABLE materias (
    id INT NOT NULL AUTO_INCREMENT,
    user_id INT NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    color VARCHAR(100) NOT NULL,
    estado TINYINT(1) DEFAULT 1,
    dificultad INT DEFAULT 1,
    PRIMARY KEY (id),
    CONSTRAINT fk_materias_usuario
        FOREIGN KEY (user_id) REFERENCES usuarios(id)
);

CREATE TABLE tareas (
    id INT NOT NULL AUTO_INCREMENT,
    dificultad TINYINT NOT NULL,
    fecha_limite DATETIME NOT NULL,
    detalles TEXT,
    estado ENUM('asignada', 'en_proceso', 'completada') NOT NULL,
    creado_en TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    user_id INT NOT NULL,
    materia_id INT NOT NULL,
    PRIMARY KEY (id),
    CONSTRAINT fk_tareas_usuario
        FOREIGN KEY (user_id) REFERENCES usuarios(id),
    CONSTRAINT fk_tareas_materia
        FOREIGN KEY (materia_id) REFERENCES materias(id)
        ON DELETE CASCADE
);
