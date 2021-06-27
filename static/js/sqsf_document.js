/**
 * Encapsula un documento SQSF
 */
class SQSFDocument {

    /**
     * Construye una instancia de SQSFDocument con los datos del documento SQSF (XML)
     * pasado por parámetro.
     * @param xml_string
     */
    constructor(xml_string) {
        this.xml_document = new DOMParser().parseFromString(xml_string, "text/xml");
        this.content = this.__parse();
    }

    /**
     * Procesa el documento XML y genera un objeto con sus datos.
     * @private
     */
    __parse() {
        let result = {info: {}, queries: []};

        // Extraer los elementos con los datos a procesar
        let _document_info = this.xml_document.getElementsByTagName("DocumentInfo")[0];
        let _queries = Array.from(this.xml_document.getElementsByTagName("Query"));

        // Información del documento
        result.info.author_id = _document_info.getAttribute("authorid");
        result.info.export_date = Date.parse(_document_info.getAttribute("export_ts"));

        // Recorrer cada consulta exportada
        _queries.forEach(query => {
            result.queries.push({
                db_id: query.getAttribute("qdbid"),
                author_id: query.getAttribute("qauthorid"),
                votes: parseInt(query.getAttribute("qvotes")),
                title: query.getElementsByTagName("QTitle")[0].textContent,
                content: query.getElementsByTagName("QContent")[0].textContent,
                description: query.getElementsByTagName("QDescription")[0].textContent,

                // Recorrer cada comentario
                comments: Array.from(query.getElementsByTagName("QComment")).map(comment => ({
                    post_date: Date.parse(comment.getAttribute("post_ts")),
                    author_id: comment.getAttribute("author_id"),
                    content: comment.getElementsByTagName("Content")[0].textContent,
                    rating: parseInt(comment.getElementsByTagName("Rating")[0].textContent)
                }))
            });
        });
        return result;
    }

    /**
     * Devuelve el contenido del documento como JSON
     */
    asJson() {
        return JSON.stringify(this.content, null, '\t');
    }

    /**
     * Genera un archivo que podrá descargarse al hacer clic en el elemento
     * pasado como parámetro
     * @param download_anchor anchor que lanza la descarga
     */
    initDownload(download_anchor) {
        let blob = new Blob([this.asJson()], { type: "application/json" });
        download_anchor.download = `${this.content.info.author_id}_${this.content.info.export_date}.json`;
        download_anchor.href = URL.createObjectURL(blob);
        download_anchor.dataset.downloadurl =
            ["application/json", download_anchor.download, download_anchor.href].join(':');
    }
}