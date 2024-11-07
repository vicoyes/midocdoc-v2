<?php /* @var $customer OsCustomerModel */ ?>
<?php
require_once __DIR__ . '/../model/modelmidocdoc_pdf.php';
require_once __DIR__ . '/../lib/dompdf/autoload.inc.php';
require_once WP_PLUGIN_DIR . '/latepoint/lib/models/customer_model.php';

use Dompdf\Dompdf;

if (!defined('ABSPATH')) {
    exit;
}

function midocdoc_generar_pdf($id_reporte, $onlySave = false) {
    $upload_dir = wp_upload_dir();
    $baseDir = $upload_dir['basedir'];
    $baseUrl = $upload_dir['baseurl'];
    $carpetaDestino = $baseDir . '/midocdoc/reportes';
    $tituloPdf = 'reporte_medico_' . $id_reporte . '.pdf';
    $rutaPdf = $carpetaDestino . '/' . $tituloPdf;
    $urlPdf = $baseUrl . '/midocdoc/reportes/' . $tituloPdf;

    // Verificar si el archivo PDF ya existe
    if (file_exists($rutaPdf) && !$onlySave) {
        // Redirigir al usuario al archivo PDF existente
        header('Location: ' . $urlPdf);
        exit;
    }

    $user_id = get_current_user_id();
    $user_info = get_userdata($user_id);
    $first_name = $user_info->first_name;
    $last_name = $user_info->last_name;
    $nombreMedico = ' Dr: ' . $first_name . ' ' . $last_name;
    $firma_id = get_user_meta($user_id, 'firma-usuario-id', true);

    $midocdoc_reoporte_model = new Midocdoc_Reporte_Model();
    $informe = $midocdoc_reoporte_model->get_informe_por_id($id_reporte);

    $customer = new OsCustomerModel($informe->id_patient);
    $nombrePaciente = $customer->first_name . ' ' . $customer->last_name;

    ob_start();
    ?>
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <title>Informe Médico</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                font-size: 14px;
                color: #333;
                margin: 0;
                padding: 0;
            }

            .container {
                background-color: #fff;
                padding: 20px;
                width: 100%;
            }

            .informe-header, .footer {
                text-align: center;
                margin-bottom: 20px;
            }

            .report-title {
                background-color: #2c3d8f;
                color: #fff;
                padding: 8px;
                border-radius: 2px;
                font-size: 14px;
                text-transform: uppercase;
                text-align: center;
                margin-bottom: 20px;
                font-family: Arial, sans-serif !important;
            }

            table {
                width: 100%;
                border-collapse: collapse;
                margin-bottom: 20px;
                background-color: #fff;
            }

            th, td {
                border: 1px solid #ddd;
                padding: 8px;
                text-align: left;
            }

            th {
                background-color: #f4f4f4;
                font-weight: bold;
            }

            tr:nth-child(even) {
                background-color: #f9f9f9;
            }

            .medicamento, .antecedente, .cita {
                margin-bottom: 10px;
            }

            .medicamento h4, .antecedente h4, .cita h4 {
                font-size: 16px;
                color: #2c408c;
                margin-bottom: 5px;
            }

            .medicamento p, .antecedente p, .cita p {
                font-size: 14px;
                color: #333;
                margin: 5px 0;
            }

            .page-break {
                page-break-before: always;
            }

            td:nth-child(1) {
                max-width: 20px;
            }

            .cita td, .antecedente td {
                width: 100%;
            }

            .medicamento table td {
                width: 50%;
            }

            #firma {
                max-width: 250px;
            }

            .logo {
                margin: 0 auto;
            }
        </style>
    </head>
    <body>
    <div class="container">
        <div class="informe-header">
            <img class="logo" src="https://midocdoc.com/wp-content/uploads/2024/08/Logo-Midocdoc-Nuevo.webp" alt="logo Mi Docddoc" width="130" height="25">
            <h1>Informe Médico</h1>
            <p>Fecha del Informe: <?php echo $informe->report_date; ?></p>
        </div>

        <!-- Información General -->
        <h4 class="report-title">Información General</h4>
        <table>
            <tbody>
                <tr>
                    <td>Nombre del Médico:</td>
                    <td><?php echo $nombreMedico; ?></td>
                </tr>
                <tr>
                    <td>Paciente:</td>
                    <td><?php echo $nombrePaciente; ?></td>
                </tr>
            </tbody>
        </table>

        <!-- Citas Médicas -->
        <h4 class="report-title">Citas Médicas</h4>
        <?php foreach ($informe->detalles->citasMedicas as $cita): ?>
            <div class="cita">
                <table>
                    <tbody>
                    <tr>
                    <td>Motivo de la Consulta:</td>
                    <td><?php echo htmlspecialchars($cita->purpose_consult); ?></td>
                </tr>
                <tr>
                    <td>Descripción detallada de los síntomas:</td>
                    <td><?php echo htmlspecialchars($cita->external_cause); ?></td>
                </tr>
                <tr>
                    <td>Tratamientos previos:</td>
                    <td><?php echo htmlspecialchars($cita->reason_consult); ?></td>
                </tr>
                <tr>
                    <td>Diagnósticos iniciales: (basados en la evaluación clínica)</td>
                    <td><?php echo htmlspecialchars($cita->current_condition); ?></td>
                </tr>
                <tr>
                    <td>Laboratorios y estudios de imagen:</td>
                    <td><?php echo htmlspecialchars($cita->systems_review); ?></td>
                </tr>
                <tr>
                    <td>Recomendaciones:</td>
                    <td><?php echo htmlspecialchars($cita->biometric_data); ?></td>
                </tr>
                <tr>
                    <td>Diagnóstico:</td>
                    <td><?php echo htmlspecialchars($cita->diagnosis); ?></td>
                </tr>
                <tr>
                    <td>Plan de seguimiento:</td>
                    <td><?php echo htmlspecialchars($cita->management_plan); ?></td>
                </tr>
                        <tr>
                            <td>Reporte:</td>
                            <td><?php echo $cita->report; ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        <?php endforeach; ?>

        <!-- Antecedentes Médicos -->
        <div class="page-break"></div>
        <h4 class="report-title">Antecedentes Médicos</h4>
        <?php foreach ($informe->detalles->antecedentesMedicos as $antecedente): ?>
            <div class="antecedente">
                <table>
                    <tbody>
                        <tr>
                            <td>Otras Causas:</td>
                            <td><?php echo $antecedente->other_causes; ?></td>
                        </tr>
                        <tr>
                            <td>Historial Familiar:</td>
                            <td><?php echo $antecedente->family_history; ?></td>
                        </tr>
                        <tr>
                            <td>Historia Quirúrgica:</td>
                            <td><?php echo $antecedente->surgical_history; ?></td>
                        </tr>
                        <tr>
                            <td>Historia Traumática:</td>
                            <td><?php echo $antecedente->traumatic_history; ?></td>
                        </tr>
                        <tr>
                            <td>Historia de Alergias:</td>
                            <td><?php echo $antecedente->allergic_history; ?></td>
                        </tr>
                        <tr>
                            <td>Historia Tóxica:</td>
                            <td><?php echo $antecedente->toxic_history; ?></td>
                        </tr>
                        <tr>
                            <td>Historia de Transfusiones:</td>
                            <td><?php echo $antecedente->transfusion_history; ?></td>
                        </tr>
                        <tr>
                            <td>Edad:</td>
                            <td><?php echo $antecedente->edad; ?></td>
                        </tr>
                        <tr>
                            <td>Fecha de Nacimiento:</td>
                            <td><?php echo $antecedente->fecha_nacimiento; ?></td>
                        </tr>
                        <tr>
                            <td>Género:</td>
                            <td><?php echo $antecedente->genero; ?></td>
                        </tr>
                        <tr>
                            <td>Fumador:</td>
                            <td><?php echo $antecedente->fuma ? 'Sí' : 'No'; ?></td>
                        </tr>
                        <tr>
                            <td>Número de Hijos:</td>
                            <td><?php echo $antecedente->numero_hijos; ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        <?php endforeach; ?>

        <div class="page-break"></div>
        <div class="seccion">
            <h4 class="report-title">Recetas con Medicamentos</h4>
            <?php foreach ($informe->detalles->recetasConMedicamentos as $receta): ?>
                <br>
                <div class="medicamento">
                    <p>ID de la Receta: <?php echo $receta->id; ?></p>
                    <h4>Medicamentos Prescritos</h4>
                    <table>
                        <tbody>
                            <?php foreach ($receta->medicamentos as $medicamento): ?>
                                <tr>
                                    <td colspan="2" style="background-color: #f4f4f4;"><strong>Nombre: <?php echo $medicamento->descricion; ?></strong></td>
                                </tr>
                                <tr>
                                    <td>Presentación:</td>
                                    <td><?php echo $medicamento->presentation; ?></td>
                                </tr>
                                <tr>
                                    <td>Concentración:</td>
                                    <td><?php echo $medicamento->concentration; ?></td>
                                </tr>
                                <tr>
                                    <td>Vía de administración:</td>
                                    <td><?php echo $medicamento->administration_route; ?></td>
                                </tr>
                                <tr>
                                    <td>Cantidad:</td>
                                    <td><?php echo $medicamento->quantity; ?></td>
                                </tr>
                                <tr>
                                    <td>Dosis:</td>
                                    <td><?php echo $medicamento->dosage; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endforeach; ?>
        </div>

        <?php
        if ($firma_id) {
            $firma_url = wp_get_attachment_url($firma_id);
            echo '<h4>Firma del Médico:</h4>';
            echo '<img id="firma" src="' . esc_url($firma_url) . '" alt="Firma del Usuario">';
        } else {
            echo 'No hay una firma cargada.';
        }
        ?>


        <div class="footer">
            <p>Generado por Midocdoc</p>
            <p>El paciente acepta que este informe médico es emitido por un médico colegiado y registrado en Venezuela</p>
        </div>
    </div>
    </body>
    </html>
    <?php
    $html = ob_get_clean();

    $domPdf = new Dompdf();
    $domPdf->loadHtml($html);
    $domPdf->setPaper('letter', 'portrait');

    $options = $domPdf->getOptions();
    $options->set(array('isRemoteEnabled' => true));
    $domPdf->setOptions($options);

    $domPdf->render();

    if (!file_exists($carpetaDestino)) {
        mkdir($carpetaDestino, 0755, true);
    }

    file_put_contents($rutaPdf, $domPdf->output());

    // Redirigir al usuario al archivo PDF generado
    if (!$onlySave) {
        header('Location: ' . $urlPdf);
        exit;
    }
}