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
        add_action('wp_ajax_nopriv_midocdoc_get_informes', array($this, 'get_informes_ajax'));
        add_action('wp_ajax_midocdoc_get_informes', array($this, 'get_informes_ajax'));
    }

    public function enqueue_assets() {
        if (has_shortcode(get_the_content(), 'midocdoc_informes_medicos')) {
            wp_enqueue_style(
                'midocdoc-informes',
                self::PLUGIN_URL . 'css/informes.css',
                array(),
                self::VERSION
            );
            wp_enqueue_script(
                'midocdoc-informes',
                self::PLUGIN_URL . 'js/informes.js',
                array('jquery'),
                self::VERSION,
                true
            );
            wp_localize_script('midocdoc-informes', 'midocdocAjax', array(
                'ajaxurl' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('midocdoc_nonce')
            ));
        }
    }

    public function render_informes_medicos($atts) {
        if (!is_user_logged_in()) {
            return '<p>Debe iniciar sesión para ver sus informes médicos.</p>';
        }

        // Usar ID de paciente específico (ID 3) para pruebas
        $customer_id = 3;

        $midocdoc_model = new Midocdoc_Model();
        $informes = $midocdoc_model->get_informacion_completa_medico_by_patient($customer_id, 1, 20);

        ob_start();
        include self::PLUGIN_DIR . 'templates/public/informes-medicos.php';
        return ob_get_clean();
    }

    public function get_informes_ajax() {
        check_ajax_referer('midocdoc_nonce', 'nonce');

        $customer_id = intval($_POST['customer_id']);
        $page = intval($_POST['page']);
        $items_per_page = 20;

        $midocdoc_model = new Midocdoc_Model();
        try {
            $informes = $midocdoc_model->get_informacion_completa_medico_by_patient($customer_id, $page, $items_per_page);
            $total_informes = $midocdoc_model->get_total_informes($customer_id);

            wp_send_json_success(array(
                'informes' => $informes,
                'total' => $total_informes
            ));
        } catch (Exception $e) {
            error_log('Error fetching informes: ' . $e->getMessage());
            wp_send_json_error('Error fetching informes.');
        }
    }
}

// Initialize shortcodes
add_action('init', function() {
    MiDocDoc_Shortcodes::get_instance();
});