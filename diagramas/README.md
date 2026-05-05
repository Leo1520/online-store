# Diagramas PlantUML - Sistema ElectroHogar

Esta carpeta contiene los diagramas UML del Sistema de Gestión de Tienda en Línea "ElectroHogar" generados en sintaxis **PlantUML**.

## Archivos Incluidos

### 1. **diagrama_clases.puml**
📊 **Diagrama de Clases**

Muestra la estructura de clases del sistema, incluyendo:
- **Modelos:** Cuenta, Cliente, Producto, Categoría, Marca, NotaVenta, etc.
- **Controladores:** Públicos (ProductoControlador, CarritoControlador) y Administrativos
- **Relaciones:** Herencia, composición y asociaciones entre clases
- **Métodos y Atributos:** De cada clase con tipos de datos

**Uso:** Visualizar arquitectura OOP del proyecto y relaciones entre entidades.

---

### 2. **diagrama_secuencia.puml**
🔄 **Diagrama de Secuencia - Proceso de Compra**

Detalla paso a paso el flujo completo de una compra:
1. Búsqueda y visualización de productos
2. Filtrado y búsqueda avanzada
3. Ver detalle del producto
4. Agregar al carrito
5. Modificar cantidad en carrito
6. Proceder al pago
7. Validar datos de pago
8. Registrar venta en BD
9. Actualizar stock
10. Generar recibo
11. Mostrar confirmación
12. Actualizar historial

**Uso:** Entender el flujo de interacción cliente-servidor durante una compra completa.

---

### 3. **diagrama_navegacion.puml**
🗺️ **Diagrama de Navegación**

Mapa de sitio del sistema con tres módulos principales:

**PÚBLICO (Cliente):**
- Inicio → Catálogo → Detalle Producto → Carrito → Pago → Confirmación
- Login → Registro → Mi Cuenta → Mis Compras
- Panel Vendedor

**ADMIN:**
- Dashboard → Gestión (Productos, Clientes, Ventas, Almacén, Roles)
- Reportes
- Configuración

**COMPARTIDAS:**
- Login/Logout
- Manejo de errores (404, 403)

**Uso:** Visualizar la estructura de navegación y accesibilidad del sitio.

---

### 4. **diagrama_despliegue.puml**
🏗️ **Diagrama de Despliegue**

Arquitectura de infraestructura del sistema:

**Componentes:**
- **Cliente:** Navegador web (Chrome, Firefox, Safari)
- **Internet:** HTTPS con SSL/TLS
- **Servidor Web:** Apache 2.4
- **Servidor App:** PHP 7.4+
- **Base de Datos:** MySQL 5.7+
- **Almacenamiento:** Recursos (imágenes, documentos)
- **Servicios Externos:** Email, Logs

**Especificaciones técnicas:**
- Puertos, versiones, capacidades
- Medidas de seguridad por capa
- Backups automáticos
- Performance esperado

**Uso:** Entender infraestructura física y requerimientos técnicos del sistema.

---

### 5. **diagrama_scrum_xp.puml**
🚀 **Diagrama SCRUM/XP - Desarrollo del Software**

Flujo de desarrollo ágil con:

**Elementos SCRUM:**
- **Product Backlog:** 4 Epics (144 story points)
- **Sprints:** 7 sprints de 2 semanas
- **Sprint Planning:** Selección de tareas
- **Daily Standup:** Sincronización diaria
- **Sprint Review:** Demostración de avances
- **Sprint Retrospective:** Mejora continua

**Fases:**
1. Sprint 1-2: Análisis y Diseño
2. Sprint 3-4: Desarrollo Cliente
3. Sprint 5-6: Desarrollo Admin
4. Sprint 7: Testing y Optimización

**Incrementos:** Entregas de valor cada sprint

**Deployment:** Publicación en producción

**Uso:** Visualizar metodología de desarrollo iterativo y entregas incrementales.

---

## Cómo Usar los Diagramas

### Opción 1: Visualizar Online en PlantUML
1. Abre https://www.plantuml.com/plantuml/uml/
2. Copia el contenido de cualquier archivo `.puml`
3. Pégalo en el editor online
4. Haz clic en "Generate/Render"

### Opción 2: Usar en tu IDE
**Si usas VS Code:**
1. Instala extensión: `PlantUML` (jebbs.plantuml)
2. Abre cualquier archivo `.puml`
3. Presiona `Alt + D` para vista previa

### Opción 3: Generar en Línea de Comandos
```bash
# Necesitas tener PlantUML instalado
plantuml diagrama_clases.puml
# Genera: diagrama_clases.png
```

---

## Información Técnica

### Versión PlantUML
- Versión: Compatible con PlantUML 1.2022+
- Sintaxis: UML 2.5

### Elementos Utilizados

| Diagrama | Elementos |
|----------|-----------|
| Clases | Classes, Packages, Relations, Methods |
| Secuencia | Actors, Participants, Messages, Loops, Alts |
| Navegación | States, Transitions, Notes |
| Despliegue | Nodes, Artifacts, Databases, Components |
| SCRUM | States, Transitions, Notes, Swimlanes |

---

## Información del Proyecto

**Proyecto:** Sistema de Gestión de Tienda en Línea - ElectroHogar  
**Versión:** 1.0  
**Fecha:** Mayo 2026  
**Tecnologías:** PHP 7.4+, MySQL 5.7+, Bootstrap 4, JavaScript  

---

## Notas Importantes

### Seguridad
- ✅ Los diagramas muestran medidas de seguridad en múltiples capas
- ✅ HTTPS/SSL obligatorio en producción
- ✅ Validación en cliente y servidor
- ✅ Contraseñas hasheadas con bcrypt

### Performance
- 📊 Tiempo de respuesta objetivo: < 2 segundos
- 🔄 Usuarios simultáneos: 500
- 💾 Tamaño esperado BD: ~5 GB
- 📈 Transacciones/hora: 100

### Mantenibilidad
- 📝 Código bien documentado
- 🧪 Testing automático
- 🔍 Code review obligatorio
- 📊 Monitoreo en producción

---

## Preguntas Frecuentes

**P: ¿Puedo modificar los diagramas?**  
R: Sí, son archivos de texto PlantUML. Edita el código y regenera.

**P: ¿Necesito instalar algo?**  
R: No, puedes usar https://www.plantuml.com/ online sin instalación.

**P: ¿En qué formato puedo exportar?**  
R: PNG, SVG, PDF (depende de tu cliente PlantUML).

**P: ¿Son los diagramas finales?**  
R: No, son documentos vivos que evolucionan con el proyecto.

---

## Contacto y Soporte

Para actualizaciones o preguntas sobre los diagramas:
- Revisa la documentación principal: `DOCUMENTACION.md`
- Consulta con el Product Owner
- Participa en Sprint Reviews

---

**Última Actualización:** Mayo 2026  
**Mantenedor:** Equipo de Desarrollo  
**Estado:** ✅ Aprobado

