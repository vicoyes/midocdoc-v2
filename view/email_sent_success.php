<?php
<<<<<<< HEAD
// Obtener el dominio actual de WordPress de forma dinámica
$domain = site_url();
=======
 
  // Obtener el dominio actual de WordPress de forma dinámica
  $domain = site_url();

>>>>>>> ba870f142a10dfa9c2b780a68c8b10f66b5cf35d

echo '
<!DOCTYPE html>
<html lang="en">
<head>
  <style>
  dotlottie-player {
    margin: 0 auto;
  }
  
  .container-email {
    text-align: center;
    font-family: "Inter", sans-serif !important;
  }
  
  button.btn.btn-success {
    padding: 15px 33px;
    border: none;
    color: white;
    background: #2C3D8F;
    cursor: pointer;
  }

  #contador {
    font-size: 25px;
    font-weight: bold;
    color: #2C3D8F;
  }
  </style>
</head>
<body>
<div class="container-email">
<h1 class="success-email">Envío Exitoso</h1>
<p><dotlottie-player src="https://lottie.host/f73faa96-a722-4e18-b984-74d33deb5f03/IuHXeGp1lG.json" background="transparent" speed="1" style="width: 300px; height: 300px;" loop autoplay></dotlottie-player></p>
<<<<<<< HEAD
<p>Su correo electrónico ha sido enviado con éxito. <b>'.$customer->email.'</b></p>
=======
<p>Su correo electrónico ha sido enviado con éxito. <b>'.$customer->email. '</b></p>
>>>>>>> ba870f142a10dfa9c2b780a68c8b10f66b5cf35d
<div class="btn btn-success" id="contador"></div>
</div>
</body>
';

<<<<<<< HEAD
// Redirigir después de un retraso de 3 segundos y mostrar un contador
echo '<script type="text/javascript">
=======
  // Redirigir después de un retraso de 3 segundos y mostrar un contador
 echo '<script type="text/javascript">
>>>>>>> ba870f142a10dfa9c2b780a68c8b10f66b5cf35d
      var segundosRestantes = 3;
      function actualizarContador() {
          if (segundosRestantes > 0) {
              document.getElementById("contador").innerHTML = "Serás redirigido en: " + segundosRestantes + " segundos.";
              segundosRestantes--;
              setTimeout(actualizarContador, 1000); // Actualizar cada segundo (1000 milisegundos)
          } else {
             history.back();
          }
      }
      actualizarContador();
<<<<<<< HEAD
  </script>';
=======
  </script>'; 

?>

>>>>>>> ba870f142a10dfa9c2b780a68c8b10f66b5cf35d
