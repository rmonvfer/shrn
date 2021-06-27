<?php
// Muestra el detalle de una única consulta
// - Título
// - Contenido
// - Comentarios (estilo Quora)
// Si el usuario es el autor de la consulta dispondrá de dos botones adicionales:
// - Borrar
// - Modificar (?)

session_start();

require_once "../controllers/Queries.php";
require_once "../controllers/Database.php";

if (!isset($_SESSION["id"])) {
    header( "location: /user/login.php" );
}

$consulta    = Database::getInstance()->get_query_by_id($_GET["qid"]);
$comentarios = Database::getInstance()->get_comments_by_query_id($_GET["qid"]);
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
            <div class="column qcard" id="<?php echo "qid_".$consulta['id_consulta'] ?>">
                <div class="row">
                    <div class="column-10 qcard-bar border-right">
                        <em class="fas fa-arrow-up clickable q-upvote"></em>
                        <span class="q-valoracion">
                            <?php echo $consulta["votos_consulta"] ?>
                        </span>
                        <em class="fas fa-arrow-down clickable q-downvote"></em>
                    </div>

                    <div class="column qcard-body">
                        <h2><?php echo $consulta["titulo_consulta"] ?></h2>
                        <p class="qcard-info">
                            Publicada por
                            <span class="qcard-author">
                                <?php echo Database::getInstance()
                                    ->get_user_by_id($consulta['id_usuario'])["nombre_usuario"]; ?>
                            </span>
                            el <span><?php echo $consulta['g_timestamp'] ?></span>
                        </p>
                        <p class="qcard-description"><?php echo $consulta['descripcion_consulta'] ?></p>
                        <div class="qcard-code">
                            <span class="qcard-details-button">Añadir filtro según mi ubicación</span>
                            <pre><?php echo $consulta['contenido_consulta'] ?></pre>
                            <textarea class="result-textarea" title="code_textarea" readonly hidden></textarea>
                            <div class="button-container">
                                <span id="result-counter"></span>
                                <button type="button" id="code-button" class="hide">
                                    <em class="fas fa-play"></em>
                                    Ejecutar
                                </button>
                            </div>
                        </div>

                        <div class="qcard-comments-toggle hide">
                            <em class="fas fa-eye"></em>
                            <span>Ver comentarios</span>
                        </div>

                        <div class="qcard-comments" hidden>
                            <div class="row border-top qcard-new-comment">
                                <div class="column qcard-comment-body">
                                    <label for="comment-textarea">Nuevo comentario</label>
                                    <textarea id="comment-textarea"></textarea>
                                    <div class="qcard-info">
                                        <button type="button">Comentar</button>
                                    </div>
                                </div>
                            </div>
                            <?php foreach($comentarios as $comentario) { ?>
                                <div class="row qcard-comment" id="cid_<?php echo $comentario['id_comentario'] ?>">
                                    <div class="column-10 qcard-comment-bar">
                                        <em class="fas fa-arrow-up clickable c-upvote"></em>
                                        <span class="c-valoracion">
                                            <?php echo $comentario['valoracion_comentario'] ?>
                                        </span>
                                    </div>

                                    <div class="column qcard-comment-body">
                                        <p><?php echo $comentario['contenido_comentario'] ?></p>
                                        <div class="qcard-info">
                                            <p>
                                                Por
                                                <?php echo Database::getInstance()
                                                    ->get_user_by_id($comentario["id_usuario"])["nombre_usuario"] ?>
                                                el <?php echo $comentario['p_timestamp'] ?>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
		</section>
	</main>
</body>
</html>