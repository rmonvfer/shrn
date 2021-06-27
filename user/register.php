<?php
/**
 * Registra a un usuario en la plataforma.
 * Puede accederse mediante GET o POST
 */

$CURRENT_PAGE = "register";

// Include controllers file
require_once '../controllers/Database.php';
require_once "../controllers/Queries.php";
require_once "../controllers/ErrorManager.php";

// Redirigir a home si ha iniciado sesión previamente
if (isset($_SESSION["id"])) {
    header( "location: /user/login.php" );
}

// Inicializar parámetros y mensajes de error
$username     = $password     = $confirm_password     = "";
$username_err = $password_err = $confirm_password_err = $global_err = "";

// [POST]: Comprueba los datos del usuario y lo introduce en la base de datos.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // El nombre de usuario está vacío
    if (empty(trim($_POST['username']))) {
        $username_err = "¡El nombre de usuario no puede estar vacío!";
    } else {
        $_user = Database::getInstance()->get_user_by_username($_POST["username"]);
        if ($_user["id_usuario"] == -1) { // Nombre disponible
            $username = trim($_POST["username"]);
        } else {
            $username_err = "¡Nombre de usuario no disponible!";
        }
    }

    // Comprobar la contraseña
    if (empty(trim($_POST["password"]))) {
        $password_err = "Introduce una contraseña";

    } else if (strlen(trim($_POST["password"])) < 6) {
        $password_err = "La contraseña debe tener 6 caracteres como mínimo.";

    } else {
        $password = trim($_POST["password"]);
    }

    // Comprobar la confirmación de contraseña
    if (empty(trim($_POST["confirm_password"]))) {
        $confirm_password_err = "Confirma la contraseña";
    } else {
        $confirm_password = trim($_POST["confirm_password"]);
        if (empty($password_err) && ($password != $confirm_password)) {
            $confirm_password_err = "¡Las contraseñas no coinciden!";
        }
    }


    // Si los campos no están vacíos INSERTAR al usuario.
    if (empty($username_err) && empty($password_err) && empty($confirm_err)) {
        if (Database::getInstance()->user_register($username, $password)) {
            header('location: login.php');

        } else {
            $global_err = "¡Vaya, ha ocurrido un error!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
	<title>Registro</title>
    <?php include_once "../fragments/head_fragment.php" ?>
</head>
<body>
    <?php include_once "../fragments/nav_fragment.php" ?>
	<main>
		<section class="container">
			<h1>Registro</h1>
        	<p>Introduce tus credenciales</p>
        	<form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
                <label for="username">Nombre de usuario</label>
                <input type="text" name="username" id="username">
				<?php ErrorManager::display_field_error($username_err); ?>

                <label for="password">Contraseña</label>
                <input type="password" name="password" id="password">
				<?php ErrorManager::display_field_error($password_err); ?>

                <label for="confirm_password">Repite la contraseña</label>
                <input type="password" name="confirm_password" id="confirm_password">
				<?php ErrorManager::display_field_error($confirm_password_err); ?>
				<?php ErrorManager::display_field_error($global_err); ?>
                
                <input type="submit" title="enviar" class="button" value="Enviar">
                <input type="reset" title="borrar" class="button-clear" value="Borrar">

        		<p>¿Ya tienes una cuenta? <a href="login.php">Inicia sesión aquí</a>.</p>
        	</form>
		</section>
	</main>
</body>
</html>