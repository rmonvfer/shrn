DROP DATABASE IF EXISTS SHRN;
CREATE DATABASE SHRN;
USE SHRN;

DROP TABLE IF EXISTS COMENTARIO;
DROP TABLE IF EXISTS USUARIO;
DROP TABLE IF EXISTS CONSULTA;

CREATE TABLE USUARIO
(
    id_usuario     VARCHAR(128) DEFAULT (uuid()) NOT NULL,
    clave_usuario  VARCHAR(128) NOT NULL,
    nombre_usuario VARCHAR(128) NOT NULL,
    shodan_key     VARCHAR(255) DEFAULT 'JgF8iUdjxdODTma08wfw2SySkJiGLBmK' NOT NULL,

    CONSTRAINT pk_usuario         PRIMARY KEY (id_usuario),
    CONSTRAINT uq_nombre_usuario  UNIQUE      (nombre_usuario)
);

CREATE TABLE COMENTARIO
(
    id_comentario         VARCHAR(128) NOT NULL DEFAULT (uuid()),
    id_usuario            VARCHAR(128) NOT NULL, -- FK de USUARIO
    id_consulta           VARCHAR(128) NOT NULL, -- FK de CONSULTA
    contenido_comentario  TEXT         NOT NULL,
    valoracion_comentario INTEGER      NOT NULL,
    p_timestamp           DATETIME     NOT NULL DEFAULT NOW(),

    CONSTRAINT pk_comentario            PRIMARY KEY (id_comentario),
    CONSTRAINT fk_comentario_usuario    FOREIGN KEY (id_usuario) REFERENCES USUARIO (id_usuario),
    CONSTRAINT ck_valoracion_comentario CHECK (0 <= valoracion_comentario )
);

CREATE TABLE CONSULTA
(
    id_consulta          VARCHAR(128) NOT NULL DEFAULT (uuid()),
    id_usuario           VARCHAR(128) NOT NULL, -- FK de Usuario
    votos_consulta       INTEGER      NOT NULL DEFAULT 0,
    titulo_consulta      VARCHAR(128) NOT NULL,
    descripcion_consulta VARCHAR(128) NOT NULL,
    contenido_consulta   TEXT         NOT NULL,
    g_timestamp          DATETIME     NOT NULL DEFAULT NOW(),

    CONSTRAINT pk_consulta         PRIMARY KEY (id_consulta),
    CONSTRAINT fk_consulta_usuario FOREIGN KEY (id_usuario) REFERENCES USUARIO (id_usuario)
);

ALTER TABLE COMENTARIO ADD
    CONSTRAINT fk_comentario_consulta FOREIGN KEY (id_consulta)
        REFERENCES CONSULTA (id_consulta) ON DELETE NO ACTION;

CREATE UNIQUE INDEX idx_nombre_usuario ON USUARIO (nombre_usuario);

-- Crear usuario principal
DROP USER IF EXISTS 'DBUSER2020'@'localhost';
CREATE USER 'DBUSER2020'@'localhost' IDENTIFIED BY 'DBPSWD2020';

-- Añadir permisos
GRANT ALL PRIVILEGES ON *.* TO 'DBUSER2020'@'localhost';
FLUSH PRIVILEGES;