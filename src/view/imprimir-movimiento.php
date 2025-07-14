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
                margin-bottom: 30px;
                font-size: 18px;
            }
            .info {
                margin-top: 30px;
                margin-bottom: 20px;
            }
            .info p {
                margin: 8px 0;
                font-size: 14px;
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
                padding: 8px;
                text-align: center;
                font-size: 12px;
            }
            th {
                background-color: #f0f0f0;
                font-weight: bold;
            }
            .signature {
                margin-top: 50px;
                display: table;
                width: 100%;
                page-break-inside: avoid;
            }
            .signature-left, .signature-right {
                display: table-cell;
                width: 48%;
                text-align: center;
                vertical-align: top;
                padding: 0 10px;
            }
            .signature-left {
                border-right: 1px solid transparent;
            }
            .signature-line {
                margin: 0 0 10px 0;
                font-size: 12px;
                text-align: center;
            }
            .signature-title {
                margin: 0 0 15px 0;
                font-size: 12px;
                font-weight: bold;
                text-align: center;
            }
            .signature-field {
                margin: 8px 0;
                font-size: 11px;
                text-align: left;
                padding: 0 5px;
            }
            .date {
                text-align: right;
                margin-top: 30px;
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
            $contenido_pdf .= "<td>" . htmlspecialchars($bien->cod_patrimonial) . "</td>";
            $contenido_pdf .= "<td>" . htmlspecialchars($bien->denominacion) . "</td>";
            $contenido_pdf .= "<td>" . htmlspecialchars($bien->marca) . "</td>";
            $contenido_pdf .= "<td>" . htmlspecialchars($bien->color) . "</td>";
            $contenido_pdf .= "<td>" . htmlspecialchars($bien->modelo) . "</td>";
            $contenido_pdf .= "<td>" . htmlspecialchars($bien->estado_conservacion) . "</td>";
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
        "d 'de' MMMM 'del' y"
    );

    // Crear el objeto DateTime con zona horaria Lima
    $fechaOriginal = new DateTime($respuesta->movimiento->fecha_registro, new DateTimeZone("America/Lima"));
    $fechaFormateada = $fechaMovimiento->format($fechaOriginal);

    $contenido_pdf .= '
        <div class="date">
            Ayacucho, ' . $fechaFormateada . '
        </div>
        <div class="signature">
            <div class="signature-left">
                <p class="signature-line">_____________________________</p>
                <p class="signature-title"><strong>ENTREGUÉ CONFORME</strong></p>
                <p class="signature-field">Nombre: _________________________</p>
                <p class="signature-field">DNI: _____________________________</p>
                <p class="signature-field">Firma: ___________________________</p>
            </div>
            <div class="signature-right">
                <p class="signature-line">_____________________________</p>
                <p class="signature-title"><strong>RECIBÍ CONFORME</strong></p>
                <p class="signature-field">Nombre: _________________________</p>
                <p class="signature-field">DNI: _____________________________</p>
                <p class="signature-field">Firma: ___________________________</p>
            </div>
        </div>
    </body>
    </html>';

    require_once('./vendor/tecnickcom/tcpdf/tcpdf.php');

    // Clase personalizada para agregar header y footer
    class MYPDF extends TCPDF {
        
        // Header personalizado
        public function Header() {
            // Obtener las dimensiones de la página
            $pageWidth = $this->getPageWidth();
            $leftMargin = $this->getMargins()['left'];
            $rightMargin = $this->getMargins()['right'];
            
            // Calcular el ancho disponible para el contenido
            $availableWidth = $pageWidth - $leftMargin - $rightMargin;
            
            // Definir el tamaño de los logos
            $logoWidth = 18;
            $logoHeight = 18;
            $headerHeight = 20;
            $headerY = 10;
            
            // Posiciones de los logos
            $escudoX = $leftMargin;
            $logoDerechoX = $pageWidth - $rightMargin - $logoWidth;
            
            // Colocar los logos
            $this->Image('https://upload.wikimedia.org/wikipedia/commons/thumb/c/cc/Escudo_nacional_del_Per%C3%BA.svg/1200px-Escudo_nacional_del_Per%C3%BA.svg.png', 
                        $escudoX, $headerY + 2, $logoWidth, $logoHeight);
            
            $this->Image('https://www.iestphuanta.edu.pe/sacademica/img/logo1.png', 
                        $logoDerechoX, $headerY + 2, $logoWidth, $logoHeight);
            
            // Calcular el ancho del rectángulo gris (entre los logos)
            $rectX = $leftMargin + $logoWidth + 3;
            $rectWidth = $availableWidth - (2 * $logoWidth) - 6; // 6 = espacios de 3px a cada lado
            
            // Fondo gris para el header
            $this->SetFillColor(128, 128, 128);
            $this->Rect($rectX, $headerY, $rectWidth, $headerHeight, 'F');
            
            // Configurar texto blanco para el header
            $this->SetTextColor(255, 255, 255);
            
            // Sección "PERÚ" - izquierda del rectángulo
            $this->SetFont('helvetica', 'B', 11);
            $peruX = $rectX + 5;
            $peruWidth = 25;
            $this->SetXY($peruX, $headerY + 6);
            $this->Cell($peruWidth, 8, 'PERÚ', 0, 0, 'C', false);
            
            // Primera línea separadora vertical
            $this->SetDrawColor(255, 255, 255);
            $this->SetLineWidth(0.5);
            $separador1X = $peruX + $peruWidth + 3;
            $this->Line($separador1X, $headerY + 2, $separador1X, $headerY + $headerHeight - 2);
            
            // Sección "Ministerio de Educación" - centro izquierda
            $this->SetFont('helvetica', '', 8);
            $ministerioX = $separador1X + 5;
            $ministerioWidth = 45;
            $this->SetXY($ministerioX, $headerY + 4);
            $this->Cell($ministerioWidth, 4, 'Ministerio', 0, 0, 'C', false);
            $this->SetXY($ministerioX, $headerY + 8);
            $this->Cell($ministerioWidth, 4, 'de Educación', 0, 0, 'C', false);
            
            // Segunda línea separadora vertical
            $separador2X = $ministerioX + $ministerioWidth + 5;
            $this->Line($separador2X, $headerY + 2, $separador2X, $headerY + $headerHeight - 2);
            
            // Sección principal "Dirección Regional de Educación Ayacucho" - centro derecha
            $this->SetFont('helvetica', '', 8);
            $direccionX = $separador2X + 5;
            $direccionWidth = $rectX + $rectWidth - $direccionX - 5; // Calcular el ancho restante
            $this->SetXY($direccionX, $headerY + 4);
            $this->Cell($direccionWidth, 4, 'Dirección Regional de Educación', 0, 0, 'C', false);
            $this->SetXY($direccionX, $headerY + 8);
            $this->Cell($direccionWidth, 4, 'Ayacucho', 0, 0, 'C', false);
            
            // Restablecer color de texto a negro para el contenido
            $this->SetTextColor(0, 0, 0);
            
            // Salto de línea después del header
            $this->Ln($headerHeight + 10);
        }
        
        // Footer personalizado
        public function Footer() {
            // Posición a 15 mm del final
            $this->SetY(-15);
            
            // Línea separadora
            $this->SetDrawColor(0, 0, 0);
            $this->SetLineWidth(0.2);
            $this->Line($this->getMargins()['left'], $this->GetY(), 
                       $this->getPageWidth() - $this->getMargins()['right'], $this->GetY());
            $this->Ln(2);
            
            // Fuente del footer
            $this->SetFont('helvetica', 'I', 8);
            $this->SetTextColor(0, 0, 0);
            
            // Texto del footer con número de página
            $this->Cell(0, 10, 'Sistema de Gestión de Bienes Patrimoniales - Página '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
        }
    }

    // Crear nuevo PDF con la clase personalizada
    $pdf = new MYPDF();

    // Información del documento
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('Jhonatan Ñaupari Farfan');
    $pdf->SetTitle('Reporte del Movimiento');
    $pdf->SetSubject('Papeleta de Rotación de Bienes');
    $pdf->SetKeywords('PDF, bienes, patrimoniales, rotación');

    // Asignar los márgenes del documento
    $pdf->SetMargins(15, 40, 15); // Izquierda, Superior, Derecha
    $pdf->SetHeaderMargin(5);
    $pdf->SetFooterMargin(10);

    // Asignar salto de página automático
    $pdf->SetAutoPageBreak(TRUE, 35); // Aumentado el margen inferior para las firmas

    // Establecer fuente
    $pdf->SetFont('helvetica', '', 12);

    // Agregar una página
    $pdf->AddPage();

    // Output the HTML content
    $pdf->writeHTML($contenido_pdf, true, false, true, false, '');

    // Close and output PDF document
    $pdf->Output('papeleta_rotacion_bienes_' . date('Y-m-d_H-i-s') . '.pdf', 'I');
}
?>