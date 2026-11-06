# Guion de prueba manual E2E: registrar venta

URL objetivo: http://localhost/bodega/

## Escenario 1: venta válida con dos líneas y subtotal mayor a 100

| Paso | Acción exacta en pantalla | Dato a ingresar | Resultado esperado visible |
|------|--------------------------|----------------|----------------------------|
| 1 | Abrir http://localhost/bodega/ | - | Se muestra la página de productos con la lista de productos y el stock actual. |
| 2 | Hacer clic en el botón «Nueva venta» | - | Se abre la pantalla de registro de venta. |
| 3 | En el primer producto, seleccionar «arroz» | Producto: arroz | El producto queda seleccionado. |
| 4 | En la primera línea, ingresar cantidad | 10 | La línea queda cargada con cantidad 10. |
| 5 | Hacer clic en «Agregar línea» | - | Aparece una segunda fila para agregar otro producto. |
| 6 | En la segunda línea, seleccionar «gaseosa» | Producto: gaseosa | El producto queda seleccionado. |
| 7 | En la segunda línea, ingresar cantidad | 5 | La línea queda cargada con cantidad 5. |
| 8 | Escribir nombre del cliente | Ana | El nombre del cliente queda visible en el formulario. |
| 9 | Hacer clic en «Guardar venta» | - | Se muestra un mensaje de éxito con subtotal, descuento y total. El subtotal debe ser mayor que 100 y el descuento debe mostrarse como 5% del subtotal. |
| 10 | Verificar el mensaje final | - | Debe aparecer un total con descuento aplicado y no debe haber mensaje de error. |
| 11 | Ir a la página principal | - | Se vuelve a http://localhost/bodega/ y se observa el stock actualizado en el listado. |
| 12 | Revisar stock en index.php | - | El stock de arroz debe haber disminuido en 10 y el de gaseosa en 5 respecto al estado inicial. |

## Escenario 2: intento de vender más que el stock

| Paso | Acción exacta en pantalla | Dato a ingresar | Resultado esperado visible |
|------|--------------------------|----------------|----------------------------|
| 1 | Abrir http://localhost/bodega/ | - | La página principal muestra los productos con su stock actual. |
| 2 | Hacer clic en «Nueva venta» | - | Se abre el formulario de venta. |
| 3 | Seleccionar un producto con stock bajo | Producto: leche | El producto leche queda seleccionado. |
| 4 | Ingresar cantidad mayor al stock disponible | 2 | El valor se ingresa en la cantidad del formulario. |
| 5 | Escribir nombre del cliente | Rosa | El nombre del cliente queda visible. |
| 6 | Hacer clic en «Guardar venta» | - | Se muestra un mensaje de error con texto de stock insuficiente y no se registra la venta. |
| 7 | Volver a index.php | - | El stock del producto sigue igual que antes del intento; no hubo descuento ni baja real. |

## Escenario 3: cantidad 0

| Paso | Acción exacta en pantalla | Dato a ingresar | Resultado esperado visible |
|------|--------------------------|----------------|----------------------------|
| 1 | Abrir http://localhost/bodega/ | - | La lista de productos aparece normalmente. |
| 2 | Hacer clic en «Nueva venta» | - | Se abre la pantalla de venta. |
| 3 | Seleccionar cualquier producto | Producto: arroz | El producto queda seleccionado. |
| 4 | Ingresar cantidad en la línea | 0 | El sistema no acepta la cantidad. |
| 5 | Hacer clic en «Guardar venta» | - | Se muestra el mensaje «La cantidad debe ser un número entero mayor que cero» y la venta no se registra. |
| 6 | Revisar el estado en index.php | - | El stock del producto no cambia respecto al estado previo. |

## Verificación final del flujo principal

Al finalizar cada escenario, confirmar en la pantalla de listado principal:
- el stock actualizado corresponde a la operación realizada;
- si la venta fue válida, la cantidad vendida desciende del stock;
- si la venta fue rechazada, el stock permanece intacto;
- los mensajes de error o éxito aparecen en pantalla y son coherentes con la operación realizada.
