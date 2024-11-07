<?php /* @var $customer OsCustomerModel */ ?>

<?php
require_once WP_PLUGIN_DIR . '/latepoint/lib/models/customer_model.php';
$idIform = isset($_GET['idinform']) ? $_GET['idinform'] : 'No se proporcionó ID';

// Prueba de la función get_informe_medico
$informe_medico = get_informe_medico($idIform);

if ($informe_medico) {
    // Variables para el array principal
    $id = $informe_medico['id']; // 11
    $report_date = $informe_medico['report_date']; // 2024-01-15 20:11:58
    $id_doctor = $informe_medico['id_doctor']; // 1
    $id_patient = $informe_medico['id_patient']; // 3

    // Variables para el primer elemento del array 'citas_medicas'
    $citas_medicas = $informe_medico['citas_medicas'][0];
    $type_consult = $citas_medicas['type_consult']; // Consulta Online
    $purpose_consult = $citas_medicas['purpose_consult']; // sadasdsa
    $external_cause = $citas_medicas['external_cause']; // dasdasdasd
    $reason_consult = $citas_medicas['reason_consult']; // asdasdas
    $current_condition = $citas_medicas['current_condition']; // asdasdasd
    $systems_review = $citas_medicas['systems_review']; // sadasdas
    $general_state = $citas_medicas['general_state']; // dasadasd
    $consciousness_state = $citas_medicas['consciousness_state']; // asdasdasd
    $biometric_data = $citas_medicas['biometric_data']; // asdasdasd
    $diagnosis = $citas_medicas['diagnosis']; // asdasdasdsa
    $management_plan = $citas_medicas['management_plan']; // asdasdas
    $notes = $citas_medicas['notes']; // asdasdas
    $appointment_date = $citas_medicas['appointment_date']; // 2024-01-15 20:11:58
    $responsible = $citas_medicas['responsible']; // Dr: Hector Munoz
    $specialty = $citas_medicas['specialty']; // (vacío)
    $patient_id = $citas_medicas['patient_id']; // 0
    $report = $citas_medicas['report']; // (vacío)
    $id_inform = $citas_medicas['id_inform']; // 11
    $id_doctor_cita = $citas_medicas['id_doctor']; // 1
    $id_patient_cita = $citas_medicas['id_patient']; // 3

    // Variables para el primer elemento del array 'antecedentes_medicos'
    $antecedentes_medicos = $informe_medico['antecedentes_medicos'][0];
    $antecedentes_id = $antecedentes_medicos['id']; // 10
    $other_causes = $antecedentes_medicos['other_causes']; // (vacío)
    $family_history = $antecedentes_medicos['family_history']; // (vacío)
    $surgical_history = $antecedentes_medicos['surgical_history']; // (vacío)
    $traumatic_history = $antecedentes_medicos['traumatic_history']; // (vacío)
    $allergic_history = $antecedentes_medicos['allergic_history']; // (vacío)
    $toxic_history = $antecedentes_medicos['toxic_history']; // (vacío)
    $transfusion_history = $antecedentes_medicos['transfusion_history']; // (vacío)
    $report_antecedentes = $antecedentes_medicos['report']; // (vacío)
    $id_inform_antecedentes = $antecedentes_medicos['id_inform']; // 11
    $id_doctor_antecedentes = $antecedentes_medicos['id_doctor']; // 1
    $id_patient_antecedentes = $antecedentes_medicos['id_patient']; // 3
    $edad = $antecedentes_medicos['edad']; // 0
    $fecha_nacimiento = $antecedentes_medicos['fecha_nacimiento']; // 0000-00-00
    $genero = $antecedentes_medicos['genero']; // (vacío)
    $fuma = $antecedentes_medicos['fuma']; // 0
    $numero_hijos = $antecedentes_medicos['numero_hijos']; // 0

    // Variables para el primer elemento del array 'recetas'
    $recetas = $informe_medico['recetas'][0];
    $receta_id = $recetas['id']; // 10
    $id_doctor_receta = $recetas['id_doctor']; // 1
    $id_paciente_receta = $recetas['id_paciente']; // 3
    $id_inform_receta = $recetas['id_inform']; // 11
    $fecha_receta = $recetas['fecha_receta']; // 0000-00-00 00:00:00

   // Variables para el array 'medicamentos'
   $medicamentos = !empty($recetas['medicamentos']) ? $recetas['medicamentos'] : [];

} else {
    echo 'Informe no encontrado';
}


echo'<div class="tab">
<button class="tablinks" onclick="openForm(event, \'AntecedentesMedicos\')" id="tabs-citas-medicas">Antecedentes Médicos</button> 
<button class="tablinks" onclick="openForm(event, \'CitasMedicas\'); citasmedicasform();" >Informe Medico</button>
<button class="tablinks" onclick="openForm(event, \'Recetas\'); iniciarAgregarMedicamento();">Recetas</button>
</div>';


$user_id = $informe_medico['id_doctor'];
$usuario_doctor = get_userdata($user_id);
$first_name_doctor = $usuario_doctor->first_name;
$last_name_doctor = $usuario_doctor->last_name;

$customer = new OsCustomerModel($informe_medico['id_patient']);
$nombrePaciente = $customer->first_name . ' ' . $customer->last_name;


// Obtener el ID de la imagen de firma del usuario
$firma_id = get_user_meta($user_id, 'firma-usuario-id', true);
?>

<div id="content-midocdoc">
<div class="formulario-citas-medicas-info">

    <div class="info-field">
        <label class="label-form cabecera" for="id_inform">Numero de Informe</label>
        <span id="id_inform">#<?php echo htmlspecialchars($idIform); ?></span>
    </div>

    <div class="info-field">
        <label class="label-form cabecera" for="id_doctor">Datos del Medico Tratante</label>
        <span id="id_doctor"><?php echo ' Dr. ' . $first_name_doctor . ' ' . $last_name_doctor . '  ';  ?></span>
    </div>

    <div class="info-field">
        <label class="label-form cabecera" for="patient_id">Fecha del Informe</label>
        <span><?php echo htmlspecialchars($informe_medico['report_date'])?></span>
    </div>
    
    <div class="info-field">
        <label class="label-form cabecera" for="patient_id">Nombre del Paciente</label>
        <span><?php echo htmlspecialchars($nombrePaciente) ?></span>
    </div>


</div>
<!-- Campos del formulario para editar la información del informe médico -->
<div id="CitasMedicas" class="tabcontent">
    <h2 class="form-title">Información General de la Consulta</h2>
    <p><strong>Motivo de la Consulta:</strong> <?php echo htmlspecialchars($purpose_consult ?? ''); ?></p>
    <p><strong>Descripción detallada de los síntomas:</strong> <?php echo htmlspecialchars($external_cause ?? ''); ?></p>
    <p><strong>Tratamientos previos:</strong> <?php echo htmlspecialchars($reason_consult ?? ''); ?></p>

    <h2 class="form-title">Diagnóstico Presuntivo</h2>
    <p><strong>Diagnósticos iniciales: (basados en la evaluación clínica)</strong> <?php echo htmlspecialchars($current_condition ?? ''); ?></p>
    
    <h2 class="form-title">Plan de Estudios y Tratamiento</h2>
    <p><strong>Laboratorios y estudios de imagen:</strong> <?php echo htmlspecialchars($systems_review ?? ''); ?></p>
    <p><strong>Recomendaciones:</strong> <?php echo htmlspecialchars($biometric_data ?? ''); ?></p>
    <p><strong>Diagnóstico:</strong> <?php echo htmlspecialchars($diagnosis ?? ''); ?></p>
    <p><strong>Plan de seguimiento:</strong> <?php echo htmlspecialchars($management_plan ?? ''); ?></p>
    
    <h2 class="form-title">Notas y Reportes</h2>
    <p><strong>Notas del Medico (Privado):</strong> <?php echo htmlspecialchars($notes ?? ''); ?></p>
    <p><strong>Reporte:</strong> <?php echo htmlspecialchars($report ?? ''); ?></p>

    <h2 class="form-title">Información del Responsable</h2>
    <p><strong>Responsable:</strong> <?php echo htmlspecialchars($responsible ?? ''); ?></p>
    
    <div id="firma_medico">
        <?php 
        // Verificar si existe una firma y mostrar la imagen
        if ($firma_id) {
            // Obtener la URL de la imagen de firma
            $firma_url = wp_get_attachment_url($firma_id);

            // Mostrar la imagen
            echo '<img src="' . esc_url($firma_url) . '" alt="Firma del Usuario">';
        } else {
            echo 'No hay una firma cargada.';
        }
        ?>
    </div>
</div>

<div id="AntecedentesMedicos" class="tabcontent">
    <h2 class="form-title">Información Personal y Demográfica</h2>
    <p><strong>Edad:</strong> <?php echo htmlspecialchars($edad ?? ''); ?></p>
    <p><strong>Fecha de Nacimiento:</strong> <?php echo htmlspecialchars($fecha_nacimiento ?? ''); ?></p>
    <p><strong>Género:</strong> <?php echo htmlspecialchars($genero ?? ''); ?></p>

    <h2 class="form-title">Hábitos y Estilo de Vida</h2>
    <p><strong>¿Fuma?</strong> <?php echo htmlspecialchars($fuma == '1' ? 'Sí' : 'No'); ?></p>
    <p><strong>Otras Causas:</strong> <?php echo htmlspecialchars($other_causes ?? ''); ?></p>

    <h2 class="form-title">Historial Médico y Familiar</h2>
    <p><strong>Número de Hijos:</strong> <?php echo htmlspecialchars($numero_hijos ?? ''); ?></p>
    <p><strong>Historial Familiar:</strong> <?php echo htmlspecialchars($family_history ?? ''); ?></p>
    <p><strong>Historial Quirúrgico:</strong> <?php echo htmlspecialchars($surgical_history ?? ''); ?></p>
    <p><strong>Historial Traumático:</strong> <?php echo htmlspecialchars($traumatic_history ?? ''); ?></p>
    <p><strong>Historial de Alergias:</strong> <?php echo htmlspecialchars($allergic_history ?? ''); ?></p>
    <p><strong>Historial Tóxico:</strong> <?php echo htmlspecialchars($toxic_history ?? ''); ?></p>
    <p><strong>Historial de Transfusiones:</strong> <?php echo htmlspecialchars($transfusion_history ?? ''); ?></p>

    <h2 class="form-title">Informes Adicionales</h2>
    <p><strong>Reporte:</strong> <?php echo htmlspecialchars($report_antecedentes ?? ''); ?></p>
</div>             

<div id="Recetas" class="tabcontent">
  <div class="formulario-receta">
        <h2>Medicamentos en la Receta</h2>
        <div id="listaMedicamentos" class="lista-medicamentos" data-id-receta="<?php echo htmlspecialchars($receta_id) ?>">
            <?php if (!empty($medicamentos)): ?>
                <?php foreach ($medicamentos as $medicamento): ?>
                    <div class="medicamento" data-id="<?php echo $medicamento['id_medicamento']; ?>">
                        <ul>
                            <li><strong>ID:</strong> <?php echo $medicamento['id_medicamento']; ?></li>
                            <li><strong>Descripción:</strong> <?php echo htmlspecialchars($medicamento['descricion']); ?></li>
                            <li><strong>Presentación:</strong> <?php echo htmlspecialchars($medicamento['presentation']); ?></li>
                            <li><strong>Concentración:</strong> <?php echo htmlspecialchars($medicamento['concentration']); ?></li>
                            <li><strong>Vía de Administración:</strong> <?php echo htmlspecialchars($medicamento['administration_route']); ?></li>
                            <li><strong>Cantidad:</strong> <?php echo htmlspecialchars($medicamento['quantity']); ?></li>
                            <li><strong>Dosificación:</strong> <?php echo htmlspecialchars($medicamento['dosage']); ?></li>
                        </ul>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No hay medicamentos agregados.</p>
            <?php endif; ?>
        </div>
  </div>
</div>