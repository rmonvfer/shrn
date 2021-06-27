<?php

session_start();

require_once '../controllers/Queries.php';
require_once "../controllers/Database.php";
require_once "../controllers/ImportExportHelper.php";

// Si el usuario no está autenticado...
if (!isset($_SESSION["id"])) {
    header('location: /user/login.php'); // Redirigirle a /user/login
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $_ids = $_POST["to_export_ids"];
    ImportExportHelper::export_queries_by_id($_ids, $_SESSION["id"]);
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
        <h1>Exportar consultas</h1>

        <?php if (count($USER_QUERIES) >= 1) { ?>

        <p>Selecciona al menos una consulta para exportarla a SQSF</p>
        <form action="<?php echo $_SERVER['REQUEST_URI'] ?>" method="post">
            <table>
                <thead>
                    <tr>
                        <th>Título</th>
                        <th>Votos</th>
                        <th>Fecha</th>
                        <th>Ver</th>
                        <th>Exportar</th>
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
                            <td>
                                <label for="id_<?php echo $consulta['id_consulta'] ?>" hidden>a</label>
                                <input id="id_<?php echo $consulta['id_consulta'] ?>" type='checkbox'
                                       title="inputforquery" name='to_export_ids[]'
                                       value="<?php echo $consulta['id_consulta'] ?>"><br>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
            <button type="submit">Exportar</button>
        </form>
        <?php
            } else {
                // En caso de no existir, mostrar un mensaje.
                echo "<p>¡Aún no has publicado nada! Vuelve aquí cuando tengas por lo menos una consulta.</p>";
            }
        ?>
    </section>
</main>
</body>
</html>