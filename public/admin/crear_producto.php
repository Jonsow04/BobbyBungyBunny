<?php
session_start();

// Verificar que el usuario sea administrador o gerente
if (!isset($_SESSION['usuario_id']) || ($_SESSION['usuario_tipo'] != 1 && $_SESSION['usuario_tipo'] != 2)) {
    header('Location: ../login.php');
    exit();
}

require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/controllers/AdminController.php';

$pdo = getConnection();
$adminController = new AdminController($pdo);
$categorias = $adminController->getCategorias();

$mensaje = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $datos = [
        'nombre' => trim($_POST['nombre']),
        'descripcion' => trim($_POST['descripcion']),
        'precio' => floatval($_POST['precio']),
        'stock' => intval($_POST['stock']),
        'categoria' => intval($_POST['categoria_id'])
    ];
    
    $imagenNombre = null;
    
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
        $resultadoImagen = $adminController->subirImagen($_FILES['imagen'], false);
        if ($resultadoImagen['success']) {
            $imagenNombre = $resultadoImagen['nombre'];
        } else {
            $error = $resultadoImagen['error'];
        }
    }
    
    if (!$error) {
        $resultado = $adminController->crearProducto($datos, $imagenNombre);
        
        if ($resultado['success']) {
            $mensaje = 'Producto creado correctamente';
            $_POST = [];
        } else {
            $error = $resultado['error'];
        }
    }
}

$titulo = 'Crear producto | Administración';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $titulo; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="assets/css/adminStyleSheet.css">
</head>
<body>
    <div class="dashboard-container">
        <div class="dashboard-header">
            <h1><i class="fas fa-plus-circle"></i> Crear nuevo producto</h1>
            <a href="dashboard.php" class="btn-cerrar-sesion"><i class="fas fa-arrow-left"></i> Volver al dashboard</a>
        </div>
        
        <div class="info-redimension">
            <i class="fas fa-info-circle"></i> 
            Las imágenes se redimensionarán automáticamente a 800x800 píxeles para optimizar la velocidad de carga.
            Formatos permitidos: JPG, PNG, GIF, WEBP. Tamaño máximo: 10MB.
        </div>
        
        <?php if ($mensaje): ?>
            <div class="alert alert-success"><?php echo $mensaje; ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <div class="form-container">
            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="categoria_id">Categoría *</label>
                    <select id="categoria_id" name="categoria_id" required>
                        <option value="">Selecciona una categoría</option>
                        <?php foreach ($categorias as $categoria): ?>
                            <option value="<?php echo $categoria['idCatArticulo']; ?>">
                                <?php echo htmlspecialchars($categoria['nombre']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="nombre">Nombre del producto *</label>
                    <input type="text" id="nombre" name="nombre" required value="<?php echo htmlspecialchars($_POST['nombre'] ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label for="descripcion">Descripción *</label>
                    <textarea id="descripcion" name="descripcion" required><?php echo htmlspecialchars($_POST['descripcion'] ?? ''); ?></textarea>
                </div>
                
                <div class="form-group">
                    <label for="precio">Precio *</label>
                    <input type="number" step="0.01" id="precio" name="precio" required value="<?php echo $_POST['precio'] ?? ''; ?>">
                </div>
                
                <div class="form-group">
                    <label for="stock">Stock *</label>
                    <input type="number" id="stock" name="stock" required value="<?php echo $_POST['stock'] ?? '0'; ?>">
                </div>
                
                <div class="form-group">
                    <label for="imagen">Imagen del producto</label>
                    <input type="file" id="imagen" name="imagen" accept="image/jpeg,image/png,image/gif,image/webp">
                    <small class="ayuda-texto">
                        <i class="fas fa-info-circle"></i> Formatos permitidos: JPG, PNG, GIF, WEBP. Tamaño máximo: 10MB (se redimensionará automáticamente)
                    </small>
                    <div class="imagen-preview" id="imagenPreview" style="display: none;">
                        <img id="previewImg" src="#" alt="Vista previa">
                    </div>
                </div>
                
                <button type="submit" class="btn-guardar"><i class="fas fa-save"></i> Guardar producto</button>
            </form>
            <a href="dashboard.php" class="btn-volver"><i class="fas fa-arrow-left"></i> Cancelar</a>
        </div>
    </div>
    
    <script>
        document.getElementById('imagen').addEventListener('change', function(e) {
            const preview = document.getElementById('imagenPreview');
            const previewImg = document.getElementById('previewImg');
            
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                    preview.style.display = 'block';
                };
                reader.readAsDataURL(this.files[0]);
            } else {
                preview.style.display = 'none';
                previewImg.src = '#';
            }
        });
    </script>
</body>
</html>