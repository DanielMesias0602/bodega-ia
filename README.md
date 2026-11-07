# Bodega

Aplicación PHP para consultar productos y registrar ventas. La conexión MySQL se
configura en `config/env.php`; el archivo está excluido de Git porque en el
hosting contendrá los datos de acceso a la base.

## Despliegue en InfinityFree

1. Crea una cuenta gratuita en [InfinityFree](https://www.infinityfree.com)
   usando tu correo institucional. Desde el panel crea un hosting con un
   subdominio gratuito. La activación puede tardar; no es necesario esperar para
   preparar la base y los archivos.
2. En el panel abre **MySQL Databases** y crea una base de datos. Anota el host,
   nombre de la base, usuario y contraseña que muestra el panel. Usa estos
   valores tal como aparecen, incluidos los prefijos que InfinityFree agregue.
3. Abre el phpMyAdmin de ese hosting, selecciona la base recién creada y entra
   en **Importar**. Selecciona `sql/bodega_produccion.sql` e inicia la
   importación. El archivo crea las tablas y productos iniciales, pero no crea
   ni selecciona una base de datos. Impórtalo una sola vez en una base vacía:
   no contiene instrucciones para borrar tablas existentes.
4. En **File Manager**, abre `htdocs` y sube el contenido de este proyecto
   directamente allí (no una carpeta contenedora adicional). No subas `.git/`,
   `tests/`, `phpunit.phar`, `phpunit-11.5.57.phar`, `.phpunit.result.cache` ni
   `config/env.php`. El archivo de base de datos se puede omitir después de
   importarlo.
5. En `htdocs/config/`, crea `env.php` a partir de `env.ejemplo.php`. Configura
   los valores de respaldo con el host, usuario, contraseña y nombre de base de
   datos anotados en el paso 2:

   ```php
   <?php
   return [
       'host' => getenv('DB_HOST') ?: 'HOST_MYSQL_DEL_PANEL',
       'usuario' => getenv('DB_USER') ?: 'USUARIO_DEL_PANEL',
       'clave' => getenv('DB_PASS') ?: 'CONTRASENA_DEL_PANEL',
       'base' => getenv('DB_NAME') ?: 'NOMBRE_DE_BASE_DEL_PANEL',
   ];
   ```

   Si el hosting permite configurar las variables `DB_HOST`, `DB_USER`,
   `DB_PASS` y `DB_NAME`, estas tienen prioridad sobre los valores de respaldo.
   No publiques ni compartas el `env.php` del servidor.
6. Cuando el subdominio esté activo, ábrelo desde un celular usando datos
   móviles. Comprueba que carguen los productos, registra una venta de prueba y
   vuelve a la lista para verificar que disminuyó el stock. Esta prueba debe
   hacerse contra el hosting ya configurado; no puede completarse desde el
   entorno local.

## Desarrollo local con XAMPP

`config/env.php` usa `localhost`, el usuario `root`, contraseña vacía y la base
`bodega` como valores locales de respaldo. Importa `sql/bodega.sql` en MySQL
local para crear la base y sus datos iniciales.
