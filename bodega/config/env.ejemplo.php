<?php
return [
    'host' => getenv('DB_HOST') ?: 'localhost',
    'usuario' => getenv('DB_USER') ?: 'root',
    'clave' => getenv('DB_PASS') ?: '',
    'base' => getenv('DB_NAME') ?: 'bodega',
];
