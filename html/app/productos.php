<?php
require_once 'conection.php';

$mensaje = '';
$tipo_mensaje = '';
$producto_editar = null;

if ($_POST && isset($_POST['accion'])) {
    $codigo = trim($_POST['codigo']);
    $nombre = trim($_POST['nombre']);
    $unidad_medida = $_POST['unidad_medida'] ?? '';
    $cantidad = $_POST['cantidad'] ?? 0;
    $numero_documento_campesino = $_POST['numero_documento_campesino'] ?? '';
    
    try {
        if ($_POST['accion'] === 'registrar') {
            if (empty($codigo) || empty($nombre) || empty($unidad_medida) || empty($cantidad) || empty($numero_documento_campesino)) {
                $mensaje = 'Todos los campos son obligatorios';
                $tipo_mensaje = 'error';
            } elseif ($cantidad <= 0) {
                $mensaje = 'La cantidad debe ser mayor a 0';
                $tipo_mensaje = 'error';
            } else {
                $stmt = $pdo->prepare("SELECT codigo FROM productos WHERE codigo = ?");
                $stmt->execute([$codigo]);
                
                if ($stmt->fetch()) {
                    $mensaje = 'El código del producto ya existe';
                    $tipo_mensaje = 'error';
                } else {
                    $stmt = $pdo->prepare("INSERT INTO productos (codigo, nombre, unidad_medida, cantidad, numero_documento_campesino) VALUES (?, ?, ?, ?, ?)");
                    $stmt->execute([$codigo, $nombre, $unidad_medida, $cantidad, $numero_documento_campesino]);
                    
                    $mensaje = 'Producto registrado exitosamente';
                    $tipo_mensaje = 'exito';
                    $_POST = [];
                }
            }
        } elseif ($_POST['accion'] === 'actualizar') {
            $codigo_original = $_POST['codigo_original'];
            $stmt = $pdo->prepare("UPDATE productos SET nombre = ?, unidad_medida = ?, cantidad = ?, numero_documento_campesino = ? WHERE codigo = ?");
            $stmt->execute([$nombre, $unidad_medida, $cantidad, $numero_documento_campesino, $codigo_original]);
            
            $mensaje = 'Producto actualizado exitosamente';
            $tipo_mensaje = 'exito';
            $modoEdicion = false;
        } elseif ($_POST['accion'] === 'buscar') {
            $termino_busqueda = trim($_POST['termino_busqueda']);
            $filtro_campesino = $_POST['filtro_campesino'] ?? '';
            
            if (!empty($termino_busqueda) || !empty($filtro_campesino)) {
                $sql = "SELECT p.*, c.nombre as nombre_campesino FROM productos p JOIN campesinos c ON p.numero_documento_campesino = c.numero_documento WHERE 1=1";
                $params = [];
                
                if (!empty($termino_busqueda)) {
                    $sql .= " AND (p.codigo LIKE ? OR p.nombre LIKE ?)";
                    $params[] = "%$termino_busqueda%";
                    $params[] = "%$termino_busqueda%";
                }
                
                if (!empty($filtro_campesino)) {
                    $sql .= " AND p.numero_documento_campesino = ?";
                    $params[] = $filtro_campesino;
                }
                
                $stmt = $pdo->prepare($sql);
                $stmt->execute($params);
                $productos_encontrados = $stmt->fetchAll();
            }
        } elseif ($_POST['accion'] === 'editar') {
            $codigo = $_POST['codigo'];
            $stmt = $pdo->prepare("SELECT * FROM productos WHERE codigo = ?");
            $stmt->execute([$codigo]);
            $producto_editar = $stmt->fetch();
        } elseif ($_POST['accion'] === 'eliminar') {
            $codigo = $_POST['codigo'];
            $stmt = $pdo->prepare("DELETE FROM productos WHERE codigo = ?");
            $stmt->execute([$codigo]);
            
            $mensaje = 'Producto eliminado exitosamente';
            $tipo_mensaje = 'exito';
        }
    } catch (PDOException $e) {
        $mensaje = 'Error al procesar producto: ' . $e->getMessage();
        $tipo_mensaje = 'error';
    }
}

// Procesar eliminación
if ($_GET && isset($_GET['eliminar'])) {
    $codigo_eliminar = $_GET['eliminar'];
    try {
        $stmt = $pdo->prepare("DELETE FROM productos WHERE codigo = ?");
        $stmt->execute([$codigo_eliminar]);
        $mensaje = 'Producto eliminado exitosamente';
        $tipoMensaje = 'success';
    } catch (PDOException $e) {
        $mensaje = 'Error al eliminar producto: ' . $e->getMessage();
        $tipoMensaje = 'error';
    }
}

// Procesar edición
if ($_GET && isset($_GET['editar'])) {
    $codigo_editar = $_GET['editar'];
    try {
        $stmt = $pdo->prepare("SELECT * FROM productos WHERE codigo = ?");
        $stmt->execute([$codigo_editar]);
        $productoEditar = $stmt->fetch();
        if ($productoEditar) {
            $modoEdicion = true;
        }
    } catch (PDOException $e) {
        $mensaje = 'Error al cargar producto para editar: ' . $e->getMessage();
        $tipoMensaje = 'error';
    }
}

$stmt = $pdo->prepare("SELECT numero_documento, nombre FROM campesinos ORDER BY nombre");
$stmt->execute();
$campesinos = $stmt->fetchAll();

$stmt = $pdo->prepare("SELECT p.*, c.nombre as nombre_campesino FROM productos p JOIN campesinos c ON p.numero_documento_campesino = c.numero_documento ORDER BY p.fecha_registro DESC");
$stmt->execute();
$todos_productos = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Productos - Mercado Campesino</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <header>
        <div class="container">
            <div class="header-content">
                <div class="logo">
                    <div class="logo-circle"></div>
                    <h1>Mercado Campesino</h1>
                </div>
                <nav class="main-nav">
                    <ul>
                        <li><a href="index.php">Inicio</a></li>
                        <li><a href="campesinos.php">Campesinos</a></li>
                        <li><a href="productos.php" class="active">Productos</a></li>
                    </ul>
                </nav>
            </div>
        </div>
    </header>

    <main class="container">
        <?php if ($mensaje): ?>
            <div class="mensaje <?php echo $tipo_mensaje; ?>"><?php echo htmlspecialchars($mensaje); ?></div>
        <?php endif; ?>

        <?php if (empty($campesinos)): ?>
            <div class="mensaje error">
                <p>No hay campesinos registrados. <a href="campesinos.php">Registrar campesino</a></p>
            </div>
        <?php else: ?>
            <section>
                <h2><?php echo $producto_editar ? 'Editar Producto' : 'Registrar Producto'; ?></h2>
                
                <form method="POST">
                    <input type="hidden" name="accion" value="<?php echo $producto_editar ? 'actualizar' : 'registrar'; ?>">
                    <?php if ($producto_editar): ?>
                        <input type="hidden" name="codigo_original" value="<?php echo htmlspecialchars($producto_editar['codigo']); ?>">
                    <?php endif; ?>
                    
                    <div class="form-group">
                        <label>Código:</label>
                        <input type="text" name="codigo" value="<?php echo $producto_editar ? htmlspecialchars($producto_editar['codigo']) : ''; ?>" <?php echo $producto_editar ? 'readonly' : 'required'; ?>>
                    </div>
                    
                    <div class="form-group">
                        <label>Nombre:</label>
                        <input type="text" name="nombre" value="<?php echo $producto_editar ? htmlspecialchars($producto_editar['nombre']) : ''; ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Unidad:</label>
                        <select name="unidad_medida" required>
                            <option value="">Seleccionar</option>
                            <?php foreach (['gramos', 'unidades', 'libras', 'kilogramos'] as $unidad): ?>
                                <option value="<?php echo $unidad; ?>" <?php echo ($producto_editar && $producto_editar['unidad_medida'] === $unidad) ? 'selected' : ''; ?>>
                                    <?php echo ucfirst($unidad); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Cantidad:</label>
                        <input type="number" name="cantidad" value="<?php echo $producto_editar ? $producto_editar['cantidad'] : ''; ?>" step="0.01" min="0.01" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Campesino:</label>
                        <select name="numero_documento_campesino" required>
                            <option value="">Seleccionar</option>
                            <?php foreach ($campesinos as $campesino): ?>
                                <option value="<?php echo htmlspecialchars($campesino['numero_documento']); ?>" <?php echo ($producto_editar && $producto_editar['numero_documento_campesino'] === $campesino['numero_documento']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($campesino['nombre']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <button type="submit" class="btn"><?php echo $producto_editar ? 'Actualizar' : 'Registrar'; ?></button>
                    <?php if ($producto_editar): ?>
                        <a href="productos.php" class="btn">Cancelar</a>
                    <?php endif; ?>
                </form>
            </section>
        <?php endif; ?>

        <section>
            <h2>Buscar Productos</h2>
            <form method="POST">
                <input type="hidden" name="accion" value="buscar">
                <div class="flex">
                    <input type="text" name="termino_busqueda" placeholder="Código o nombre" value="<?php echo isset($_POST['termino_busqueda']) ? htmlspecialchars($_POST['termino_busqueda']) : ''; ?>">
                    <select name="filtro_campesino">
                        <option value="">Todos</option>
                        <?php foreach ($campesinos as $campesino): ?>
                            <option value="<?php echo htmlspecialchars($campesino['numero_documento']); ?>" <?php echo (isset($_POST['filtro_campesino']) && $_POST['filtro_campesino'] == $campesino['numero_documento']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($campesino['nombre']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit" class="btn">Buscar</button>
                </div>
            </form>
        </section>

        <section>
            <h2>Lista de Productos</h2>
            <?php 
            $productos_mostrar = isset($productos_encontrados) ? $productos_encontrados : $todos_productos;
            if (empty($productos_mostrar)): 
            ?>
                <p>No hay productos registrados.</p>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Producto</th>
                            <th>Cantidad</th>
                            <th>Unidad</th>
                            <th>Campesino</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($productos_mostrar as $producto): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($producto['codigo']); ?></td>
                                <td><?php echo htmlspecialchars($producto['nombre']); ?></td>
                                <td><?php echo $producto['cantidad']; ?></td>
                                <td><?php echo htmlspecialchars($producto['unidad_medida']); ?></td>
                                <td><?php echo htmlspecialchars($producto['nombre_campesino']); ?></td>
                                <td>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="accion" value="editar">
                                        <input type="hidden" name="codigo" value="<?php echo htmlspecialchars($producto['codigo']); ?>">
                                        <button type="submit" class="btn">Editar</button>
                                    </form>
                                    <form method="POST" style="display: inline;" onsubmit="return confirm('¿Eliminar producto?');">
                                        <input type="hidden" name="accion" value="eliminar">
                                        <input type="hidden" name="codigo" value="<?php echo htmlspecialchars($producto['codigo']); ?>">
                                        <button type="submit" class="btn">Eliminar</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </section>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2024 Mercado Campesino - Vereda Pueblo Rico</p>
        </div>
    </footer>

    <!-- Modal para notificaciones -->
    <div id="modal" class="modal" style="display: none;">
        <div class="modal-content">
            <span class="close">&times;</span>
            <div class="modal-icon">
                <span id="modal-icon">✅</span>
            </div>
            <h3 id="modal-title">¡Éxito!</h3>
            <p id="modal-message">Operación completada exitosamente</p>
            <button id="modal-btn" class="btn">Aceptar</button>
        </div>
    </div>

    <script>
        // Función para mostrar modal
        function mostrarModal(tipo, titulo, mensaje) {
            const modal = document.getElementById('modal');
            const modalIcon = document.getElementById('modal-icon');
            const modalTitle = document.getElementById('modal-title');
            const modalMessage = document.getElementById('modal-message');
            const modalContent = document.querySelector('.modal-content');
            
            if (tipo === 'success') {
                modalIcon.textContent = '✅';
                modalContent.style.borderTop = '4px solid var(--success-color, #28a745)';
            } else {
                modalIcon.textContent = '❌';
                modalContent.style.borderTop = '4px solid var(--error-color, #dc3545)';
            }
            
            modalTitle.textContent = titulo;
            modalMessage.textContent = mensaje;
            modal.style.display = 'block';
        }
        
        // Cerrar modal
        document.querySelector('.close').onclick = function() {
            document.getElementById('modal').style.display = 'none';
        }
        
        document.getElementById('modal-btn').onclick = function() {
            document.getElementById('modal').style.display = 'none';
        }
        
        window.onclick = function(event) {
            const modal = document.getElementById('modal');
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        }
        
        // Mostrar modal si hay mensaje de éxito o error
        <?php if (!empty($mensaje) && $tipo_mensaje === 'exito'): ?>
            mostrarModal('success', '¡Producto Registrado!', '<?php echo addslashes($mensaje); ?>');
        <?php elseif (!empty($mensaje) && $tipo_mensaje === 'error'): ?>
            mostrarModal('error', 'Error', '<?php echo addslashes($mensaje); ?>');
        <?php endif; ?>
    </script>
</body>
</html>