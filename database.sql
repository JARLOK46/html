-- Base de datos para el Mercado Campesino
-- Creación de la base de datos
CREATE DATABASE IF NOT EXISTS mercado_campesino;
USE mercado_campesino;

-- Tabla campesinos
-- Almacena la información básica de cada campesino
CREATE TABLE campesinos (
    numero_documento VARCHAR(20) PRIMARY KEY,  -- Número de documento único
    nombre VARCHAR(100) NOT NULL,              -- Nombre del campesino
    telefono VARCHAR(15) NOT NULL,             -- Teléfono de contacto
    nombre_finca VARCHAR(100) NOT NULL,        -- Nombre de la finca
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP  -- Fecha de registro automática
);

-- Tabla productos
-- Almacena los productos que ofrece cada campesino
CREATE TABLE productos (
    codigo VARCHAR(20) PRIMARY KEY,            -- Código único del producto
    nombre VARCHAR(100) NOT NULL,              -- Nombre del producto
    unidad_medida ENUM('gramos', 'unidades', 'libras', 'kilogramos') NOT NULL,  -- Unidad de medida
    cantidad DECIMAL(10,2) NOT NULL,           -- Cantidad disponible
    numero_documento_campesino VARCHAR(20) NOT NULL,  -- Referencia al campesino
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,  -- Fecha de registro
    
    -- Clave foránea que conecta el producto con el campesino
    FOREIGN KEY (numero_documento_campesino) REFERENCES campesinos(numero_documento) 
        ON DELETE CASCADE ON UPDATE CASCADE
);

-- Índices para mejorar el rendimiento de las consultas
CREATE INDEX idx_productos_nombre ON productos(nombre);
CREATE INDEX idx_campesinos_nombre ON campesinos(nombre);

-- Datos de ejemplo para pruebas (opcional)
INSERT INTO campesinos (numero_documento, nombre, telefono, nombre_finca) VALUES
('12345678', 'Juan Pérez', '3001234567', 'Finca La Esperanza'),
('87654321', 'María González', '3009876543', 'Finca El Paraíso');

INSERT INTO productos (codigo, nombre, unidad_medida, cantidad, numero_documento_campesino) VALUES
('PROD001', 'Tomate', 'kilogramos', 50.00, '12345678'),
('PROD002', 'Lechuga', 'unidades', 30.00, '12345678'),
('PROD003', 'Zanahoria', 'libras', 25.00, '87654321');