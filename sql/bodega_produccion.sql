CREATE TABLE producto (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    nombre VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    precio DECIMAL(10,2) NOT NULL,
    stock INT UNSIGNED NOT NULL DEFAULT 0,
    PRIMARY KEY (id),
    CHECK (precio > 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE venta (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    fecha_hora DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    subtotal DECIMAL(10,2) NOT NULL,
    descuento DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    total DECIMAL(10,2) NOT NULL,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE detalle_venta (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    id_venta INT UNSIGNED NOT NULL,
    id_producto INT UNSIGNED NOT NULL,
    cantidad INT UNSIGNED NOT NULL,
    precio_unitario DECIMAL(10,2) NOT NULL,
    PRIMARY KEY (id),
    FOREIGN KEY (id_venta) REFERENCES venta(id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,
    FOREIGN KEY (id_producto) REFERENCES producto(id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,
    CHECK (cantidad > 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO producto (nombre, precio, stock) VALUES
('arroz', 5.50, 20),
('aceite', 12.00, 10),
('leche', 3.20, 0),
('azúcar', 4.80, 15),
('atún', 8.90, 7),
('gaseosa', 6.50, 30);
