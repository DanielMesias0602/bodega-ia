<?php
return [
    'host' => getenv('DB_HOST') ?: 'localhost',
    'usuario' => getenv('DB_USER') ?: 'root',
    'clave' => getenv('DB_PASS') ?: '',
    'base' => getenv('DB_TEST_NAME') ?: 'bodega_test',
];
