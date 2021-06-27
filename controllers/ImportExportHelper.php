<?php

require_once "Database.php";
require_once "Helper.php";

/**
 * Class ImportExportHelper
 * Encapsula toda la funcionalidad de importado / exportado de consultas
 */
class ImportExportHelper
{
    /**
     * Produce un fichero XML válido con las consultas del usuario y sus
     * comentarios asociados
     * @param $id_array "array con los ids de las consultas a exportar"
     * @param $author_id "id del autor de la exportación"
     */
    public static function export_queries_by_id($id_array, $author_id)
    {
        // Creates an instance of the DOMImplementation class
        $imp = new DOMImplementation;

        // Creates a DOMDocumentType instance
        $dtd = $imp->createDocumentType('sqsf', '', '../static/xml_schemas/sqsf.dtd');

        $sqsf_document = $imp->createDocument("", "", $dtd);
        $sqsf_document->encoding = "UTF-8";
        $sqsf_document->formatOutput= true;

        $root = $sqsf_document->createElement("sqsf");
        $root->setAttribute("version", 1.0);
        $sqsf_document->appendChild($root);

        $document_info= $sqsf_document->createElement("DocumentInfo");
        $document_info->setAttribute("export_ts", date('Y-m-d h:i:s') );
        $document_info->setAttribute("authorid", $author_id );

        $queries= $sqsf_document->createElement("SharedQueries");
        $root->appendChild($document_info);
        $root->appendChild($queries);

        foreach ($id_array as $query_id) {
            $_query = Database::getInstance()->get_query_by_id($query_id);
            $_comments = Database::getInstance()->get_comments_by_query_id($query_id);

            $query = $sqsf_document->createElement("Query");
            $query->setAttribute("qdbid", $_query["id_consulta"]);
            $query->setAttribute("qauthorid", $_query["id_usuario"]);
            $query->setAttribute("qvotes", $_query["votos_consulta"]);
            $queries->appendChild($query);

            $title       = $sqsf_document->createElement("QTitle", $_query["titulo_consulta"]);
            $content     = $sqsf_document->createElement("QContent", $_query["contenido_consulta"]);
            $description = $sqsf_document->createElement("QDescription", $_query["descripcion_consulta"]);
            $comments    = $sqsf_document->createElement("QComments");

            // Recorrer los comentarios
            foreach ($_comments as $_comment) {
                $qcomment = $sqsf_document->createElement("QComment");
                $qcomment->setAttribute("post_ts", $_comment["p_timestamp"]);
                $qcomment->setAttribute("author_id", $_comment["id_usuario"]);

                $qcontent = $sqsf_document->createElement("Content", $_comment["contenido_comentario"]);
                $qrating  = $sqsf_document->createElement("Rating", $_comment["valoracion_comentario"]);

                // Añadir los elementos del comentario
                $qcomment->appendChild($qcontent);
                $qcomment->appendChild($qrating);

                // Añadir el comentario a la consulta
                $comments->appendChild($qcomment);
            }

            // Añadir los elementos a la consulta
            $query->appendChild($title);
            $query->appendChild($content);
            $query->appendChild($description);
            $query->appendChild($comments);
        }

        $sqsf_document->formatOutput = TRUE;

        // El nombre del archivo incluye el id del autor y un guid aleatorio para prevenir la fuerza bruta
        $filename = $author_id."_".Helper::guidv4().".xml";

        // Validar el documento generado.
        if ($sqsf_document->validate()) {
            // Forzar un cuadro de descarga.
            header('Content-Disposition: attachment; filename="'.$filename.'"');
            echo $sqsf_document->saveXML();
            exit(); // Evitar que se guarde también el HTML de la página.
        }
    }

    /**
     * Importa las consultas de un fichero de consultas
     * @param $content "contenido del fichero sqsf"
     * @param $uid "id del usuario logueado"
     */
    public static function import_queries_from_file_contents($content, $uid) {
        $_content = simplexml_import_dom($content);
        foreach ($_content->SharedQueries->children() as $_query) {
            $_qid = Helper::guidv4();
            Database::getInstance()
                ->insert_query_with_qid($_qid, $uid, $_query->QTitle, $_query->QDescription, $_query->QContent);
        }
    }
}