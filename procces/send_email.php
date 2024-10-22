<?php /* @var $customer OsCustomerModel */ ?>
<?php
if (!defined('ABSPATH')) {
    exit;
}

require_once 'generar-pdf.php';
require_once __DIR__ . '/../model/modelmidocdoc_pdf.php';
echo '<script src="https://unpkg.com/@dotlottie/player-component@latest/dist/dotlottie-player.mjs" type="module"></script>';

function midocdoc_enviar_email($id_paciente, $informe_id) {
    $user_id = get_current_user_id();
    $user_info = get_userdata($user_id);
    $first_name = $user_info->first_name;
    $last_name = $user_info->last_name;

    $carpetaDestino = 'uploads/midocdoc/reportes';
    $tituloPdf = 'reporte_medico_' . $informe_id . '.pdf';
    $rutaPdf = $carpetaDestino . '/' . $tituloPdf;
    $rutaNormalizada = str_replace('\\', '/', WP_CONTENT_DIR) . '/' . $rutaPdf;

    if (!file_exists($rutaNormalizada)) {
        midocdoc_generar_pdf($informe_id, TRUE);
    }

    if (empty($id_paciente)) {
        echo 'ID del paciente no proporcionado.';
        return;
    }

    $customer = new OsCustomerModel($id_paciente);

    if (!$customer) {
        echo 'No se pudo cargar la información del paciente.';
        return;
    }

    if (empty($customer->email) || empty($customer->first_name) || empty($customer->last_name)) {
        echo 'Información del paciente incompleta.';
        return;
    }

    if (!filter_var($customer->email, FILTER_VALIDATE_EMAIL)) {
        echo 'Correo electrónico no válido.';
        return;
    }

    $to = $customer->email;
    $subject = 'Informe Médico del Paciente ' . $customer->first_name . ' ' . $customer->last_name;
    $body = '<p>Estimado/a ' . $customer->first_name . ' ' . $customer->last_name . '</p>
    <p>Esperamos que se encuentre bien. Adjunto a este correo encontrará su informe médico personalizado, correspondiente a la consulta realizada. Le recomendamos revisar detenidamente toda la información y, en caso de tener alguna duda o requerir aclaraciones adicionales, no dude en ponerse en contacto con nosotros.</p>
    <p>Agradecemos la confianza depositada en nuestro servicio y nos reiteramos a su disposición para cualquier consulta futura.</p>
    <p>Atentamente,</p>
    <p><strong> Dr. ' . $first_name . ' ' . $last_name . '</strong></p>
    <p><strong>Medico Tratante</strong></p>';

    $headers = array('Content-Type: text/html; charset=UTF-8');
    $attachments = $rutaNormalizada;

    if (wp_mail($to, $subject, $body, $headers, $attachments)) {
        include(plugin_dir_path(__FILE__) . '../view/email_sent_success.php');
        exit;
    } else {
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
<p><dotlottie-player src="https://lottie.host/b820f58c-663c-4e6f-b3ab-7bddbb977e89/VAHQ0WmbvV.json" background="transparent" speed="1" style="width: 300px; height: 300px;" loop autoplay></dotlottie-player></p>
<p>No pudimos enviar el correo. <b>' . $customer->email . ' por favor intentelo nuevamente</b></p>
<div class="btn btn-success" id="contador"></div>
</div>
</body>';

        echo '<script type="text/javascript">
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
          </script>';
    }
}
