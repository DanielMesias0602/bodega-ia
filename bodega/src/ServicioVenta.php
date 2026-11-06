<?php

class ServicioVenta
{
    public static function validarCantidad(int $cantidad): bool
    {
        return $cantidad > 0;
    }

    public static function hayStock(int $cantidad, int $stock): bool
    {
        return $cantidad > 0 && $cantidad <= $stock;
    }

    public static function validarVenta(array $items, array $productos): void
    {
        if (empty($items)) {
            throw new InvalidArgumentException('Debe agregar al menos un producto.');
        }

        $acumuladoPorProducto = [];

        foreach ($items as $item) {
            $idProducto = (int) ($item['id'] ?? 0);
            $cantidadRaw = $item['cantidad'] ?? null;

            if (is_float($cantidadRaw) || is_double($cantidadRaw) || (is_string($cantidadRaw) && trim($cantidadRaw) !== '' && !preg_match('/^-?\d+$/', trim($cantidadRaw)))) {
                throw new InvalidArgumentException('La cantidad debe ser un número entero mayor que cero');
            }

            $cantidad = (int) $cantidadRaw;

            if (!self::validarCantidad($cantidad)) {
                throw new InvalidArgumentException('La cantidad debe ser un número entero mayor que cero');
            }

            $producto = null;
            foreach ($productos as $productoActual) {
                if ((int) ($productoActual['id'] ?? 0) === $idProducto) {
                    $producto = $productoActual;
                    break;
                }
            }

            if ($producto === null) {
                throw new InvalidArgumentException('Producto no encontrado.');
            }

            $acumuladoPorProducto[$idProducto] = ($acumuladoPorProducto[$idProducto] ?? 0) + $cantidad;

            if ($acumuladoPorProducto[$idProducto] > (int) ($producto['stock'] ?? 0)) {
                throw new InvalidArgumentException('Stock insuficiente para ' . $producto['nombre']);
            }
        }
    }

    public static function calcularSubtotal(array $items): float
    {
        $subtotal = 0.0;

        foreach ($items as $item) {
            $cantidad = isset($item['cantidad']) ? (int) $item['cantidad'] : 0;
            $precio = isset($item['precio']) ? (float) $item['precio'] : 0.0;
            $subtotal += $cantidad * $precio;
        }

        return round($subtotal, 2);
    }

    public static function calcularDescuento(float $subtotal): float
    {
        if ($subtotal > 100) {
            return round($subtotal * 0.05, 2);
        }

        return 0.0;
    }

    public static function calcularTotal(float $subtotal): float
    {
        return round($subtotal - self::calcularDescuento($subtotal), 2);
    }
}
