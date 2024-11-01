
-- Tabla para auditoría de productos
CREATE TABLE log_productos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    producto_id INT,
    accion ENUM('INSERT', 'UPDATE', 'DELETE'),
    campo_modificado VARCHAR(50),
    valor_anterior VARCHAR(255),
    valor_nuevo VARCHAR(255),
    usuario VARCHAR(50),
    fecha_cambio TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabla para auditoría de ventas
CREATE TABLE log_ventas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    venta_id INT,
    accion ENUM('INSERT', 'UPDATE', 'DELETE'),
    estado_anterior VARCHAR(20),
    estado_nuevo VARCHAR(20),
    usuario VARCHAR(50),
    fecha_cambio TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabla para historial de precios
CREATE TABLE historial_precios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    producto_id INT,
    precio_anterior DECIMAL(10,2),
    precio_nuevo DECIMAL(10,2),
    fecha_cambio TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    usuario VARCHAR(50)
);

-- Tabla para control de inventario
CREATE TABLE movimientos_inventario (
    id INT AUTO_INCREMENT PRIMARY KEY,
    producto_id INT,
    tipo_movimiento ENUM('ENTRADA', 'SALIDA'),
    cantidad INT,
    motivo VARCHAR(100),
    stock_anterior INT,
    stock_nuevo INT,
    fecha_movimiento TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


DELIMITER //

-- Trigger para auditar cambios en productos
CREATE TRIGGER tr_productos_update
AFTER UPDATE ON productos
FOR EACH ROW
BEGIN
    IF OLD.precio != NEW.precio THEN
        INSERT INTO log_productos (producto_id, accion, campo_modificado, valor_anterior, valor_nuevo, usuario)
        VALUES (NEW.id, 'UPDATE', 'precio', OLD.precio, NEW.precio, CURRENT_USER());
        
        INSERT INTO historial_precios (producto_id, precio_anterior, precio_nuevo, usuario)
        VALUES (NEW.id, OLD.precio, NEW.precio, CURRENT_USER());
    END IF;
    
    IF OLD.stock != NEW.stock THEN
        INSERT INTO log_productos (producto_id, accion, campo_modificado, valor_anterior, valor_nuevo, usuario)
        VALUES (NEW.id, 'UPDATE', 'stock', OLD.stock, NEW.stock, CURRENT_USER());
        
        INSERT INTO movimientos_inventario (
            producto_id,
            tipo_movimiento,
            cantidad,
            motivo,
            stock_anterior,
            stock_nuevo
        )
        VALUES (
            NEW.id,
            CASE 
                WHEN NEW.stock > OLD.stock THEN 'ENTRADA'
                ELSE 'SALIDA'
            END,
            ABS(NEW.stock - OLD.stock),
            'Actualización de stock',
            OLD.stock,
            NEW.stock
        );
    END IF;
END //

-- Trigger para validar stock antes de actualizar
CREATE TRIGGER tr_validar_stock
BEFORE UPDATE ON productos
FOR EACH ROW
BEGIN
    IF NEW.stock < 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'El stock no puede ser negativo';
    END IF;
END //

-- Trigger para auditar ventas
CREATE TRIGGER tr_ventas_update
AFTER UPDATE ON ventas
FOR EACH ROW
BEGIN
    INSERT INTO log_ventas (venta_id, accion, estado_anterior, estado_nuevo, usuario)
    VALUES (NEW.id, 'UPDATE', OLD.estado, NEW.estado, CURRENT_USER());
    
    -- Si la venta se cancela, restaurar el stock
    IF NEW.estado = 'cancelada' AND OLD.estado != 'cancelada' THEN
        UPDATE productos p
        JOIN detalles_venta dv ON p.id = dv.producto_id
        SET p.stock = p.stock + dv.cantidad
        WHERE dv.venta_id = NEW.id;
    END IF;
END //

-- Trigger para nuevos productos
CREATE TRIGGER tr_nuevos_productos
AFTER INSERT ON productos
FOR EACH ROW
BEGIN
    INSERT INTO log_productos (producto_id, accion, campo_modificado, valor_nuevo, usuario)
    VALUES (NEW.id, 'INSERT', 'nuevo_producto', NEW.nombre, CURRENT_USER());
    
    -- Si el stock inicial es mayor que 0, registrar como entrada de inventario
    IF NEW.stock > 0 THEN
        INSERT INTO movimientos_inventario (
            producto_id,
            tipo_movimiento,
            cantidad,
            motivo,
            stock_anterior,
            stock_nuevo
        )
        VALUES (
            NEW.id,
            'ENTRADA',
            NEW.stock,
            'Stock inicial',
            0,
            NEW.stock
        );
    END IF;
END //

DELIMITER ;

/////



-- Índices para la tabla productos
CREATE INDEX idx_productos_categoria ON productos(categoria_id);
CREATE INDEX idx_productos_precio ON productos(precio);
CREATE INDEX idx_productos_stock ON productos(stock);
CREATE INDEX idx_productos_nombre_precio ON productos(nombre, precio);

-- Índices para la tabla ventas
CREATE INDEX idx_ventas_fecha ON ventas(fecha_venta);
CREATE INDEX idx_ventas_cliente_fecha ON ventas(cliente_id, fecha_venta);
CREATE INDEX idx_ventas_estado ON ventas(estado);

-- Índices para la tabla detalles_venta
CREATE INDEX idx_detalles_producto ON detalles_venta(producto_id);
CREATE INDEX idx_detalles_compuesto ON detalles_venta(venta_id, producto_id);

-- Índices para búsquedas de texto
ALTER TABLE productos ADD FULLTEXT INDEX ft_idx_productos_nombre (nombre, descripcion);
        


        