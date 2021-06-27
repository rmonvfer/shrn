<?php
session_start();

$ALL_QUERIES = array();

require_once "../controllers/Queries.php";
require_once "../controllers/Database.php";

if (!isset($_SESSION["id"])) {
    header( "location: /user/login.php" );
}

$ALL_QUERIES = Database::getInstance()->get_all_queries_by_date();
?>

<!DOCTYPE html>
<html lang="es">
<head>
	<title>Página principal</title>
    <?php include_once "../fragments/head_fragment.php" ?>
</head>
<body>
    <?php require_once "../fragments/nav_fragment.php" ?>
	<main>
		<section class="container">
            <h1>Últimas consultas de la comunidad</h1>

            <?php
            if (count($ALL_QUERIES) >= 1) {
                foreach ($ALL_QUERIES as $consulta) { ?>
                    <div class="row qcard-row">
                        <div class="column qcard" id="qid_<?php echo $consulta["id_consulta"] ?>">
                            <div class="row">
                                <div class="column-10 qcard-bar">
                                    <span>
                                        <?php echo Database::getInstance()
                                                ->count_total_query_comments_by_id($consulta["id_consulta"]); ?>
                                    </span>
                                    <em class="fas fa-comment"></em>
                                </div>
                                <div class="column qcard-body-nopadding">
                                    <p>
                                        <a href="/query/detail.php?qid=<?php echo $consulta['id_consulta'] ?>">
                                            <?php echo $consulta['titulo_consulta'] ?>
                                        </a>
                                    </p>
                                    <p class="qcard-info">
                                        Publicada por <span class="qcard-author">
                                        <?php
                                            echo Database::getInstance()
                                                ->get_user_by_id($consulta['id_usuario'])["nombre_usuario"];
                                        ?>
                                        </span>
                                        el <span><?php echo $consulta['g_timestamp'] ?></span>
                                    </p>
                                    <p class="qcard-description"><?php echo $consulta['descripcion_consulta'] ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
            <?php
                }
            } else {
                echo "<p>¡Vaya, no hemos podido obtener las consultas! Inténtalo de nuevo más tarde.</p>";
            }
            ?>
		</section>
	</main>
</body>
</html>