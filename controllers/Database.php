<?php

require_once "Queries.php";
require_once "../configuration/Configuration.php";

/**
 * Encapsula una instancia de mysqli de forma que solo haya un punto
 * de acceso a la capa de datos.
 */
class Database {
    private static ?Database $instance = null;
    private mysqli $db;

    private function __construct() {
        $this->db = new mysqli(
            Configuration::$db_host,     // Database host
            Configuration::$db_username, // Database username
            Configuration::$db_password, // Database password
            Configuration::$db_name);    // Database name
    }

    public static function getInstance(): ?Database
    {
        if (self::$instance == null)
        {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    /**
     * Instancia de una conexión al servidor mysql
     * @return mysqli
     */
    public function get_db(): mysqli
    {
        return $this->db;
    }

    /**
     * @param $username "Nombre del usuario"
     * @param $password "Contraseña del usuario"
     * @return bool "true si se crea correctamente, false en caso contrario"
     */
    public function user_register($username, $password): bool
    {
        $_inserted = false;
        if ($stmt = $this->db->prepare( Queries::$QUERY_INSERT_USER )) {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt->bind_param('ss', $username, $hashed_password);

            $_inserted = $stmt->execute();
            $stmt->close();
        }
        return $_inserted;
    }

    /**
     * Comprueba las credenciales de un usuario.
     * @param $username "Nombre de usuario comprobar"
     * @param $password "Contraseña del usuario"
     * @return bool "true si las credenciales son correctas, false en caso contrario"
     */
    public function user_login($username, $password): bool
    {
        $_result = false;
        if ($stmt = $this->db->prepare( Queries::$QUERY_FIND_USER_BY_NAME )) {
            $stmt->bind_param('s', $username);

            if ($stmt->execute()) {
                $result = $stmt->get_result()->fetch_assoc();
                $_result = isset($result["id_usuario"]) && password_verify($password, $result["clave_usuario"]);
            }
            $stmt->close();
        }
        return $_result;
    }

    /**
     * Devuelve los datos de un usuario identificado por su nombre
     * @param $username "Nombre del usuario a buscar"
     * @return array "Datos del usuario o stub en caso contrario"
     */
    public function get_user_by_username($username): array
    {
        $_user = array();
        if ($stmt = $this->db->prepare( Queries::$QUERY_FIND_USER_BY_NAME )) {
            $stmt->bind_param('s', $username);

            if ($stmt->execute()) {
                $result = $stmt->get_result()->fetch_assoc();
                if (isset($result["nombre_usuario"])) {
                    $_user = $result;
                } else {
                    $_user["nombre_usuario"] = "Usuario eliminado";
                    $_user["id_usuario"] = -1;
                }
            }
            $stmt->close();
        }
        return $_user;
    }

    /**
     * Busca a un usuario por su id
     * @param $uid "id del usuario a buscar"
     * @return array "array con los datos del usuario"
     */
    public function get_user_by_id($uid): array
    {
        $_user = array();
        $stmt = $this->db->prepare( Queries::$QUERY_FIND_USER_BY_ID );

        if ( $stmt ) {
            $stmt->bind_param('s', $uid);
            if ($stmt->execute()) {
                $result = $stmt->get_result()->fetch_assoc();
                if (isset($result["nombre_usuario"])) {
                    $_user = $result;
                } else {
                    $_user["nombre_usuario"] = "Usuario eliminado";
                    $_user["id_usuario"] = -1;
                }
            }
            $stmt->close();
        }
        return $_user;
    }

    /**
     * @param $uid "Id del usuario autor de la consulta"
     * @param $title "Título de la consulta a publicar"
     * @param $description "Descripción de la consulta"
     * @param $query "Consulta en sí"
     * @return bool "true si se crea correctamente, false en caso contrario"
     */
    public function insert_query($uid, $title, $description, $query): bool
    {
        $_result = false;
        if ($stmt = $this->db->prepare( Queries::$QUERY_INSERT_QUERY )) {
            $stmt->bind_param('ssss', $uid, $title, $description, $query);

            $_result = $stmt->execute();
            echo $stmt->error;
            $stmt->close();
        }
        return $_result;
    }


    public function insert_query_with_qid($qid, $uid, $title, $description, $query): bool
    {
        $_result = false;
        if (!empty(trim($title))) {
            if ($stmt = $this->db->prepare( Queries::$QUERY_INSERT_QUERY_WITH_QID )) {
                $stmt->bind_param('sssss', $qid, $uid, $title, $description, $query);

                $_result = $stmt->execute();
                echo $stmt->error;
                $stmt->close();
            }
        }
        return $_result;
    }

    /**
     * Busca una consulta identificada por su id
     * @param $qid "id de la consulta
     * @return array "datos de la consulta"
     */
    public function get_query_by_id($qid): array
    {
        $_query = array();
        $stmt = $this->db->prepare( Queries::$QUERY_FIND_QUERY_BY_ID );

        if ( $stmt ) {
            $stmt->bind_param('s', $qid);
            if ($stmt->execute()) {
                $result = $stmt->get_result()->fetch_assoc();
                if (isset($result["id_consulta"])) {
                    $_query = $result;
                } else {
                    $_query["titulo_consulta"] = "Consulta eliminada";
                    $_query["id_consulta"] = -1;
                }
            }
            $stmt->close();
        }
        return $_query;
    }


    /**
     * Obtiene todas las consultas publicadas ordenadas por fecha
     */
    public function get_all_queries_by_date(): array
    {
        $_result = null;
        if ($result = $this->db->query( Queries::$QUERY_FIND_ALL_QUERIES_SORTED_BY_DATE )) {
            $_result = $result->fetch_all(MYSQLI_ASSOC);
        }
        return $_result;
    }

    /**
     * Obtiene las consultas publicadas por un usuario identificado por su id
     * @param $uid "id del usuario"
     * @return array "array de arrays, cada uno de ellos con una de las consultas del usuario"
     */
    public function get_user_queries_by_user_id($uid): array
    {
        $_result = array();
        if ($stmt = $this->db->prepare(Queries::$QUERY_FIND_QUERY_BY_USER_ID)) {
            $stmt->bind_param('s', $uid);

            if ($stmt->execute()) {
                $_result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
            }
            $stmt->close();
        }
        return $_result;
    }

    /**
     * Obtiene un comentario identificado por su id
     * @param $cid "Id del comentario"
     * @return array "Datos del comentario"
     */
    public function get_comment_by_id($cid): array
    {
        $_comment = array();
        $stmt = $this->db->prepare( Queries::$QUERY_FIND_COMMENT_BY_ID );

        if ( $stmt ) {
            $stmt->bind_param('s', $cid);
            if ($stmt->execute()) {
                $result = $stmt->get_result()->fetch_assoc();
                if (isset($result["id_comentario"])) {
                    $_comment = $result;
                } else {
                    $_comment["titulo_consulta"] = "Consulta eliminada";
                    $_comment["id_consulta"] = -1;
                }
            }
            $stmt->close();
        }
        return $_comment;
    }

    /**
     * Obtiene todos los comentarios de una consulta ordenados por fecha
     * @param $qid "Id de la consulta"
     * @return array "Comentarios asociados a esa consulta"
     */
    public function get_comments_by_query_id($qid): array
    {
        $_result = array();
        if ($stmt = $this->db->prepare( Queries::$QUERY_FIND_COMMENTS_BY_QUERY_ID )) {
            $stmt->bind_param('s', $qid);

            if ($stmt->execute()) {
                $_result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
            }
            $stmt->close();
        }
        return $_result;
    }

    /**
     * Inserta un comentario emitido por un usuario en una consulta.
     * @param $uid "Id del usuario que comenta"
     * @param $qid "Id de la consulta que recibe el comentario"
     * @param $cid "Id del comentario a insertar (GUIDv4)"
     * @param $comment_text "Contenido del comentario"
     * @return bool "true si el comentario se ha insertado, false en caso contrario"
     */
    public function insert_comment_on_query_by_user($cid, $uid, $qid, $comment_text): bool
    {
        $_result = false;

        // Evitar XSS (1 y 2)
        $comment_text  = htmlspecialchars($comment_text);

        if ($stmt = $this->db->prepare( Queries::$QUERY_INSERT_COMMENT_ON_QUERY_ID_BY_USER_ID )) {
            $stmt->bind_param('ssss', $cid, $uid, $qid, $comment_text);
            $_result = $stmt->execute();
            $stmt->close();
        }
        return $_result;
    }

    /**
     * Devuelve las consultas más votadas
     * @return array "consultas más votadas"
     */
    public function get_top_queries(): array
    {
        $_result = array();
        if($stmt= $this->db->query(Queries::$QUERY_FIND_TOP_QUERIES)) {
            $_result = $stmt->fetch_all(MYSQLI_ASSOC);
        }
        $stmt->close();
        return $_result;
    }

    /**
     * Incrementa la puntuación de un comentario en una unidad
     * @param $cid "Id del comentario a modificar
     * @return bool "true si se modifica, false en caso contrario"
     */
    public function upvote_comment_by_id($cid): bool
    {
        $_result = false;

        if ($stmt = $this->db->prepare( Queries::$QUERY_UPVOTE_COMMENT_BY_ID )) {
            $stmt->bind_param('s', $cid);
            $_result = $stmt->execute();
            $stmt->close();
        }
        return $_result;
    }

    /**
     * Incrementa la puntuación de una consulta en una unidad
     * @param $qid "Id de la consulta a modificar
     * @return bool "true si se modifica, false en caso contrario"
     */
    public function upvote_query_by_id($qid): bool
    {
        $_result = false;

        if ($stmt = $this->db->prepare( Queries::$QUERY_UPVOTE_QUERY_BY_ID )) {
            $stmt->bind_param('s', $qid);
            $_result = $stmt->execute();
            $stmt->close();
        }
        return $_result;
    }

    /**
     * Decrementa la puntuación de una consulta en una unidad
     * @param $qid "Id de la consulta a modificar
     * @return bool "true si se modifica, false en caso contrario"
     */
    public function downvote_query_by_id($qid): bool
    {
        $_result = false;

        if ($stmt = $this->db->prepare( Queries::$QUERY_DOWNVOTE_QUERY_BY_ID )) {
            $stmt->bind_param('s', $qid);
            $_result = $stmt->execute();
            $stmt->close();
        }
        return $_result;
    }

    /**
     * Cuenta el total de comentarios en una consulta.
     * @param $qid "Id de la consulta a consultar"
     * @return int "numero de comentarios"
     */
    public function count_total_query_comments_by_id($qid): int
    {
        $_result = 0;

        if ($stmt = $this->db->prepare( Queries::$QUERY_COUNT_COMMENTS_PER_QUERY_BY_ID )) {
            $stmt->bind_param('s', $qid);
            $stmt->execute();
            $_result = $stmt->get_result()->fetch_assoc()["total"];
            $stmt->close();
        }
        return $_result;
    }
}


