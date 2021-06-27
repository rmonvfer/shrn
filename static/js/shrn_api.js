/**
 * Encapsula la interacción con la API de la plataforma
 * esto es, métodos para gestionar comentarios, votos...
 */
class ShodaninAPI  {
    /**
     * Solicita la creación de un comentario al servidor
     * @param qid "id de la pregunta sobre la que se comenta"
     * @param comment "texto del comentario"
     */
    static create_comment(qid, comment) {
        if (comment.trim().length > 0) {
            $.ajax({
                type: "POST",
                url: '/api/base.php',
                contentType: "application/json",
                dataType: "json",
                headers : {
                    "X-Key": Cookie.get("x-key-id")
                },
                data: JSON.stringify({
                    "action": "new_comment",
                    "payload": {
                        "qid": qid,
                        "content": comment.trim()
                    }
                }),
                success: function(response) {
                    let res_comment = response.comment;
                    console.log(res_comment);
                    $(`
                    <div class="row qcard-comment" id="">
                        <div class="column-10 qcard-comment-bar">
                            <em class="fas fa-arrow-up"></em>
                            <span>${res_comment.valoracion_comentario}</span>
                        </div>
                        <div class="column qcard-comment-body">
                            <p>${comment.replace("\n", " ")}</p>
                            <div class="qcard-info">
                                <p>Por ${res_comment.nombre_usuario} el ${res_comment.p_timestamp}</p>
                            </div>
                        </div>
                    </div>
                `).insertAfter(".qcard-new-comment");
                }
            });

            $("#comment-textarea").val(""); // Vaciar
        }
    }

    /**
     * Incrementa en uno la puntuación de un comentario.
     * @param cid id del comentario a votar
     */
    static upvote_comment(cid) {
        $.ajax({
            type: "POST",
            url: '/api/base.php',
            contentType: "application/json",
            dataType: "json",
            headers : {
                "X-Key": Cookie.get("x-key-id")
            },
            data: JSON.stringify({
                "action": "upvote_comment",
                "payload": { "cid": cid }
            }),
            success: function(response) {
                $(`#cid_${cid}`).addClass("clicked").find(".c-valoracion").text(response.votes);
            }
        });
    }

    /**
     * Incrementa en uno la puntuación de una consulta
     * @param qid id de la consulta a votar
     */
    static upvote_query(qid) {
        $.ajax({
            type: "POST",
            url: '/api/base.php',
            contentType: "application/json",
            dataType: "json",
            headers : {
                "X-Key": Cookie.get("x-key-id")
            },
            data: JSON.stringify({
                "action": "upvote_query",
                "payload": { "qid": qid }
            }),
            success: response => {
                $(`#qid_${qid}`).addClass("clicked").find(".q-valoracion").text(response.votes);
            }
        });
    }

    /**
     * Decrementa en uno la puntuación de una consulta
     * @param qid id de la consulta a votar
     */
    static downvote_query(qid) {
        $.ajax({
            type: "POST",
            url: '/api/base.php',
            contentType: "application/json",
            dataType: "json",
            headers : {
                "X-Key": Cookie.get("x-key-id")
            },
            data: JSON.stringify({
                "action": "downvote_query",
                "payload": { "qid": qid }
            }),
            success: response => {
                $(`#qid_${qid}`).addClass("clicked").find(".q-valoracion").text(response.votes);
            }
        });
    }
}