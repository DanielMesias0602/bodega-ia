# Plan de pruebas

## 1. Objetivo y alcance

### Objetivo
Validar que la aplicación cumple con las reglas de negocio definidas en el documento de requisitos y que el flujo principal de venta funciona sin errores funcionales ni de persistencia.

### Alcance probado
Se prueban los módulos directamente relacionados con la lógica de negocio y la operación de ventas:
- Gestión de productos (listado y validación de creación, según requisitos de producto).
- Validación de cantidad al registrar una venta (R3).
- Control de stock al registrar una venta (R1).
- Cálculo de subtotal, descuento y total (R2).
- Confirmación de venta y actualización de stock (R4).

### Alcance no probado
No se prueban ni se incluyen en este plan:
- Autenticación y autorización.
- Reportes, facturación electrónica ni exportación.
- Módulos fuera del alcance del sistema indicado en requirements.md.
- Procedimientos almacenados ni triggers.

---

## 2. Matriz de pruebas por regla de negocio

| Regla | Nivel de prueba | Técnica | Datos de prueba | Criterio de aceptación |
|------|----------------|---------|----------------|----------------------|
| R1: Control de stock | Unitaria | Valores límite y tabla de decisión | cantidades 0, 1, stock exacto, stock + 1; mismo producto repetido en la venta | La cantidad debe ser mayor que cero y la suma acumulada por producto no puede exceder el stock disponible; si se excede, la venta se rechaza. |
| R2: Cálculo de descuento y total | Unitaria | Valores límite y partición de equivalencia | subtotal 0, 100, 100.01, 250 | Si subtotal > 100, descuento = 5% del subtotal redondeado a 2 decimales; si subtotal <= 100, descuento = 0.00 y total = subtotal. |
| R3: Validación de cantidad al registrar ítem | Unitaria | Valores límite y partición de equivalencia | 0, -1, 1.5, "abc", 1, 9999 | Se rechaza cualquier cantidad que no sea un entero mayor que cero; el ítem no se añade a la lista y se muestra el mensaje exacto. |
| R4: Confirmación de venta y actualización de stock | Integración | Tabla de decisión y rollback | venta válida con stock suficiente; venta con stock insuficiente; transacción con error de base de datos | Cuando la venta es válida, cada producto reduce su stock en la cantidad vendida dentro de una sola transacción; si la operación falla o hay stock insuficiente, todo queda sin cambios y se muestra error. |

### Notas sobre la técnica
- Valores límite: se usan para verificar los bordes explícitos del negocio, como 0, 1, 100, 100.01 y stock exacto.
- Partición de equivalencia: se agrupan valores válidos e inválidos según la regla de cantidad y stock.
- Tabla de decisión: se usa para validar combinaciones de cantidad, stock y subtotal, especialmente en R1 y R2.

---

## 3. Niveles de prueba que se ejecutarán hoy

### 3.1 Pruebas unitarias con PHPUnit sobre ServicioVenta
Objetivo: validar reglas R1, R2 y R3 sin depender de la base de datos.

Cobertura esperada:
- `validarCantidad()`
- `hayStock()`
- `validarVenta()`
- `calcularSubtotal()`
- `calcularDescuento()`
- `calcularTotal()`

### 3.2 Pruebas de integración sobre RepositorioMySQL con base bodega_test
Objetivo: verificar la persistencia, transacciones y descuento del stock según R4.

Requisitos mínimos:
- Base de datos separada: `bodega_test`.
- Insert inicial de productos para pruebas.
- Verificación del estado del stock antes y después de la operación.
- Asegurar rollback sobre falla de transacción.

### 3.3 Pruebas E2E manuales sobre venta.php
Objetivo: validar el flujo real del usuario desde la pantalla de venta hasta la confirmación.

Escenarios mínimos:
- Venta válida con stock suficiente.
- Venta con cantidad inválida.
- Venta con stock insuficiente.
- Venta con subtotal mayor a 100 para verificar descuento.

---

## 4. Criterio de salida para desplegar

La versión se considera lista para desplegar cuando se cumplen todas estas condiciones medibles:

1. 100 % de las pruebas unitarias de `ServicioVenta` pasan en PHPUnit (0 fallos).
2. 100 % de las pruebas de integración sobre `RepositorioMySQL` pasan en `bodega_test` (0 fallos).
3. 0 defectos críticos abiertos relacionados con R1, R2, R3 y R4.
4. El flujo manual de `venta.php` queda validado en el caso feliz y en los errores esperados de cantidad, stock y descuento.
5. No se detectan inconsistencias de stock ni errores de cálculo de descuento en los escenarios definidos en requirements.md.

Si estas condiciones se cumplen, la versión está lista para despliegue.
