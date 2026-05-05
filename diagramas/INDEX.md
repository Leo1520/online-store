# 🎯 ACCESO RÁPIDO A LOS DIAGRAMAS - ElectroHogar

Todos los diagramas PlantUML del proyecto están generados y listos para visualizar.

---

## 📋 Lista de Diagramas

### 1️⃣ **Diagrama de Clases**
📁 **Archivo:** `diagrama_clases.puml`

Estructura OOP completa del sistema:
- 18 Modelos (Cuenta, Cliente, Producto, etc.)
- 16 Controladores (públicos y administrativos)
- Relaciones entre clases
- Métodos y atributos de cada clase

👁️ **Ver online:** [Copiar código y pegar en PlantUML](https://www.plantuml.com/plantuml/uml/)

---

### 2️⃣ **Diagrama de Secuencia**
📁 **Archivo:** `diagrama_secuencia.puml`

Flujo completo de una compra (12 pasos):
1. Búsqueda de productos
2. Visualización detalle
3. Agregar al carrito
4. Modificar cantidades
5. Procesar pago
6. Registrar venta
7. Actualizar stock
8. Generar recibo
9. Confirmación al cliente

👁️ **Ideal para:** Entender interacción cliente-servidor durante transacción

---

### 3️⃣ **Diagrama de Navegación**
📁 **Archivo:** `diagrama_navegacion.puml`

Mapa completo del sitio web:
- **Público:** Catálogo, Carrito, Login, Mi Cuenta (Cliente)
- **Admin:** Dashboard, Gestiones, Reportes
- **Vendedor:** Panel de ventas y comisiones
- **Errores:** Manejo de excepciones

👁️ **Ideal para:** Usuarios finales y arquitectos de información

---

### 4️⃣ **Diagrama de Despliegue**
📁 **Archivo:** `diagrama_despliegue.puml`

Infraestructura técnica:
- **Cliente:** Navegador web
- **Red:** Internet HTTPS
- **Web Server:** Apache 2.4
- **App Server:** PHP 7.4+
- **Database:** MySQL 5.7+
- **Storage:** Recursos y documentos
- **Servicios:** Email, Logs

👁️ **Ideal para:** DevOps, arquitectos de sistemas

---

### 5️⃣ **Diagrama SCRUM/XP**
📁 **Archivo:** `diagrama_scrum_xp.puml`

Metodología ágil de desarrollo:
- **Product Backlog:** 4 Epics (144 pts)
- **7 Sprints:** Iteraciones de 2 semanas
- **Ceremonias:** Planning, Standup, Review, Retrospective
- **Incrementos:** Entregas versión v0.1 a v1.0

👁️ **Ideal para:** Product Owners, Scrum Masters, equipo desarrollo

---

## 🚀 CÓMO VISUALIZAR LOS DIAGRAMAS

### ✅ Opción 1: Online (Recomendado - Sin instalación)
```
1. Ir a: https://www.plantuml.com/plantuml/uml/
2. Copiar contenido del archivo .puml
3. Pegar en el editor
4. Hacer clic en "Render"
```

### ✅ Opción 2: VS Code (Local)
```
1. Instalar extensión: "PlantUML" (jebbs.plantuml)
2. Abrir archivo .puml
3. Presionar Alt + D para preview
4. Click derecho → "Export Diagram"
```

### ✅ Opción 3: Línea de Comandos
```bash
# Instalar PlantUML (si no lo tienes)
# En Windows con Chocolatey:
choco install plantuml

# Luego generar imagen:
plantuml diagrama_clases.puml
# Genera: diagrama_clases.png
```

---

## 📊 TABLA RESUMEN

| # | Diagrama | Tipo | Propósito | Audience |
|---|----------|------|----------|----------|
| 1 | Clases | UML | Estructura OOP | Desarrolladores |
| 2 | Secuencia | UML | Flujo transacción | Analistas |
| 3 | Navegación | IA | Mapa sitio | UX/Product |
| 4 | Despliegue | UML | Infraestructura | DevOps/Arq |
| 5 | SCRUM/XP | Proceso | Desarrollo ágil | Todo el equipo |

---

## 📁 ESTRUCTURA DE CARPETAS

```
online-store/
├── diagramas/                    ← 📍 ESTÁS AQUÍ
│   ├── diagrama_clases.puml
│   ├── diagrama_secuencia.puml
│   ├── diagrama_navegacion.puml
│   ├── diagrama_despliegue.puml
│   ├── diagrama_scrum_xp.puml
│   ├── README.md                 (Documentación detallada)
│   └── INDEX.md                  (Este archivo)
├── DOCUMENTACION.md              (Documentación completa del proyecto)
├── index.php
├── controladores/
├── modelos/
├── vistas/
├── config/
└── ...
```

---

## 💡 TIPS Y TRUCOS

### Para Desarrolladores
- 📌 Usa el diagrama de **Clases** para entender la arquitectura
- 📌 Usa el diagrama de **Secuencia** al implementar nuevas features
- 📌 Revisa el diagrama de **Navegación** antes de crear una vista

### Para DevOps
- 📌 Diagrama **Despliegue** es la guía de configuración
- 📌 Verifica puertos, versiones y requisitos
- 📌 Considera replicación y backups

### Para Product Owners
- 📌 Diagrama **SCRUM/XP** muestra progress y entregas
- 📌 Diagrama **Navegación** muestra funcionalidades
- 📌 Diagrama **Secuencia** valida flujos de negocio

---

## 📝 INFORMACIÓN TÉCNICA

**PlantUML Version:** Compatible 1.2022+  
**UML Version:** 2.5  
**Formato:** ASCII UML Diagrams  
**Generador:** Online (plantuml.com) o Local (CLI)  

---

## ✨ ACTUALIZACIÓN Y MANTENIMIENTO

Los diagramas se actualizan en los siguientes casos:

- ✅ Cambios en arquitectura
- ✅ Nuevas clases o controladores
- ✅ Cambios en flujos críticos
- ✅ Cambios en despliegue/infraestructura
- ✅ Evolución de metodología SCRUM

**Próxima revisión:** Fin de cada sprint

---

## 🔗 REFERENCIAS RÁPIDAS

- **Documentación Completa:** [DOCUMENTACION.md](../DOCUMENTACION.md)
- **PlantUML Online:** https://www.plantuml.com/plantuml/uml/
- **PlantUML Docs:** https://plantuml.com/guide
- **VS Code Extension:** https://marketplace.visualstudio.com/items?itemName=jebbs.plantuml

---

## 📞 SOPORTE

¿Problemas visualizando los diagramas?

**Paso 1:** Verifica que uses navegador moderno (Chrome, Firefox, Edge)  
**Paso 2:** Intenta copiar/pegar el código en plantuml.com  
**Paso 3:** Revisa la sintaxis en el archivo .puml  
**Paso 4:** Contacta al equipo de desarrollo  

---

**Generado:** Mayo 2026  
**Proyecto:** Sistema de Gestión ElectroHogar  
**Versión Diagramas:** 1.0  
**Estado:** ✅ Activo

---

¡Gracias por revisar los diagramas del proyecto! 🎉
