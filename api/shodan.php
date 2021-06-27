<?php
// No es posible llamar a la API REST de shodan desde el navegador
// porque no envía las cabeceras adecuadas (CORS), esto es una posible
// solución (enviarlas desde el servidor como middleware)

$key = $_GET["key"];
$query = $_GET["query"];

$cURL = curl_init();
$curl_query = 'https://api.shodan.io/shodan/host/search?key=' . $key . '&query=' . $query;

curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);
curl_setopt($cURL, CURLOPT_URL, $curl_query);

$response = json_decode(curl_exec($cURL), true);
curl_close($cURL);

if (count($response["matches"]) == 0) {
	http_response_code(500);
	echo json_encode(array("count" => 0, "data" => "Error"));
	
} else {
	http_response_code(200); // Internal Server Error
	echo json_encode(
		array(
			"count" => count($response["matches"]),
			"data" => array_slice($response["matches"], 0, 5)
		)
	);
}

