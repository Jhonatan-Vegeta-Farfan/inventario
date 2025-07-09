<?php
$ruta = explode("/", $_GET['views']);
if (!isset($ruta[1]) || $ruta[1] == "") {
    header("Location: " . BASE_URL . "movimientos");
    exit; // Asegúrate de salir después de redirigir
}

$curl = curl_init(); // Inicia la sesión cURL
curl_setopt_array($curl, array(
    CURLOPT_URL => BASE_URL_SERVER . "src/control/Movimiento.php?tipo=buscar_movimiento_id&sesion=" . $_SESSION['sesion_id'] . "&token=" . $_SESSION['sesion_token'] . "&data=" . $ruta[1], // URL a la que se conecta
    CURLOPT_RETURNTRANSFER => true, // Devuelve el resultado como una cadena del tipo curl_exec
    CURLOPT_FOLLOWLOCATION => true, // Sigue el encabezado que le envíe el servidor
    CURLOPT_ENCODING => "", // Permite decodificar la respuesta
    CURLOPT_MAXREDIRS => 10, // Máximo de encabezados a seguir
    CURLOPT_TIMEOUT => 30, // Tiempo máximo para ejecutar
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1, // Usa la versión declarada
    CURLOPT_CUSTOMREQUEST => "GET", // Tipo de petición
    CURLOPT_HTTPHEADER => array(
        "x-rapidapi-host: " . BASE_URL_SERVER,
        "x-rapidapi-key: XXXX"
    ), // Configura las cabeceras enviadas al servicio
));

$response = curl_exec($curl); // Respuesta generada
$err = curl_error($curl); // Muestra errores en caso de existir

curl_close($curl); // Termina la sesión 

if ($err) {
    echo "cURL Error #:" . $err; // Mostramos el error
    exit; // Asegúrate de salir si hay un error
} else {
    $respuesta = json_decode($response); // En caso de funcionar correctamente

    $contenido_pdf = '
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <title>Papeleta de Rotación de Bienes</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                margin: 40px;
            }
            h2 {
                text-align: center;
                text-transform: uppercase;
            }
            .info {
                margin-top: 30px;
                margin-bottom: 20px;
            }
            .info p {
                margin: 8px 0;
            }
            .bold {
                font-weight: bold;
            }
            table {
                width: 100%;
                border-collapse: collapse;
                margin-top: 15px;
            }
            th, td {
                border: 1px solid #000;
                padding: 6px;
                text-align: center;
                font-size: 14px;
            }
            .signature {
                margin-top: 80px;
                display: flex;
                justify-content: space-between;
            }
            .signature div {
                text-align: center;
                width: 45%;
            }
            .date {
                text-align: right;
                margin-top: 20px;
                font-size: 14px;
            }
        </style>
    </head>
    <body>
        <h2>PAPELETA DE ROTACIÓN DE BIENES</h2>
        <div class="info">
            <p><span class="bold">ENTIDAD</span>: DIRECCIÓN REGIONAL DE EDUCACIÓN - AYACUCHO</p>
            <p><span class="bold">ÁREA</span>: OFICINA DE ADMINISTRACIÓN</p>
            <p><span class="bold">ORIGEN</span>: ' . $respuesta->amb_origen->codigo . '_' . $respuesta->amb_origen->detalle . '</p>
            <p><span class="bold">DESTINO</span>: ' . $respuesta->amb_destino->codigo . '_' . $respuesta->amb_destino->detalle . '</p>
            <p><span class="bold">MOTIVO (*)</span>: ' . $respuesta->movimiento->descripcion . '</p>
        </div>
        <table>
            <thead>
                <tr>
                    <th>ITEM</th>
                    <th>CÓDIGO PATRIMONIAL</th>
                    <th>NOMBRE DEL BIEN</th>
                    <th>MARCA</th>
                    <th>COLOR</th>
                    <th>MODELO</th>
                    <th>ESTADO</th>
                </tr>
            </thead>
            <tbody>';

    if (empty($respuesta->detalle)) {
        $contenido_pdf .= '<tr><td colspan="7">No se encontraron bienes registrados para este movimiento.</td></tr>';
    } else {
        $contador = 1;
        foreach ($respuesta->detalle as $bien) {
            $contenido_pdf .= "<tr>";
            $contenido_pdf .= "<td>" . $contador . "</td>";
            $contenido_pdf .= "<td>" . $bien->cod_patrimonial . "</td>";
            $contenido_pdf .= "<td>" . $bien->denominacion . "</td>";
            $contenido_pdf .= "<td>" . $bien->marca . "</td>";
            $contenido_pdf .= "<td>" . $bien->color . "</td>";
            $contenido_pdf .= "<td>" . $bien->modelo . "</td>";
            $contenido_pdf .= "<td>" . $bien->estado_conservacion . "</td>";
            $contenido_pdf .= "</tr>";
            $contador++;
        }
    }

    $contenido_pdf .= '
            </tbody>
        </table>';

    // Asegura que se use la zona horaria correcta en todo el script
    date_default_timezone_set("America/Lima");

    // Crear el formateador con idioma español y zona horaria Lima
    $fechaMovimiento = new IntlDateFormatter(
        "es_ES",
        IntlDateFormatter::LONG,
        IntlDateFormatter::NONE,
        "America/Lima",
        IntlDateFormatter::GREGORIAN,
        "d 'de' MMMM 'del\' y"
    );

    // Crear el objeto DateTime con zona horaria Lima
    $fechaOriginal = new DateTime($respuesta->movimiento->fecha_registro, new DateTimeZone("America/Lima"));
    $fechaFormateada = $fechaMovimiento->format($fechaOriginal);

    $contenido_pdf .= '
        <div class="date">
            Ayacucho, ' . $fechaFormateada . '
        </div>
        <div class="signature">
            <div>
                <p>------------------------------</p>
                <p>ENTREGUE CONFORME</p>
            
                <p>------------------------------</p>
                <p>RECIBÍ CONFORME</p>
            </div>
        </div>
    </body>
    </html>';

    require_once('./vendor/tecnickcom/tcpdf/tcpdf.php');

    // Crear nuevo PDF 
    $pdf = new TCPDF();

    // Información del documento
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('Jhonatan Ñaupari Farfan');
    $pdf->SetTitle('Reporte del Movimiento');

    // Asignar los márgenes del documento
    $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);

    // Asignar salto de página automático
    $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

    // Establecer fuente
    $pdf->SetFont('helvetica', '', 12);

    // Agregar una página
    $pdf->AddPage();

    // Output the HTML content
    $pdf->writeHTML($contenido_pdf);

    // Close and output PDF document
    $pdf->Output('example_006.pdf', 'I');
}
?>
