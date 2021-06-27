<?php
// Envía comentarios a una consulta ya existente.
// Solo podrá accederse mediante POST

require_once "../controllers/Database.php";
require_once "../controllers/Helper.php";

$_database = Database::getInstance();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $user_key = $_SERVER['HTTP_X_KEY'] ?? "";

    // Recibir los datos como JSON
    // Fuente: https://stackoverflow.com/questions/18866571/receive-json-post-with-php
    $_POST = json_decode(file_get_contents('php://input'), true);

    // Cabeceras genéricas para responder con JSON
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Methods: POST");
    header("Access-Control-Max-Age: 3600");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Cookie");

    $_payload = $_POST["payload"] ?? "";
    $_action = (isset($_POST["action"])) ? $_POST["action"] : "default";
    $_response = array();

    // Acciones no autenticadas
    switch (strtolower($_action)) {
        case "get_top_queries":
            http_response_code(200);
            echo json_encode(array("message" => "OK", "queries" => $_database->get_top_queries()));
            exit(); // Parar la petición.
    }

    if (empty(trim($user_key))) {
        http_response_code(403); // No autorizado
        echo json_encode(array("message" => "No autorizado", "reason" => "¡Envía la cookie de sesión!"));
    }

    $_user = $_database->get_user_by_id($user_key);

    // Acciones autenticadas
    switch (strtolower($_action)) { // Asegurar que la action está siempre en minúscula

        // Nuevo comentario
        case "new_comment":
            // Generar un GUID único como identificador del comentario a insertar.
            // Luego se usará para obtener los datos del comentario.
            $_guid = Helper::guidv4();

            $_valid_params = isset($_user["id_usuario"], $_payload["qid"], $_payload["content"]);
            if ($_valid_params && $_database->insert_comment_on_query_by_user(
                    $_guid, $_user["id_usuario"], $_payload["qid"], $_payload["content"])) {

                // Añadir el nombre del usuario a la respuesta porque es necesario en la vista.
                $_comment_data = $_database->get_comment_by_id($_guid);
                $_comment_data["nombre_usuario"] =
                    $_database->get_user_by_id($_comment_data["id_usuario"])["nombre_usuario"];

                // Devolver los datos del comentario
                $_response = array("message" => "Insertado. ¡OK!", "comment" => $_comment_data);
                http_response_code(200);

            } else {
                $_response = array("message" => "Error. ¡KO!", "reason" => "Debes proporcionar uid, qid y content");
                http_response_code(500);
            }
            break;

        // Incrementar la puntuación de un comentario
        case "upvote_comment":
            if (isset($_payload["cid"]) && $_database->upvote_comment_by_id($_payload["cid"])) {
                $votos = $_database->get_comment_by_id($_payload["cid"])["valoracion_comentario"];
                $_response = array("message" => "Actualizado. ¡OK!", "votes" => $votos);
                http_response_code(200);

            } else {
                $_response = array("message" => "Error. ¡KO!", "reason" => "Debes proporcionar cid.");
                http_response_code(500);
            }
            break;

        // Incrementar la puntuación de una consulta
        case "upvote_query":
            if (isset($_payload["qid"]) && $_database->upvote_query_by_id($_payload["qid"])) {
                $votos = $_database->get_query_by_id($_payload["qid"])["votos_consulta"];
                $_response = array("message" => "Actualizado. ¡OK!", "votes" => $votos);
                http_response_code(200);

            } else {
                $_response = array("message" => "Error. ¡KO!", "reason" => "Debes proporcionar qid.");
                http_response_code(500);
            }
            break;

        // Decrementar la puntuación de una consulta
        case "downvote_query":
            if (isset($_payload["qid"]) && $_database->downvote_query_by_id($_payload["qid"])) {
                $votos = $_database->get_query_by_id($_payload["qid"])["votos_consulta"];
                $_response = array("message" => "Actualizado. ¡OK!", "votes" => $votos);
                http_response_code(200);

            } else {
                $_response = array("message" => "Error. ¡KO!", "reason" => "Debes proporcionar qid.");
                http_response_code(500);
            }
            break;

        // Predeterminada
        default:
            $_response = array("message" => "Recibida action: $_action. ¡OK!");
            http_response_code(200);
            break;
    }
    echo json_encode($_response);

} else {
    $method = $_SERVER['REQUEST_METHOD'];
    http_response_code(405); // Método no válido
    echo json_encode(
        array("message" => "Método no válido", "reason" => "Solo se permiten peticiones POST y tu has usado $method")
    );
}