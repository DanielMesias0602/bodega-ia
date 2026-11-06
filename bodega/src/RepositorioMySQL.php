<?php

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/ServicioVenta.php';

class RepositorioMySQL
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function listarProductos(): array
    {
        $stmt = $this->pdo->query('SELECT * FROM producto ORDER BY nombre ASC');
        return $stmt->fetchAll();
    }

    public function obtenerProducto($id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM producto WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $id]);
        $producto = $stmt->fetch();

        return $producto ?: null;
    }

    public function registrarVenta($cliente, $items)
    {
        $cliente = trim((string) $cliente);

        if ($cliente === '') {
            throw new InvalidArgumentException('El cliente es obligatorio.');
        }

        ServicioVenta::validarVenta($items, $this->listarProductos());

        $subtotal = ServicioVenta::calcularSubtotal($items);
        $descuento = ServicioVenta::calcularDescuento($subtotal);
        $total = ServicioVenta::calcularTotal($subtotal);

        $this->pdo->beginTransaction();

        try {
            $stmtVenta = $this->pdo->prepare(
                'INSERT INTO venta (fecha_hora, subtotal, descuento, total) VALUES (NOW(), ?, ?, ?)'
            );
            $stmtVenta->execute([$subtotal, $descuento, $total]);
            $idVenta = (int) $this->pdo->lastInsertId();

            foreach ($items as $item) {
                $idProducto = (int) ($item['id'] ?? 0);
                $cantidad = (int) ($item['cantidad'] ?? 0);
                $producto = $this->obtenerProducto($idProducto);

                if ($producto === null) {
                    throw new InvalidArgumentException('Producto no encontrado.');
                }

                $stmtDetalle = $this->pdo->prepare(
                    'INSERT INTO detalle_venta (id_venta, id_producto, cantidad, precio_unitario) VALUES (?, ?, ?, ?)'
                );
                $stmtDetalle->execute([
                    $idVenta,
                    $idProducto,
                    $cantidad,
                    $producto['precio'],
                ]);

                $stmtUpdate = $this->pdo->prepare(
                    'UPDATE producto SET stock = stock - ? WHERE id = ?'
                );
                $stmtUpdate->execute([$cantidad, $idProducto]);
            }

            $this->pdo->commit();
            return $idVenta;
        } catch (Throwable $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }
}
