<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

function actualizar_informe_medico() {
    // Verificar nonce y permisos
    if (!current_user_can('edit_posts')) {
        wp_send_json_error(['message' => 'No tienes permisos para realizar esta acción']);
        return;
    }

    // Verificar ID del informe
    $informe_id = isset($_POST['informe_id']) ? intval($_POST['informe_id']) : 0;
    if (!$informe_id) {
        wp_send_json_error(['message' => 'ID de informe no válido']);
        return;
    }

    global $wpdb;

    try {
        // Iniciar transacción
        $wpdb->query('START TRANSACTION');

        // 1. Actualizar citas médicas
        $resultado_citas = $wpdb->update(
            $wpdb->prefix . 'midocdoc_citas_medicas',
            array(
                'purpose_consult' => sanitize_text_field($_POST['purpose_consult']),
                'external_cause' => sanitize_text_field($_POST['external_cause']),
                'reason_consult' => sanitize_textarea_field($_POST['reason_consult']),
                'current_condition' => sanitize_textarea_field($_POST['current_condition']),
                'systems_review' => sanitize_textarea_field($_POST['systems_review']),
                'general_state' => sanitize_text_field($_POST['general_state']),
                'consciousness_state' => sanitize_text_field($_POST['consciousness_state']),
                'biometric_data' => sanitize_textarea_field($_POST['biometric_data']),
                'diagnosis' => sanitize_textarea_field($_POST['diagnosis']),
                'management_plan' => sanitize_textarea_field($_POST['management_plan']),
                'notes' => sanitize_textarea_field($_POST['notes']),
                'report' => sanitize_textarea_field($_POST['report'])
            ),
            array('id_inform' => $informe_id),
            array('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s'),
            array('%d')
        );

        if ($resultado_citas === false) {
            throw new Exception('Error al actualizar la cita médica');
        }

        // 2. Actualizar antecedentes médicos
        $resultado_antecedentes = $wpdb->update(
            $wpdb->prefix . 'midocdoc_antecedentes_medicos',
            array(
                'edad' => intval($_POST['edad']),
                'fecha_nacimiento' => sanitize_text_field($_POST['fecha_nacimiento']),
                'genero' => sanitize_text_field($_POST['genero']),
                'fuma' => intval($_POST['fuma']),
                'numero_hijos' => intval($_POST['numero_hijos']),
                'other_causes' => sanitize_textarea_field($_POST['other_causes']),
                'family_history' => sanitize_textarea_field($_POST['family_history']),
                'surgical_history' => sanitize_textarea_field($_POST['surgical_history']),
                'traumatic_history' => sanitize_textarea_field($_POST['traumatic_history']),
                'allergic_history' => sanitize_textarea_field($_POST['allergic_history']),
                'toxic_history' => sanitize_textarea_field($_POST['toxic_history']),
                'transfusion_history' => sanitize_textarea_field($_POST['transfusion_history'])
            ),
            array('id_inform' => $informe_id),
            array('%d', '%s', '%s', '%d', '%d', '%s', '%s', '%s', '%s', '%s', '%s', '%s'),
            array('%d')
        );

        if ($resultado_antecedentes === false) {
            throw new Exception('Error al actualizar los antecedentes médicos');
        }

        // 3. Manejar recetas y medicamentos
        if (!empty($_POST['medicamentos'])) {
            // Decodificar el JSON de medicamentos
            $medicamentos = json_decode(stripslashes($_POST['medicamentos']), true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception('Error en el formato de los medicamentos');
            }

            // Primero, obtener o crear la receta
            $receta_actual = $wpdb->get_row($wpdb->prepare(
                "SELECT id FROM {$wpdb->prefix}midocdoc_recetas WHERE id_inform = %d",
                $informe_id
            ));

            if ($receta_actual) {
                $receta_id = $receta_actual->id;
                // Actualizar fecha de receta
                $wpdb->update(
                    $wpdb->prefix . 'midocdoc_recetas',
                    array('fecha_receta' => sanitize_text_field($_POST['fecha_receta'])),
                    array('id' => $receta_id)
                );
            } else {
                // Crear nueva receta
                $wpdb->insert(
                    $wpdb->prefix . 'midocdoc_recetas',
                    array(
                        'id_inform' => $informe_id,
                        'id_doctor' => get_current_user_id(),
                        'id_paciente' => intval($_POST['id_patient']),
                        'fecha_receta' => sanitize_text_field($_POST['fecha_receta'])
                    ),
                    array('%d', '%d', '%d', '%s')
                );
                $receta_id = $wpdb->insert_id;
            }

            // Eliminar medicamentos anteriores
            $wpdb->delete(
                $wpdb->prefix . 'midocdoc_medicamentos',
                array('id_receta' => $receta_id),
                array('%d')
            );

            // Insertar nuevos medicamentos
            foreach ($medicamentos as $medicamento) {
                $resultado_medicamento = $wpdb->insert(
                    $wpdb->prefix . 'midocdoc_medicamentos',
                    array(
                        'id_receta' => $receta_id,
                        'id_inform' => $informe_id,
                        'descricion' => sanitize_text_field($medicamento['descricion']),
                        'presentation' => sanitize_text_field($medicamento['presentation']),
                        'concentration' => sanitize_text_field($medicamento['concentration']),
                        'administration_route' => sanitize_text_field($medicamento['administration_route']),
                        'quantity' => intval($medicamento['quantity']),
                        'dosage' => sanitize_text_field($medicamento['dosage']),
                        'postdated' => current_time('mysql'),
                        'requires_disability' => 0
                    ),
                    array('%d', '%d', '%s', '%s', '%s', '%s', '%d', '%s', '%s', '%d')
                );

                if ($resultado_medicamento === false) {
                    throw new Exception('Error al insertar medicamento');
                }
            }
        }

        // 4. Actualizar fecha del informe principal
        $wpdb->update(
            $wpdb->prefix . 'midocdoc_informes',
            array('report_date' => current_time('mysql')),
            array('id' => $informe_id)
        );

        // Si todo salió bien, confirmar la transacción
        $wpdb->query('COMMIT');

        // Enviar respuesta de éxito
        wp_send_json_success([
            'message' => 'Informe actualizado correctamente',
            'informe_id' => $informe_id
        ]);

    } catch (Exception $e) {
        // Si algo salió mal, revertir todos los cambios
        $wpdb->query('ROLLBACK');
        wp_send_json_error([
            'message' => 'Error al actualizar el informe: ' . $e->getMessage()
        ]);
    }
}

// Si es una llamada AJAX, procesar la actualización
if (defined('DOING_AJAX') && DOING_AJAX) {
    actualizar_informe_medico();
}
?>