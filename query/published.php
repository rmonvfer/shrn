<?php
// Muestra el listado de consultas publicadas por el usuario que ha iniciado sesión
// A esta ruta puede accederse mediante GET

session_start();

require_once '../controllers/Queries.php';
require_once "../controllers/Database.php";

// Si el usuario no está autenticado...
if (!isset($_SESSION["id"])) {
    header('location: /user/login.php'); // Redirigirle a /user/login
}

$USER_QUERIES = Database::getInstance()->get_user_queries_by_user_id($_SESSION["id"]);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <title>Mis consultas</title>
    <?php include_once "../fragments/head_fragment.php" ?>
</head>
<body>
<main>
    <?php include_once "../fragments/nav_fragment.php" ?>
    <section class="container">
        <h1>Mis consultas publicadas</h1>

        <?php
        if (count($USER_QUERIES) >= 1) { ?>
            <table>
            <thead>
                <tr>
                    <th>Título</th>
                    <th>Votos</th>
                    <th>Fecha</th>
                    <th>Ver</th>
                </tr>
            </thead>
            <tbody>
        <?php
        foreach ($USER_QUERIES as $consulta) { ?>
                <tr>
                    <td><?php echo $consulta["titulo_consulta"] ?></td>
                    <td><?php echo $consulta["votos_consulta"] ?></td>
                    <td><?php echo $consulta["g_timestamp"] ?></td>
                    <td>
                        <a class="button" href="/query/detail.php?qid=<?php echo $consulta['id_consulta'] ?>">
                            Ver <em class="fas fa-arrow-right"></em>
                        </a>
                    </td>
                </tr>
        <?php } ?>
            </tbody>
            </table>
        <?php
        } else {
            // En caso de no existir, mostrar un mensaje.
            echo "<p>¡No has publicado ninguna consulta!</p>";
        }
        ?>
    </section>
</main>
</body>
</html>