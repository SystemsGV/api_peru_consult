<?php
// Datos
$token = 'apis-token-9070.xhw89HY4G6Rv5ZPufoGLsmPkt8U7DE16';
$ruc = $_POST['ruc']; // Asegúrate de que estás recibiendo el RUC desde un formulario POST

// Iniciar llamada a API
$curl = curl_init();

// Buscar RUC
curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://api.apis.net.pe/v2/sunat/ruc?numero=' . $ruc,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_SSL_VERIFYPEER => 0,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 2,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_CUSTOMREQUEST => 'GET',
  CURLOPT_HTTPHEADER => array(
    'Referer: http://apis.net.pe/api-ruc',
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
$empresa = json_decode($response);

// Verificar si la decodificación fue exitosa y si se encontraron los datos esperados
if ($empresa === null) {
  echo json_encode(array("error" => "Datos incompletos o inválidos"));
  exit;
}

// Manejar posibles errores de la API
if (isset($empresa->error)) {
  echo json_encode(array("error" => $empresa->error->message));
  exit;
}

$razonSocial = $empresa->razonSocial ?? '';
$estado = $empresa->estado ?? '';

// Devolver los datos en formato JSON
echo json_encode(array(
  "success" => true,
  "razon_social" => $razonSocial,
  "estado" => $estado
));
