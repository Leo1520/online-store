# GUÍA RÁPIDA - Tienda en Línea MVC

## 🚀 Pasos Iniciales

### 1️⃣ Verificar la Base de Datos
- Abre MySQL Workbench
- Verifica que se creó la BD `mydb` ejecutando:
  ```sql
  USE mydb;
  SHOW TABLES;
  ```

### 2️⃣ Configurar la Aplicación
Si es necesario, edita las credenciales en `config/Database.php`:
```php
private $usuario = 'root';
private $password = '';
```

### 3️⃣ Cargar Datos de Prueba
Accede a:
```
http://localhost/online-store/setup.php
```

Esto creará:
- ✅ 10 productos de ejemplo
- ✅ Marcas, categorías e industrias
- ✅ 3 sucursales con stock
- ✅ Cuenta admin: `admin` / `admin123`
- ✅ Cuenta cliente: `cliente1` / `cliente123`

### 4️⃣ Acceder a la Tienda
```
http://localhost/online-store/public/index.php
```

---

## 📚 Controladores y Acciones

### 🛍️ ProductoControlador
```
?controlador=productos&accion=listar          // Ver todos los productos
?controlador=productos&accion=detalle&id=1    // Ver un producto
?controlador=productos&accion=buscar&q=laptop // Buscar productos
```

### 👤 AutenticacionControlador
```
?controlador=autenticacion&accion=mostrarLogin      // Formulario login
?controlador=autenticacion&accion=mostrarRegistro   // Formulario registro
?controlador=autenticacion&accion=perfil            // Ver perfil
?controlador=autenticacion&accion=cerrarSesion      // Logout
```

### 🛒 CarritoControlador
```
?controlador=carrito&accion=mostrar           // Ver carrito
?controlador=carrito&accion=agregar&id=1      // Agregar producto
?controlador=carrito&accion=actualizar&id=1   // Actualizar cantidad
?controlador=carrito&accion=eliminar&id=1     // Quitar producto
?controlador=carrito&accion=checkout          // Crear orden
```

### 💳 PagoControlador
```
?controlador=pago&accion=mostrar&id=1  // Formulario pago
?controlador=pago&accion=procesar&id=1 // Procesar pago
```

### ⚙️ AdminControlador
```
?controlador=admin&accion=panel                    // Panel principal
?controlador=admin&accion=listarMarcas             // Gestionar marcas
?controlador=admin&accion=listarCategorias         // Gestionar categorías
?controlador=admin&accion=listarIndustrias         // Gestionar industrias
```

---

## 🗂️ Estructura de Carpetas

```
online-store/
├── public/
│   └── index.php              ← PUNTO DE ENTRADA
├── app/
│   ├── Models/                ← Lógica de datos
│   ├── Controllers/           ← Lógica de negocio
│   └── Views/                 ← Interfaz de usuario
├── config/                    ← Configuración
├── Recursos/
│   ├── css/
│   └── imagenes/              ← Guardar fotos de productos
└── sql/
    └── mydb.sql               ← Script BD
```

---

## 💾 Modelos Disponibles

| Modelo | Tabla | Métodos Principales |
|--------|-------|-------------------|
| Producto | Producto | obtenerTodos(), buscar(), crear(), actualizar(), eliminar() |
| Cliente | Cliente | obtenerPorCi(), obtenerPorUsuario(), crear(), actualizar() |
| Cuenta | Cuenta | obtenerPorUsuario(), crear(), actualizarPassword() |
| NotaVenta | NotaVenta | obtenerPorNro(), obtenerPorCliente(), crear() |
| Marca | Marca | obtenerTodos(), obtenerPorId(), crear(), actualizar(), eliminar() |
| Categoria | Categoria | obtenerTodos(), obtenerPorId(), crear(), actualizar(), eliminar() |

---

## 🔄 Flujos de Compra Típicos

### Compra como Cliente Nuevo
1. `?controlador=autenticacion&accion=mostrarRegistro`
2. Completa formulario y envía
3. `?controlador=autenticacion&accion=mostrarLogin`
4. Inicia sesión
5. `?controlador=productos&accion=listar`
6. Busca y selecciona productos
7. `?controlador=carrito&accion=mostrar`
8. Revisa y procede al pago
9. Completa datos de tarjeta

### Compra con Cuenta Existente
1. Login directo
2. Seleccionar productos
3. Ver carrito
4. Proceder a pago

---

## 🧪 Datos de Prueba para Pago

Para probar el módulo de pago, usa cualquiera de estos datos:

| Campo | Valor |
|-------|-------|
| Número de Tarjeta | 4532 1234 5678 9010 |
| Nombre Titular | Juan Pérez |
| Fecha Expiración | 12/25 |
| CVV | 123 |

**Nota**: El sistema de pago es simulado para demostración.

---

## 🐛 Troubleshooting Rápido

### "Error de conexión a la base de datos"
```bash
# Verifica que MySQL está corriendo:
# En Windows: MySQL debe estar en Servicios
# En Linux: sudo systemctl status mysql
```

### "No puedo crear productos"
- ✅ Verificar que eres admin
- ✅ Crear carpeta `Recursos/imagenes/`
- ✅ Dar permisos: `chmod 755`

### "El carrito desaparece"
- Las sesiones se guardan por navegador
- Limpiar cookies si hay problemas
- Verificar que las sesiones están habilitadas en PHP

---

## 📝 Variables de Sesión (para desarrollo)

```php
// Debugar en cualquier vista:
echo '<pre>';
print_r($_SESSION);
echo '</pre>';
```

---

## 🔐 Seguridad

✅ Implementado:
- Contraseñas con bcrypt
- Prepared statements (anti SQL injection)
- Sanitización de entrada/salida
- Validación de emails
- Autenticación por sesiones
- CSRF básico en formularios

---

## 📖 Más Información

Ver archivo completo: `ARQUITECTURA_MVC.md`

---

**¡Listo! Tu tienda está completamente funcional con arquitectura MVC.**
