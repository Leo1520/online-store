# 🛒 Tienda en Línea - Guía de Uso

## ✅ Cambios Realizados

### 1. **Seguridad SQL (Prepared Statements)**
- ✅ Todos los archivos PHP ahora usan **prepared statements** en lugar de SQL directo
- ✅ Previene inyección SQL
- Archivos actualizados:
  - `Admin/inicio_sesion.php`
  - `Admin/agregar_producto.php`
  - `Admin/editar_producto.php`
  - `Admin/gestion_productos.php`
  - `carrito.php`
  - `pago.php`
  - `index.php`

### 2. **Seguridad de Contraseñas**
- ✅ Contraseñas ahora usan **bcrypt (password_hash)**
- ✅ Verificación con `password_verify()`
- Usuario admin:
  - **Usuario:** `administrador`
  - **Contraseña:** `12345`

### 3. **Base de Datos Mejorada**
- ✅ Nuevas tablas:
  - `ordenes` - guarda todas las compras
  - `detalles_orden` - detalles por producto de cada orden
- ✅ Relaciones con claves foráneas
- ✅ Transacciones para garantizar integridad

### 4. **Carrito y Stock**
- ✅ Validación de stock antes de agregar
- ✅ Carrito persiste en sesión
- ✅ Actualización automática de stock al completar compra

### 5. **Sistema de Órdenes**
- ✅ Guardado de órdenes con datos del cliente
- ✅ Número de orden único
- ✅ Detalles de cada producto pagado
- ✅ Transacciones para evitar inconsistencias

### 6. **Estructura de Carpetas**
- ✅ `recursos/imagenes/` - para guardar imágenes de productos
- ✅ `recursos/css/estilos.css` - estilos personalizados

### 7. **Validaciones**
- ✅ Validación de extensiones de imagen (jpg, jpeg, png, gif, webp)
- ✅ Campos requeridos en formularios
- ✅ Prevención de caracteres especiales en nombres de archivo
- ✅ Escapado de HTML con `htmlspecialchars()`

### 8. **Mensajes de Usuario**
- ✅ Mensajes de éxito/error en sesión
- ✅ Alertas Bootstrap desplegables

---

## 🚀 Cómo Usar

### **1. Base de Datos**
```bash
1. Abre phpMyAdmin o MySQL Workbench
2. Crea una nueva base de datos
3. Ejecuta el archivo: sql/comercio_electronico.sql
```

### **2. Panel de Administración**
```
URL: http://localhost/online-store/Admin/inicio_sesion.php
Usuario: administrador
Contraseña: 12345
```

**Funciones del Admin:**
- Agregar productos
- Editar productos
- Eliminar productos
- Visualizar lista de productos

### **3. Tienda (Cliente)**
```
URL: http://localhost/online-store/index.php
```

**Flujo de compra:**
1. Ver productos disponibles
2. Agregar al carrito (con validación de stock)
3. Ver carrito y eliminar si es necesario
4. Proceder al pago
5. Completar datos de envío
6. Ver confirmación de compra

---

## 📦 Estructura del Proyecto

```
online-store/
├── Admin/
│   ├── inicio_sesion.php      ← Login admin
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
