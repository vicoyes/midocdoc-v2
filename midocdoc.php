<?php
/*
Plugin Name: Mi Doctor Plugin
Description: Plugins informe del médico para Midocdoc importante: tiene que estar activo latepoint
Version: 2.0
Author: Hector Muñoz midocdoc
*/

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Incluir el archivo del modelo
require_once plugin_dir_path(__FILE__) . 'model/modelmidocdoc.php';

// Crear tablas personalizadas
function crear_tablas_personalizadas() {
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
            'appointment_date' => "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL",
            'responsible' => 'varchar(255) NOT NULL',
            'specialty' => 'varchar(255) NOT NULL',
            'patient_id' => 'int NOT NULL',
            'report' => 'text NOT NULL',
            'id_inform' => 'int NOT NULL',
            'id_doctor' => 'int NOT NULL',
            'id_patient' => 'int NOT NULL',
            'PRIMARY KEY' => '(id)'
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
            'PRIMARY KEY' => '(id)'
        ],
        'midocdoc_recetas' => [
            'id' => 'int NOT NULL AUTO_INCREMENT',
            'id_doctor' => 'int NOT NULL',
            'id_paciente' => 'int NOT NULL',
            'id_inform' => 'int NOT NULL',
            'fecha_receta' => 'datetime NOT NULL',
            'PRIMARY KEY' => '(id)'
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
            'postdated' => "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL",
            'requires_disability' => 'tinyint(1) NOT NULL',
            'PRIMARY KEY' => '(id_medicamento)',
            'FOREIGN KEY' => '(id_receta) REFERENCES ' . $wpdb->prefix . 'midocdoc_recetas(id)'
        ],
        'midocdoc_informes' => [
            'id' => 'int NOT NULL AUTO_INCREMENT',
            'report_date' => "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL",
            'id_doctor' => 'int NOT NULL',
            'id_patient' => 'int NOT NULL',
            'PRIMARY KEY' => '(id)'
        ]
    ];

    foreach ($tables as $table_name => $columns) {
        $table_name = $wpdb->prefix . $table_name;
        $sql = "CREATE TABLE $table_name (";
        foreach ($columns as $column => $definition) {
            $sql .= "$column $definition,";
        }
        $sql = rtrim($sql, ',') . ") $charset_collate;";
        dbDelta($sql);
    }
}

register_activation_hook(__FILE__, 'crear_tablas_personalizadas');

// Añadir scripts y estilos personalizados
function mi_custom_tabs_script() {
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
add_action('wp_enqueue_scripts', 'mi_custom_tabs_script');

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
function agregar_sidebar_y_scripts() {
    include plugin_dir_path(__FILE__) . 'includes/sidebar-medical-reports.php';
    include plugin_dir_path(__FILE__) . 'includes/sidebar-inform.php';
}

add_action('latepoint_init', 'agregar_sidebar_y_scripts');

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
    require_once plugin_dir_path(__FILE__) . 'procces/actualizar-informe.php';
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