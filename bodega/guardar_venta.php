<?php
require_once __DIR__ . '/config/conexion.php';
require_once __DIR__ . '/src/ServicioVenta.php';
require_once __DIR__ . '/src/RepositorioMySQL.php';

$repo = new RepositorioMySQL(conectar());
$cliente = trim((string) ($_POST['cliente'] ?? ''));
$productosPost = $_POST['producto'] ?? [];
$cantidadesPost = $_POST['cantidad'] ?? [];
$items = [];

if ($cliente === '') {
    echo renderVentaPage('El cliente es obligatorio.');
    exit;
}

foreach ($productosPost as $index => $idProducto) {
    $idProducto = (int) $idProducto;
    if ($idProducto <= 0) {
        continue;
    }

    $cantidad = isset($cantidadesPost[$index]) ? (int) $cantidadesPost[$index] : 0;
    $producto = $repo->obtenerProducto($idProducto);

    if ($producto === null) {
        echo renderVentaPage('Producto no encontrado.');
        exit;
    }

    $items[] = [
        'id' => $idProducto,
        'cantidad' => $cantidad,
        'precio' => (float) $producto['precio'],
    ];
}

if (empty($items)) {
    echo renderVentaPage('Debe agregar al menos un producto.');
    exit;
}

try {
    $productos = $repo->listarProductos();
    ServicioVenta::validarVenta($items, $productos);

    $subtotal = ServicioVenta::calcularSubtotal($items);
    $descuento = ServicioVenta::calcularDescuento($subtotal);
    $total = ServicioVenta::calcularTotal($subtotal);
    $idVenta = $repo->registrarVenta($cliente, $items);

    echo renderResultado('Venta registrada correctamente.', [
        'cliente' => $cliente,
        'idVenta' => $idVenta,
        'subtotal' => $subtotal,
        'descuento' => $descuento,
        'total' => $total,
    ]);
} catch (Throwable $e) {
    echo renderResultado('Error: ' . $e->getMessage(), [], true);
}

function renderVentaPage(string $mensaje): string
{
    $repo = new RepositorioMySQL(conectar());
    $productos = $repo->listarProductos();

    $html = '<!DOCTYPE html>';
    $html .= '<html lang="es"><head>';
    $html .= '<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">';
    $html .= '<title>Bodega - Error</title>';
    $html .= '<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">';
    $html .= '</head><body class="bg-light"><div class="container py-5">';
    $html .= '<div class="alert alert-danger" role="alert">' . htmlspecialchars($mensaje) . '</div>';
    $html .= '<a href="venta.php" class="btn btn-primary">Volver al formulario</a>';
    $html .= '</div></body></html>';

    return $html;
}

function renderResultado(string $mensaje, array $datos = [], bool $esError = false): string
{
    $class = $esError ? 'alert-danger' : 'alert-success';

    $html = '<!DOCTYPE html>';
    $html .= '<html lang="es"><head>';
    $html .= '<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">';
    $html .= '<title>Bodega - Resultado</title>';
    $html .= '<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">';
    $html .= '</head><body class="bg-light"><div class="container py-5">';
    $html .= '<div class="alert ' . $class . '" role="alert">' . htmlspecialchars($mensaje) . '</div>';

    if (!empty($datos)) {
        $html .= '<div class="card shadow-sm"><div class="card-body">';
        $html .= '<p><strong>Cliente:</strong> ' . htmlspecialchars($datos['cliente']) . '</p>';
        $html .= '<p><strong>Venta ID:</strong> ' . (int) $datos['idVenta'] . '</p>';
        $html .= '<p><strong>Subtotal:</strong> S/ ' . number_format((float) $datos['subtotal'], 2, '.', '') . '</p>';
        $html .= '<p><strong>Descuento:</strong> S/ ' . number_format((float) $datos['descuento'], 2, '.', '') . '</p>';
        $html .= '<p><strong>Total:</strong> S/ ' . number_format((float) $datos['total'], 2, '.', '') . '</p>';
        $html .= '</div></div>';
    }

    $html .= '<div class="mt-3 d-flex gap-2">';
    $html .= '<a href="index.php" class="btn btn-primary">Ver productos</a>';
    $html .= '<a href="venta.php" class="btn btn-outline-dark">Nueva venta</a>';
    $html .= '</div>';
    $html .= '</div></body></html>';

    return $html;
}
