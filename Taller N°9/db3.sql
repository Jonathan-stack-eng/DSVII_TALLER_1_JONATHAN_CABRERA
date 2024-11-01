ELIMITER //

-- Procedimiento para registrar una nueva venta
CREATE PROCEDURE sp_registrar_venta(
    IN p_cliente_id INT,
    IN p_producto_id INT,
    IN p_cantidad INT,
    OUT p_venta_id INT
)
BEGIN
    DECLARE v_precio DECIMAL(10,2);
    DECLARE v_subtotal DECIMAL(10,2);
    DECLARE v_stock INT;
    
    -- Verificar stock disponible
    SELECT stock, precio INTO v_stock, v_precio 
    FROM productos 
    WHERE id = p_producto_id;
    
    IF v_stock >= p_cantidad THEN
        -- Iniciar transacción
        START TRANSACTION;
        
        -- Calcular subtotal
        SET v_subtotal = v_precio * p_cantidad;
        
        -- Insertar venta
        INSERT INTO ventas (cliente_id, total, estado)
        VALUES (p_cliente_id, v_subtotal, 'completada');
        
        SET p_venta_id = LAST_INSERT_ID();
        
        -- Insertar detalle de venta
        INSERT INTO detalles_venta (venta_id, producto_id, cantidad, precio_unitario, subtotal)
        VALUES (p_venta_id, p_producto_id, p_cantidad, v_precio, v_subtotal);
        
        -- Actualizar stock
        UPDATE productos 
        SET stock = stock - p_cantidad 
        WHERE id = p_producto_id;
        
        -- Confirmar transacción
        COMMIT;
    ELSE
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Stock insuficiente';
    END IF;
END //

-- Procedimiento para obtener estadísticas de cliente
CREATE PROCEDURE sp_estadisticas_cliente(
    IN p_cliente_id INT
)
BEGIN
    SELECT 
        c.nombre,
        c.nivel_membresia,
        COUNT(v.id) as total_compras,
        COALESCE(SUM(v.total), 0) as total_gastado,
        COALESCE(AVG(v.total), 0) as promedio_compra,
        (SELECT GROUP_CONCAT(DISTINCT p.nombre)
         FROM ventas v2
         JOIN detalles_venta dv ON v2.id = dv.venta_id
         JOIN productos p ON dv.producto_id = p.id
         WHERE v2.cliente_id = p_cliente_id
         LIMIT 3) as ultimos_productos
    FROM clientes c
    LEFT JOIN ventas v ON c.id = v.cliente_id
    WHERE c.id = p_cliente_id
    GROUP BY c.id;
END //

-- Procedimiento para actualizar precios por categoría
CREATE PROCEDURE sp_actualizar_precios_categoria(
    IN p_categoria_id INT,
    IN p_porcentaje DECIMAL(5,2)
)
BEGIN
    DECLARE v_affected_rows INT;
    
    UPDATE productos
    SET precio = precio * (1 + p_porcentaje/100)
    WHERE categoria_id = p_categoria_id;
    
    SELECT ROW_COUNT() INTO v_affected_rows;
    
    SELECT 
        CONCAT('Se actualizaron ', v_affected_rows, ' productos. ',
               'Nuevo promedio de precios: $', 
               (SELECT AVG(precio) 
                FROM productos 
                WHERE categoria_id = p_categoria_id)
        ) as resultado;
END //

-- Procedimiento para reporte de ventas por período
CREATE PROCEDURE sp_reporte_ventas(
    IN p_fecha_inicio DATE,
    IN p_fecha_fin DATE
)
BEGIN
    SELECT 
        DATE(v.fecha_venta) as fecha,
        COUNT(DISTINCT v.id) as total_ventas,
        COUNT(DISTINCT v.cliente_id) as total_clientes,
        SUM(v.total) as ingresos_totales,
        AVG(v.total) as ticket_promedio,
        SUM(dv.cantidad) as productos_vendidos,
        GROUP_CONCAT(DISTINCT c.nombre) as compradores
    FROM ventas v
    JOIN detalles_venta dv ON v.id = dv.venta_id
    JOIN clientes c ON v.cliente_id = c.id
    WHERE DATE(v.fecha_venta) BETWEEN p_fecha_inicio AND p_fecha_fin
    GROUP BY DATE(v.fecha_venta);
END //

DELIMITER ;