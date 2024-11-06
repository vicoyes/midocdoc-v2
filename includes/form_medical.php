<?php /* @var $customer OsCustomerModel */ ?>

<?php

session_start();
$id_paciente = isset($_SESSION['id_paciente']) ? $_SESSION['id_paciente'] : null;

$id = $id_paciente;

echo'<div class="tab">
<button class="tablinks" onclick="openForm(event, \'AntecedentesMedicos\')" id="tabs-citas-medicas">Antecedentes Médicos</button> 
<button class="tablinks" onclick="openForm(event, \'CitasMedicas\');">Informe Medico</button>
<button class="tablinks" onclick="openForm(event, \'Recetas\'); iniciarAgregarMedicamento();">Recetas</button>
</div>';


$user_id = get_current_user_id();
$user_info = get_userdata($user_id);
$first_name = $user_info->first_name;
$last_name = $user_info->last_name;


// Obtener el ID de la imagen de firma del usuario
$firma_id = get_user_meta($user_id, 'firma-usuario-id', true);

$customer = new OsCustomerModel($id);
$nombrePaciente = $customer->first_name . ' ' . $customer->last_name;
$nombreMedico =  ' Dr: '. $first_name . ' ' . $last_name;

?>


<div id="content-midocdoc">
<div class="formulario-citas-medicas-info">

    <div class="info-field">
        <label class="label-form cabecera" for="id_inform">Numero de Informe</label>
        <span id="id_inform">No Generado</span>
    </div>

    <div class="info-field">
        <label class="label-form cabecera" for="id_doctor">Datos del Medico Tratante</label>
        <span id="id_doctor"><?php echo ' Dr. ' . $first_name . ' ' . $last_name . '  ';  ?></span>
    </div>

    <div class="info-field">
        <label class="label-form cabecera" for="patient_id">Fecha del informe</label>
    <span id="fecha_informe"><?php echo date('Y-m-d'); ?></span>
    </div>
    
    <div class="info-field">
        <label class="label-form cabecera" for="patient_id">Nombre del Paciente</label>
        <span><?php echo $nombrePaciente; ?></span>
    </div>


</div>
<div id="CitasMedicas" class="tabcontent">
  <!-- Formulario Citas Médicas -->
  <form action=""  method="post" class="formulario-citas-medicas" id="citas-medicas" style="display:block;">
  
  <input type="text" name="id_patient" id="id_patient" value="<?php echo htmlspecialchars($id); ?>"  visibility="hidden">
 
  <h2 class="form-title">Información General de la Consulta</h2>


    <label class="label-form" for="purpose_consult">Motivo de la Consulta:</label>
    <input type="text" id="purpose_consult" class="campo-con-contador os-form-control" name="purpose_consult" maxlength="255"><br><br>

    <label class="label-form" for="external_cause">Descripción detallada de los síntomas:</label>
    <input type="text" id="external_cause" class="campo-con-contador os-form-control" name="external_cause" maxlength="255" ><br><br>

    <label class="label-form" for="reason_consult">Tratamientos previos:</label>
    <textarea id="reason_consult" name="reason_consult" class="os-form-control"></textarea><br><br>

     <h2 class="form-title">Diagnóstico Presuntivo</h2>
    <label class="label-form" for="current_condition">Diagnósticos iniciales: (basados en la evaluación clínica)</label>
    <textarea id="current_condition" name="current_condition" class="os-form-control"></textarea><br><br>
    
    <h2 class="form-title">Plan de Estudios y Tratamiento</h2>

    <label class="label-form" for="systems_review">Laboratorios y estudios de imagen:</label>
    <textarea id="systems_review" name="systems_review" class="os-form-control"></textarea><br><br>

    <!--<label class="label-form" for="general_state" style="display:none;">Estado General:</label>
    <input style="display:none;" type="text" id="general_state" class="campo-con-contador os-form-control" name="general_state" maxlength="255"><br><br>

    <label class="label-form" for="consciousness_state">Estado de Conciencia:</label>
    <input type="text" id="consciousness_state" class="campo-con-contador os-form-control" name="consciousness_state" maxlength="255"><br><br>-->

    <label class="label-form" for="biometric_data">Recomendaciones:</label>
    <textarea id="biometric_data" name="biometric_data" class="os-form-control"></textarea><br><br>

    <label class="label-form" for="diagnosis">Diagnóstico:</label>
    <textarea id="diagnosis" name="diagnosis" class="os-form-control"></textarea><br><br>

    <label class="label-form" for="management_plan">Plan de seguimiento:</label>
    <textarea id="management_plan" name="management_plan" class="os-form-control"></textarea><br><br>
    
    <h2 class="form-title">Notas y Reportes</h2>
    <label class="label-form" for="notes">Notas del Medico (Privado):</label>
    <textarea id="notes" name="notes" class="os-form-control"></textarea><br><br>

    <label class="label-form" for="report">Reporte:</label>
    <textarea id="report" name="report" class="os-form-control"></textarea><br><br>

    <h2 class="form-title">Información del Responsable</h2>
    <label class="label-form" for="responsible">Responsable:</label>
    <input type="text" id="responsible" name="responsible" class="os-form-control" value="<?php echo htmlspecialchars($nombreMedico); ?>" ><br><br>
    
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
    <input type="number" id="edad" name="edad" class="os-form-control"><br><br>

    <label class="label-form" for="fecha_nacimiento" class=" os-form-control">Fecha de Nacimiento:</label>
    <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" class="os-form-control"><br><br>

    <label class="label-form" for="genero" class=" os-form-control">Género:</label>
    <select id="genero" name="genero" class="os-form-control">
        <option value="">Seleccionar...</option>
        <option value="Femenino">Femenino</option>
        <option value="Masculino">Masculino</option>
        <option value="Otro">Otro</option>
    </select><br><br>
    
    <h2 class="form-title">Hábitos y Estilo de Vida</h2>
    <label class="label-form" for="fuma" class=" os-form-control">¿Fuma?</label>
    <select id="fuma" name="fuma" class="os-form-control">
        <option value="">Seleccionar...</option>
        <option value="1">Sí</option>
        <option value="0">No</option>
    </select><br><br>
    <label class="label-form" for="other_causes" class=" os-form-control">Otras Causas:</label>
    <textarea id="other_causes" name="other_causes" class="os-form-control" placeholder="podría incluir preguntas sobre dieta, ejercicio, consumo de alcohol, etc."></textarea><br><br>

    <h2 class="form-title">Historial Médico y Familiar</h2>
    <label class="label-form" for="numero_hijos" class=" os-form-control">Número de Hijos:</label>
    <input type="number" id="numero_hijos" class="campo-con-contador os-form-control" name="numero_hijos" maxlength="255"><br><br>

    <label class="label-form" for="family_history" class=" os-form-control">Historial Familiar:</label>
    <textarea id="family_history" name="family_history" class="os-form-control"></textarea><br><br>

    <label class="label-form" for="surgical_history" class=" os-form-control">Historial Quirúrgico:</label>
    <textarea id="surgical_history" name="surgical_history" class="os-form-control"></textarea><br><br>

    <label class="label-form" for="traumatic_history" class=" os-form-control">Historial Traumático:</label>
    <textarea id="traumatic_history" name="traumatic_history" class="os-form-control"></textarea><br><br>

    <label class="label-form" for="allergic_history" class=" os-form-control">Historial de Alergias:</label>
    <textarea id="allergic_history" name="allergic_history" class="os-form-control"></textarea><br><br>

    <label class="label-form" for="toxic_history" class=" os-form-control">Historial Tóxico:</label>
    <textarea id="toxic_history" name="toxic_history" class="os-form-control"></textarea><br><br>

    <label class="label-form" for="transfusion_history" class=" os-form-control">Historial de Transfusiones:</label>
    <textarea id="transfusion_history" name="transfusion_history" class="os-form-control"></textarea><br><br>

    <h2 class="form-title">Informes Adicionales</h2>

    <label class="label-form" for="report" class=" os-form-control">Reporte:</label>
    <textarea id="report" name="report" class="os-form-control"></textarea><br><br>

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
    <button type="button" id="btnAgregar" onclick="agregarMedicamento()"><i class="latepoint-icon latepoint-icon-plus-circle" ></i> Agregar Medicamento</button>

    <!-- Lista de medicamentos -->
    <div id="listaMedicamentos" class="lista-medicamentos">
        <!-- Los medicamentos agregados aparecerán aquí -->
    </div>

    <!-- Botón para crear receta 
    <button type="submit" id="recetas">Crear Receta</button> -->
</form>

</div>

</div>

<button onclick="mostrarPopup()" id="enviar-form-citas-medicas-abajo"  style="display:block;">Guardar Informe</button>

<div id="miPopup" class="popup">
    <div class="popup-contenido">
        <span class="cerrar" onclick="cerrarPopup()">&times;</span>
        <p style="text-align: center;">¿Estás seguro de que deseas guardar el informe médico?</p>
        <div id="mensajeRespuesta"></div>
        <div id="loading" style="display:none;">
            <div class="spinner-form"></div> 
            Guardando informe médico...
        </div>
        <div id="contenedor-botones-guarda">
            <input type="button" value="Cancelar" id="cancelar-informe" onclick="cerrarPopup()">
            <input type="button" value="Guardar" id="guardar-informe" onclick="citasmedicasform()">
        </div>
    </div>
</div>