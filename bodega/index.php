<?php
require_once __DIR__ . '/config/conexion.php';
require_once __DIR__ . '/src/RepositorioMySQL.php';

$repo = new RepositorioMySQL(conectar());
$productos = $repo->listarProductos();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bodega - Productos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0">Productos de la bodega</h1>
            <a href="venta.php" class="btn btn-primary">Nueva venta</a>
        </div>

        <div class="card shadow-sm">
            <div class="card-body p-0">
                <table class="table table-striped table-hover mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>Producto</th>
                            <th>Precio</th>
                            <th>Stock</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($productos)): ?>
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">No hay productos registrados.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($productos as $producto): ?>
                                <tr>
                                    <td><?= htmlspecialchars((string) $producto['id']); ?></td>
                                    <td><?= htmlspecialchars($producto['nombre']); ?></td>
                                    <td>S/ <?= number_format((float) $producto['precio'], 2, '.', ''); ?></td>
                                    <td>
                                        <?php if ((int) $producto['stock'] <= 0): ?>
                                            <span class="badge bg-danger">Sin stock</span>
                                        <?php else: ?>
                                            <span class="badge bg-success"><?= (int) $producto['stock']; ?></span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
