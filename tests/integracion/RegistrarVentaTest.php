<?php

require_once __DIR__ . '/../../config/conexion_test.php';
require_once __DIR__ . '/../../src/RepositorioMySQL.php';

use PHPUnit\Framework\TestCase;

class RegistrarVentaTest extends TestCase
{
    private PDO $pdo;
    private RepositorioMySQL $repo;

    protected function setUp(): void
    {
        $config = require __DIR__ . '/../../config/env_test.php';
        $rootPdo = new PDO(
            sprintf('mysql:host=%s;charset=utf8mb4', $config['host']),
            $config['usuario'],
            $config['clave'],
            [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            ]
        );

        $rootPdo->exec('CREATE DATABASE IF NOT EXISTS bodega_test');

        $this->pdo = conectar_test();
        $this->repo = new RepositorioMySQL($this->pdo);

        $sql = file_get_contents(__DIR__ . '/../../sql/bodega_test.sql');
        $sql = preg_replace('/CREATE DATABASE IF NOT EXISTS bodega_test;?\s*/i', '', $sql);
        $sql = preg_replace('/USE bodega_test;?\s*/i', '', $sql);

        foreach (array_filter(array_map('trim', preg_split('/;\s*/', $sql)), fn ($statement) => $statement !== '') as $statement) {
            $this->pdo->exec($statement . ';');
        }
    }

    public function testRegistraVentaYDescuentaStock(): void
    {
        $items = [
            ['id' => 1, 'cantidad' => 3, 'precio' => 5.50],
        ];

        $idVenta = $this->repo->registrarVenta('Ana', $items);

        $stmtStock = $this->pdo->query("SELECT stock FROM producto WHERE id = 1");
        $stock = (int) $stmtStock->fetchColumn();
        $this->assertSame(7, $stock);

        $stmtVenta = $this->pdo->query('SELECT COUNT(*) FROM venta');
        $this->assertSame(1, (int) $stmtVenta->fetchColumn());

        $stmtDetalle = $this->pdo->query('SELECT COUNT(*) FROM detalle_venta');
        $this->assertSame(1, (int) $stmtDetalle->fetchColumn());

        $this->assertGreaterThan(0, $idVenta);
    }

    public function testNoRegistraVentaCuandoNoHayStock(): void
    {
        $items = [
            ['id' => 2, 'cantidad' => 1, 'precio' => 3.20],
        ];

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Stock insuficiente para leche');

        $this->repo->registrarVenta('Ana', $items);

        $stmtVenta = $this->pdo->query('SELECT COUNT(*) FROM venta');
        $this->assertSame(0, (int) $stmtVenta->fetchColumn());

        $stmtDetalle = $this->pdo->query('SELECT COUNT(*) FROM detalle_venta');
        $this->assertSame(0, (int) $stmtDetalle->fetchColumn());
    }

    protected function tearDown(): void
    {
        $this->pdo->exec('DROP TABLE IF EXISTS detalle_venta');
        $this->pdo->exec('DROP TABLE IF EXISTS venta');
        $this->pdo->exec('DROP TABLE IF EXISTS producto');
    }
}
