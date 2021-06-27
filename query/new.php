<?php
// Crea una nueva consulta asociada a un usuario concreto.
// A esta ruta puede accederse mediante GET o POST
// - [GET]:  Mostrar el formulario para crear una nueva Consulta
// - [POST]: Procesar los datos del formulario recibido.

// En todos los casos debe comprobarse que el usuario esté autenticado, si
// no lo está se redirige a la página de inicio de sesión con un bonito
// mensaje de error TODO: implementar el mensaje de error en /login

session_start();

require_once '../controllers/Database.php';
require_once "../controllers/Queries.php";
require_once "../controllers/ErrorManager.php";

// Si el usuario no está autenticado...
if (!isset($_SESSION["id"])) {
    header( "location: /user/login.php" );
}

$title       = $title_err        = "";
$description = $description_err  = "";
$query       = $query_err        = "";
$global_err  = "";

// [POST]: Comprueba los datos del usuario y lo introduce en la base de datos.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Inicializar parámetros y mensajes de error
    $title       = trim($_POST['title']);
    $description = trim($_POST['description']);
    $query       = trim($_POST['query']);

    // Mensajes de error
    $title_err       = (empty($title))       ? "¡Debes proporcionar un título!"       : "";
    $description_err = (empty($description)) ? "¡Debes proporcionar una descripción!" : "";
    $query_err       = (empty($query))       ? "¡Debes proporcionar una consulta!"    : "";
    $global_err      = "";

    // Si los campos no están vacíos INSERTAR la consulta.
    if (!empty($title) && !empty($description) && !empty($query)) {
        if (Database::getInstance()->insert_query($_SESSION["id"], $title, $description, $query)) {
            header('location: /query/published.php');
        } else {
            $global_err = "Algo ha salido mal, inténtalo de nuevo más tarde.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <title>Nueva consulta</title>
    <?php include_once "../fragments/head_fragment.php" ?>
</head>
<body>
<main>
    <?php include_once "../fragments/nav_fragment.php" ?>
    <section class="container">
        <h1>Nueva Consulta</h1>
        <p>Introduce los datos de la consulta para poder compartirla</p>

        <?php ErrorManager::display_field_error($global_err); ?>

        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
            <label for="title">Título</label>
            <input type="text" name="title" id="title" placeholder="Tesla Chargers" >
            <?php ErrorManager::display_field_error($title_err); ?>

            <label for="description">Descripción</label>
            <textarea name="description" id="description" placeholder="Tesla PowerPack leaks its IP..."></textarea>
            <?php ErrorManager::display_field_error($description_err); ?>

            <label for="query">Consulta</label>
            <textarea name="query" id="query"
                      placeholder="http.title:'Tesla PowerPack System' http.component:'d3' -ga3ca4f2"></textarea>
            <?php ErrorManager::display_field_error($query_err); ?>

            <div class="row">
                <div class="column column-25 column-offset-75">
                    <input type="submit" class="button-primary" value="Enviar">
                </div>
            </div>
        </form>
    </section>
</main>
</body>
</html>