<?php
function crear_informe_medico() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $path = preg_replace('/wp-content(?!.*wp-content).*/', '', __DIR__);
        require_once($path . 'wp-load.php');

        global $wpdb;

        // Obtener los datos del formulario
        $id_paciente = sanitize_text_field($_POST['id_paciente']);
        $id_doctor = get_current_user_id();
        $current_date = current_time('mysql');

        // ****** Citas Medicas *******
        $purpose_consult = isset($_POST['purpose_consult']) ? sanitize_text_field($_POST['purpose_consult']) : '';
        $external_cause = isset($_POST['external_cause']) ? sanitize_text_field($_POST['external_cause']) : '';
        $reason_consult = isset($_POST['reason_consult']) ? sanitize_text_field($_POST['reason_consult']) : '';
        $current_condition = isset($_POST['current_condition']) ? sanitize_text_field($_POST['current_condition']) : '';
        $systems_review = isset($_POST['systems_review']) ? sanitize_text_field($_POST['systems_review']) : '';
        $general_state = isset($_POST['general_state']) ? sanitize_text_field($_POST['general_state']) : '';
        $consciousness_state = isset($_POST['consciousness_state']) ? sanitize_text_field($_POST['consciousness_state']) : '';
        $biometric_data = isset($_POST['biometric_data']) ? sanitize_text_field($_POST['biometric_data']) : '';
        $diagnosis = isset($_POST['diagnosis']) ? sanitize_text_field($_POST['diagnosis']) : '';
        $management_plan = isset($_POST['management_plan']) ? sanitize_text_field($_POST['management_plan']) : '';
        $notes = isset($_POST['notes']) ? sanitize_text_field($_POST['notes']) : '';
        $report = isset($_POST['report']) ? sanitize_text_field($_POST['report']) : '';
        $responsible = isset($_POST['responsible']) ? sanitize_text_field($_POST['responsible']) : '';

        // Insertar el informe médico
        $table_name = $wpdb->prefix . 'midocdoc_informes';
        $wpdb->insert(
            $table_name,
            array(
                'report_date' => $current_date,
                'id_doctor' => $id_doctor,
                'id_patient' => $id_paciente,
            )
        );

        // Obtener el ID del informe recién creado
        $registro_id = $wpdb->insert_id;

        // Insertar citas médicas
        $table_name = $wpdb->prefix . 'midocdoc_citas_medicas';
        $wpdb->insert(
            $table_name,
            array(
                'id_inform' => $registro_id,
                'type_consult' => 'Consulta Online',
                'purpose_consult' => $purpose_consult,
                'external_cause' => $external_cause,
                'reason_consult' => $reason_consult,
                'current_condition' => $current_condition,
                'systems_review' => $systems_review,
                'general_state' => $general_state,
                'consciousness_state' => $consciousness_state,
                'biometric_data' => $biometric_data,
                'diagnosis' => $diagnosis,
                'management_plan' => $management_plan,
                'notes' => $notes,
                'report' => $report,
                'responsible' => $responsible,
                'id_doctor' => $id_doctor,
                'id_patient' => $id_paciente,
                'appointment_date' => $current_date,
            )
        );

        // ****** Antecedentes Medicos *******
        $other_causes = sanitize_text_field($_POST['other_causes']);
        $family_history = sanitize_text_field($_POST['family_history']);
        $surgical_history = sanitize_text_field($_POST['surgical_history']);
        $traumatic_history = sanitize_text_field($_POST['traumatic_history']);
        $allergic_history = sanitize_text_field($_POST['allergic_history']);
        $toxic_history = sanitize_text_field($_POST['toxic_history']);
        $transfusion_history = sanitize_text_field($_POST['transfusion_history']);
        $report = sanitize_text_field($_POST['report']);
        $edad = sanitize_text_field($_POST['edad']);
        $fecha_nacimiento = sanitize_text_field($_POST['fecha_nacimiento']);
        $genero = sanitize_text_field($_POST['genero']);
        $fuma = sanitize_text_field($_POST['fuma']);
        $numero_hijos = sanitize_text_field($_POST['numero_hijos']);

        $table_name_antecedentes = $wpdb->prefix . 'midocdoc_antecedentes_medicos';
        $wpdb->insert(
            $table_name_antecedentes,
            array(
                'id_inform' => $registro_id,
                'other_causes' => $other_causes,
                'family_history' => $family_history,
                'surgical_history' => $surgical_history,
                'traumatic_history' => $traumatic_history,
                'allergic_history' => $allergic_history,
                'toxic_history' => $toxic_history,
                'transfusion_history' => $transfusion_history,
                'report' => $report,
                'edad' => $edad,
                'fecha_nacimiento' => $fecha_nacimiento,
                'genero' => $genero,
                'fuma' => $fuma,
                'numero_hijos' => $numero_hijos,
            )
        );

        // ****** Recetas y Medicamentos *******
        $id_doctor_recetas = isset($_POST['id_doctor']) ? sanitize_text_field($_POST['id_doctor']) : $id_doctor;
        $id_paciente_recetas = isset($_POST['id_paciente']) ? sanitize_text_field($_POST['id_paciente']) : $id_paciente;
        $fecha_receta = sanitize_text_field($_POST['fecha_receta']);

        $table_name_recetas = $wpdb->prefix . 'midocdoc_recetas';
        $wpdb->insert(
            $table_name_recetas,
            array(
                'id_inform' => $registro_id,
                'id_doctor' => $id_doctor_recetas,
                'id_paciente' => $id_paciente_recetas,
                'fecha_receta' => $fecha_receta,
            )
        );

        // Obtener el ID de la receta recién creada
        $receta_id = $wpdb->insert_id;

        if ($wpdb->last_error) {
            echo 'Error al crear la información del paciente: ' . $wpdb->last_error;
        } else {
            echo '<div id="Respuestas-servidor" id-inform="' . $registro_id . '"><b>La información del paciente se creó correctamente.</b></div>';
        }

        // Manejo de nuevos medicamentos
        if (isset($_POST['medicamentos'])) {
            $nuevos_medicamentos = json_decode(stripslashes($_POST['medicamentos']), true);
            if ($nuevos_medicamentos !== null && json_last_error() === JSON_ERROR_NONE) {
                $table_name_medicamentos = $wpdb->prefix . 'midocdoc_medicamentos';
                foreach ($nuevos_medicamentos as $medicamento) {
                    // Verificar la existencia de las claves en el array
                    $descricion = isset($medicamento['descricion']) ? sanitize_text_field($medicamento['descricion']) : '';
                    $presentation = isset($medicamento['presentation']) ? sanitize_text_field($medicamento['presentation']) : '';
                    $concentration = isset($medicamento['concentration']) ? sanitize_text_field($medicamento['concentration']) : '';
                    $administration_route = isset($medicamento['administration_route']) ? sanitize_text_field($medicamento['administration_route']) : '';
                    $quantity = isset($medicamento['quantity']) ? sanitize_text_field($medicamento['quantity']) : '';
                    $dosage = isset($medicamento['dosage']) ? sanitize_text_field($medicamento['dosage']) : '';

                    $wpdb->insert(
                        $table_name_medicamentos,
                        array(
                            'id_receta' => $receta_id,
                            'descricion' => $descricion,
                            'presentation' => $presentation,
                            'concentration' => $concentration,
                            'administration_route' => $administration_route,
                            'quantity' => $quantity,
                            'dosage' => $dosage,
                            'postdated' => $fecha_receta,
                            'id_inform' => $registro_id, // Asegúrate de que id_inform se está enviando correctamente
                            'requires_disability' => 0 // Ajusta según sea necesario
                        )
                    );
                }
            } else {
                echo "Error al decodificar los nuevos medicamentos: " . json_last_error_msg();
            }
        } else {
            echo "No se recibieron nuevos medicamentos";
        }
    }
    wp_die(); // Esta función debe terminar con wp_die() para que WordPress sepa que la solicitud ha terminado
}
?>