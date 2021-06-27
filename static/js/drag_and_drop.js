$(function () {
    $("#upload-button").click(function () {
        let formData = new FormData();
        formData.append('file_to_import', $('#file')[0].files[0]);

        $.ajax({
            type: 'POST',
            url: '/user/import.php',
            contentType: false,
            processData: false,
            withCredentials: true,
            data: formData
        });
    });

    $("#file").change(async () => {
        let loaded_file = document.getElementById("file").files[0];
        let loaded_file_content = await loaded_file.text(); // File API
        let sqsf_parser = new SQSFDocument(loaded_file_content);
        let parsed_file_content = sqsf_parser.content;
        let total_comments = parsed_file_content.queries
            .reduce((accumulator, currentComment) => (accumulator + currentComment.comments.length), 0)

        console.log(parsed_file_content);

        // Insertar los datos de nuevo en el DOM
        $("#file-stats")
            .append(`
                <p><strong>Nombre: </strong>${loaded_file.name}</p>
                <p><strong>Tamaño: </strong>${loaded_file.size / 1000}Kb</p>
                <p><strong>Timestamp: </strong>${parsed_file_content.info.export_date}</p>
                <p><strong>Nº Consultas: </strong>${parsed_file_content.queries.length}</p>
                <p><strong>Nº Comentarios: </strong>${total_comments}</p>
                <a class="button" type="application/json" id="json-download">Descargar JSON</a>
            `)
            .css("display", "block");

        sqsf_parser.initDownload(document.getElementById("json-download"));
    });
})