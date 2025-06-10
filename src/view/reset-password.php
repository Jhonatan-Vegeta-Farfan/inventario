<!DOCTYPE html>
 <html lang="es">
 <head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Recuperar Contraseña - Dragon Ball</title>
  <style>
  body {
  margin: 0;
  padding: 0;
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 100vh;
  font-family: 'Comic Sans MS', cursive, sans-serif;
  background-size: cover;
  color: #fff;
  }
 

  .login-container {
  background: rgba(0, 0, 0, 0.7);
  padding: 40px 30px;
  border-radius: 15px;
  box-shadow: 0 5px 15px rgba(0, 0, 0, 0.5);
  text-align: center;
  width: 90%;
  max-width: 400px;
  margin: 0 auto; /* Centrar horizontalmente */
  }
 

  .login-container h1 {
  font-size: 2.5rem;
  margin-bottom: 20px;
  color: #ffcc00;
  }
 

  .login-container input {
  width: 100%;
  padding: 10px;
  margin: 10px 0;
  border: none;
  border-radius: 5px;
  outline: none;
  font-size: 1rem;
  }
 

  .login-container input[type="password"] {
  background: rgba(255, 255, 255, 0.9);
  color: #333;
  }
 

  .login-container input::placeholder {
  color: #888;
  }
 

  .login-container button {
  width: 100%;
  padding: 10px;
  margin-top: 20px;
  background: #ff5722;
  border: none;
  border-radius: 5px;
  color: #fff;
  font-size: 1rem;
  cursor: pointer;
  transition: background 0.3s ease;
  }
 

  .login-container button:hover {
  background: #ffcc00;
  }
 

  .login-container a {
  display: block;
  margin-top: 15px;
  color: #ffcc00;
  text-decoration: none;
  font-size: 0.9rem;
  }
 

  .login-container a:hover {
  text-decoration: underline;
  }
 

  @media (max-width: 600px) {
  .login-container {
  padding: 30px 20px;
  }
 

  .login-container h1 {
  font-size: 2rem;
  }
  }
  </style>
  <!-- Sweet Alerts css -->
  <link href="<?php echo BASE_URL ?>src/view/pp/plugins/sweetalert2/sweetalert2.min.css" rel="stylesheet" type="text/css" />
  <script>
  const base_url = '<?php echo BASE_URL; ?>';
  const base_url_server = '<?php echo BASE_URL_SERVER; ?>';
  </script>
 </head>
 

 <body>
  <input type="hidden" id="data" value='<?php echo $_GET['data']; ?>'>
  <input type="hidden" id="data2" value='<?php echo urldecode($_GET['data2']); ?>'>
 

  <div class="login-container">
  <h1>Recuperar Contraseña</h1>
  <img src="https://upload.wikimedia.org/wikipedia/commons/5/59/Dragon_Ball_Z_Logo_B.png" alt="Dragon Ball Logo" width="100%">
  <h4>Sistema de Control de Inventario</h4>
  <form id="frm_reset_password">
  <input type="password" id="password" id="password" placeholder="Nueva Contraseña" required>
  <input type="password" id="password1" id="password1" placeholder="Confirmar Nueva Contraseña" required>
  <button  type="button" onclick="validar_imputs_password();">Actualizar Contraseña</button>
  </form>
  </div>
  <script src="<?php echo BASE_URL; ?>src/view/js/principal.js"></script>
  <script src="<?php echo BASE_URL ?>src/view/pp/plugins/sweetalert2/sweetalert2.min.js"></script>
  <script>
    validar_datos_reset_password();
  </script>

 </body>
 </html>
 