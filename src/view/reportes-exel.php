
<?php 
$ruta = explode("/", $_GET['views']);

if (!isset($ruta[1]) || $ruta[1] == "") {
    header("location: " . BASE_URL. "404");

}
    require './vendor/autoload.php';

    use PhpOffice\PhpSpreadsheet\Spreadsheet;
    use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
  

if($ruta[1] == "bienes"){
    $curl = curl_init(); //inicia la sesión cURL
    curl_setopt_array($curl, array(
        CURLOPT_URL => BASE_URL_SERVER."src/control/Bien.php?tipo=ObtenerTodosBienes&sesion=".$_SESSION['sesion_id']."&token=".$_SESSION['sesion_token'], //url a la que se conecta
        CURLOPT_RETURNTRANSFER => true, //devuelve el resultado como una cadena del tipo curl_exec
        CURLOPT_FOLLOWLOCATION => true, //sigue el encabezado que le envíe el servidor
        CURLOPT_ENCODING => "", // permite decodificar la respuesta y puede ser"identity", "deflate", y "gzip", si está vacío recibe todos los disponibles.
        CURLOPT_MAXREDIRS => 10, // Si usamos CURLOPT_FOLLOWLOCATION le dice el máximo de encabezados a seguir
        CURLOPT_TIMEOUT => 30, // Tiempo máximo para ejecutar
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1, // usa la versión declarada
        CURLOPT_CUSTOMREQUEST => "GET", // el tipo de petición, puede ser PUT, POST, GET o Delete dependiendo del servicio
        CURLOPT_HTTPHEADER => array(
            "x-rapidapi-host: ".BASE_URL_SERVER,
            "x-rapidapi-key: XXXX"
        ), //configura las cabeceras enviadas al servicio
    )); //curl_setopt_array configura las opciones para una transferencia cURL

    $response = curl_exec($curl); // respuesta generada
    $err = curl_error($curl); // muestra errores en caso de existir

    curl_close($curl); // termina la sesión 

    if ($err) {
        echo "cURL Error #:" . $err; // mostramos el error
    } else {
       $respuesta = json_decode($response);
       $bienes = $respuesta->bienes;

       // Crear el Excel
            $spreadsheet = new Spreadsheet();
            $spreadsheet->getProperties()->setCreator("YO")->setLastModifiedBy("yo")->setTitle("ReporteBienes")->setDescription("yo");
            $activeWorkSheet = $spreadsheet->getActiveSheet();
            $activeWorkSheet->setTitle("Bienes");  

            // Estilo en negrita
            $styleArray = [
                'font' => [
                    'bold' => true,
                ]
            ];

            // Aplica negrita a la fila 1 (de A1 a R1 si son 18 columnas)
            $activeWorkSheet->getStyle('A1:R1')->applyFromArray($styleArray);
            
            $headers = [
                'ID', 'Id ingreso bienes', 'id ambiente', 'cod patrimonial', 'denominacion', 'marca', 'Modelo', 'tipo', 'Color',
                'serie', 'dimensiones', 'valor', 'situacion', 'estado conservacion', 'observaciones',
                'fecha registro', 'usuario registro', 'estado'
             ];

            // Asignar cabeceras en la fila 1
            foreach ($headers as $i => $header) {
                $columna = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i + 1);
                $activeWorkSheet->setCellValue($columna . '1', $header);
            }

           // Llenar los datos
            $row = 2;
            foreach ($bienes as $bien) {
                $atributos = [
                    $bien->id ?? '',
                    $bien->ingresonombre ?? '',
                    $bien->ambiente ?? '',
                    $bien->cod_patrimonial ?? '',
                    $bien->denominacion ?? '',
                    $bien->marca ?? '',
                    $bien->modelo ?? '',
                    $bien->tipo ?? '',
                    $bien->color ?? '',
                    $bien->serie ?? '',
                    $bien->dimensiones ?? '',
                    $bien->valor ?? '',
                    $bien->situacion ?? '',
                    $bien->estado_conservacion ?? '',
                    $bien->observaciones ?? '',
                    $bien->fecha_registro ?? '',
                    $bien->usuarioregistro ?? '',
                    $bien->estado ?? ''
                ];

                foreach ($atributos as $i => $valor) {
                    $columna = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i + 1);
                    $activeWorkSheet->setCellValue($columna . $row, $valor);
                }

                $row++;
            }


            ob_clean();
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment; filename="reporte_bienes.xlsx"');
            header('Cache-Control: max-age=0');

            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
            exit;

  }
}
if($ruta[1] == "usuarios"){
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => BASE_URL_SERVER."src/control/Usuario.php?tipo=listarUsuarios&sesion=".$_SESSION['sesion_id']."&token=".$_SESSION['sesion_token'], //url a la que se conecta
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true, 
        CURLOPT_ENCODING => "", 
        CURLOPT_MAXREDIRS => 10, 
        CURLOPT_TIMEOUT => 30, 
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_HTTPHEADER => array(
            "x-rapidapi-host: ".BASE_URL_SERVER,
            "x-rapidapi-key: XXXX"
        ),
    )); 
    $response = curl_exec($curl); 
    $err = curl_error($curl); 
    curl_close($curl);
    if ($err) {
        echo "cURL Error #:" . $err; 
    } else {
       $respuesta = json_decode($response);
       $usuarios = $respuesta->usuarios;

            $spreadsheet = new Spreadsheet();
            $spreadsheet->getProperties()->setCreator("JHONATAN")->setLastModifiedBy("yo")->setTitle("ReporteBienes")->setDescription("yo");
            $activeWorkSheet = $spreadsheet->getActiveSheet();
            $activeWorkSheet->setTitle("usuarios");  

            $styleArray = [
                'font' => [
                    'bold' => true,
                ]
            ];
            // Aplica negrita a la fila 1 (de A1 a R1 si son 18 columnas)
            $activeWorkSheet->getStyle('A1:R1')->applyFromArray($styleArray);
            
            $headers = [
                'ID', 'DNI', 'NOMBRES Y APELLIDOS', 'CORREO', 'TELEFONO', 'ESTADO', 'FECHA DE REGISTRO'
             ];

            // Asignar cabeceras en la fila 1
            foreach ($headers as $i => $header) {
                $columna = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i + 1);
                $activeWorkSheet->setCellValue($columna . '1', $header);
            }

           // Llenar los datos
            $row = 2;
            foreach ($usuarios as $usuario) {
                $usuario->estado = 1? "activo":"inactivo";
                $atributos = [
                    $usuario->id ,
                    $usuario->dni ,
                    $usuario->nombres_apellidos ,
                    $usuario->correo ,
                    $usuario->telefono ,
                    $usuario->estado ,
                    $usuario->fecha_registro
                ];

                foreach ($atributos as $i => $valor) {
                    $columna = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i + 1);
                    $activeWorkSheet->setCellValue($columna . $row, $valor);
                }
                $row++;
            }
            ob_clean();
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment; filename="reporte_usuarios.xlsx"');
            header('Cache-Control: max-age=0');
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
            exit;
  }

}
if($ruta[1] == "instituciones"){
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => BASE_URL_SERVER."src/control/Institucion.php?tipo=listar&sesion=".$_SESSION['sesion_id']."&token=".$_SESSION['sesion_token'], //url a la que se conecta
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true, 
        CURLOPT_ENCODING => "", 
        CURLOPT_MAXREDIRS => 10, 
        CURLOPT_TIMEOUT => 30, 
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_HTTPHEADER => array(
            "x-rapidapi-host: ".BASE_URL_SERVER,
            "x-rapidapi-key: XXXX"
        ),
    )); 
    $response = curl_exec($curl); 
    $err = curl_error($curl); 
    curl_close($curl);
    if ($err) {
        echo "cURL Error #:" . $err; 
    } else {
       $respuesta = json_decode($response);
       $instituciones = $respuesta->contenido;

            $spreadsheet = new Spreadsheet();
            $spreadsheet->getProperties()->setCreator("JHONATAN")->setLastModifiedBy("yo")->setTitle("ReporteBienes")->setDescription("yo");
            $activeWorkSheet = $spreadsheet->getActiveSheet();
            $activeWorkSheet->setTitle("instituciones");  

            $styleArray = [
                'font' => [
                    'bold' => true,
                ]
            ];
            // Aplica negrita a la fila 1 (de A1 a R1 si son 18 columnas)
            $activeWorkSheet->getStyle('A1:R1')->applyFromArray($styleArray);
            
            $headers = [
                'ID', 'BENEFICIARIO', 'CODIGO MODULAR', 'RUC', 'NOMBRE'
             ];

            // Asignar cabeceras en la fila 1
            foreach ($headers as $i => $header) {
                $columna = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i + 1);
                $activeWorkSheet->setCellValue($columna . '1', $header);
            }

           // Llenar los datos
            $row = 2;
            foreach ($instituciones as $insttucion) {
                $atributos = [
                    $insttucion->id ,
                    $insttucion->beneficiario ,
                    $insttucion->cod_modular ,
                    $insttucion->ruc ,
                    $insttucion->nombre
                ];

                foreach ($atributos as $i => $valor) {
                    $columna = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i + 1);
                    $activeWorkSheet->setCellValue($columna . $row, $valor);
                }
                $row++;
            }
            ob_clean();
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment; filename="reporte_instituciones.xlsx"');
            header('Cache-Control: max-age=0');
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
            exit;
  }

}
if($ruta[1] == "ambientes"){
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => BASE_URL_SERVER."src/control/Ambiente.php?tipo=listarTodosAmbientes&sesion=".$_SESSION['sesion_id']."&token=".$_SESSION['sesion_token'], //url a la que se conecta
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true, 
        CURLOPT_ENCODING => "", 
        CURLOPT_MAXREDIRS => 10, 
        CURLOPT_TIMEOUT => 30, 
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_HTTPHEADER => array(
            "x-rapidapi-host: ".BASE_URL_SERVER,
            "x-rapidapi-key: XXXX"
        ),
    )); 
    $response = curl_exec($curl); 
    $err = curl_error($curl); 
    curl_close($curl);
    if ($err) {
        echo "cURL Error #:" . $err; 
    } else {
       $respuesta = json_decode($response);
       $ambientes = $respuesta->contenido;

            $spreadsheet = new Spreadsheet();
            $spreadsheet->getProperties()->setCreator("JHONATAN")->setLastModifiedBy("yo")->setTitle("ReporteBienes")->setDescription("yo");
            $activeWorkSheet = $spreadsheet->getActiveSheet();
            $activeWorkSheet->setTitle("ambientes");  

            $styleArray = [
                'font' => [
                    'bold' => true,
                ]
            ];
            // Aplica negrita a la fila 1 (de A1 a R1 si son 18 columnas)
            $activeWorkSheet->getStyle('A1:R1')->applyFromArray($styleArray);
            
            $headers = [
                'ID', 'INSTITUCION', 'ENCARGADO', 'CODIGO', 'DETALLE','OTROS DETALLE'
             ];

            // Asignar cabeceras en la fila 1
            foreach ($headers as $i => $header) {
                $columna = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i + 1);
                $activeWorkSheet->setCellValue($columna . '1', $header);
            }

           // Llenar los datos
            $row = 2;
            foreach ($ambientes as $ambiente) {
                $atributos = [
                    $ambiente->id ,
                    $ambiente->institucion ,
                    $ambiente->encargado ,
                    $ambiente->codigo ,
                    $ambiente->detalle,
                    $ambiente->otros_detalle
                ];

                foreach ($atributos as $i => $valor) {
                    $columna = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i + 1);
                    $activeWorkSheet->setCellValue($columna . $row, $valor);
                }
                $row++;
            }
            ob_clean();
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment; filename="reporte_ambientes.xlsx"');
            header('Cache-Control: max-age=0');
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
            exit;
  }

}
if($ruta[1] == "movimientos"){
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => BASE_URL_SERVER."src/control/Movimiento.php?tipo=ListarMovimientos&sesion=".$_SESSION['sesion_id']."&token=".$_SESSION['sesion_token'], //url a la que se conecta
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true, 
        CURLOPT_ENCODING => "", 
        CURLOPT_MAXREDIRS => 10, 
        CURLOPT_TIMEOUT => 30, 
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_HTTPHEADER => array(
            "x-rapidapi-host: ".BASE_URL_SERVER,
            "x-rapidapi-key: XXXX"
        ),
    )); 
    $response = curl_exec($curl); 
    $err = curl_error($curl); 
    curl_close($curl);
    if ($err) {
        echo "cURL Error #:" . $err; 
    } else {
       $respuesta = json_decode($response);
       $movimientos = $respuesta->movimientos;

            $spreadsheet = new Spreadsheet();
            $spreadsheet->getProperties()->setCreator("JHONATAN")->setLastModifiedBy("yo")->setTitle("ReporteBienes")->setDescription("yo");
            $activeWorkSheet = $spreadsheet->getActiveSheet();
            $activeWorkSheet->setTitle("movimientos");  

            $styleArray = [
                'font' => [
                    'bold' => true,
                ]
            ];
            // Aplica negrita a la fila 1 (de A1 a R1 si son 18 columnas)
            $activeWorkSheet->getStyle('A1:R1')->applyFromArray($styleArray);
            
            $headers = [
                'ID', 'AMBIENTE DE ORIGEN', 'AMBIENTE DE DESTINO', 'USUARIO REGISTRO', 'FECHA REGISTRO','DESCRIPCION','INSTITUCION'
             ];

            // Asignar cabeceras en la fila 1
            foreach ($headers as $i => $header) {
                $columna = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i + 1);
                $activeWorkSheet->setCellValue($columna . '1', $header);
            }

           // Llenar los datos
            $row = 2;
            foreach ($movimientos as $datos) {
                $atributos = [
                    $datos->id ,
                    $datos->origenname ,
                    $datos->destinoname ,
                    $datos->usuarioname ,
                    $datos->fecha,
                    $datos->descripcion,
                    $datos->institucionname
                ];

                foreach ($atributos as $i => $valor) {
                    $columna = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i + 1);
                    $activeWorkSheet->setCellValue($columna . $row, $valor);
                }
                $row++;
            }
            ob_clean();
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment; filename="reporte_movimienos.xlsx"');
            header('Cache-Control: max-age=0');
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
            exit;
  }

}

  ?>