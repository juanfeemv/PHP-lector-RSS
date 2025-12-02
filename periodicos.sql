/* 1. Limpiar tablas viejas si existen */
DROP TABLE IF EXISTS elpais;
DROP TABLE IF EXISTS elmundo;

/* 2. Crear tabla El País (Sintaxis PostgreSQL) */
CREATE TABLE elpais (
    id SERIAL PRIMARY KEY,
    titulo VARCHAR(500) NOT NULL,
    link VARCHAR(2048) UNIQUE,
    descripcion TEXT,
    categoria VARCHAR(255),
    "fPubli" DATE, 
    contenido TEXT
);

/* 3. Crear tabla El Mundo (Sintaxis PostgreSQL) */
CREATE TABLE elmundo (
    id SERIAL PRIMARY KEY,
    titulo VARCHAR(500) NOT NULL,
    link VARCHAR(2048) UNIQUE,
    descripcion TEXT,
    categoria VARCHAR(255),
    "fPubli" DATE,
    contenido TEXT
);