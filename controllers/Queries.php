<?php

class Queries {
    public static string $QUERY_FIND_USER_BY_NAME
        = 'SELECT id_usuario, nombre_usuario, clave_usuario, shodan_key FROM USUARIO WHERE nombre_usuario = ?';

    public static string $QUERY_FIND_USER_BY_ID
        = 'SELECT * FROM USUARIO WHERE id_usuario = ?';

    public static string $QUERY_INSERT_USER
        = 'INSERT INTO USUARIO (nombre_usuario, clave_usuario) VALUES (?,?)';

    public static string $QUERY_INSERT_QUERY_WITH_QID
        = 'INSERT INTO CONSULTA (id_consulta, id_usuario, titulo_consulta, descripcion_consulta, contenido_consulta) VALUES (?,?,?,?,?)';

    public static string $QUERY_INSERT_QUERY
        = 'INSERT INTO CONSULTA (id_usuario, titulo_consulta, descripcion_consulta, contenido_consulta) VALUES (?,?,?,?)';

    public static string $QUERY_FIND_QUERY_BY_USER_ID
        = 'SELECT * FROM CONSULTA WHERE id_usuario = ?';

    public static string $QUERY_FIND_TOP_QUERIES
        = 'SELECT * FROM CONSULTA WHERE votos_consulta > 10 ORDER BY votos_consulta DESC';

    public static string $QUERY_FIND_QUERY_BY_ID
        = 'SELECT * FROM CONSULTA WHERE id_consulta = ?';

    public static string $QUERY_FIND_ALL_QUERIES_SORTED_BY_DATE
        = 'SELECT * FROM CONSULTA ORDER BY g_timestamp DESC';

    public static string $QUERY_INSERT_COMMENT_ON_QUERY_ID_BY_USER_ID
        = 'INSERT INTO COMENTARIO (id_comentario, id_usuario, id_consulta, valoracion_comentario, contenido_comentario) 
           VALUES (?,?,?,0,?)';

    public static string $QUERY_FIND_COMMENTS_BY_QUERY_ID
        = 'SELECT * FROM COMENTARIO WHERE id_consulta = ? ORDER BY p_timestamp DESC';

    public static string $QUERY_FIND_COMMENT_BY_ID
        = 'SELECT * FROM COMENTARIO WHERE id_comentario = ?';

    public static string $QUERY_UPVOTE_COMMENT_BY_ID
        = 'UPDATE COMENTARIO SET valoracion_comentario = valoracion_comentario +1 WHERE id_comentario = ?';

    public static string $QUERY_UPVOTE_QUERY_BY_ID
        = 'UPDATE CONSULTA SET votos_consulta = CONSULTA.votos_consulta +1 WHERE id_consulta = ?';

    public static string $QUERY_DOWNVOTE_QUERY_BY_ID
        = 'UPDATE CONSULTA SET votos_consulta = CONSULTA.votos_consulta -1 WHERE id_consulta = ?';

    public static string $QUERY_COUNT_COMMENTS_PER_QUERY_BY_ID
        = 'SELECT COUNT(*) AS total FROM COMENTARIO WHERE id_consulta = ?';
}