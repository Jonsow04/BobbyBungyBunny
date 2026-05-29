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

$id = $_GET['id'] ?? 0;
$producto = $adminController->getProductoById($id);

if (!$producto) {
    header('Location: dashboard.php');
    exit();
}

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
        $resultado = $adminController->actualizarProducto($id, $datos, $imagenNombre);
        
        if ($resultado['success']) {
            $mensaje = 'Producto actualizado correctamente';
            $producto = $adminController->getProductoById($id);
        } else {
            $error = $resultado['error'];
        }
    }
}

$titulo = 'Editar producto | Administración';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $titulo; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="assets/css/adminStyleSheet.css">
    <style>
        .form-container {
            max-width: 700px;
            margin: 2rem auto;
            background: white;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: var(--sombra);
        }
        .form-group {
            margin-bottom: 1rem;
        }
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: bold;
            color: var(--cafe-noir);
        }
        .form-group input, .form-group select, .form-group textarea {
            width: 100%;
            padding: 0.8rem;
            border: 1px solid var(--tan);
            border-radius: 6px;
            font-family: inherit;
        }
        .form-group textarea {
            resize: vertical;
            min-height: 100px;
        }
        .form-group input[type="file"] {
            padding: 0.5rem;
            background: var(--fondo);
        }
        .imagen-actual {
            margin: 1rem 0;
            text-align: center;
            padding: 1rem;
            background: var(--fondo);
            border-radius: 8px;
        }
        .imagen-actual img {
            max-width: 150px;
            max-height: 150px;
            border-radius: 8px;
            border: 1px solid var(--tan);
        }
        .imagen-preview {
            margin-top: 1rem;
            text-align: center;
        }
        .imagen-preview img {
            max-width: 200px;
            max-height: 200px;
            border-radius: 8px;
            border: 1px solid var(--tan);
            padding: 5px;
        }
        .btn-guardar {
            background: var(--success);
            color: white;
            padding: 0.8rem 1.5rem;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 1rem;
            width: 100%;
        }
        .btn-guardar:hover {
            background: #219a52;
        }
        .btn-volver {
            display: inline-block;
            margin-top: 1rem;
            color: var(--cafe-noir);
            text-decoration: none;
        }
        .producto-id {
            background: var(--fondo);
            padding: 0.5rem;
            border-radius: 6px;
            margin-bottom: 1rem;
            text-align: center;
            color: var(--cafe-noir);
        }
        .alert {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1rem;
        }
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .ayuda-texto {
            font-size: 0.75rem;
            color: var(--verde-moss);
            margin-top: 0.25rem;
            display: block;
        }
        .info-redimension {
            background: var(--fondo);
            padding: 0.8rem;
            border-radius: 8px;
            margin-bottom: 1rem;
            font-size: 0.85rem;
            border-left: 3px solid var(--success);
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <div class="dashboard-header">
            <h1><i class="fas fa-edit"></i> Editar producto</h1>
            <a href="dashboard.php" class="btn-cerrar-sesion"><i class="fas fa-arrow-left"></i> Volver al dashboard</a>
        </div>
        
        <div class="info-redimension">
            <i class="fas fa-info-circle"></i> 
            Las imágenes se redimensionarán automáticamente a 800x800 píxeles para optimizar la velocidad de carga.
        </div>
        
        <?php if ($mensaje): ?>
            <div class="alert alert-success"><?php echo $mensaje; ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <div class="form-container">
            <div class="producto-id">
                <strong>ID del producto:</strong> <?php echo $producto['idArticulo']; ?>
            </div>
            
            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="categoria_id">Categoría *</label>
                    <select id="categoria_id" name="categoria_id" required>
                        <option value="">Selecciona una categoría</option>
                        <?php foreach ($categorias as $categoria): ?>
                            <option value="<?php echo $categoria['idCatArticulo']; ?>" <?php echo ($categoria['idCatArticulo'] == $producto['categoria_id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($categoria['nombre']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="nombre">Nombre del producto *</label>
                    <input type="text" id="nombre" name="nombre" required value="<?php echo htmlspecialchars($producto['nombre']); ?>">
                </div>
                
                <div class="form-group">
                    <label for="descripcion">Descripción *</label>
                    <textarea id="descripcion" name="descripcion" required><?php echo htmlspecialchars($producto['descripcion']); ?></textarea>
                </div>
                
                <div class="form-group">
                    <label for="precio">Precio *</label>
                    <input type="number" step="0.01" id="precio" name="precio" required value="<?php echo $producto['precio']; ?>">
                </div>
                
                <div class="form-group">
                    <label for="stock">Stock *</label>
                    <input type="number" id="stock" name="stock" required value="<?php echo $producto['stock']; ?>">
                </div>
                
                <?php if ($producto['imagen']): ?>
                    <div class="imagen-actual">
                        <label>Imagen actual</label>
                        <div>
                            <img src="/assets/multimedia/productos/<?php echo $producto['imagen']; ?>" alt="Imagen del producto">
                        </div>
                    </div>
                <?php endif; ?>
                
                <div class="form-group">
                    <label for="imagen">Nueva imagen (opcional)</label>
                    <input type="file" id="imagen" name="imagen" accept="image/jpeg,image/png,image/gif,image/webp">
                    <small class="ayuda-texto">
                        <i class="fas fa-info-circle"></i> Formatos permitidos: JPG, PNG, GIF, WEBP. Tamaño máximo: 10MB
                    </small>
                    <div class="imagen-preview" id="imagenPreview" style="display: none;">
                        <img id="previewImg" src="#" alt="Vista previa">
                    </div>
                </div>
                
                <button type="submit" class="btn-guardar"><i class="fas fa-save"></i> Actualizar producto</button>
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