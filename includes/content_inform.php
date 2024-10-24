<?php /* @var $customer OsCustomerModel */ ?>

<?php
$id_paciente = isset($_GET['id']) ? $_GET['id'] : 'No se proporcionó ID';
session_start();
$_SESSION['id_paciente'] = $id_paciente;

if (!defined('ABSPATH')) {
    exit;
}

$current_user = wp_get_current_user();
$current_user_id = $current_user->ID;

$customer = new OsCustomerModel($id_paciente);
$nombrePaciente = $customer->first_name . ' ' . $customer->last_name;

$midocdoc_model = new Midocdoc_Model();
$citas_medicas = $midocdoc_model->get_informacion_completa_medico($current_user_id, $id_paciente);

echo '<div id="inform_medical" pacient_id="' . $id_paciente . '"> <a class="latepoint-btn latepoint-btn-link ver-detalles" id="ver-detalles BtnInformNew" ><i class="latepoint-icon latepoint-icon-plus"></i> Nuevo informe</a>  </div>';

usort($citas_medicas->informes, function($a, $b) {
    $fechaHoraA = strtotime($a->report_date);
    $fechaHoraB = strtotime($b->report_date);

    if ($fechaHoraA == $fechaHoraB) {
        return $b->id - $a->id;
    }

    return $fechaHoraB - $fechaHoraA;
});

if (empty($citas_medicas)) {
    echo "<div class='appointment-card white-box-content'>No se encontraron citas médicas.</div>";
    echo '<div id="container-para-formulario"></div>';
    return;
}

$nombre_completo_medico = $current_user->first_name . ' ' . $current_user->last_name;

foreach ($citas_medicas->informes as $informe) {
    ?>
    <div class='appointment-card white-box-content' id='appointment-card-<?php echo $informe->id; ?>'>
        <div class='appointment-date'>
            <div class='appointment-actions'>
                <a href="?midocdoc_enviar_email=<?php echo $id_paciente; ?>&informe_id=<?php echo $informe->id; ?>" 
                   class='button-action'>
                    <i class='latepoint-icon latepoint-icon-message-square'></i> Enviar
                </a>
                <a href="?midocdoc_generar_pdf=<?php echo $informe->id; ?>" 
                   class='button-action' 
                   target='_blank'>
                    <i class='latepoint-icon latepoint-icon-paperclip'></i> Descargar
                </a>
                <a class='button-action edit_form_button'  onclick=' buttonEditForm(<?php echo $informe->id; ?>)'>
                    <i class='latepoint-icon latepoint-icon-edit-3'></i> Editar
                </a>
            </div>
        </div>
        
        <div class='appointment-details'>
            <h3><?php echo $nombrePaciente; ?></h3>
            <p class="paciente-name">Dr <?php echo $nombre_completo_medico; ?></p>
            
        </div>
        
        <div class='card-2'>
            <?php if (!empty($informe->detalles->citasMedicas)): ?>
                <?php foreach ($informe->detalles->citasMedicas as $citaMedica): ?>
                    <div class='diagnosis-section'>
                        <h4>Diagnóstico</h4>
                        <p><?php echo $citaMedica->diagnosis; ?></p>
                        <div class='footer-card'>
                        <p>
                <i class="latepoint-icon latepoint-icon-calendar"></i>
                <?php echo $informe->report_date; ?>
            </p>
            <p class='informe'>#<?php echo $informe->id; ?></p>
                        </div>
                        
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
    <?php
}

echo '<div id="container-para-formulario"></div>';
?>
