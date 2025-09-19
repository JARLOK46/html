<?php
require_once 'conection.php';

$total_campesinos = 0;
$total_productos = 0;

try {
    $stmt = $pdo->query("SELECT COUNT(*) FROM campesinos");
    $total_campesinos = $stmt->fetchColumn();    
    $stmt = $pdo->query("SELECT COUNT(*) FROM productos");
    $total_productos = $stmt->fetchColumn();
} catch (PDOException $e) {
    // Error silencioso
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mercado Campesino - Vereda Pueblo Rico</title>
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
                <li><a href="index.php" class="active">Inicio</a></li>
                <li><a href="campesinos.php">Campesinos</a></li>
                <li><a href="productos.php">Productos</a></li>
            </ul>
        </div>
    </nav>

    <main>
        <div class="container">
            <section>
                <h1>Bienvenido al Sistema de Mercado Campesino</h1>
                <p>Sistema para la gestión de campesinos y productos de la Vereda Pueblo Rico.</p>
                
                <div class="stats">
                    <div class="stat">
                        <span><?php echo $total_campesinos; ?></span>
                        <label>Campesinos</label>
                    </div>
                    <div class="stat">
                        <span><?php echo $total_productos; ?></span>
                        <label>Productos</label>
                    </div>
                </div>
                
                <div class="actions">
                    <a href="campesinos.php">Gestionar Campesinos</a>
                    <a href="productos.php">Gestionar Productos</a>
                </div>
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