<?php
// Credenciales de conexión con la base de datos
$nombre_servidor = "localhost";
$nombre_usuario = "root";
$contrasena = "root";
$nombre_bd = "web";

// Comprobación de uso de POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recoge datos del formulario
    $usuario = $_POST['usuario'];
    $contrasena_usuario = $_POST['contraseña'];

    // Conexión con la base de datos
    $conexion = new mysqli($nombre_servidor, $nombre_usuario, $contrasena, $nombre_bd);

    // Comprobación de fallo al conectar
    if ($conexion->connect_error) {
        die("Conexión fallida: " . $conexion->connect_error);
    }

    // Consulta de usuario y contraseña
    $consulta = "SELECT id FROM usuarios WHERE nombre_usuario = '$usuario' AND contraseña = '$contrasena_usuario'";
    $resultado = $conexion->query($consulta);

    // Comprueba si la consulta anterior devuelve un resultado
    if ($resultado->num_rows == 1) {
        session_start();
        $_SESSION["usuario_id"] = $resultado->fetch_assoc()["id"];

        // Redirigir al usuario al visor
        header("Location: visor.php");
        exit;
    } else {
        // Mensaje de error credenciales de usuario
        echo "Error: Usuario o contraseña incorrectos.";
    }

    // Cerrar consulta
    $conexion->close();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión</title>
    <style>
        /* Estilos para la página */
        body {
            background-color: #add8e6; /* Fondo azul claro */
            margin: 0;
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            position: relative; /* Mete los elementos dentro del contenedor relatvio */
        }
        /* Contenedor grande centrado */
        .contenedor {
            text-align: center;
        }
        /* Estilo para el título */
        h2 {
            color: black;
        }
        /* Caja de inicio de sesión */
        .caja-inicio-sesion {
            background-color: rgba(255, 255, 255, 0.8); /* Fondo con opacidad bajada al 80% */
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            width: 300px;
            margin: 0 auto;
        }
        /* Estilo del formulario dentro del inicio de sesión */
        .caja-inicio-sesion form {
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        /* Estilo texto y contraseña */
        .caja-inicio-sesion input[type="text"],
        .caja-inicio-sesion input[type="password"] {
            padding: 10px;
            margin: 10px 0;
            width: 80%;
            border-radius: 8px;
            border: 1px solid #ccc;
        }
        /* Estilo enviar */
        .caja-inicio-sesion input[type="submit"] {
            padding: 10px 20px;
            border-radius: 8px;
            border: none;
            background-color: #007bff;
            color: white;
            cursor: pointer;
        }
        /* Efecto hover */
        .caja-inicio-sesion input[type="submit"]:hover {
            background-color: #0056b3;
        }
        /* Botón de solicitud de acceso */
        .solicitud-acceso {
            position: absolute;
            bottom: 20px;
            right: 20px;
            padding: 10px 20px;
            border-radius: 8px;
            border: none;
            background-color: #007bff;
            color: white;
            cursor: pointer;
        }
        /* Efecto hover para el botón de solicitar acceso */
        .solicitud-acceso:hover {
            background-color: #0056b3;
        }
        /* Estilo para el modal */
        /* El modal es la ventana emergente */
        .modal {
            display: none;  /* De normal el modal esta escondido */
            position: fixed;  /* Fija el modal */
            z-index: 1; /* Coloca el modal por encima de todo */
            left: 0; 
            top: 0; 
            width: 100%; 
            height: 100%; 
            overflow: auto;  /* Permite que se pueda bajar y subir por el contenido */
            background-color: rgba(0, 0, 0, 0.5); 
            justify-content: center; 
            align-items: center;
        }
        /* Contenido del modal */
        .contenido-modal {
            background-color: rgba(255, 255, 255, 0.8);
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            width: 300px;
            text-align: center;
        }
        /* Estilo para el botón de cerrar el modal */
        .cerrar {
            color: black;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }
        /* Efecto hover y focus para cerrar */
        .cerrar:hover,
        .cerrar:focus {
            color: #000;
            text-decoration: none;
            cursor: pointer;
        }
        /* Estilo para el botón del correo */
        .boton-correo {
            padding: 10px 20px;
            border-radius: 8px;
            border: none;
            background-color: #007bff;
            color: white;
            cursor: pointer;
            text-decoration: none;
        }
        /* Efecto hover para el botón del correo */
        .boton-correo:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <!-- Contenedor principal -->
    <div class="contenedor">
        <!-- Titulo de la página -->
        <h2>Visor de Fotografías</h2>
        <!-- Inicio de sesión -->
        <div class="caja-inicio-sesion">
            <!-- Formulario de inicio de sesión -->
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <label for="usuario">Usuario:</label>
                <input type="text" id="usuario" name="usuario" required><br>
                <label for="contraseña">Contraseña:</label>
                <input type="password" id="contraseña" name="contraseña" required><br>
                <input type="submit" value="Iniciar Sesión">
            </form>
        </div>
    </div>

    <!-- Botón para solicitar acceso -->
    <button class="solicitud-acceso" onclick="document.getElementById('modal').style.display='flex'">Solicitar acceso</button>

    <!-- Modal para solicitar acceso -->
    <div id="modal" class="modal">
        <div class="contenido-modal">
            <!-- Botón de cerrar el modal -->
            <span class="cerrar" onclick="document.getElementById('modal').style.display='none'">&times;</span>
            <!-- Contenido del modal -->
            <p>Para solicitar acceso deberás indicar en el correo tu nombre, el evento por el cual pides las fotos y algún dato característico del vehículo (Matrícula, color, modificaciones etc.) Gracias por la comprensión.</p>
            <a href="mailto:1717980@alu.murciaeduca.es?subject=ALTA_USUARIO" class="boton-correo">Solicitar Acceso</a>
        </div>
    </div>

    <script>
        // Obtener el modal
        var modal = document.getElementById('modal');
        // Cerrar el modal si se pulsa fuera
        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
    </script>
</body>
</html>