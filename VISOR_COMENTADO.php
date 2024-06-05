<?php
// Credenciales de conexión con la base de datos
$nombre_servidor = "localhost";
$nombre_usuario = "root";
$contrasena = "root";
$nombre_bd = "web";

// Inicio session
session_start();

// Comprobación de si se quiere cerrar sesión
if (isset($_GET['cerrar_sesion'])) {
    session_unset(); // Limpia las variables de la sesión
    session_destroy(); // Destruye la sesión
    header("Location: index.php"); // Redirige al usuario a index.php
    exit;
}

// Verificar si el usuario está autenticado
if (!isset($_SESSION["usuario_id"])) {
    header("Location: login.php"); // Redirigir al usuario a la página de inicio de sesión
    exit;
}

// Obtener ID de usuario de la sesión
$usuario_id = $_SESSION["usuario_id"];

// Conexión con la base de datos
$conexion = new mysqli($nombre_servidor, $nombre_usuario, $contrasena, $nombre_bd);

// Comprobación de fallo al conectar
if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
}

// Arrays para las imagenes y los usuarios sin fotos
$imagenes = [];
$usuarios_sin_fotos = [];

// Si el usuario es administrador id = 1, saca todo
if ($usuario_id == 1) {
    $consulta = "SELECT usuarios.id AS usuario_id, usuarios.nombre_usuario, imagenes.imagen_url 
                FROM usuarios 
                LEFT JOIN imagenes ON usuarios.id = imagenes.usuario_id";
    $resultado = $conexion->query($consulta);

    // Comprobación de si la consulta devuelve algo
    if ($resultado->num_rows > 0) {
        while ($fila = $resultado->fetch_assoc()) {
            if ($fila['imagen_url']) {
                $imagenes[$fila['nombre_usuario']][] = $fila['imagen_url']; // Añade las imagenes al array
            } else {
                $usuarios_sin_fotos[$fila['nombre_usuario']] = "No se encontraron fotografías"; // Añade los usuarios sin fotos
            //Se hacen las dos cosas ya que desde la vista del admin salen tanto los usuarios confotos como los usuarios isn fotos
            }
        }
    }
} else {
    // Si el usuario no es admin, osea no tiene id 1 solo va a sacar las fotos con ese id
    $consulta = "SELECT imagen_url FROM imagenes WHERE usuario_id = $usuario_id";
    $resultado = $conexion->query($consulta);

    // Comprobación de si devuelve resultados
    if ($resultado->num_rows > 0) {
        while ($fila = $resultado->fetch_assoc()) {
            $imagenes[] = $fila["imagen_url"]; // Añade las imagenes al array
        }
    } else {
        //si no tiene fotos saca este mensaje
        $mensaje = "No se encontraron imágenes para este usuario.";
    }
}

// Cerrar conexión con la base de datos
$conexion->close();
?>



<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visor de Fotografías</title>
    <style>
        body {
            background-color: #add8e6;
            margin: 0;
            font-family: Arial, sans-serif;
            text-align: center;
        }
        h1 {
            color: black;
            margin-top: 20px;
        }
        .galeria {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 10px;
            padding: 20px;
        }
        .galeria img {
            width: 200px;
            height: 200px;
            object-fit: cover;
            border-radius: 10px;
            cursor: pointer;
        }
        .galeria img.expanded {
            width: 400px;
            height: 400px;
            max-width: 50%; /* tamaño 50% */
            max-height: 50vh;
            object-fit: contain;
            cursor: zoom-out;
        }
        .instrucciones {
            background-color: rgba(255, 255, 255, 0.8);
            padding: 10px;
            border-radius: 12px;
            position: fixed;
            bottom: 10px;
            width: 80%;
            margin: 0 auto;
            left: 50%;
            transform: translateX(-50%);
            text-align: center;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }
        .boton-cerrar-sesion {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background-color: #ff4c4c;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 12px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <!-- Título principal -->
    <h1>Visor de Fotografías</h1>
    <?php if (isset($mensaje)) : ?>
        <!-- Mostrar mensaje si no hay imágenes -->
        <p><?php echo $mensaje; ?></p>
    <?php else : ?>
        <?php if ($usuario_id == 1) : ?>
            <!-- Si es admin muestra todo -->
            <?php foreach ($imagenes as $usuario => $urls) : ?>
                <h2><?php echo htmlspecialchars($usuario); ?></h2>
                <div class="galeria">
                    <!-- Con el foreach va a ir pasando por el array de fotos y sacando la url de cada una para poder mostrarlas -->
                    <?php foreach ($urls as $imagen_url) : ?>
                        <!-- el src de la imagen lo saca con una consulta de php sobre $imagen_url para mostrarlo en el html y también usa el onclick para el cambio de tamaño de la imagen -->
                        <img src="<?php echo htmlspecialchars($imagen_url); ?>" alt="Imagen" onclick="toggleImageSize(this)">
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>
            <!-- Aquí si no tiene imagenes entonces va al array de usuarios sin fotos y saca su mensaje -->
            <?php foreach ($usuarios_sin_fotos as $usuario => $mensaje) : ?>
                <h2><?php echo htmlspecialchars($usuario); ?></h2>
                <p><?php echo $mensaje; ?></p>
            <?php endforeach; ?>
        <?php else : ?>
            <!-- Si el usuario no es admin solo va a sacar las fotos de ese usuario -->
            <div class="galeria">
                <?php foreach ($imagenes as $imagen_url) : ?>
                    <img src="<?php echo htmlspecialchars($imagen_url); ?>" alt="Imagen" onclick="toggleImageSize(this)">
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>
    <div class="instrucciones">
        <!-- Instrucciones -->
        <p>Instrucciones de uso: Para ver la foto en su resolución correcta, pulsa sobre la imagen.<br>
        Para descargar la foto pulsa con el botón derecho del cursor y haz click en guardar imagen como.</p>
    </div>
    <button class="boton-cerrar-sesion" onclick="location.href='visor.php?cerrar_sesion=true'">Cerrar Sesión</button>
    <script>
        // Función para alternar el tamaño de la imagen
        function toggleImageSize(img) {
            if (img.classList.contains('expanded')) {
                img.classList.remove('expanded');
            } else {
                img.classList.add('expanded');
            }
        }
    </script>
</body>
</html>
