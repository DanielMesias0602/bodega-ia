# Revisión de seguridad del proyecto

## Alcance y método

Revisión estática de los archivos PHP presentes en el proyecto, contrastando
su funcionalidad con las categorías de **OWASP Top 10:2021**. Se revisaron las
consultas del repositorio, el tratamiento de datos en las vistas, el flujo de
registro de ventas y las configuraciones disponibles. No se revisaron la
configuración de Apache/PHP de XAMPP, el hosting ni controles de red.

Esta revisión no equivale a una certificación ni a una prueba de penetración
completa.

## Hallazgos por categoría OWASP

| Categoría OWASP Top 10:2021 | Resultado observado |
|---|---|
| A01: Broken Access Control | No hay autenticación ni control de roles. El alcance del laboratorio excluye gestión de usuarios; si la aplicación se publica para uso real, cualquier visitante que alcance el formulario podría intentar registrar ventas. Restringir el acceso o incorporar autorización antes de un despliegue público. |
| A02: Cryptographic Failures | No se observan datos de pago ni contraseñas de usuarios. `config/env.php` mantiene las credenciales fuera del repositorio si se excluye correctamente de Git. En producción, usar HTTPS y no publicar ni registrar credenciales. |
| A03: Injection | En `RepositorioMySQL.php`, los valores variables de consultas de selección, inserción y actualización se envían mediante `PDO::prepare()` y `execute()`. Las consultas sin parámetros (`listarProductos()` y lecturas fijas de las pruebas) no concatenan datos de usuario. Esto reduce el riesgo de SQL Injection en las consultas revisadas. |
| A04: Insecure Design | La venta valida cantidad y stock, pero `guardar_venta.php` convierte cantidades recibidas con `(int)` antes de validarlas. Una entrada como `1.5` puede truncarse a `1` en el endpoint, aunque la validación del servicio rechaza decimales si los recibe sin conversión. Además, la validación de stock ocurre antes de la transacción y la actualización resta stock sin una condición atómica de stock disponible; ventas concurrentes podrían exceder existencias. |
| A05: Security Misconfiguration | No se encontraron archivos de configuración de despliegue que permitan comprobar headers HTTP, listado de directorios, mensajes de error de PHP o permisos. La aplicación carga Bootstrap desde un CDN sin atributo de integridad; fijar versión y evaluar SRI o servir el recurso localmente. |
| A06: Vulnerable and Outdated Components | El proyecto no muestra un gestor de dependencias PHP ni inventario/escaneo de componentes. PHPUnit es una herramienta de desarrollo y no debería publicarse en el hosting, conforme a las instrucciones del README. |
| A07: Identification and Authentication Failures | No existe autenticación de usuarios, conforme al alcance declarado. No desplegar la aplicación en una red pública sin definir primero quién puede registrar operaciones. |
| A08: Software and Data Integrity Failures | No se observan controles de integridad/verificación de artefactos en el flujo de despliegue revisado. La carga de Bootstrap desde CDN tampoco tiene verificación SRI. Publicar artefactos revisados y proteger el proceso de despliegue. |
| A09: Security Logging and Monitoring Failures | No se observa registro de auditoría de ventas ni de errores en los archivos revisados. Añadir registro protegido y sin secretos si el sistema se usa fuera del laboratorio. |
| A10: Server-Side Request Forgery (SSRF) | No se identificaron solicitudes del servidor a URLs controladas por el usuario ni funcionalidad que acepte destinos de red. No aplica al flujo revisado. |

## Salidas HTML

Las vistas revisadas escapan valores dinámicos antes de incorporarlos al HTML
mediante `htmlspecialchars()`: nombre e identificador de producto en
`index.php`, nombre de producto y mensajes en `venta.php`, y mensajes y nombre
del cliente en `guardar_venta.php`. Los valores numéricos de stock e ID se
convierten a entero y los importes se formatean numéricamente. Esto mitiga XSS
reflejado y almacenado en esos puntos observados. Para hacer explícito el
contexto, se recomienda usar `htmlspecialchars($valor, ENT_QUOTES |
ENT_SUBSTITUTE, 'UTF-8')` de forma consistente.

## Prueba manual de inyección

Se verificó la configuración local de ambas conexiones con los valores
predeterminados `root` y contraseña vacía. La conexión fue rechazada por el
servidor:

```text
SQLSTATE[HY000] [1045] Access denied for user 'root'@'localhost'
(using password: NO)
```

Por este motivo, **no fue posible ejecutar una prueba manual de inyección
contra la aplicación conectada a MySQL**, ni afirmar que esta prueba pasó. El
resultado de esta revisión es una inspección estática de las consultas: las
consultas variables observadas usan parámetros PDO y no interpolan el payload
en SQL.

Cuando las credenciales locales permitan conectar, repetir la prueba en un
entorno de desarrollo con un valor de prueba inofensivo como
`' OR '1'='1` en un campo que llegue a una consulta parametrizada. Confirmar
que no se altera el conjunto de resultados, no se registra una operación
inesperada y que los mensajes se muestran escapados. No ejecutar pruebas de
inyección destructivas ni contra producción.

## Recomendaciones prioritarias

1. Ajustar el endpoint para validar el formato entero original de cada cantidad
   antes de convertirlo a entero, y repetir la comprobación de stock dentro de
   la transacción con una actualización atómica.
2. Configurar y probar credenciales locales correctas; mantener `env.php`
   fuera del control de versiones y usar credenciales de mínimo privilegio en
   producción.
3. Antes de exponer la aplicación públicamente, decidir controles de acceso,
   protección CSRF, headers de seguridad y registro de eventos.
4. No desplegar PHPUnit ni otros archivos exclusivos de pruebas en el hosting.

