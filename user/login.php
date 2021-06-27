<?php
// Inicia la sesión para un usuario registrado.
// A esta página puede accederse mediante GET o POST.

require_once "../controllers/Database.php";
require_once "../controllers/Queries.php";
require_once "../controllers/ErrorManager.php";

// Inicializar variables
$username     = $password     = '';
$username_err = $password_err = '';
$login_error  = $login_error  = '';

// Inicializar el almacenamiento de datos en sesión.
session_start();

// Redirigir a home si ha iniciado sesión previamente
if (isset($_SESSION["id"])) {
    header( "location: /user/home.php" );
}

// [POST]: Buscar los datos del usuario y crear una sesión si existe
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Comprobar que el usuario no está vacío
    if (empty(trim($_POST['username']))) {
        $username_err = '¡Tienes que introducir un nombre de usuario!';
    } else {
        $username = trim($_POST['username']);
    }

    // Comprobar que la contraseña no está vacía
    if (empty(trim($_POST['password']))) {
        $password_err = '¡Tienes que introducir una contraseña!';
    } else {
        $password = trim($_POST['password']);
    }

    // Validar credenciales
    if ( empty($username_err) && empty($password_err) ) {
        if ( Database::getInstance()->user_login($username, $password) ){
            $user = Database::getInstance()->get_user_by_username($username);

            // Almacenar los datos del usuario en la sesión
            $_SESSION['id']       = $user["id_usuario"];
            $_SESSION['username'] = $user["nombre_usuario"];
            $_SESSION['api_key']  = $user["shodan_key"];

            setcookie( "x-key-id", $user["id_usuario"], time() + (86400 * 30), "/");
            setcookie( "x-shodan-key", $user["shodan_key"], time() + (86400 * 30), "/");

            header('location: home.php');
        } else {
            $login_error = '¡Usuario o contraseña incorrectos!';
        }
    }
}

// [GET]: Devolver la vista con el formulario correspondiente
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <title>Inicio de sesión</title>
    <?php include_once "../fragments/head_fragment.php" ?>
</head>
<body>
    <?php include_once "../fragments/nav_fragment.php" ?>
    <main>
        <section class="container">
            <h1>Inicio de Sesión</h1>
            <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
                <fieldset>
                    <label for="username">Usuario</label>
                    <input type="text" name="username" id="username" placeholder="Tu nombre de usuario">
                    <?php ErrorManager::display_field_error($username_err); ?>

                    <label for="password">Contraseña</label>
                    <input type="password" name="password" id="password" placeholder="*********">
                    <?php ErrorManager::display_field_error($password_err); ?>
                    <?php ErrorManager::display_field_error($login_error); ?>

                    <input class="button" type="submit" value="Enviar">

                    <p>¿No tienes una cuenta? <a href="register.php">Regístrate</a>.</p>
                </fieldset>
            </form>
        </section>
    </main>
</body>
</html>