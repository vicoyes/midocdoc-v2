<?php
/*
Plugin Name: Mi Doctor Plugin
Description: Plugins informe del médico para Midocdoc importante: tiene que estar activo latepoint
Version: 2.0.3
Author: Hector Muñoz midocdoc
*/

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Incluir el archivo del modelo
require_once plugin_dir_path(__FILE__) . 'model/modelmidocdoc.php';

// Crear tablas personalizadas
function crear_tablas_personalizadas() {
    ob_start(); // Iniciar el buffer de salida

    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

    $tables = [
        'midocdoc_citas_medicas' => [
            'id' => 'int NOT NULL AUTO_INCREMENT',
            'type_consult' => 'varchar(255) NOT NULL',
            'purpose_consult' => 'varchar(255) NOT NULL',
            'external_cause' => 'varchar(255) NOT NULL',
            'reason_consult' => 'text NOT NULL',
            'current_condition' => 'text NOT NULL',
            'systems_review' => 'text NOT NULL',
            'medical_history_id' => 'int NOT NULL',
            'general_state' => 'varchar(255) NOT NULL',
            'consciousness_state' => 'varchar(255) NOT NULL',
            'biometric_data' => 'text NOT NULL',
            'diagnosis' => 'text NOT NULL',
            'management_plan' => 'text NOT NULL',
            'notes' => 'text NOT NULL',
            'appointment_date' => "datetime NOT NULL",
            'responsible' => 'varchar(255) NOT NULL',
            'specialty' => 'varchar(255) NOT NULL',
            'patient_id' => 'int NOT NULL',
            'report' => 'text NOT NULL',
            'id_inform' => 'int NOT NULL',
            'id_doctor' => 'int NOT NULL',
            'id_patient' => 'int NOT NULL',
            'PRIMARY KEY' => 'id'
        ],
        'midocdoc_antecedentes_medicos' => [
            'id' => 'int NOT NULL AUTO_INCREMENT',
            'other_causes' => 'text NOT NULL',
            'family_history' => 'text NOT NULL',
            'surgical_history' => 'text NOT NULL',
            'traumatic_history' => 'text NOT NULL',
            'allergic_history' => 'text NOT NULL',
            'toxic_history' => 'text NOT NULL',
            'transfusion_history' => 'text NOT NULL',
            'report' => 'text NOT NULL',
            'id_inform' => 'int NOT NULL',
            'id_doctor' => 'int NOT NULL',
            'id_patient' => 'int NOT NULL',
            'edad' => 'tinyint(4)',
            'fecha_nacimiento' => 'date',
            'genero' => 'varchar(50)',
            'fuma' => 'tinyint(1)',
            'numero_hijos' => 'smallint(5)',
            'PRIMARY KEY' => 'id'
        ],
        'midocdoc_recetas' => [
            'id' => 'int NOT NULL AUTO_INCREMENT',
            'id_doctor' => 'int NOT NULL',
            'id_paciente' => 'int NOT NULL',
            'id_inform' => 'int NOT NULL',
            'fecha_receta' => 'datetime NOT NULL',
            'PRIMARY KEY' => 'id'
        ],
        'midocdoc_medicamentos' => [
            'id_medicamento' => 'int NOT NULL AUTO_INCREMENT',
            'id_receta' => 'int NOT NULL',
            'descricion' => 'varchar(255) NOT NULL',
            'presentation' => 'varchar(255) NOT NULL',
            'concentration' => 'varchar(255) NOT NULL',
            'administration_route' => 'varchar(255) NOT NULL',
            'quantity' => 'int NOT NULL',
            'dosage' => 'varchar(255) NOT NULL',
            'id_inform' => 'int NOT NULL',
            'postdated' => "datetime NOT NULL",
            'requires_disability' => 'tinyint(1) NOT NULL',
            'PRIMARY KEY' => 'id_medicamento',
            'FOREIGN KEY' => '(id_receta) REFERENCES ' . $wpdb->prefix . 'midocdoc_recetas(id)'
        ],
        'midocdoc_informes' => [
            'id' => 'int NOT NULL AUTO_INCREMENT',
            'report_date' => "datetime NOT NULL",
            'id_doctor' => 'int NOT NULL',
            'id_patient' => 'int NOT NULL',
            'PRIMARY KEY' => 'id'
        ]
    ];

    foreach ($tables as $table_name => $columns) {
        $table_name = $wpdb->prefix . $table_name;
        $columns_sql = [];
        foreach ($columns as $column => $definition) {
            $columns_sql[] = "$column $definition";
        }
        $sql = "CREATE TABLE $table_name (" . implode(', ', $columns_sql) . ") $charset_collate;";
        dbDelta($sql);
    }

    ob_end_clean(); // Limpiar el buffer de salida
}

register_activation_hook(__FILE__, 'crear_tablas_personalizadas');

// Añadir scripts y estilos personalizados
function mi_custom_tabs_script() {
    ?>
<div class="overlay-inform"></div>
<div id="sidebar-form">
    <button id="close-btn-inform" class="close-btn-inform" onclick="closeInformSidebar()">&times;</button>
    <div id="container-para-formulario">
        <!-- Aquí se cargará el formulario -->
    </div>
</div>

<div class="sidebar-overlay"></div>
<div id="medicalReportsSidebar" class="sidebar" style="display:none;">
    <div class="sidebar-content">
        <span id="closeSidebar" class="close-btn" onclick="closeMedicalReportsSidebar()">&times;</span>
        <h2>Informes Médicos</h2>
        <div id="medicalReportsContent">
            <!-- Aquí se cargará el contenido del informe médico dinámicamente -->
        </div>
    </div>
</div>
    <?php
    $css_files = ['latepoin_inform.css'];
    $js_files = ['table_new.js', 'sidebar.js', 'edit-medical.js'];

    foreach ($css_files as $css) {
        wp_enqueue_style('mi-plugin-css-' . $css, plugin_dir_url(__FILE__) . 'css/' . $css);
    }

    foreach ($js_files as $js) {
        wp_enqueue_script('mi-plugin-js-' . $js, plugin_dir_url(__FILE__) . 'js/' . $js, ['jquery'], false, true);
    }

    wp_localize_script('mi-plugin-js-table_new.js', 'datosAjax', ['ajaxurl' => admin_url('admin-ajax.php')]);
}

add_action('latepoint_top_bar_before_actions', 'mi_custom_tabs_script');
//add_action('wp_enqueue_scripts', 'mi_custom_tabs_script');

// Cargar contenido específico (AJAX o directamente)
function cargar_inform_content() {
    $id = isset($_GET['id']) ? $_GET['id'] : 'No se proporcionó ID';
    include(plugin_dir_path(__FILE__) . 'includes/content_inform.php');
    wp_die();
}

add_action('wp_ajax_cargar_inform_content', 'cargar_inform_content');
add_action('wp_ajax_nopriv_cargar_inform_content', 'cargar_inform_content');

// Cargar formulario de editar informe (AJAX)
function cargar_edit_medical_content() {
   $idIform = isset($_GET['idinform']) ? $_GET['idinform'] : 'No se proporcionó ID';
    include(plugin_dir_path(__FILE__) . 'includes/edit_medical.php');
    wp_die();
}

add_action('wp_ajax_cargar_edit_medical_content', 'cargar_edit_medical_content');
add_action('wp_ajax_nopriv_cargar_edit_medical_content', 'cargar_edit_medical_content');

// Cargar formulario médico (AJAX)
function cargar_form_medical_content() {
    $id = isset($_GET['id']) ? $_GET['id'] : 'No se proporcionó ID';
    include(plugin_dir_path(__FILE__) . 'includes/form_medical.php');
    wp_die();
}

add_action('wp_ajax_cargar_form_medical_content', 'cargar_form_medical_content');
add_action('wp_ajax_nopriv_cargar_form_medical_content', 'cargar_form_medical_content');

// Agregar sidebar y scripts
/*function agregar_sidebar_y_scripts() {
    // Verificar si los archivos existen antes de incluirlos
    //$sidebar_medical_reports_path = plugin_dir_path(__FILE__) . 'includes/sidebar-medical-reports.php';
    //$sidebar_inform_path = plugin_dir_path(__FILE__) . 'includes/sidebar-inform.php';

    if (file_exists($sidebar_medical_reports_path)) {
        include $sidebar_medical_reports_path;
    } else {
        error_log("El archivo sidebar-medical-reports.php no existe.");
    }

    if (file_exists($sidebar_inform_path)) {
        include $sidebar_inform_path;
    } else {
        error_log("El archivo sidebar-inform.php no existe.");
    }
}

add_action('latepoint_init', 'agregar_sidebar_y_scripts');*/

// Firma de usuario
function permitir_carga_archivos_en_perfil() {
    echo 'enctype="multipart/form-data"';
}

add_action('user_edit_form_tag', 'permitir_carga_archivos_en_perfil');

function agregar_campo_firma_usuario($user) {
    ?>
    <h3><?php _e("Firma del Usuario", "text-domain"); ?></h3>
    <table class="form-table">
        <tr>
            <th><label for="firma-usuario"><?php _e("Subir Firma", "text-domain"); ?></label></th>
            <td>
                <input type="file" name="firma-usuario" id="firma-usuario" /><br />
                <?php
                $firma_id = get_user_meta($user->ID, 'firma-usuario-id', true);
                if ($firma_id) {
                    echo wp_get_attachment_image($firma_id);
                }
                ?>
            </td>
        </tr>
    </table>
    <?php
}

add_action('show_user_profile', 'agregar_campo_firma_usuario');
add_action('edit_user_profile', 'agregar_campo_firma_usuario');

function guardar_campo_firma_usuario($user_id) {
    if (!current_user_can('edit_user', $user_id)) {
        return false;
    }

    require_once(ABSPATH . 'wp-admin/includes/image.php');
    require_once(ABSPATH . 'wp-admin/includes/file.php');
    require_once(ABSPATH . 'wp-admin/includes/media.php');

    if (!empty($_FILES['firma-usuario']['name'])) {
        $attach_id = media_handle_upload('firma-usuario', 0);
        if (is_wp_error($attach_id)) {
            wp_die('Error al subir archivo: ' . $attach_id->get_error_message());
        } else {
            update_user_meta($user_id, 'firma-usuario-id', $attach_id);
        }
    }
}

add_action('personal_options_update', 'guardar_campo_firma_usuario');
add_action('edit_user_profile_update', 'guardar_campo_firma_usuario');

// Manejar solicitud AJAX para 'form_medical.php'
function manejar_ajax_mi_accion_custom() {
    include_once plugin_dir_path(__FILE__) . 'includes/form_medical.php';
    wp_die();
}

add_action('wp_ajax_mi_accion_custom', 'manejar_ajax_mi_accion_custom');

// Generar PDF
function midocdoc_generar_pdf_handler() {
    if (isset($_GET['midocdoc_generar_pdf'])) {
        $id_reporte = intval($_GET['midocdoc_generar_pdf']);
        require_once plugin_dir_path(__FILE__) . 'procces/generar-pdf.php';
        midocdoc_generar_pdf($id_reporte);
        exit;
    }

    if (isset($_GET['midocdoc_enviar_email'])) {
        $id_paciente = intval($_GET['midocdoc_enviar_email']);
        $informe_id = isset($_GET['informe_id']) ? intval($_GET['informe_id']) : null;
        require_once plugin_dir_path(__FILE__) . 'procces/send_email.php';
        midocdoc_enviar_email($id_paciente, $informe_id);
        exit;
    }

    if (isset($_GET['action']) && $_GET['action'] === 'midocdoc_eliminar_pdf') {
        $id_reporte = intval($_GET['midocdoc_eliminar_pdf']);
        if (midocdoc_eliminar_pdf($id_reporte)) {
            echo 'PDF eliminado correctamente';
        } else {
            echo 'Error al eliminar PDF';
        }
        exit;
    }
    
}

// funcion para eliminar pdf
function midocdoc_eliminar_pdf($id_reporte) {
    $upload_dir = wp_upload_dir();
    $baseDir = $upload_dir['basedir'];
    $carpetaDestino = $baseDir . '/midocdoc/reportes';
    $tituloPdf = 'reporte_medico_' . $id_reporte . '.pdf';
    $rutaPdf = $carpetaDestino . '/' . $tituloPdf;

    if (file_exists($rutaPdf)) {
        if (unlink($rutaPdf)) {
            return true;
        } else {
            return false;
        }
    } else {
        return false;
    }
}


add_action('init', 'midocdoc_generar_pdf_handler');

// Agregar el manejador para la edición
function midocdoc_edit_handler() {
    if (isset($_GET['midocdoc_editar'])) {
        require_once plugin_dir_path(__FILE__) . 'includes/edit_medical.php';
        cargar_edit_medical_content();
        exit;
    }
}
add_action('init', 'midocdoc_edit_handler');

// Agregar el endpoint AJAX para actualización
function manejar_ajax_actualizar_informe() {
    require_once plugin_dir_path(__FILE__) . 'formulario-medical-update.php';
    actualizar_informe_medico();
}
add_action('wp_ajax_actualizar_informe_medico', 'manejar_ajax_actualizar_informe');

// Registrar el script de edición
function registrar_script_edicion() {
    wp_enqueue_script(
        'midocdoc-edit',
        plugin_dir_url(__FILE__) . 'js/edit-medical.js',
        array('jquery'),
        '1.0',
        true
    );

    wp_localize_script('midocdoc-edit', 'midocdocAjax', array(
        'ajaxurl' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('midocdoc_update_report')
    ));
}
add_action('admin_enqueue_scripts', 'registrar_script_edicion');

// Método para obtener informe médico
function get_informe_medico($idIform) {
    global $wpdb;

    // Nombre de las tablas
    $table_informes = $wpdb->prefix . 'midocdoc_informes';
    $table_citas_medicas = $wpdb->prefix . 'midocdoc_citas_medicas';
    $table_antecedentes_medicos = $wpdb->prefix . 'midocdoc_antecedentes_medicos';
    $table_recetas = $wpdb->prefix . 'midocdoc_recetas';
    $table_medicamentos = $wpdb->prefix . 'midocdoc_medicamentos';

    // Obtener el informe médico
    $informe_medico = $wpdb->get_row(
        $wpdb->prepare("SELECT * FROM $table_informes WHERE id = %d", $idIform),
        ARRAY_A
    );

    if (!$informe_medico) {
        return null; // Informe no encontrado
    }

    // Obtener citas médicas
    $citas_medicas = $wpdb->get_results(
        $wpdb->prepare("SELECT * FROM $table_citas_medicas WHERE id_inform = %d", $idIform),
        ARRAY_A
    );

    // Obtener antecedentes médicos
    $antecedentes_medicos = $wpdb->get_results(
        $wpdb->prepare("SELECT * FROM $table_antecedentes_medicos WHERE id_inform = %d", $idIform),
        ARRAY_A
    );

    // Obtener recetas y sus medicamentos
    $recetas = $wpdb->get_results(
        $wpdb->prepare("SELECT * FROM $table_recetas WHERE id_inform = %d", $idIform),
        ARRAY_A
    );

    foreach ($recetas as &$receta) {
        $medicamentos = $wpdb->get_results(
            $wpdb->prepare("SELECT * FROM $table_medicamentos WHERE id_receta = %d", $receta['id']),
            ARRAY_A
        );
        $receta['medicamentos'] = $medicamentos;
    }

    // Combinar toda la información
    $informe_medico['citas_medicas'] = $citas_medicas;
    $informe_medico['antecedentes_medicos'] = $antecedentes_medicos;
    $informe_medico['recetas'] = $recetas;

    return $informe_medico;
}
?>