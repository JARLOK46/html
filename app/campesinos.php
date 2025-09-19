<?php
require_once 'conection.php';

$mensaje = '';
$tipoMensaje = '';

if ($_POST && isset($_POST['accion']) && $_POST['accion'] == 'registrar') {
    $numero_documento = trim($_POST['numero_documento']);
    $nombre = trim($_POST['nombre']);
    $telefono = trim($_POST['telefono']);
    $nombre_finca = trim($_POST['nombre_finca']);
    
    if (empty($numero_documento) || empty($nombre) || empty($telefono) || empty($nombre_finca)) {
        $mensaje = 'Todos los campos son obligatorios';
        $tipoMensaje = 'error';
    } else {
        try {
            $stmt = $pdo->prepare("SELECT numero_documento FROM campesinos WHERE numero_documento = ?");
            $stmt->execute([$numero_documento]);
            
            if ($stmt->rowCount() > 0) {
                $mensaje = 'Ya existe un campesino con ese documento';
                $tipoMensaje = 'error';
            } else {
                $stmt = $pdo->prepare("INSERT INTO campesinos (numero_documento, nombre, telefono, nombre_finca) VALUES (?, ?, ?, ?)");
                $stmt->execute([$numero_documento, $nombre, $telefono, $nombre_finca]);
                $mensaje = 'Campesino registrado exitosamente';
                $tipoMensaje = 'success';
                $numero_documento = $nombre = $telefono = $nombre_finca = '';
            }
        } catch (PDOException $e) {
            $mensaje = 'Error: ' . $e->getMessage();
            $tipoMensaje = 'error';
        }
    }
}

if (isset($_GET['eliminar'])) {
    try {
        $stmt = $pdo->prepare("DELETE FROM campesinos WHERE numero_documento = ?");
        $stmt->execute([$_GET['eliminar']]);
        $mensaje = 'Campesino eliminado';
        $tipoMensaje = 'success';
    } catch (PDOException $e) {
        $mensaje = 'Error al eliminar';
        $tipoMensaje = 'error';
    }
}

$campesino_editar = null;
if (isset($_GET['editar'])) {
    $stmt = $pdo->prepare("SELECT * FROM campesinos WHERE numero_documento = ?");
    $stmt->execute([$_GET['editar']]);
    $campesino_editar = $stmt->fetch(PDO::FETCH_ASSOC);
}

if ($_POST && isset($_POST['accion']) && $_POST['accion'] == 'actualizar') {
    $numero_documento_original = $_POST['numero_documento_original'];
    $numero_documento = trim($_POST['numero_documento']);
    $nombre = trim($_POST['nombre']);
    $telefono = trim($_POST['telefono']);
    $nombre_finca = trim($_POST['nombre_finca']);
    
    try {
        $stmt = $pdo->prepare("UPDATE campesinos SET numero_documento = ?, nombre = ?, telefono = ?, nombre_finca = ? WHERE numero_documento = ?");
        $stmt->execute([$numero_documento, $nombre, $telefono, $nombre_finca, $numero_documento_original]);
        $mensaje = 'Campesino actualizado';
        $tipoMensaje = 'success';
        $campesino_editar = null;
    } catch (PDOException $e) {
        $mensaje = 'Error al actualizar';
        $tipoMensaje = 'error';
    }
}

$busqueda = $_GET['busqueda'] ?? '';
try {
    if (!empty($busqueda)) {
        $stmt = $pdo->prepare("SELECT * FROM campesinos WHERE numero_documento LIKE ? OR nombre LIKE ? ORDER BY nombre");
        $busqueda_param = "%$busqueda%";
        $stmt->execute([$busqueda_param, $busqueda_param]);
    } else {
        $stmt = $pdo->query("SELECT * FROM campesinos ORDER BY nombre");
    }
    $campesinos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $campesinos = [];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Campesinos - Mercado Campesino</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <header>
        <div class="container">
            <h1>🌱 Mercado Campesino</h1>
            <p>Vereda Pueblo Rico</p>
        </div>
    </header>

    <nav>
        <div class="container">
            <ul>
                <li><a href="index.php">Inicio</a></li>
                <li><a href="campesinos.php" class="active">Campesinos</a></li>
                <li><a href="productos.php">Productos</a></li>
            </ul>
        </div>
    </nav>

    <main>
        <div class="container">
            <h1>Gestión de Campesinos</h1>

            <?php if (!empty($mensaje)): ?>
                <div class="message <?php echo $tipoMensaje; ?>">
                    <p><?php echo htmlspecialchars($mensaje); ?></p>
                </div>
            <?php endif; ?>

            <section>
                <h2><?php echo $campesino_editar ? 'Editar Campesino' : 'Registrar Campesino'; ?></h2>
                
                <form method="POST">
                    <input type="hidden" name="accion" value="<?php echo $campesino_editar ? 'actualizar' : 'registrar'; ?>">
                    <?php if ($campesino_editar): ?>
                        <input type="hidden" name="numero_documento_original" value="<?php echo htmlspecialchars($campesino_editar['numero_documento']); ?>">
                    <?php endif; ?>
                    
                    <div>
                        <label for="numero_documento">Número de Documento:</label>
                        <input type="text" id="numero_documento" name="numero_documento" 
                               value="<?php echo htmlspecialchars($campesino_editar['numero_documento'] ?? ''); ?>" required>
                    </div>
                    
                    <div>
                        <label for="nombre">Nombre:</label>
                        <input type="text" id="nombre" name="nombre" 
                               value="<?php echo htmlspecialchars($campesino_editar['nombre'] ?? ''); ?>" required>
                    </div>
                    
                    <div>
                        <label for="telefono">Teléfono:</label>
                        <input type="tel" id="telefono" name="telefono" 
                               value="<?php echo htmlspecialchars($campesino_editar['telefono'] ?? ''); ?>" required>
                    </div>
                    
                    <div>
                        <label for="nombre_finca">Nombre de la Finca:</label>
                        <input type="text" id="nombre_finca" name="nombre_finca" 
                               value="<?php echo htmlspecialchars($campesino_editar['nombre_finca'] ?? ''); ?>" required>
                    </div>
                    
                    <div>
                        <button type="submit" class="btn">
                            <?php echo $campesino_editar ? 'Actualizar' : 'Registrar'; ?>
                        </button>
                        <?php if ($campesino_editar): ?>
                            <a href="campesinos.php" class="btn">Cancelar</a>
                        <?php endif; ?>
                    </div>
                </form>
            </section>

            <section>
                <h2>Buscar Campesinos</h2>
                <form method="GET">
                    <input type="text" name="busqueda" value="<?php echo htmlspecialchars($busqueda); ?>" placeholder="Buscar...">
                    <button type="submit" class="btn">Buscar</button>
                    <a href="campesinos.php" class="btn">Limpiar</a>
                </form>
            </section>

            <section>
                <h2>Lista de Campesinos</h2>
                <?php if (empty($campesinos)): ?>
                    <p>No hay campesinos registrados.</p>
                <?php else: ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Documento</th>
                                <th>Nombre</th>
                                <th>Teléfono</th>
                                <th>Finca</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($campesinos as $campesino): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($campesino['numero_documento']); ?></td>
                                    <td><?php echo htmlspecialchars($campesino['nombre']); ?></td>
                                    <td><?php echo htmlspecialchars($campesino['telefono']); ?></td>
                                    <td><?php echo htmlspecialchars($campesino['nombre_finca']); ?></td>
                                    <td>
                                        <a href="campesinos.php?editar=<?php echo urlencode($campesino['numero_documento']); ?>" class="btn">Editar</a>
                                        <a href="campesinos.php?eliminar=<?php echo urlencode($campesino['numero_documento']); ?>" class="btn" onclick="return confirm('¿Eliminar campesino?');">Eliminar</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </section>
        </div>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2024 Mercado Campesino - Vereda Pueblo Rico</p>
        </div>
    </footer>
</body>
</html>