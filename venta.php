<?php
require_once __DIR__ . '/config/conexion.php';
require_once __DIR__ . '/src/RepositorioMySQL.php';

$repo = new RepositorioMySQL(conectar());
$productos = $repo->listarProductos();
$error = $_GET['error'] ?? '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bodega - Nueva venta</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0">Registrar venta</h1>
            <a href="index.php" class="btn btn-outline-dark">Volver a productos</a>
        </div>

        <?php if ($error !== ''): ?>
            <div class="alert alert-danger" role="alert"><?= htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form action="guardar_venta.php" method="post" class="card shadow-sm">
            <div class="card-body">
                <div class="mb-3">
                    <label for="cliente" class="form-label">Cliente</label>
                    <input type="text" class="form-control" id="cliente" name="cliente" value="" required>
                </div>

                <div id="items">
                    <div class="row g-3 item-row align-items-end">
                        <div class="col-md-6">
                            <label class="form-label">Producto</label>
                            <select class="form-select" name="producto[]" required>
                                <option value="">Seleccione un producto</option>
                                <?php foreach ($productos as $producto): ?>
                                    <option value="<?= (int) $producto['id']; ?>">
                                        <?= htmlspecialchars($producto['nombre']); ?> (Stock: <?= (int) $producto['stock']; ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Cantidad</label>
                            <input type="number" class="form-control" name="cantidad[]" min="1" step="1" value="1" required>
                        </div>
                        <div class="col-md-2">
                            <button type="button" class="btn btn-outline-danger remove-item">Quitar</button>
                        </div>
                    </div>
                </div>

                <div class="mt-3 d-flex justify-content-between align-items-center">
                    <button type="button" id="add-item" class="btn btn-secondary">Agregar línea</button>
                    <button type="submit" class="btn btn-primary">Guardar venta</button>
                </div>
            </div>
        </form>
    </div>

    <script>
        const itemsContainer = document.getElementById('items');
        const addItemButton = document.getElementById('add-item');

        function createItemRow() {
            const row = document.querySelector('.item-row').cloneNode(true);
            row.querySelectorAll('select, input').forEach((field) => {
                if (field.name === 'producto[]') {
                    field.selectedIndex = 0;
                }
                if (field.name === 'cantidad[]') {
                    field.value = 1;
                }
            });
            return row;
        }

        addItemButton.addEventListener('click', () => {
            itemsContainer.appendChild(createItemRow());
        });

        document.addEventListener('click', (event) => {
            if (event.target && event.target.classList.contains('remove-item')) {
                const rows = document.querySelectorAll('.item-row');
                if (rows.length > 1) {
                    event.target.closest('.item-row').remove();
                }
            }
        });
    </script>
</body>
</html>
