<?php

$data  = $_GET['data']  ?? null;
$data2 = urldecode($_GET['data2']) ?? null;

// Validar y sanitizar
$data  = htmlspecialchars($data);
$data2 = htmlspecialchars($data2);

?>

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

<!-- From Uiverse.io by xerith_8140 --> 
<div class="create-account-container">
  <div class="create-account-header">
    <h2 class="texr-p">Restablecer Contraseña</h2>
    <p class="texr-p">Ingresa una contraseña segura</p>
  </div>
  <div class="create-account-content">
    <form id="reset_pass_form">
      <input class="input" type="hidden" id="data" name="data" value="<?php echo $data ?>">
      <input class="input" type="hidden" id="data2" name="data2" value="<?php echo $data2 ?>">
      <input type="password" id="Password" name="Password" placeholder="Nueva contraseña" />
      <input type="password" id="Password1" name="Password1" placeholder="Repetir Contraseña" />

      <input type="button" value="Cambiar contraseña" onclick="validar_inputs_password();" />
    </form>
    <p class="create-account-continue-with texr-p"> continuar </p>
    <button class="create-account-gmail" id="button_r_pass">
      <svg
        aria-hidden="true"
        xmlns="http://www.w3.org/2000/svg"
        fill="currentColor"
        viewBox="0 0 24 24"
      >
        <path
          fill-rule="evenodd"
          d="M12.006 2a9.847 9.847 0 0 0-6.484 2.44 10.32 10.32 0 0 0-3.393 6.17 10.48 10.48 0 0 0 1.317 6.955 10.045 10.045 0 0 0 5.4 4.418c.504.095.683-.223.683-.494 0-.245-.01-1.052-.014-1.908-2.78.62-3.366-1.21-3.366-1.21a2.711 2.711 0 0 0-1.11-1.5c-.907-.637.07-.621.07-.621.317.044.62.163.885.346.266.183.487.426.647.71.135.253.318.476.538.655a2.079 2.079 0 0 0 2.37.196c.045-.52.27-1.006.635-1.37-2.219-.259-4.554-1.138-4.554-5.07a4.022 4.022 0 0 1 1.031-2.75 3.77 3.77 0 0 1 .096-2.713s.839-.275 2.749 1.05a9.26 9.26 0 0 1 5.004 0c1.906-1.325 2.74-1.05 2.74-1.05.37.858.406 1.828.101 2.713a4.017 4.017 0 0 1 1.029 2.75c0 3.939-2.339 4.805-4.564 5.058a2.471 2.471 0 0 1 .679 1.897c0 1.372-.012 2.477-.012 2.814 0 .272.18.592.687.492a10.05 10.05 0 0 0 5.388-4.421 10.473 10.473 0 0 0 1.313-6.948 10.32 10.32 0 0 0-3.39-6.165A9.847 9.847 0 0 0 12.007 2Z"
          clip-rule="evenodd"
        ></path>
      </svg>
      Github
    </button>
  </div>
  <p>
    By clicking continue, you agree to our <a href="">Terms of Service</a> and
    <a href="">Privacy Policy</a>.
  </p>
</div>
<script src="<?php echo BASE_URL ?>src/view/pp/plugins/sweetalert2/sweetalert2.min.js"></script>
<script src="<?php echo  BASE_URL;?>src/view/js/principal.js"></script>
<script>
  validar_datos_reset_password();
</script>
</body>
</html>