<?php /* @var $customer OsCustomerModel */ ?>

<?php
$idIform = isset($_GET['idinform']) ? $_GET['idinform'] : 'No se proporcionó ID';

// Prueba de la función get_informe_medico
$informe_medico = get_informe_medico($idIform);

if ($informe_medico) {
    // Mostrar el informe médico en un formato legible
    echo '<pre>';
    print_r($informe_medico);
    echo '</pre>';

    // Variables para el array principal
    $id = $informe_medico['id']; // 11
    $report_date = $informe_medico['report_date']; // 2024-01-15 20:11:58
    $id_doctor = $informe_medico['id_doctor']; // 1
    $id_patient = $informe_medico['id_patient']; // 3

    // Variables para el primer elemento del array 'citas_medicas'
    $citas_medicas = $informe_medico['citas_medicas'][0];
    $cita_id = $citas_medicas['id']; // 12
    $type_consult = $citas_medicas['type_consult']; // Consulta Online
    $purpose_consult = $citas_medicas['purpose_consult']; // sadasdsa
    $external_cause = $citas_medicas['external_cause']; // dasdasdasd
    $reason_consult = $citas_medicas['reason_consult']; // asdasdas
    $current_condition = $citas_medicas['current_condition']; // asdasdasd
    $systems_review = $citas_medicas['systems_review']; // sadasdas
    $medical_history_id = $citas_medicas['medical_history_id']; // 0
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

    // Variables para el array 'medicamentos' (que está vacío en este caso)
    $medicamentos = !empty($informe_medico['medicamentos']) ? $informe_medico['medicamentos'] : [];

} else {
    echo 'Informe no encontrado';
}


echo'<div class="tab">
<button class="tablinks" onclick="openForm(event, \'CitasMedicas\'); citasmedicasform();" id="tabs-citas-medicas">Informe Medico</button>
<button class="tablinks" onclick="openForm(event, \'AntecedentesMedicos\')">Antecedentes Médicos</button> 
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
<span><?php echo htmlspecialchars($informe_medico['report_date'])?></span>
<div class="formulario-citas-medicas-info">

    <div class="info-field">
        <label class="label-form cabecera" for="id_inform">ID Informe</label>
        <span id="id_inform"><?php echo htmlspecialchars($idIform); ?></span>
    </div>

    <div class="info-field">
        <label class="label-form cabecera" for="id_doctor">Datos del Medico Tratante</label>
        <span id="id_doctor"><?php echo ' Dr. ' . $first_name_doctor . ' ' . $last_name_doctor . '  ';  ?></span>
    </div>

    <div class="info-field">
        <label class="label-form cabecera" for="patient_id">ID del Paciente</label>
        <span id="patient_id"><?php echo htmlspecialchars($informe_medico['id_patient']); ?></span>
    </div>
    
    <div class="info-field">
        <label class="label-form cabecera" for="patient_id">Nombre del Paciente</label>
        <span><?php echo htmlspecialchars($nombrePaciente) ?></span>
    </div>


</div>
<!-- Campos del formulario para editar la información del informe médico -->
<div id="CitasMedicas" class="tabcontent">
<input type="hidden" id="registro_id" name="registro_id" value="<?php echo htmlspecialchars($informe_medico['id']); ?>">
<form action="" method="post" class="formulario-citas-medicas" id="citas-medicas" style="display:block;">
        <input type="hidden" name="id_patient" id="id_patient" value="<?php echo htmlspecialchars($id_patient_cita ?? ''); ?>" visibility="hidden">

        <h2 class="form-title">Información General de la Consulta</h2>

        <label class="label-form" for="purpose_consult">Objetivos de la Consulta:</label>
        <input type="text" id="purpose_consult" class="campo-con-contador os-form-control" name="purpose_consult" maxlength="255" value="<?php echo htmlspecialchars($purpose_consult ?? ''); ?>"><br><br>

        <label class="label-form" for="external_cause">Causa Externa:</label>
        <input type="text" id="external_cause" class="campo-con-contador os-form-control" name="external_cause" maxlength="255" value="<?php echo htmlspecialchars($external_cause ?? ''); ?>"><br><br>

        <label class="label-form" for="reason_consult">Razón de la Consulta:</label>
        <textarea id="reason_consult" name="reason_consult" class="os-form-control"><?php echo htmlspecialchars($reason_consult ?? ''); ?></textarea><br><br>

        <h2 class="form-title">Evaluación del Paciente</h2>
        <label class="label-form" for="current_condition">Condición Actual:</label>
        <textarea id="current_condition" name="current_condition" class="os-form-control"><?php echo htmlspecialchars($current_condition ?? ''); ?></textarea><br><br>

        <label class="label-form" for="systems_review">Revisión de Sistemas:</label>
        <textarea id="systems_review" name="systems_review" class="os-form-control"><?php echo htmlspecialchars($systems_review ?? ''); ?></textarea><br><br>

        <label class="label-form" for="general_state">Estado General:</label>
        <input type="text" id="general_state" class="campo-con-contador os-form-control" name="general_state" maxlength="255" value="<?php echo htmlspecialchars($general_state ?? ''); ?>"><br><br>

        <label class="label-form" for="consciousness_state">Estado de Conciencia:</label>
        <input type="text" id="consciousness_state" class="campo-con-contador os-form-control" name="consciousness_state" maxlength="255" value="<?php echo htmlspecialchars($consciousness_state ?? ''); ?>"><br><br>

        <h2 class="form-title">Datos Clínicos y Biométricos</h2>

        <label class="label-form" for="biometric_data">Datos Biométricos:</label>
        <textarea id="biometric_data" name="biometric_data" class="os-form-control"><?php echo htmlspecialchars($biometric_data ?? ''); ?></textarea><br><br>

        <h2 class="form-title">Diagnóstico y Plan de Manejo</h2>

        <label class="label-form" for="diagnosis">Diagnóstico:</label>
        <textarea id="diagnosis" name="diagnosis" class="os-form-control"><?php echo htmlspecialchars($diagnosis ?? ''); ?></textarea><br><br>

        <label class="label-form" for="management_plan">Plan de Manejo:</label>
        <textarea id="management_plan" name="management_plan" class="os-form-control"><?php echo htmlspecialchars($management_plan ?? ''); ?></textarea><br><br>
        
        <h2 class="form-title">Notas y Reportes</h2>
        <label class="label-form" for="notes">Notas del Medico (Privado):</label>
        <textarea id="notes" name="notes" class="os-form-control"><?php echo htmlspecialchars($notes ?? ''); ?></textarea><br><br>

        <label class="label-form" for="report">Reporte:</label>
        <textarea id="report" name="report" class="os-form-control"><?php echo htmlspecialchars($report ?? ''); ?></textarea><br><br>

        <h2 class="form-title">Información del Responsable</h2>
        <label class="label-form" for="responsible">Responsable:</label>
        <input type="text" id="responsible" name="responsible" class="os-form-control" value="<?php echo htmlspecialchars($responsible ?? ''); ?>"><br><br>
        
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
        <!--<div id="Contenedor-btn-enviar"><button class="latepoint-top-new-appointment-btn latepoint-btn latepoint-btn-primary siguiente" id="sigiuiente" onclick="openForm(event, 'AntecedentesMedicos')">Siguiente</button></div> -->
    </form>
</div>

<div id="AntecedentesMedicos" class="tabcontent">
<form action="" method="post" class="formulario-antecedentes-medicos" id="formulario-antecedentes-medicos">

<!-- Campos Editables -->
<h2 class="form-title">Información Personal y Demográfica</h2>
<label class="label-form" for="edad" class=" os-form-control">Edad:</label>
<input type="number" id="edad" name="edad" class="os-form-control" value="<?php echo htmlspecialchars($edad ?? ''); ?>"><br><br>

<label class="label-form" for="fecha_nacimiento" class=" os-form-control">Fecha de Nacimiento:</label>
<input type="date" id="fecha_nacimiento" name="fecha_nacimiento" class="os-form-control" value="<?php echo htmlspecialchars($fecha_nacimiento ?? ''); ?>"><br><br>

<label class="label-form" for="genero" class=" os-form-control">Género:</label>
<select id="genero" name="genero" class="os-form-control">
    <option value="">Seleccionar...</option>
    <option value="Femenino" <?php echo ($genero == 'Femenino') ? 'selected' : ''; ?>>Femenino</option>
    <option value="Masculino" <?php echo ($genero == 'Masculino') ? 'selected' : ''; ?>>Masculino</option>
    <option value="Otro" <?php echo ($genero == 'Otro') ? 'selected' : ''; ?>>Otro</option>
</select><br><br>

<h2 class="form-title">Hábitos y Estilo de Vida</h2>
<label class="label-form" for="fuma" class=" os-form-control">¿Fuma?</label>
<select id="fuma" name="fuma" class="os-form-control">
    <option value="">Seleccionar...</option>
    <option value="1" <?php echo ($fuma == '1') ? 'selected' : ''; ?>>Sí</option>
    <option value="0" <?php echo ($fuma == '0') ? 'selected' : ''; ?>>No</option>
</select><br><br>
<label class="label-form" for="other_causes" class=" os-form-control">Otras Causas:</label>
<textarea id="other_causes" name="other_causes" class="os-form-control" placeholder="podría incluir preguntas sobre dieta, ejercicio, consumo de alcohol, etc."><?php echo htmlspecialchars($other_causes ?? ''); ?></textarea><br><br>

<h2 class="form-title">Historial Médico y Familiar</h2>
<label class="label-form" for="numero_hijos" class=" os-form-control">Número de Hijos:</label>
<input type="number" id="numero_hijos" class="campo-con-contador os-form-control" name="numero_hijos" maxlength="255" value="<?php echo htmlspecialchars($numero_hijos ?? ''); ?>"><br><br>

<label class="label-form" for="family_history" class=" os-form-control">Historial Familiar:</label>
<textarea id="family_history" name="family_history" class="os-form-control"><?php echo htmlspecialchars($family_history ?? ''); ?></textarea><br><br>

<label class="label-form" for="surgical_history" class=" os-form-control">Historial Quirúrgico:</label>
<textarea id="surgical_history" name="surgical_history" class="os-form-control"><?php echo htmlspecialchars($surgical_history ?? ''); ?></textarea><br><br>

<label class="label-form" for="traumatic_history" class=" os-form-control">Historial Traumático:</label>
<textarea id="traumatic_history" name="traumatic_history" class="os-form-control"><?php echo htmlspecialchars($traumatic_history ?? ''); ?></textarea><br><br>

<label class="label-form" for="allergic_history" class=" os-form-control">Historial de Alergias:</label>
<textarea id="allergic_history" name="allergic_history" class="os-form-control"><?php echo htmlspecialchars($allergic_history ?? ''); ?></textarea><br><br>

<label class="label-form" for="toxic_history" class=" os-form-control">Historial Tóxico:</label>
<textarea id="toxic_history" name="toxic_history" class="os-form-control"><?php echo htmlspecialchars($toxic_history ?? ''); ?></textarea><br><br>

<label class="label-form" for="transfusion_history" class=" os-form-control">Historial de Transfusiones:</label>
<textarea id="transfusion_history" name="transfusion_history" class="os-form-control"><?php echo htmlspecialchars($transfusion_history ?? ''); ?></textarea><br><br>

<h2 class="form-title">Informes Adicionales</h2>

<label class="label-form" for="report" class=" os-form-control">Reporte:</label>
<textarea id="report" name="report" class="os-form-control"><?php echo htmlspecialchars($report_antecedentes ?? ''); ?></textarea><br><br>

<input type="submit" value="Guardar" style="display:none;">
</form>
</div>             

<div id="Recetas" class="tabcontent">
  <div class="formulario-receta">
        <h2>Añadir Medicamento a la Receta</h2>
 <form id="formReceta">
    <!-- Campos de la receta -->
    <div class="campo">
        <label class="label-form" for="id_doctor" style="display:none;">ID Doctor:</label>
        <input type="text" id="id_doctor" name="id_doctor" style="display:none;"  value="" disabled>
    </div>
    <div class="campo">
        <label class="label-form" for="id_paciente" style="display:none;">ID Paciente:</label>
        <input type="text" id="id_paciente" name="id_paciente" style="display:none;" value="<?php echo htmlspecialchars($id); ?>" disabled>
    </div>
    <div class="campo">
        <label class="label-form" for="fecha_receta">Fecha Receta:</label>
        <input type="date" id="fecha_receta" name="fecha_receta" class="os-form-control">
    </div>

    <!-- Campos de medicamento -->
    <div class="campo">
        <label class="label-form" for="descricion">Descripción (Nombre del Medicamento):</label>
        <input type="text" id="descricion" class="campo-con-contador campo-recetas os-form-control" name="descricion[]" maxlength="255">
    </div>
    <div class="campo">
        <label class="label-form" for="presentation">Presentación:</label>
        <input type="text" id="presentation" class="campo-con-contador campo-recetas os-form-control" name="presentation[]" maxlength="255">
    </div>
    <div class="campo">
        <label class="label-form" for="concentration">Concentración:</label>
        <input type="text" id="concentration" class="campo-con-contador campo-recetas os-form-control" name="concentration[]" maxlength="255">
    </div>
    <div class="campo">
        <label class="label-form" for="administration_route">Vía de Administración:</label>
        <input type="text" id="administration_route" class="campo-con-contador campo-recetas os-form-control"  name="administration_route[]" maxlength="255">
    </div>
    <div class="campo">
        <label class="label-form" for="quantity">Cantidad:</label>
        <input type="number" id="quantity" name="quantity[]" class="campo-con-contador campo-recetas os-form-control" maxlength="255">
    </div>
    <div class="campo">
        <label class="label-form" for="dosage">Dosificación:</label>
        <input type="text" id="dosage" name="dosage[]" class="campo-con-contador campo-recetas os-form-control" placeholder="Detallar las instrucciones" maxlength="255">
    </div>

    <!-- Botón para agregar medicamento -->
    <button type="button" id="btnAgregar"><i class="latepoint-icon latepoint-icon-plus-circle"></i> Agregar Medicamento</button>

<!-- Lista de medicamentos -->
<div id="listaMedicamentos" class="lista-medicamentos">
    <?php if (!empty($medicamentos)): ?>
        <?php foreach ($medicamentos as $medicamento): ?>
            <div class="medicamento">
                <ul>
                    <li>Descripción: <?php echo htmlspecialchars($medicamento['descricion']); ?></li>
                    <li>Presentación: <?php echo htmlspecialchars($medicamento['presentation']); ?></li>
                    <li>Concentración: <?php echo htmlspecialchars($medicamento['concentration']); ?></li>
                    <li>Vía de Administración: <?php echo htmlspecialchars($medicamento['administration_route']); ?></li>
                    <li>Cantidad: <?php echo htmlspecialchars($medicamento['quantity']); ?></li>
                    <li>Dosificación: <?php echo htmlspecialchars($medicamento['dosage']); ?></li>
                </ul>
                <button class="btn-eliminar" onclick="eliminarMedicamento(<?php echo $medicamento['id_medicamento']; ?>)">Eliminar</button>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No hay medicamentos agregados.</p>
    <?php endif; ?>
</div>

    <!-- Botón para crear receta 
    <button type="submit" id="recetas">Crear Receta</button> -->
</form>

</div>

</div>

<button onclick="mostrarPopup()" id="enviar-form-citas-medicas-abajo" style="display:none;">Actualizar Informe</button>

<div id="miPopup" class="popup">
    <div class="popup-contenido">
        <span class="cerrar" onclick="cerrarPopup()">&times;</span>
        <p>¿Estás seguro de que deseas actualizar el informe médico?</p>
        <div id="loading" style="display:none;">
            <div class="spinner-form"></div> Actualizando informe médico...
        </div>
        <div id="contenedor-botones-update">
            <input type="button" value="Cancelar" id="cancelar-informe" onclick="cerrarPopup()">
            <input type="button" value="Actualizar" id="actualizar-informe" onclick="actualizarCitasMedicasForm()">
        </div>
        <div id="mensajeRespuesta"></div>
        
    </div>
</div>
