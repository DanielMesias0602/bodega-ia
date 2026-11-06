# Casos de prueba

| ID | Regla | Técnica | Entrada exacta | Resultado esperado | Nivel |
|----|-------|---------|----------------|-------------------|-------|
| CP-01 | R1: Control de stock | Valores límite | Producto con stock 5; cantidad 5 | Se acepta el ítem porque la cantidad es igual al stock disponible. | Unitaria |
| CP-02 | R1: Control de stock | Valores límite | Producto con stock 5; cantidad 6 | Se rechaza el ítem con mensaje «Stock insuficiente para [nombre del producto]». | Unitaria |
| CP-03 | R1: Control de stock | Valores límite | Producto con stock 0; cantidad 0 | Se rechaza porque la cantidad es 0 y además el stock del producto es 0. | Unitaria |
| CP-04 | R2: Cálculo de descuento y total | Valores límite | Subtotal 100.00 | Descuento = 0.00; total = 100.00. | Unitaria |
| CP-05 | R2: Cálculo de descuento y total | Valores límite | Subtotal 100.01 | Descuento = 5.00; total = 95.01. | Unitaria |
| CP-06 | R3: Validación de cantidad | Valores límite | cantidad 0 | Se rechaza con el mensaje «La cantidad debe ser un número entero mayor que cero». | Unitaria |
| CP-07 | R3: Validación de cantidad | Valores límite | cantidad -1 | Se rechaza con el mensaje «La cantidad debe ser un número entero mayor que cero». | Unitaria |
| CP-08 | R3: Validación de cantidad | Valores límite | cantidad 1 | Se acepta porque es un entero mayor que cero y menor o igual al stock. | Unitaria |
| CP-09 | R3: Validación de cantidad | Valores límite | cantidad 1.5 | Se rechaza con el mensaje «La cantidad debe ser un número entero mayor que cero». | Unitaria |
| CP-10 | R1: Control de stock | Valores límite | Producto con stock 5; cantidad 5 y otra línea del mismo producto con cantidad 1 | Se rechaza la venta porque la suma acumulada es 6 > 5. | Unitaria |
| CP-11 | R1: Control de stock | Valores límite | Producto con stock 5; cantidad 4 y otra línea del mismo producto con cantidad 1 | Se acepta porque la suma acumulada es 5 = stock disponible. | Unitaria |
| CP-12 | R4: Confirmación de venta y actualización de stock | Tabla de decisión | Producto con stock 5; venta de cantidad 5 | La venta se confirma y el stock restante queda en 0. | Integración |
| CP-13 | R4: Confirmación de venta y actualización de stock | Tabla de decisión | Producto con stock 5; venta de cantidad 6 | La venta no se confirma y el stock queda sin cambios. | Integración |
| CP-14 | R4: Confirmación de venta y actualización de stock | Rollback | Transacción fallida al insertar venta o detalle | Se revierte la operación y el stock original permanece intacto. | Integración |
