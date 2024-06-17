<?php
// Datos
$token = 'apis-token-9070.xhw89HY4G6Rv5ZPufoGLsmPkt8U7DE16';
$dni = $_POST['dni']; // Asegúrate de que estás recibiendo el DNI desde un formulario POST

// Iniciar llamada a API
$curl = curl_init();

// Buscar DNI
curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://api.apis.net.pe/v2/reniec/dni?numero=' . $dni,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_SSL_VERIFYPEER => 0,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 2,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_CUSTOMREQUEST => 'GET',
  CURLOPT_HTTPHEADER => array(
    'Referer: https://apis.net.pe/consulta-dni-api',
    'Authorization: Bearer ' . $token
  ),
));

$response = curl_exec($curl);
curl_close($curl);

// Verificar si la respuesta es válida
if ($response === false) {
  echo json_encode(array("error" => "Error al realizar la solicitud a la API"));
  exit;
}

// Decodificar la respuesta JSON
$persona = json_decode($response);

// Verificar si la decodificación fue exitosa y si se encontraron los datos esperados
if ($persona === null) {
  echo json_encode(array("error" => "Datos incompletos o inválidos"));
  exit;
}

// Manejar posibles errores de la API
if (isset($persona->error)) {
  echo json_encode(array("error" => $persona->error->message));
  exit;
}

$nombres = $persona->nombres ?? '';
$apellidoPaterno = $persona->apellidoPaterno ?? '';
$apellidoMaterno = $persona->apellidoMaterno ?? '';
$nombreCompleto = $nombres . ' ' . $apellidoPaterno . ' ' . $apellidoMaterno;

// Devolver los datos en formato JSON
echo json_encode(array(
  "success" => true,
  "nombre_completo" => $nombreCompleto
));
