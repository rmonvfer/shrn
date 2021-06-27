<?php

session_start();

require_once '../controllers/Queries.php';
require_once "../controllers/Database.php";
require_once "../controllers/ImportExportHelper.php";
require_once "../controllers/ErrorManager.php";

$error = "";

// Si el usuario no está autenticado...
if (!isset($_SESSION["id"])) {
    header('location: /user/login.php'); // Redirigirle a /user/login
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $xmlDoc = new DOMDocument();
    $xmlDoc->load($_FILES["file_to_import"]["tmp_name"]);
    $valid = $xmlDoc->schemaValidate("../static/xml_schemas/sqsf.xsd");

    if ($valid) {
        try {
			ImportExportHelper::import_queries_from_file_contents($xmlDoc, $_SESSION["id"]);
			header("location: /query/published.php");
			
        } catch(Exception $exception) {
            $error = "Error al importar el documento XML. Posiblemente malformado.";
        }
    } else {
		$error = "Error al importar el documento XML. Documento NO VÁLIDO";
    }
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <title>Mis consultas</title>
    <?php include_once "../fragments/head_fragment.php" ?>
</head>
<body>
    <?php include_once "../fragments/nav_fragment.php" ?>
    <main>
        <section class="container">
            <h1>Importar consultas</h1>
            <p>
                Desde aquí podrás cargar un archivo SQSF <em>(Shareable Shodan Query Format)</em> que hayas exportado
                previamente o que hayas obtenido de otro servicio. También podrás descargar el contenido en formato JSON
                para manipularlo con otras herramientas.
            </p>
            <?php ErrorManager::display_field_error($error); ?>
        </section>
        <section class="container">
            <div class="row qcard">
                <div class="column">
                    <form id="file-upload"
                          action="<?php echo $_SERVER['REQUEST_URI'] ?>" method="post" enctype="multipart/form-data">
                        <div class="box-input">
                            <label for="file">
                                <strong>Haz click para seleccionar un archivo</strong>
                                <input type="file" name="file_to_import" id="file" />
                            </label>
                        </div>
                    </form>
                </div>
                <div class="column qcard" id="file-stats">
                    <p>Detalles del archivo seleccionado</p>
                </div>
            </div>
            <div class="row">
                <div class="column">
                    <button id="upload-button" type="button">Importar</button>
                </div>
            </div>
        </section>
    </main>
</body>
</html>