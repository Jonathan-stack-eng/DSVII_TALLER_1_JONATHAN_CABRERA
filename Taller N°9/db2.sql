
-- Vista para resumen de productos por categoría
CREATE VIEW vista_resumen_categorias AS
SELECT 
    c.nombre AS categoria,
    COUNT(p.id) AS total_productos,
    SUM(p.stock) AS total_stock,
    ROUND(AVG(p.precio), 2) AS precio_promedio,
    MIN(p.precio) AS precio_minimo,
    MAX(p.precio) AS precio_maximo
FROM categorias c
LEFT JOIN productos p ON c.id = p.categoria_id
GROUP BY c.id, c.nombre;

-- Vista para detalles de ventas completas
CREATE VIEW vista_detalles_ventas AS
SELECT 
    v.id AS venta_id,
    c.nombre AS cliente,
    c.email AS cliente_email,
    p.nombre AS producto,
    dv.cantidad,
    dv.precio_unitario,
    dv.subtotal,
    v.fecha_venta,
    v.estado
FROM ventas v
JOIN clientes c ON v.cliente_id = c.id
JOIN detalles_venta dv ON v.id = dv.venta_id
JOIN productos p ON dv.producto_id = p.id;

-- Vista para productos más vendidos
CREATE VIEW vista_productos_populares AS
SELECT 
    p.id,
    p.nombre AS producto,
    p.precio,
    cat.nombre AS categoria,
    SUM(dv.cantidad) AS total_vendido,
    SUM(dv.subtotal) AS ingresos_totales,
    COUNT(DISTINCT v.cliente_id) AS compradores_unicos
FROM productos p
JOIN categorias cat ON p.categoria_id = cat.id
LEFT JOIN detalles_venta dv ON p.id = dv.producto_id
LEFT JOIN ventas v ON dv.venta_id = v.id
GROUP BY p.id, p.nombre, p.precio, cat.nombre
ORDER BY total_vendido DESC;

-- Vista para rendimiento de clientes
CREATE VIEW vista_rendimiento_clientes AS
SELECT 
    c.id,
    c.nombre,
    c.email,
    c.nivel_membresia,
    COUNT(v.id) AS total_compras,
    SUM(v.total) AS total_gastado,
    ROUND(AVG(v.total), 2) AS promedio_compra,
    MAX(v.fecha_venta) AS ultima_compra
FROM clientes c
LEFT JOIN ventas v ON c.id = v.cliente_id AND v.estado = 'completada'
GROUP BY c.id, c.nombre, c.email, c.nivel_membresia;
