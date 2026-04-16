# Tienda en Línea - Proyecto MVC

## 📋 Descripción
Sistema de comercio electrónico basado en arquitectura MVC con PHP puro. Incluye gestión de productos, carrito de compras, autenticación de usuarios, y panel de administración.

## 🏗️ Estructura del Proyecto

```
online-store/
├── public/                      # Punto de entrada (index.php)
├── app/
│   ├── Models/                 # Modelos de datos
│   │   ├── Modelo.php
│   │   ├── Producto.php
│   │   ├── Cliente.php
│   │   ├── Cuenta.php
│   │   ├── NotaVenta.php
│   │   ├── DetalleNotaVenta.php
│   │   ├── Marca.php
│   │   ├── Categoria.php
│   │   ├── Industria.php
│   │   ├── Sucursal.php
│   │   └── DetalleProductoSucursal.php
│   ├── Controllers/            # Controladores
│   │   ├── Controlador.php
│   │   ├── ProductoControlador.php
│   │   ├── AutenticacionControlador.php
│   │   ├── CarritoControlador.php
│   │   ├── PagoControlador.php
│   │   └── AdminControlador.php
│   └── Views/                  # Vistas
│       ├── layout.php
│       ├── Productos/
│       ├── Autenticacion/
│       ├── Carrito/
│       ├── Pago/
│       └── Admin/
├── config/                     # Configuración
│   ├── Database.php
│   └── Utilidades.php
├── Recursos/
│   └── imagenes/              # Imágenes de productos
└── sql/
    └── mydb.sql               # Script de base de datos
```

## 🚀 Instalación y Configuración

### 1. **Crear la Base de Datos**
```bash
# En MySQL Workbench o línea de comandos:
mysql -u root -p < sql/mydb.sql
```

### 2. **Configurar la Conexión**
Editar `config/Database.php` con tus credenciales de MySQL:
```php
private $host = 'localhost';
private $db_name = 'mydb';
private $usuario = 'root';
private $password = '';
```

### 3. **Configurar el Servidor Web**
La aplicación debe ejecutarse desde la carpeta `public/`:
```
http://localhost/online-store/public/
```

### 4. **Crear Carpetas Necesarias**
```bash
mkdir -p Recursos/imagenes
chmod 755 Recursos/imagenes
```

## 📚 Funcionalidades

### Cliente (Usuario Final)
- ✅ Registro e inicio de sesión
- ✅ Ver catálogo de productos
- ✅ Buscar productos
- ✅ Agregar productos al carrito
- ✅ Ver carrito y actualizar cantidades
- ✅ Procesar pagos
- ✅ Ver perfil y actualizar datos
- ✅ Historial de compras

### Administrador
- ✅ Gestión de productos (CRUD)
- ✅ Gestión de marcas
- ✅ Gestión de categorías
- ✅ Gestión de industrias
- ✅ Gestión de sucursales
- ✅ Gestión de stock
- ✅ Ver todas las ventas
- ✅ Panel de control

## 🔐 Seguridad Implementada

- ✅ Contraseñas encriptadas con bcrypt
- ✅ Prepared statements contra inyección SQL
- ✅ Sanitización de entrada/salida
- ✅ Validación de emails
- ✅ Sesiones seguras

## 📝 Ejemplos de Uso

### Acceder a la Tienda
```
http://localhost/online-store/public/index.php
```

### Flujo Básico de Compra
1. Registrarse: `?controlador=autenticacion&accion=mostrarRegistro`
2. Iniciar sesión: `?controlador=autenticacion&accion=mostrarLogin`
3. Ver productos: `?controlador=productos&accion=listar`
4. Ver carrito: `?controlador=carrito&accion=mostrar`
5. Pagar: `?controlador=pago&accion=mostrar&id=nroNotaVenta`

### Acceso Administrativo
- Login: `Admin/inicio_sesion.php`
- Panel: `?controlador=admin&accion=panel`

## 🗄️ Tablas de Base de Datos

| Tabla | Descripción |
|-------|-------------|
| Cuenta | Datos de login de usuarios |
| Cliente | Información de clientes |
| Producto | Catálogo de productos |
| Marca | Marcas de productos |
| Categoria | Categorías de productos |
| Industria | Industrias de productos |
| NotaVenta | Órdenes de compra |
| DetalleNotaVenta | Detalles de órdenes |
| Sucursal | Sucursales |
| DetalleProductoSucursal | Stock por sucursal |

## 🛠️ Tecnologías Utilizadas

- **Backend**: PHP 7.4+
- **Base de Datos**: MySQL 5.7+
- **Frontend**: HTML5, CSS3, Bootstrap 4
- **Patrón**: MVC (Model-View-Controller)

## 📖 Convenciones del Código

- **Modelos**: Nombre de tabla en singular con PascalCase
- **Controladores**: Nombre + "Controlador"
- **Métodos**: camelCase
- **Variables**: camelCase
- **Comentarios**: Español en la mayoría, inglés en código base

## ⚙️ Variables de Sesión Importantes

```php
$_SESSION['usuario']        // Usuario actual
$_SESSION['cliente']        // Datos del cliente
$_SESSION['carrito']        // Carrito de compras
$_SESSION['nroNotaVenta']   // Último pedido
$_SESSION['admin']          // Usuario administrador
$_SESSION['mensaje']        // Mensajes de éxito
$_SESSION['error']          // Mensajes de error
```

## 🔄 Flujo de Autenticación

1. Usuario accede a `login.php`
2. Envía usuario y contraseña
3. Se valida contra tabla `Cuenta`
4. Se carga información de `Cliente`
5. Se establece sesión con datos del usuario

## 💳 Proceso de Pago (Simulado)

1. Usuario agrega productos al carrito
2. Click en "Proceder al Pago"
3. Se crea `NotaVenta` y detalles
4. Se muestra formulario de pago
5. Se validan datos de tarjeta
6. Se muestra comprobante

## 🐛 Troubleshooting

### Error de conexión a BD
- Verificar credenciales en `config/Database.php`
- Asegurar que MySQL está ejecutándose
- Verificar que la BD `mydb` existe

### Imágenes no cargan
- Crear carpeta `Recursos/imagenes/`
- Verificar permisos: `chmod 755`
- Usar imágenes en formato JPG, PNG, GIF

### Problemas de sesión
- Limpiar cookies del navegador
- Verificar que sessions está habilitado en PHP
- Revisar permisos de escritura en servidor

## 📞 Soporte

Para reportar bugs o sugerencias, contacta al equipo de desarrollo.

---

**Última actualización**: 16 de abril de 2026
**Versión**: 1.0.0 (MVC)
