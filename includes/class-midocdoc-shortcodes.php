<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class MiDocDoc_Shortcodes {
    private static $instance = null;

    // Define constants
    const PLUGIN_URL = MIDOCDOC_PLUGIN_URL;
    const PLUGIN_DIR = MIDOCDOC_PLUGIN_DIR;
    const VERSION = '1.0.0';

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_shortcode('midocdoc_informes_medicos', array($this, 'render_informes_medicos'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_assets'));
    }

    public function enqueue_assets() {
        if (has_shortcode(get_the_content(), 'midocdoc_informes_medicos')) {
            wp_enqueue_style(
                'midocdoc-informes',
                self::PLUGIN_URL . 'css/informes.css',
                array(),
                self::VERSION
            );
        }
    }

    public function render_informes_medicos() {
        if (!is_user_logged_in()) {
            return '<p>Debe iniciar sesión para ver sus informes médicos.</p>';
        }

        // Usar ID de paciente específico (ID 3) para pruebas
        $customer_id = 3;

        $informes = $this->get_informes_medicos($customer_id);
        if (empty($informes->informes)) {
            return '<p>No hay informes médicos disponibles.</p>';
        }

        ob_start();
        $this->render_template($informes);
        return ob_get_clean();
    }

    private function get_customer_id() {
        global $wpdb;
        $current_user_id = get_current_user_id();
        return $wpdb->get_var($wpdb->prepare(
            "SELECT id FROM {$wpdb->prefix}latepoint_customers WHERE wp_user_id = %d",
            $current_user_id
        ));
    }

    private function get_informes_medicos($customer_id) {
        $midocdoc_model = new Midocdoc_Model();
        return $midocdoc_model->get_informacion_completa_medico_by_patient($customer_id);
    }

    private function render_template($informes) {
        include self::PLUGIN_DIR . 'templates/public/informes-medicos.php';
    }
}

// Initialize shortcodes
add_action('init', function() {
    MiDocDoc_Shortcodes::get_instance();
});