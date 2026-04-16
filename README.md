# 🏪 Tienda en Línea - E-Commerce MVC

> **Sistema completo de comercio electrónico con arquitectura Model-View-Controller**

## 📦 Proyecto Actualizado

Este proyecto ha sido **totalmente refactorizado** desde la estructura original a una **arquitectura MVC profesional**, siguiendo las mejores prácticas de desarrollo web.

## ✨ Características Principales

### Para Clientes
- 🛍️ Catálogo de productos con búsqueda
- 🛒 Carrito de compras funcional
- 👤 Sistema de registro e inicio de sesión
- 💳 Módulo de pagos (simulado)
- 📋 Historial de compras
- 👨‍💼 Gestión de perfil

### Para Administradores
- 📊 Panel de control
- 🔧 CRUD de productos
- 🏷️ Gestión de marcas y categorías
- 📦 Control de inventario
- 🏢 Gestión de sucursales
- 📈 Reportes de ventas

## 🏗️ Arquitectura MVC

```
Models (M)
    ↓ Lógica de datos
Controllers (C)
    ↓ Lógica de negocio
Views (V)
    ↓ Interfaz de usuario
Browser
```

## 🚀 Inicio Rápido

### 1. Importar Base de Datos
```bash
mysql -u root -p mydb < sql/mydb.sql
```

### 2. Ejecutar Setup
```
http://localhost/online-store/setup.php
```

### 3. Acceder a la Tienda
```
http://localhost/online-store/public/index.php
```

### Credenciales de Prueba
- **Admin**: usuario: `admin` / contraseña: `admin123`
- **Cliente**: usuario: `cliente1` / contraseña: `cliente123`

## 📚 Documentación

- **[ARQUITECTURA_MVC.md](ARQUITECTURA_MVC.md)** - Documentación completa del proyecto
- **[GUIA_RAPIDA.md](GUIA_RAPIDA.md)** - Guía de inicio rápido

## 📂 Estructura del Proyecto

```
online-store/
├── public/
│   └── index.php                 ← Punto de entrada principal
├── app/
│   ├── Models/                   ← Modelos de datos (8 modelos)
│   ├── Controllers/              ← Controladores (5 controladores)
│   └── Views/                    ← Vistas (Bootstrap 4)
├── config/
│   ├── Database.php              ← Conexión a BD
│   └── Utilidades.php            ← Funciones auxiliares
├── sql/
│   └── mydb.sql                  ← Script de BD
├── Recursos/
│   ├── css/
│   └── imagenes/                 ← Imágenes de productos
├── setup.php                     ← Crear datos de prueba
├── ARQUITECTURA_MVC.md           ← Documentación técnica
└── GUIA_RAPIDA.md                ← Guía de inicio rápido
```

## 🗄️ Base de Datos

**11 tablas completamente normalizadas:**

| Tabla | Propósito |
|-------|-----------|
| Cuenta | Autenticación |
| Cliente | Información de clientes |
| Producto | Catálogo |
| Marca | Clasificación de productos |
| Categoria | Clasificación de productos |
| Industria | Clasificación de productos |
| NotaVenta | Órdenes de compra |
| DetalleNotaVenta | Líneas de órdenes |
| Sucursal | Ubicaciones |
| DetalleProductoSucursal | Inventario |

## 🔧 Tecnologías Utilizadas

| Componente | Tecnología |
|------------|-----------|
| Backend | PHP 7.4+ |
| BD | MySQL 5.7+ |
| Frontend | HTML5, CSS3, Bootstrap 4 |
| Patrón | MVC |
| Seguridad | bcrypt, Prepared Statements |

## 📊 Modelos Implementados

### Base
- `Modelo` - Clase base para todos los modelos

### Productos
- `Producto` - Gestión de productos
- `Marca` - Gestión de marcas
- `Categoria` - Gestión de categorías
- `Industria` - Gestión de industrias

### Usuarios y Ventas
- `Cuenta` - Autenticación
- `Cliente` - Información de clientes
- `NotaVenta` - Órdenes
- `DetalleNotaVenta` - Detalles de órdenes

### Inventario
- `Sucursal` - Ubicaciones
- `DetalleProductoSucursal` - Stock por sucursal

## 🎮 Controladores Implementados

| Controlador | Responsabilidad |
|-------------|-----------------|
| ProductoControlador | Gestión de catálogo |
| AutenticacionControlador | Login, registro, perfil |
| CarritoControlador | Carrito de compras |
| PagoControlador | Procesamiento de pagos |
| AdminControlador | Panel administrativo |

## 🔐 Características de Seguridad

✅ **Implementadas:**
- Encriptación bcrypt para contraseñas
- Prepared Statements contra inyección SQL
- Sanitización de entrada/salida
- Validación de emails
- Autenticación por sesiones
- Control de acceso por roles

## 🎨 Interfaz de Usuario

- ✅ Responsive design con Bootstrap 4
- ✅ Iconos con Bootstrap Icons
- ✅ Formularios validados
- ✅ Navegación intuitiva
- ✅ Alertas de usuario

## 🔄 Flujo de Datos Típico

```
Usuario → Controlador → Modelo → BD
                ↓
            Validación
                ↓
    Actualiza Vista → Bootstrap HTML → Navegador
```

## 📋 Checklist de Funcionalidades

### Tienda
- [x] Listar productos
- [x] Ver detalles de producto
- [x] Buscar productos
- [x] Carrito de compras
- [x] Agregar/editar/quitar items
- [x] Procesar pago (simulado)
- [x] Comprobante de compra

### Usuarios
- [x] Registro
- [x] Inicio de sesión
- [x] Perfil de usuario
- [x] Actualizar datos
- [x] Historial de compras
- [x] Cerrar sesión

### Admin
- [x] Panel de control
- [x] CRUD de productos
- [x] CRUD de marcas
- [x] CRUD de categorías
- [x] CRUD de industrias
- [x] Gestión de sucursales

## 🚀 Deployment

Para producción:
1. Cambiar credenciales de BD en `config/Database.php`
2. Habilitar HTTPS
3. Configurar variables de entorno
4. Implementar gateway de pago real
5. Hacer backup regular de BD

## 📞 Soporte

Para problemas o sugerencias:
1. Revisar [ARQUITECTURA_MVC.md](ARQUITECTURA_MVC.md)
2. Verificar [GUIA_RAPIDA.md](GUIA_RAPIDA.md)
3. Revisar logs de BD

## 📄 Licencia

Proyecto educativo para demostración de patrones MVC.

---

**Versión**: 1.0.0 (MVC)  
**Última actualización**: 16 de abril de 2026  
**Estado**: ✅ Producción
│   ├── panel_control.php      ← Panel principal admin
│   ├── gestion_productos.php  ← Lista de productos
│   ├── agregar_producto.php   ← Crear producto
│   ├── editar_producto.php    ← Editar producto
│   └── cerrar_sesion.php      ← Logout
├── Incluir/
│   ├── conexion.php           ← Conexión a BD
│   ├── encabezado.php         ← Navbar
│   └── pie.php                ← Footer
├── Recursos/
│   ├── imagenes/              ← Imágenes de productos
│   └── css/
│       └── estilos.css        ← Estilos personalizados
├── sql/
│   └── comercio_electronico.sql ← Script de BD
├── index.php                  ← Página de inicio
├── carrito.php                ← Carrito de compras
├── pago.php                   ← Formulario de pago
└── pago_exitoso.php           ← Confirmación de compra
```

---

## 🔐 Características de Seguridad

### ✅ Implementadas
- Prepared statements (evita SQL injection)
- Password hashing con bcrypt
- Escapado de HTML (htmlspecialchars)
- Validación de extensiones de archivo
- Sesiones PHP para autenticación
- Transacciones en base de datos
- Eliminación de archivos huérfanos

### 🔄 Próximas mejoras (Opcionales)
- [ ] Validación de email (envío de confirmación)
- [ ] Sistema de cuenta de usuario (cliente)
- [ ] Historial de órdenes
- [ ] Panel de admin mejorado con estadísticas
- [ ] Métodos de pago reales (Stripe, PayPal)
- [ ] Búsqueda y filtrado de productos
- [ ] Categorías de productos
- [ ] Carrito persistente en BD

---

## ⚙️ Configuración

### Base de datos
Archivo: `Incluir/conexion.php`
```php
$host = "localhost";
$usuario = "root";
$password = "";  // Tu contraseña de MySQL
$base_de_datos = "comercio_electronico";
```

### Variables de servidor
- Asegúrate que `sessions` está habilitado en PHP
- Permisos de escritura en `recursos/imagenes/`

---

## 🧪 Pruebas

### Test de flujo completo:
1. ✅ Inicia sesión como administrador
2. ✅ Agrega un producto con imagen
3. ✅ Edita el producto
4. ✅ Ve a la tienda y agrega al carrito
5. ✅ Intenta agregar más del stock disponible (debe mostrar error)
6. ✅ Procede al pago con datos completos
7. ✅ Verifica que se creó la orden en BD

### Verificar Base de Datos:
```sql
-- Ver órdenes
SELECT * FROM ordenes;

-- Ver detalles de órdenes
SELECT * FROM detalles_orden;

-- Ver stock actualizado
SELECT id_producto, nombre, stock FROM productos;
```

---

## 📝 Notas

- Las imágenes se almacenan en `recursos/imagenes/` con timestamp para evitar duplicados
- Las contraseñas están hasheadas y son irrecuperables
- El carrito se limpia automáticamente tras una compra exitosa
- Las órdenes no se pueden eliminar (histórico de venta)

---

## ❓ Preguntas Frecuentes

**P: ¿Se puede cambiar la contraseña del admin?**
R: Sí, con `password_hash('nueva_contraseña', PASSWORD_BCRYPT)` en la BD

**P: ¿Dónde se guardan las imágenes?**
R: En `recursos/imagenes/` con nombre `timestamp_original.extension`

**P: ¿Se pierden los carritos al cerrar navegador?**
R: Sí, están basados en sesión. Se pueden persistir en BD si deseas.

**P: ¿Cómo accedo a las órdenes?**
R: Actualmente están en la BD. Un próximo step sería crear panel admin para verlas.

---

**Versión:** 1.0  
**Fecha:** Abril 2026  
**Estado:** ✅ Funcional y listo para usar
