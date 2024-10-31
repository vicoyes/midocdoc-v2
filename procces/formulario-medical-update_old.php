<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $path = preg_replace('/wp-content(?!.*wp-content).*/', '', __DIR__);
    require_once($path . 'wp-load.php');

    global $wpdb;

    // Obtener el ID del registro que se va a actualizar
    $registro_id = sanitize_text_field($_POST['registro_id']);

    // ****** Citas Medicas *******
    $purpose_consult = sanitize_text_field($_POST['purpose_consult']);
    $external_cause = sanitize_text_field($_POST['external_cause']);
    $reason_consult = sanitize_text_field($_POST['reason_consult']);
    $current_condition = sanitize_text_field($_POST['current_condition']);
    $systems_review = sanitize_text_field($_POST['systems_review']);
    $general_state = sanitize_text_field($_POST['general_state']);
    $consciousness_state = sanitize_text_field($_POST['consciousness_state']);
    $biometric_data = sanitize_text_field($_POST['biometric_data']);
    $diagnosis = sanitize_text_field($_POST['diagnosis']);
    $management_plan = sanitize_text_field($_POST['management_plan']);
    $notes = sanitize_text_field($_POST['notes']);
    $report = sanitize_text_field($_POST['report']);
    $responsible = sanitize_text_field($_POST['responsible']);
    $id_patient = sanitize_text_field($_POST['id_patient']);

    $current_user = wp_get_current_user();
    $user_id = $current_user->ID;
    $current_date = current_time('mysql');
    $fechaReceta = sanitize_text_field($_POST['fecha_receta']);

    // Actualizar el informe médico
    $table_name = $wpdb->prefix . 'midocdoc_informes';
    $wpdb->update(
        $table_name,
        array(
            'report_date' => $current_date,
            'id_doctor' => $user_id,
            'id_patient' => $id_patient,
        ),
        array('id' => $registro_id)
    );

    // Actualizar citas médicas
    $table_name = $wpdb->prefix . 'midocdoc_citas_medicas';
    $wpdb->update(
        $table_name,
        array(
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
            'id_doctor' => $user_id,
            'id_patient' => $id_patient,
            'appointment_date' => $current_date,
        ),
        array('id_inform' => $registro_id)
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
    $wpdb->update(
        $table_name_antecedentes,
        array(
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
        ),
        array('id_inform' => $registro_id)
    );

    // ****** Recetas y Medicamentos *******
    $id_doctor_recetas = isset($_POST['id_doctor']) ? sanitize_text_field($_POST['id_doctor']) : $user_id;
    $id_paciente_recetas = isset($_POST['id_paciente']) ? sanitize_text_field($_POST['id_paciente']) : $id_patient;
    $fecha_receta = sanitize_text_field($_POST['fecha_receta']);

    $table_name_recetas = $wpdb->prefix . 'midocdoc_recetas';
    $wpdb->update(
        $table_name_recetas,
        array(
            'id_doctor' => $id_doctor_recetas,
            'id_paciente' => $id_paciente_recetas,
            'fecha_receta' => $fecha_receta,
        ),
        array('id_inform' => $registro_id)
    );

    if ($wpdb->last_error) {
        echo 'Error al actualizar la información del paciente: ' . $wpdb->last_error;
    } else {
        echo '<div id="Respuestas-servidor"><b>La información del paciente se actualizó correctamente.</b></div>';
    }

    if (isset($_POST['medicamentos'])) {
        $medicamentos = json_decode(stripslashes($_POST['medicamentos']), true);

        if ($medicamentos === null && json_last_error() !== JSON_ERROR_NONE) {
            echo "Error al decodificar los medicamentos: " . json_last_error_msg();
        } else {
            $table_name_medicamentos = $wpdb->prefix . 'midocdoc_medicamentos';
            // Primero, eliminar los medicamentos existentes para este informe
            $wpdb->delete($table_name_medicamentos, array('id_receta' => $registro_id));

            // Luego, insertar los nuevos medicamentos
            foreach ($medicamentos as $medicamento) {
                $wpdb->insert(
                    $table_name_medicamentos,
                    array(
                        'id_receta' => $registro_id,
                        'descricion' => sanitize_text_field($medicamento['descripción']),
                        'presentation' => sanitize_text_field($medicamento['presentación']),
                        'concentration' => sanitize_text_field($medicamento['concentración']),
                        'administration_route' => sanitize_text_field($medicamento['vía de administración']),
                        'quantity' => sanitize_text_field($medicamento['cantidad']),
                        'dosage' => sanitize_text_field($medicamento['dosificación']),
                        'postdated' => $fecha_receta,
                    )
                );
            }
        }
    } else {
        echo "No se recibieron medicamentos";
    }
}
?>