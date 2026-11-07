<?php

function conectar_test(): PDO
{
    $configFile = __DIR__ . '/env_test.php';
    if (!is_file($configFile)) {
        $configFile = __DIR__ . '/env_test.ejemplo.php';
    }

    $config = require $configFile;

    $dsn = sprintf(
        'mysql:host=%s;dbname=%s;charset=utf8mb4',
        $config['host'],
        $config['base']
    );

    $pdo = new PDO($dsn, $config['usuario'], $config['clave'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);

    return $pdo;
}
