<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

session_start();
require_once('../model/admin-sesionModel.php');
require_once('../model/admin-usuarioModel.php');
require_once('../model/adminModel.php');

require '../../vendor/autoload.php';

require '../../vendor/phpmailer/phpmailer/src/Exception.php';
require '../../vendor/phpmailer/phpmailer/src/PHPMailer.php';
require '../../vendor/phpmailer/phpmailer/src/SMTP.php';

$tipo = $_GET['tipo'];

//instanciar la clase categoria model
$objSesion = new SessionModel();
$objUsuario = new UsuarioModel();
$objAdmin = new AdminModel();

//variables de sesion
$id_sesion = $_POST['sesion'];
$token = $_POST['token'];

if ($tipo == "validar_datos_reset_password") {
    $id_email = $_POST['id'];
    $token_email = $_POST['token'];
    $arr_Respuesta = array('status' => false, 'msg' => 'Link Caducado');
    $datos_usuario = $objUsuario->buscarUsuarioById($id_email);

    if ($datos_usuario->reset_password == 1 && password_verify($datos_usuario->token_password, $token_email)) {
      $arr_Respuesta = array('status' => true, 'msg' => 'Ok');
    }
    echo json_encode($arr_Respuesta);
  }

  //tarea
// funcion para actualizar contraseña
if ($tipo == "actualizar_password_reset") {
    $id_usuario = $_POST['id'];
    $nueva_password = $_POST['password'];
    $token_email = $_POST['token'];
    
    $arr_Respuesta = array('status' => false, 'msg' => 'Error al actualizar contraseña');
    
    // Verificar que el usuario existe y el token es válido
    $datos_usuario = $objUsuario->buscarUsuarioById($id_usuario);
    
    if ($datos_usuario && $datos_usuario->reset_password == 1 && password_verify($datos_usuario->token_password, $token_email)) {
        // Actualizar contraseña y limpiar datos de reset
        $resultado = $objUsuario->actualizarPasswordYLimpiarReset($id_usuario, $nueva_password);
        
        if ($resultado) {
            $arr_Respuesta = array(
                'status' => true, 
                'msg' => 'Contraseña actualizada correctamente'
            );
        } else {
            $arr_Respuesta = array(
                'status' => false, 
                'msg' => 'Error al guardar en la base de datos'
            );
        }
    } else {
        $arr_Respuesta = array(
            'status' => false, 
            'msg' => 'Token inválido o expirado'
        );
    }
    
    echo json_encode($arr_Respuesta);
}


if ($tipo == "listar_usuarios_ordenados_tabla") {
    $arr_Respuesta = array('status' => false, 'msg' => 'Error_Sesion');
    if ($objSesion->verificar_sesion_si_activa($id_sesion, $token)) {
        //print_r($_POST);
        $pagina = $_POST['pagina'];
        $cantidad_mostrar = $_POST['cantidad_mostrar'];
        $busqueda_tabla_dni = $_POST['busqueda_tabla_dni'];
        $busqueda_tabla_nomap = $_POST['busqueda_tabla_nomap'];
        $busqueda_tabla_estado = $_POST['busqueda_tabla_estado'];
        //repuesta
        $arr_Respuesta = array('status' => false, 'contenido' => '');
        $busqueda_filtro = $objUsuario->buscarUsuariosOrderByApellidosNombres_tabla_filtro($busqueda_tabla_dni, $busqueda_tabla_nomap, $busqueda_tabla_estado);
        $arr_Usuario = $objUsuario->buscarUsuariosOrderByApellidosNombres_tabla($pagina, $cantidad_mostrar, $busqueda_tabla_dni, $busqueda_tabla_nomap, $busqueda_tabla_estado);
        $arr_contenido = [];
        if (!empty($arr_Usuario)) {
            // recorremos el array para agregar las opciones de las categorias
            for ($i = 0; $i < count($arr_Usuario); $i++) {
                // definimos el elemento como objeto
                $arr_contenido[$i] = (object) [];
                // agregamos solo la informacion que se desea enviar a la vista
                $arr_contenido[$i]->id = $arr_Usuario[$i]->id;
                $arr_contenido[$i]->dni = $arr_Usuario[$i]->dni;
                $arr_contenido[$i]->nombres_apellidos = $arr_Usuario[$i]->nombres_apellidos;
                $arr_contenido[$i]->correo = $arr_Usuario[$i]->correo;
                $arr_contenido[$i]->telefono = $arr_Usuario[$i]->telefono;
                $arr_contenido[$i]->estado = $arr_Usuario[$i]->estado;
                $opciones = '<button type="button" title="Editar" class="btn btn-primary waves-effect waves-light" data-toggle="modal" data-target=".modal_editar' . $arr_Usuario[$i]->id . '"><i class="fa fa-edit"></i></button>
                                <button class="btn btn-info" title="Resetear Contraseña" onclick="reset_password(' . $arr_Usuario[$i]->id . ')"><i class="fa fa-key"></i></button>';
                $arr_contenido[$i]->options = $opciones;
            }
            $arr_Respuesta['total'] = count($busqueda_filtro);
            $arr_Respuesta['status'] = true;
            $arr_Respuesta['contenido'] = $arr_contenido;
        }
    }
    echo json_encode($arr_Respuesta);
}
if ($tipo == "registrar") {
    $arr_Respuesta = array('status' => false, 'msg' => 'Error_Sesion');
    if ($objSesion->verificar_sesion_si_activa($id_sesion, $token)) {
        //print_r($_POST);
        //repuesta
        if ($_POST) {
            $dni = $_POST['dni'];
            $apellidos_nombres = $_POST['apellidos_nombres'];
            $correo = $_POST['correo'];
            $telefono = $_POST['telefono'];
            $password = $_POST['password'];

            if ($dni == "" || $apellidos_nombres == "" || $correo == "" || $telefono == ""|| $password == "") {
                //repuesta
                $arr_Respuesta = array('status' => false, 'mensaje' => 'Error, campos vacíos');
            } else {
                $arr_Usuario = $objUsuario->buscarUsuarioByDni($dni);
                if ($arr_Usuario) {
                    $arr_Respuesta = array('status' => false, 'mensaje' => 'Registro Fallido, Usuario ya se encuentra registrado');
                } else {
                    $id_usuario = $objUsuario->registrarUsuario($dni, $apellidos_nombres, $correo, $telefono, $password);
                    if ($id_usuario > 0) {
                        // array con los id de los sistemas al que tendra el acceso con su rol registrado
                        // caso de administrador y director
                        $arr_Respuesta = array('status' => true, 'mensaje' => 'Registro Exitoso');
                    } else {
                        $arr_Respuesta = array('status' => false, 'mensaje' => 'Error al registrar usuario');
                    }
                }
            }
        }
    }
    echo json_encode($arr_Respuesta);
}

if ($tipo == "actualizar") {
    $arr_Respuesta = array('status' => false, 'msg' => 'Error_Sesion');
    if ($objSesion->verificar_sesion_si_activa($id_sesion, $token)) {
        //print_r($_POST);
        //repuesta
        if ($_POST) {
            $id = $_POST['data'];
            $dni = $_POST['dni'];
            $nombres_apellidos = $_POST['nombres_apellidos'];
            $correo = $_POST['correo'];
            $telefono = $_POST['telefono'];
            $estado = $_POST['estado'];

            if ($id == "" || $dni == "" || $nombres_apellidos == "" || $correo == "" || $telefono == "" || $estado == "") {
                //repuesta
                $arr_Respuesta = array('status' => false, 'mensaje' => 'Error, campos vacíos');
            } else {
                $arr_Usuario = $objUsuario->buscarUsuarioByDni($dni);
                if ($arr_Usuario) {
                    if ($arr_Usuario->id == $id) {
                        $consulta = $objUsuario->actualizarUsuario($id, $dni, $nombres_apellidos, $correo, $telefono, $estado);
                        if ($consulta) {
                            $arr_Respuesta = array('status' => true, 'mensaje' => 'Actualizado Correctamente');
                        } else {
                            $arr_Respuesta = array('status' => false, 'mensaje' => 'Error al actualizar registro');
                        }
                    } else {
                        $arr_Respuesta = array('status' => false, 'mensaje' => 'dni ya esta registrado');
                    }
                } else {
                    $consulta = $objUsuario->actualizarUsuario($id, $dni, $nombres_apellidos, $correo, $telefono, $estado);
                    if ($consulta) {
                        $arr_Respuesta = array('status' => true, 'mensaje' => 'Actualizado Correctamente');
                    } else {
                        $arr_Respuesta = array('status' => false, 'mensaje' => 'Error al actualizar registro');
                    }
                }
            }
        }
    }
    echo json_encode($arr_Respuesta);
}
if ($tipo == "reiniciar_password") {
    $arr_Respuesta = array('status' => false, 'msg' => 'Error_Sesion');
    if ($objSesion->verificar_sesion_si_activa($id_sesion, $token)) {
        //print_r($_POST);
        $id_usuario = $_POST['id'];
        $password = $objAdmin->generar_llave(10);
        $pass_secure = password_hash($password, PASSWORD_DEFAULT);
        $actualizar = $objUsuario->actualizarPassword($id_usuario, $pass_secure);
        if ($actualizar) {
            $arr_Respuesta = array('status' => true, 'mensaje' => 'Contraseña actualizado correctamente a: ' . $password);
        } else {
            $arr_Respuesta = array('status' => false, 'mensaje' => 'Hubo un problema al actualizar la contraseña, intente nuevamente');
        }
    }
    echo json_encode($arr_Respuesta);
}

if($tipo =="sent_email_password"){
    $arr_Respuesta = array('status' => false, 'msg' => 'Error_Sesion');
    if ($objSesion->verificar_sesion_si_activa($id_sesion, $token)){
        $datos_sesion = $objSesion->buscarSesionLoginById($id_sesion);
        $datos_usuario = $objUsuario->buscarUsuarioById($datos_sesion->id_usuario);
        $nombreusuario = $datos_usuario->nombres_apellidos;
        $llave = $objAdmin->generar_llave(30);
        $token = password_hash($llave, PASSWORD_DEFAULT);
        $update = $objUsuario->updateResetPassword($datos_sesion->id_usuario, $llave, 1);
        if ($update) {
           
            //Import PHPMailer classes into the global namespace
//These must be at the top of your script, not inside a function

//Load Composer's autoloader (created by composer, not included with PHPMailer)

//Create an instance; passing `true` enables exceptions
$mail = new PHPMailer(true);

try {
    //Server settings
    $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
    $mail->isSMTP();                                            //Send using SMTP
    $mail->Host       = 'mail.dpweb2024.com';                     //Set the SMTP server to send through
    $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
    $mail->Username   = 'jhonatannfarfan@dpweb2024.com';                     //SMTP username
    $mail->Password   = 'b,*?ccE-n1J7';                               //SMTP password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
    $mail->Port       = 465;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

    //Recipients
    $mail->setFrom('inventario_jhonatannfarfan@dpweb2024.com', 'Cambio de Contraseña');
    $mail->addAddress($datos_usuario->correo, $datos_usuario->nombres_apellidos);     //Add a recipient

    /*$mail->addAddress('ellen@example.com');               //Name is optional
    $mail->addReplyTo('info@example.com', 'Information');
    $mail->addCC('cc@example.com');
    $mail->addBCC('bcc@example.com');

    //Attachments
    $mail->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
    $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name
    */

    //Content
    $mail->isHTML(true); //Set email format to HTML
    $mail->CharSet = 'UTF-8';                                 
    $mail->Subject = 'Cambio de contraseña - Sistema de Inventario';
    $mail->Body    = '

    
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Actualización de Contraseña</title>
        <style>
            body {
                margin: 0;
                padding: 0;
                background-color: #f4f4f4;
            }
            
            .container {
                max-width: 600px;
                margin: auto;
                background-color: #000000;
                font-family: Arial, sans-serif;
                color: #ffffff;
                border: 1px solid #dd0000;
                box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
                border-radius: 8px;
                overflow: hidden;
            }
            
            .header {
                background-color: #dd0000;
                color: white;
                padding: 20px;
                text-align: center;
            }
            
            .header img {
                max-width: 100%;
                height: auto;
                margin-bottom: 10px;
            }
            
            .header h2 {
                font-size: 2.5em;
                margin: 0;
            }

            .content {
                padding: 30px;
            }
            
            .content h1 {
                font-size: 1.5em;
                margin-bottom: 20px;
            }
            
            .content p {
                font-size: 1em;
                line-height: 1.5;
            }
            
            .button {
                display: inline-block;
                background-color: #dd0000;
                color: #ffffff !important;
                padding: 12px 25px;
                margin: 20px 0;
                text-decoration: none;
                border-radius: 4px;
                transition: background-color 0.3s;
                text-align: center;
            }
            
            .button:hover {
                background-color: #ff3333;
            }
            
            .footer {
                background-color: #333333;
                text-align: center;
                padding: 15px;
                font-size: 0.8em;
                color: #ffffff;
            }
            
            @media screen and (max-width: 600px) {
                .content, .header, .footer {
                    padding: 15px !important;
                }
                .button {
                    padding: 10px 20px !important;
                }
                .header h2 {
                    font-size: 2em; /* Ajuste para pantallas pequeñas */
                }
                .content h1 {
                    font-size: 1.2em; /* Ajuste para pantallas pequeñas */
                }
                .content p {
                    font-size: 0.9em; /* Ajuste para pantallas pequeñas */
                }
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="header">
                <a href="https://www.tusitio.com">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/5/59/Dragon_Ball_Z_Logo_B.png" alt="Logo de la Empresa">
                </a>
                <h2>VegetaStoR</h2>
            </div>
            <div class="content">
            <h1>Hola '. $nombreusuario .'</h1>
                <p>
                    Te informamos que tu contraseña ha sido actualizada exitosamente. Si no realizaste este cambio, por favor contáctanos de inmediato.
                </p>
                <p>
                    Para mayor seguridad, te recomendamos que cambies tu contraseña regularmente. Si deseas cambiarla nuevamente, puedes hacerlo a través del siguiente enlace:
                </p>
                <a href="'.BASE_URL.'reset-password/?data='.$datos_usuario->id.'&data2='. urlencode($token).'" class="button">Cambiar mi Contraseña</a>
                <p>Gracias por confiar en nosotros, no responder este mensaje.</p>
            </div>
            <div class="footer">
                © 2025 Sistema de Inventario. Todos los derechos reservados.<br>
                <a href="'.BASE_URL.'" style="color: #ffffff;">Cancelar suscripción</a>
            </div>
        </div>
    </body>
    </html>
    
    ';

    $mail->send();
    echo 'Message has been sent';
} catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}

        }else{
            echo "fallo al actualizar";
        }
        //print_r($token);//
    }
}

