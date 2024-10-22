<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Midocdoc_Reporte_Model {
    private $wpdb;

    public function __construct() {
        global $wpdb;
        $this->wpdb = $wpdb;
    }

    public function get_informacion_completa_medico($id_doctor, $id_paciente = null) {
        $informacionMedico = new stdClass();
        $informacionMedico->informes = $this->get_informes($id_doctor, $id_paciente);

        foreach ($informacionMedico->informes as $informe) {
            $informe->detalles = $this->get_detalle_informe($informe->id);
        }

        return $informacionMedico;
    }

    public function get_informe_por_id($id_informe) {
        $informe = $this->get_informe($id_informe);

        if ($informe) {
            $informe->detalles = $this->get_detalle_informe($informe->id);
            return $informe;
        }

        return null;
    }

    private function get_informes($id_doctor, $id_paciente = null) {
        $table_name = $this->wpdb->prefix . 'midocdoc_informes';
        $query = "SELECT * FROM $table_name WHERE id_doctor = %d";

        if ($id_paciente !== null) {
            $query = $this->wpdb->prepare($query . " AND id_patient = %d", $id_doctor, $id_paciente);
        } else {
            $query = $this->wpdb->prepare($query, $id_doctor);
        }

        return $this->wpdb->get_results($query);
    }

    private function get_informe($id_informe) {
        $table_name = $this->wpdb->prefix . 'midocdoc_informes';
        $query = $this->wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id_informe);
        return $this->wpdb->get_row($query);
    }

    private function get_detalle_informe($id_informe) {
        $detalleInforme = new stdClass();
        $detalleInforme->citasMedicas = $this->get_citas_medicas_por_informe($id_informe);
        $detalleInforme->antecedentesMedicos = $this->get_antecedentes_medicos_por_informe($id_informe);
        $detalleInforme->recetasConMedicamentos = $this->get_recetas_con_medicamentos_por_informe($id_informe);
        return $detalleInforme;
    }

    private function get_citas_medicas_por_informe($id_informe) {
        $table_name = $this->wpdb->prefix . 'midocdoc_citas_medicas';
        $query = $this->wpdb->prepare("SELECT * FROM $table_name WHERE id_inform = %d", $id_informe);
        return $this->wpdb->get_results($query);
    }

    private function get_antecedentes_medicos_por_informe($id_informe) {
        $table_name = $this->wpdb->prefix . 'midocdoc_antecedentes_medicos';
        $query = $this->wpdb->prepare("SELECT * FROM $table_name WHERE id_inform = %d", $id_informe);
        return $this->wpdb->get_results($query);
    }

    private function get_recetas_por_informe($id_informe) {
        $table_name_recetas = $this->wpdb->prefix . 'midocdoc_recetas';
        $query = $this->wpdb->prepare("SELECT * FROM $table_name_recetas WHERE id_inform = %d", $id_informe);
        return $this->wpdb->get_results($query);
    }

    private function get_medicamentos_por_receta($id_receta) {
        $table_name_medicamentos = $this->wpdb->prefix . 'midocdoc_medicamentos';
        $query = $this->wpdb->prepare("SELECT * FROM $table_name_medicamentos WHERE id_receta = %d", $id_receta);
        return $this->wpdb->get_results($query);
    }

    private function get_recetas_con_medicamentos_por_informe($id_informe) {
        $recetas = $this->get_recetas_por_informe($id_informe);
        foreach ($recetas as $key => $receta) {
            $recetas[$key]->medicamentos = $this->get_medicamentos_por_receta($receta->id);
        }
        return $recetas;
    }
}