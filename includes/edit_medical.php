<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Función principal para cargar el contenido de edición
 */
function cargar_edit_medical_content() {
    // Validar que tenemos un ID de informe
    if (!isset($_GET['informe_id'])) {
        return 'ID de informe no proporcionado';
    }

    // Obtener el ID del informe
    $informe_id = intval($_GET['informe_id']);

    // Cargar datos necesarios
    $datos = obtener_datos_informe($informe_id);
    if (!$datos) {
        return 'No se encontró el informe solicitado';
    }

    // Extraer variables para usar en el template
    extract($datos);

    // Comenzar el output del formulario
    ob_start();
    ?>
    <div id="content-midocdoc">
        <!-- Información del informe -->
        <div class="formulario-citas-medicas-info">
            <div class="info-field">
                <label class="label-form" for="id_inform">ID Informe:</label>
                <span id="id_inform"><?php echo esc_html($informe_id); ?></span>
                <input type="hidden" id="informe_id" name="informe_id" value="<?php echo esc_attr($informe_id); ?>">
                <input type="hidden" id="editing_mode" name="editing_mode" value="1">
            </div>

            <div class="info-field">
                <label class="label-form" for="id_doctor">Datos del Medico Tratante:</label>
                <span id="id_doctor"><?php echo esc_html($nombre_medico); ?></span>
            </div>

            <div class="info-field">
                <label class="label-form" for="patient_id">ID del Paciente:</label>
                <span id="patient_id"><?php echo esc_html($informe->id_patient); ?></span>
            </div>

            <div class="info-field">
                <label class="label-form" for="nombre_paciente">Nombre del Paciente:</label>
                <span id="nombre_paciente"><?php echo esc_html($nombre_paciente); ?></span>
            </div>
        </div>

        <!-- Tabs de navegación -->
        <div class="tab">
            <button class="tablinks active" onclick="openForm(event, 'CitasMedicas'); citasmedicasform();" id="tabs-citas-medicas">
                Informe Médico
            </button>
            <button class="tablinks" onclick="openForm(event, 'AntecedentesMedicos')">
                Antecedentes Médicos
            </button>
            <button class="tablinks" onclick="openForm(event, 'Recetas'); iniciarAgregarMedicamento();">
                Recetas
            </button>
        </div>

        <!-- Contenido de las tabs -->
        <?php 
        // Cargar cada sección del formulario
        include(plugin_dir_path(__FILE__) . '../templates/form-citas-medicas.php');
        include(plugin_dir_path(__FILE__) . '../templates/form-antecedentes.php');
        include(plugin_dir_path(__FILE__) . '../templates/form-recetas.php');
        ?>

        <!-- Botones de acción -->
        <button onclick="mostrarPopup()" id="enviar-form-citas-medicas-abajo">Actualizar Informe</button>

        <!-- Pop-up de confirmación -->
        <div id="miPopup" class="popup">
            <div class="popup-contenido">
                <span class="cerrar" onclick="cerrarPopup()">&times;</span>
                <p>¿Estás seguro de que deseas actualizar el informe médico?</p>
                <div id="contenedor-botones-guarda">
                    <input type="button" value="Cancelar" id="cancelar-informe" onclick="cerrarPopup()">
                    <input type="submit" value="Actualizar" id="guardar-informe">
                </div>
                <div id="mensajeRespuesta"></div>
            </div>
        </div>
    </div>
    <?php
    return ob_get_clean();
}

/**
 * Función auxiliar para obtener todos los datos necesarios del informe
 */
function obtener_datos_informe($informe_id) {
    global $wpdb;

    // Obtener el informe principal
    $informe = $wpdb->get_row($wpdb->prepare(
        "SELECT * FROM {$wpdb->prefix}midocdoc_informes WHERE id = %d",
        $informe_id
    ));

    if (!$informe) {
        return false;
    }

    // Obtener datos de la cita médica
    $cita_medica = $wpdb->get_row($wpdb->prepare(
        "SELECT * FROM {$wpdb->prefix}midocdoc_citas_medicas WHERE id_inform = %d",
        $informe_id
    ));

    // Obtener antecedentes médicos
    $antecedentes = $wpdb->get_row($wpdb->prepare(
        "SELECT * FROM {$wpdb->prefix}midocdoc_antecedentes_medicos WHERE id_inform = %d",
        $informe_id
    ));

    // Obtener receta y medicamentos
    $receta = $wpdb->get_row($wpdb->prepare(
        "SELECT * FROM {$wpdb->prefix}midocdoc_recetas WHERE id_inform = %d",
        $informe_id
    ));

    $medicamentos = [];
    if ($receta) {
        $medicamentos = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}midocdoc_medicamentos WHERE id_receta = %d",
            $receta->id
        ));
    }

    // Obtener información del paciente
    $nombre_paciente = '';
    if (class_exists('OsCustomerModel')) {
        $customer = new OsCustomerModel($informe->id_patient);
        $nombre_paciente = $customer->first_name . ' ' . $customer->last_name;
    }

    // Obtener información del médico
    $user_id = get_current_user_id();
    $user_info = get_userdata($user_id);
    $nombre_medico = 'Dr: ' . $user_info->first_name . ' ' . $user_info->last_name;
    $firma_id = get_user_meta($user_id, 'firma-usuario-id', true);

    // Retornar todos los datos necesarios
    return array(
        'informe' => $informe,
        'cita_medica' => $cita_medica,
        'antecedentes' => $antecedentes,
        'receta' => $receta,
        'medicamentos' => $medicamentos,
        'nombre_paciente' => $nombre_paciente,
        'nombre_medico' => $nombre_medico,
        'firma_id' => $firma_id,
        'informe_id' => $informe_id
    );
}

// Si es una petición AJAX, cargar el contenido
if (defined('DOING_AJAX') && DOING_AJAX) {
    $content = cargar_edit_medical_content();
    echo $content;
    wp_die();
}