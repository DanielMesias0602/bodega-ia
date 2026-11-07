# Análisis de fallo: descuento en subtotal exacto de S/ 100,00

## Contexto

La regla R2 establece que solo se aplica un descuento del 5 % cuando el subtotal
es **mayor** que S/ 100,00. Para un subtotal exactamente igual a S/ 100,00, el
descuento esperado es S/ 0,00.

El método afectado es `ServicioVenta::calcularDescuento()` en
`src/ServicioVenta.php`. La prueba de regresión es
`testDescuentoCeroCuandoSubtotalEsExactamente100()` en
`tests/ServicioVentaTest.php`.

## Síntoma reproducido

Se cambió temporalmente la condición de `$subtotal > 100` a
`$subtotal >= 100` y se ejecutó:

```text
php phpunit.phar tests\ServicioVentaTest.php --filter testDescuentoCeroCuandoSubtotalEsExactamente100
```

Resultado de PHPUnit 11.5.57:

```text
There was 1 failure:

1) ServicioVentaTest::testDescuentoCeroCuandoSubtotalEsExactamente100
Failed asserting that 5.0 matches expected 0.0.

tests\ServicioVentaTest.php:99

FAILURES!
Tests: 1, Assertions: 2, Failures: 1.
```

El resultado corresponde a la regla defectuosa: `100 * 0.05` produce un
descuento de S/ 5,00, cuando en el límite exacto no debe aplicarse descuento.

## Hipótesis analizadas

1. **Defecto en el código — confirmada.** La condición `>= 100` incluye
   indebidamente el valor límite de S/ 100,00.
2. **Defecto en la prueba — descartada.** La regla R2 especifica que el
   descuento comienza cuando el subtotal supera S/ 100,00. La expectativa de
   S/ 0,00 para S/ 100,00 es correcta.
3. **Defecto en los datos — descartada.** La prueba calcula un subtotal de
   S/ 100,00 con cantidad 10 y precio unitario S/ 10,00; el valor de entrada
   coincide con el límite que define la regla.

## Solución y confirmación

El cambio mínimo es restaurar la comparación estricta:

```php
if ($subtotal > 100) {
```

Con esta condición, el descuento para S/ 100,00 es S/ 0,00 y para subtotales
superiores a S/ 100,00 se mantiene el 5 % redondeado a dos decimales. Tras
restaurar el operador, la suite unitaria se ejecutó correctamente:

```text
OK (11 tests, 20 assertions)
```

