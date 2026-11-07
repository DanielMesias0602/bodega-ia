<?php

require_once __DIR__ . '/../src/ServicioVenta.php';

use PHPUnit\Framework\TestCase;

class ServicioVentaTest extends TestCase
{
    public function testAceptaCantidadUno(): void
    {
        $this->assertSame(true, ServicioVenta::validarCantidad(1));
    }

    public function testRechazaCantidadCero(): void
    {
        $this->assertSame(false, ServicioVenta::validarCantidad(0));
    }

    public function testRechazaCantidadNegativa(): void
    {
        $this->assertSame(false, ServicioVenta::validarCantidad(-1));
    }

    public function testRechazaCantidadDecimal(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('La cantidad debe ser un número entero mayor que cero');
        ServicioVenta::validarVenta([
            ['id' => 1, 'cantidad' => 1.5],
        ], [
            ['id' => 1, 'nombre' => 'arroz', 'stock' => 10],
        ]);
    }

    public function testAceptaVentaIgualAlStock(): void
    {
        $items = [
            ['id' => 1, 'cantidad' => 5],
        ];
        $productos = [
            ['id' => 1, 'nombre' => 'arroz', 'stock' => 5],
        ];

        $this->assertSame(true, ServicioVenta::hayStock(5, 5));
        ServicioVenta::validarVenta($items, $productos);
        $this->assertTrue(true);
    }

    public function testRechazaVentaPorStockExcedido(): void
    {
        $items = [
            ['id' => 1, 'cantidad' => 6],
        ];
        $productos = [
            ['id' => 1, 'nombre' => 'arroz', 'stock' => 5],
        ];

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Stock insuficiente para arroz');
        ServicioVenta::validarVenta($items, $productos);
    }

    public function testAceptaVentaConSumaAcumuladaIgualAlStock(): void
    {
        $items = [
            ['id' => 1, 'cantidad' => 4],
            ['id' => 1, 'cantidad' => 1],
        ];
        $productos = [
            ['id' => 1, 'nombre' => 'arroz', 'stock' => 5],
        ];

        ServicioVenta::validarVenta($items, $productos);
        $this->assertTrue(true);
    }

    public function testRechazaVentaConSumaAcumuladaMayorAlStock(): void
    {
        $items = [
            ['id' => 1, 'cantidad' => 5],
            ['id' => 1, 'cantidad' => 1],
        ];
        $productos = [
            ['id' => 1, 'nombre' => 'arroz', 'stock' => 5],
        ];

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Stock insuficiente para arroz');
        ServicioVenta::validarVenta($items, $productos);
    }

    public function testDescuentoCeroCuandoSubtotalEsExactamente100(): void
    {
        $items = [
            ['cantidad' => 10, 'precio' => 10.00],
        ];

        $this->assertEqualsWithDelta(100.00, ServicioVenta::calcularSubtotal($items), 0.001);
        $this->assertEqualsWithDelta(0.00, ServicioVenta::calcularDescuento(100.00), 0.001);
        $this->assertEqualsWithDelta(100.00, ServicioVenta::calcularTotal(100.00), 0.001);
    }

    public function testAplicaDescuentoCuandoSubtotalEs100Punto01(): void
    {
        $this->assertEqualsWithDelta(5.00, ServicioVenta::calcularDescuento(100.01), 0.001);
        $this->assertEqualsWithDelta(95.01, ServicioVenta::calcularTotal(100.01), 0.001);
    }

    public function testCalculaTotalConDescuentoCuandoSubtotalEsCienPuntoUno(): void
    {
        $items = [
            ['cantidad' => 10, 'precio' => 10.01],
        ];

        $this->assertEqualsWithDelta(100.10, ServicioVenta::calcularSubtotal($items), 0.001);
        $this->assertEqualsWithDelta(5.01, ServicioVenta::calcularDescuento(100.10), 0.001);
        $this->assertEqualsWithDelta(95.09, ServicioVenta::calcularTotal(100.10), 0.001);
    }

    public function testRechazaVentaCuandoProductoTieneStockCero(): void
    {
        $items = [
            ['id' => 1, 'cantidad' => 1],
        ];
        $productos = [
            ['id' => 1, 'nombre' => 'leche', 'stock' => 0],
        ];

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Stock insuficiente para leche');
        ServicioVenta::validarVenta($items, $productos);
    }
}
