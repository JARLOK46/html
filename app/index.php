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
            <div class="header-content">
                <div class="logo">
                    <?php
                    // Buscar imagen de logo en la carpeta uploads/logo/
                    $logo_path = 'uploads/logo/';
                    $logo_image = null;
                    
                    if (is_dir($logo_path)) {
                        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                        $files = scandir($logo_path);
                        
                        foreach ($files as $file) {
                            if ($file != '.' && $file != '..') {
                                $file_extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                                if (in_array($file_extension, $allowed_extensions)) {
                                    $logo_image = $logo_path . $file;
                                    break; // Usar la primera imagen encontrada
                                }
                            }
                        }
                    }
                    
                    if ($logo_image && file_exists($logo_image)): ?>
                        <img src="<?php echo htmlspecialchars($logo_image); ?>" alt="Logo Mercado Campesino" class="logo-image">
                    <?php else: ?>
                        <div class="logo-circle"></div>
                    <?php endif; ?>
                    <h1>Mercado Campesino</h1>
                </div>
                <nav class="main-nav">
                    <ul>
                        <li><a href="index.php" class="active">Inicio</a></li>
                        <li><a href="campesinos.php">Campesinos</a></li>
                        <li><a href="productos.php">Productos</a></li>
                    </ul>
                </nav>
            </div>
        </div>
    </header>

    <main>
        <div class="container">
            <!-- Sección Hero -->
            <section class="hero-section">
                <div class="hero-content">
                    <h1>🌱 Bienvenido al Sistema de Mercado Campesino</h1>
                    <p class="hero-subtitle">Conectando la tradición agrícola de la Vereda Pueblo Rico con la tecnología moderna</p>
                    <p class="hero-description">Nuestro sistema facilita la gestión integral de campesinos y productos agrícolas, promoviendo el desarrollo sostenible y el comercio justo en nuestra comunidad rural.</p>
                </div>
            </section>

            <!-- Estadísticas -->
            <section class="stats-section">
                <h2>📊 Estadísticas del Sistema</h2>
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon">👨‍🌾</div>
                        <div class="stat-number"><?php echo $total_campesinos; ?></div>
                        <div class="stat-label">Campesinos Registrados</div>
                        <div class="stat-description">Productores activos en nuestra comunidad</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon">🥕</div>
                        <div class="stat-number"><?php echo $total_productos; ?></div>
                        <div class="stat-label">Productos Disponibles</div>
                        <div class="stat-description">Variedad de productos agrícolas frescos</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon">🌿</div>
                        <div class="stat-number">100%</div>
                        <div class="stat-label">Productos Orgánicos</div>
                        <div class="stat-description">Cultivados sin químicos dañinos</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon">🚚</div>
                        <div class="stat-number">24h</div>
                        <div class="stat-label">Entrega Fresca</div>
                        <div class="stat-description">Del campo a tu mesa en menos de 24 horas</div>
                    </div>
                </div>
            </section>

            <!-- Acciones Principales -->
            <section class="actions-section">
                <h2>🛠️ Gestión del Sistema</h2>
                <div class="actions-grid">
                    <div class="action-card">
                        <div class="action-icon">👥</div>
                        <h3>Gestionar Campesinos</h3>
                        <p>Registra, actualiza y administra la información de los productores de nuestra comunidad.</p>
                        <a href="campesinos.php" class="btn btn-primary">Ir a Campesinos</a>
                    </div>
                    <div class="action-card">
                        <div class="action-icon">🛒</div>
                        <h3>Gestionar Productos</h3>
                        <p>Administra el catálogo de productos agrícolas disponibles en el mercado campesino.</p>
                        <a href="productos.php" class="btn btn-primary">Ir a Productos</a>
                    </div>
                </div>
            </section>

            <!-- Información Adicional -->
            <section class="info-section">
                <div class="info-grid">
                    <div class="info-card">
                        <h3>🌾 Nuestra Misión</h3>
                        <p>Fortalecer la economía rural mediante la digitalización de procesos agrícolas, conectando directamente a los campesinos con los consumidores y promoviendo prácticas sostenibles.</p>
                    </div>
                    <div class="info-card">
                        <h3>🎯 Objetivos</h3>
                        <ul>
                            <li>Mejorar la trazabilidad de productos agrícolas</li>
                            <li>Facilitar el acceso a mercados locales</li>
                            <li>Promover la agricultura sostenible</li>
                            <li>Fortalecer la comunidad campesina</li>
                        </ul>
                    </div>
                    <div class="info-card">
                        <h3>🌍 Impacto Social</h3>
                        <p>Contribuimos al desarrollo sostenible de la Vereda Pueblo Rico, mejorando la calidad de vida de las familias campesinas y garantizando productos frescos y saludables para toda la comunidad.</p>
                    </div>
                </div>
            </section>

            <!-- Galería de Imágenes -->
            <section class="gallery-section">
                <h2>📸 Galería del Mercado Campesino</h2>
                <div class="gallery-grid">
                    <?php
                    $images_dir = 'uploads/images/';
                    $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                    
                    if (is_dir($images_dir)) {
                        $images = array_diff(scandir($images_dir), array('.', '..'));
                        $image_count = 0;
                        
                        foreach ($images as $image) {
                            $file_extension = strtolower(pathinfo($image, PATHINFO_EXTENSION));
                            if (in_array($file_extension, $allowed_extensions)) {
                                $image_count++;
                                echo '<div class="gallery-item">';
                                echo '<img src="' . $images_dir . htmlspecialchars($image) . '" alt="Imagen del mercado campesino" loading="lazy">';
                                echo '<div class="gallery-overlay">';
                                echo '<span class="gallery-title">' . htmlspecialchars(pathinfo($image, PATHINFO_FILENAME)) . '</span>';
                                echo '</div>';
                                echo '</div>';
                            }
                        }
                        
                        if ($image_count === 0) {
                            echo '<div class="no-images">';
                            echo '<p>🖼️ No hay imágenes disponibles en este momento.</p>';
                            echo '<p class="upload-info">Las imágenes se pueden subir a la carpeta: <code>uploads/images/</code></p>';
                            echo '</div>';
                        }
                    } else {
                        echo '<div class="no-images">';
                        echo '<p>📁 La carpeta de imágenes no existe.</p>';
                        echo '<p class="upload-info">Crear la carpeta: <code>uploads/images/</code></p>';
                        echo '</div>';
                    }
                    ?>
                </div>
            </section>

            <!-- Características del Sistema -->
            <section class="features-section">
                <h2>✨ Características del Sistema</h2>
                <div class="features-grid">
                    <div class="feature-item">
                        <span class="feature-icon">🔒</span>
                        <h4>Seguro y Confiable</h4>
                        <p>Protección de datos con tecnología PDO y validaciones robustas</p>
                    </div>
                    <div class="feature-item">
                        <span class="feature-icon">📱</span>
                        <h4>Responsive</h4>
                        <p>Accesible desde cualquier dispositivo: móvil, tablet o computadora</p>
                    </div>
                    <div class="feature-item">
                        <span class="feature-icon">⚡</span>
                        <h4>Rápido y Eficiente</h4>
                        <p>Interfaz optimizada para una experiencia de usuario fluida</p>
                    </div>
                    <div class="feature-item">
                        <span class="feature-icon">🔄</span>
                        <h4>Actualización en Tiempo Real</h4>
                        <p>Información siempre actualizada sobre inventarios y disponibilidad</p>
                    </div>
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