<?php
return [
    'host' => getenv('DB_HOST') ?: '127.0.0.1;port=3307',
    'usuario' => getenv('DB_USER') ?: 'root',
    'clave' => getenv('DB_PASS') ?: '',
    'base' => getenv('DB_NAME') ?: 'bodega_test',
];