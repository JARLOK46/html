# Sistema de Mercado Campesino 🌱

## Descripción del Proyecto

Este es un sistema web completo para la gestión del **Mercado Campesino de la Vereda Pueblo Rico**. Permite llevar un control e inventario de los productos que se ofrecen los fines de semana, gestionando tanto la información de los campesinos como sus productos.

## Características Principales

### 🧑‍🌾 Gestión de Campesinos
- Registro de datos básicos (documento, nombre, teléfono, finca)
- Validación de documentos únicos
- Listado completo de campesinos registrados
- Eliminación de campesinos (con productos asociados)

### 🥕 Gestión de Productos (CRUD Completo)
- **Crear**: Registrar nuevos productos
- **Leer**: Consultar productos por código o nombre
- **Actualizar**: Modificar datos (excepto código)
- **Eliminar**: Remover productos del sistema
- Filtrado por campesino
- Unidades de medida predefinidas (gramos, unidades, libras, kilogramos)

### 🎨 Diseño Web
- Interfaz moderna y responsiva
- CSS puro (sin Bootstrap)
- Colores temáticos verdes (agricultura)
- Navegación intuitiva
- Mensajes de éxito y error

## Estructura del Proyecto

```
campesinos/
├── app/
│   ├── index.php          # Página de inicio
│   ├── campesinos.php     # Gestión de campesinos
│   ├── productos.php      # Gestión de productos (CRUD)
│   ├── conection.php      # Conexión a base de datos
│   └── css/
│       └── styles.css     # Estilos CSS
├── database.sql           # Script de base de datos
└── README.md             # Este archivo
```

## Instalación y Configuración

### Requisitos Previos
- XAMPP instalado y funcionando
- PHP 7.4 o superior
- MySQL/MariaDB
- Navegador web moderno

### Pasos de Instalación

1. **Copiar archivos**
   ```
   Copia la carpeta 'campesinos' en: C:\xampp\htdocs\
   ```

2. **Iniciar servicios XAMPP**
   - Abre el Panel de Control de XAMPP
   - Inicia Apache y MySQL

3. **Crear la base de datos**
   - Abre phpMyAdmin: http://localhost/phpmyadmin
   - Importa o ejecuta el archivo `database.sql`
   - Esto creará la base de datos `mercado_campesino` con las tablas necesarias

4. **Acceder al sistema**
   - Abre tu navegador
   - Ve a: http://localhost/campesinos/app/

## Base de Datos

### Tabla: campesinos
```sql
- numero_documento (VARCHAR(20), PRIMARY KEY)
- nombre (VARCHAR(100), NOT NULL)
- telefono (VARCHAR(15), NOT NULL)
- nombre_finca (VARCHAR(100), NOT NULL)
- fecha_registro (TIMESTAMP, DEFAULT CURRENT_TIMESTAMP)
```

### Tabla: productos
```sql
- codigo (VARCHAR(20), PRIMARY KEY)
- nombre (VARCHAR(100), NOT NULL)
- unidad_medida (ENUM: 'gramos', 'unidades', 'libras', 'kilogramos')
- cantidad (DECIMAL(10,2), NOT NULL)
- numero_documento_campesino (VARCHAR(20), FOREIGN KEY)
- fecha_registro (TIMESTAMP, DEFAULT CURRENT_TIMESTAMP)
```

### Relación
- Un campesino puede tener múltiples productos
- Cada producto pertenece a un solo campesino
- Eliminación en cascada (si se elimina un campesino, se eliminan sus productos)

## Funcionalidades Técnicas

### Seguridad
- **PDO con Prepared Statements**: Previene inyecciones SQL
- **Validación de datos**: Tanto en frontend como backend
- **Escape de HTML**: Previene XSS
- **Manejo de errores**: Try-catch para operaciones de base de datos

### Validaciones
- Campos obligatorios
- Documentos únicos para campesinos
- Códigos únicos para productos
- Cantidades mayores a 0
- Selección obligatoria de campesino para productos

### Características de Usabilidad
- Mensajes informativos de éxito y error
- Confirmaciones antes de eliminar
- Formularios que mantienen datos en caso de error
- Búsqueda y filtrado de productos
- Diseño responsivo para móviles

## Guía de Uso

### 1. Configuración Inicial
1. Ejecuta el script `database.sql` en phpMyAdmin
2. Verifica que la conexión funcione visitando la página de inicio

### 2. Registrar Campesinos
1. Ve a la sección "Campesinos"
2. Completa el formulario con:
   - Número de documento (único)
   - Nombre completo
   - Teléfono
   - Nombre de la finca
3. Haz clic en "Registrar Campesino"

### 3. Gestionar Productos
1. Ve a la sección "Productos"
2. Para registrar:
   - Completa todos los campos del formulario
   - Selecciona el campesino propietario
   - El código debe ser único
3. Para buscar:
   - Usa el formulario de búsqueda por código o nombre
   - Filtra por campesino específico
4. Para editar:
   - Haz clic en "Editar" en la tabla
   - Modifica los datos (excepto el código)
   - Guarda los cambios
5. Para eliminar:
   - Haz clic en "Eliminar" y confirma la acción

## Explicación del Código

### Conexión a Base de Datos (conection.php)
```php
// Configuración usando variables de entorno o valores por defecto
$DB_HOST = getenv('DB_HOST') ?: '127.0.0.1';
$DB_NAME = getenv('DB_NAME') ?: 'mercado_campesino';

// PDO con configuración segura
$pdo = new PDO($dsn, $DB_USER, $DB_PASS, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
]);
```

### Prepared Statements para Seguridad
```php
// Inserción segura
$stmt = $pdo->prepare("INSERT INTO campesinos (numero_documento, nombre, telefono, nombre_finca) VALUES (?, ?, ?, ?)");
$stmt->execute([$numero_documento, $nombre, $telefono, $nombre_finca]);

// Consulta segura
$stmt = $pdo->prepare("SELECT * FROM productos WHERE codigo = ?");
$stmt->execute([$codigo]);
```

### Validación y Sanitización
```php
// Validación de campos
if (empty($numero_documento) || empty($nombre)) {
    $mensaje = 'Todos los campos son obligatorios';
}

// Escape de HTML para mostrar datos
echo htmlspecialchars($campesino['nombre']);
```

### CSS Responsivo
```css
/* Diseño flexible */
.flex {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
}

/* Responsive para móviles */
@media (max-width: 768px) {
    .header-content {
        flex-direction: column;
    }
}
```

## Personalización

### Cambiar Colores
Edita el archivo `css/styles.css` y modifica las variables de color:
```css
/* Colores principales */
header {
    background: linear-gradient(135deg, #2e7d32, #4caf50);
}

.btn {
    background-color: #4caf50;
}
```

### Agregar Campos
1. Modifica la base de datos agregando columnas
2. Actualiza los formularios HTML
3. Modifica las consultas PHP

### Cambiar Unidades de Medida
Edita el ENUM en la base de datos y los options en el HTML:
```sql
ALTER TABLE productos MODIFY unidad_medida ENUM('gramos', 'unidades', 'libras', 'kilogramos', 'toneladas');
```

## Solución de Problemas

### Error de Conexión a Base de Datos
- Verifica que MySQL esté ejecutándose en XAMPP
- Confirma que la base de datos `mercado_campesino` existe
- Revisa las credenciales en `conection.php`

### Páginas en Blanco
- Activa la visualización de errores PHP
- Revisa los logs de Apache en XAMPP
- Verifica la sintaxis PHP

### Estilos No Cargan
- Confirma que la ruta del CSS sea correcta
- Verifica que el archivo `styles.css` exista
- Limpia la caché del navegador

## Mejoras Futuras

- Sistema de autenticación de usuarios
- Reportes en PDF
- Gráficos de inventario
- API REST para móviles
- Sistema de notificaciones
- Backup automático de datos

## Contacto y Soporte

Este sistema fue desarrollado como ejercicio educativo para aprender:
- Desarrollo web con PHP
- Bases de datos relacionales
- Diseño CSS responsivo
- Operaciones CRUD
- Seguridad web básica

---

**Nota**: Este es un sistema educativo. Para uso en producción, considera implementar medidas adicionales de seguridad, validación y optimización.