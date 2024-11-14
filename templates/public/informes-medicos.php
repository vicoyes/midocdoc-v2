<?php
// File: templates/public/informes-medicos.php

if (!defined('ABSPATH')) exit;

$customer_id = 3; // Usar ID de paciente específico para pruebas
?>

<div id="midocdoc-informes-container" data-customer-id="<?php echo esc_attr($customer_id); ?>">
    <div class="midocdoc-informes-lista">
        <h3>Mis Informes Médicos</h3>
        <div class="filter-bar">
            <select id="filter-doctor" class="filter-select">
                <option value=""><?php esc_html_e('Todos los Doctores', 'midocdoc'); ?></option>
                <?php
                $doctors = get_users(array('role' => 'latepoint_agent'));
                foreach ($doctors as $doctor) {
                    echo '<option value="' . esc_attr($doctor->ID) . '">' . esc_html($doctor->display_name) . '</option>';
                }
                ?>
            </select>
            <select id="filter-date-range" class="filter-select">
                <option value=""><?php esc_html_e('Todas las Fechas', 'midocdoc'); ?></option>
                <option value="this_week"><?php esc_html_e('Esta Semana', 'midocdoc'); ?></option>
                <option value="this_month"><?php esc_html_e('Este Mes', 'midocdoc'); ?></option>
                <option value="custom"><?php esc_html_e('Personalizada', 'midocdoc'); ?></option>
            </select>
            <input type="date" id="filter-date" class="filter-input" placeholder="<?php esc_attr_e('Fecha del Informe', 'midocdoc'); ?>" style="display: none;">
            <button id="apply-filters" class="filter-button"><?php esc_html_e('Aplicar Filtros', 'midocdoc'); ?></button>
            <button id="reset-filters" class="filter-button"><?php esc_html_e('Borrar Filtros', 'midocdoc'); ?></button>
            <button id="toggle-grid" class="view-button"><?php esc_html_e('Vista de Cuadrícula', 'midocdoc'); ?></button>
            <button id="toggle-list" class="view-button"><?php esc_html_e('Vista de Listado', 'midocdoc'); ?></button>
        </div>
        <div id="informes-container" class="grid-view"></div>
        <div class="spinner-form" id="spinner" style="display: none;"></div>
        <div class="pagination">
            <button id="prev-page" class="pagination-button"><?php esc_html_e('Anterior', 'midocdoc'); ?></button>
            <div id="pagination-numbers"></div>
            <button id="next-page" class="pagination-button"><?php esc_html_e('Siguiente', 'midocdoc'); ?></button>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const $ = jQuery;
    const toggleGridButton = $('#toggle-grid');
    const toggleListButton = $('#toggle-list');
    const informesContainer = $('#informes-container');
    const filterDoctor = $('#filter-doctor');
    const filterDateRange = $('#filter-date-range');
    const filterDate = $('#filter-date');
    const applyFiltersButton = $('#apply-filters');
    const resetFiltersButton = $('#reset-filters');
    const prevPageButton = $('#prev-page');
    const nextPageButton = $('#next-page');
    const paginationNumbers = $('#pagination-numbers');
    const spinner = $('#spinner');

    let currentPage = 1;
    const itemsPerPage = 20;

    filterDateRange.on('change', function() {
        if ($(this).val() === 'custom') {
            filterDate.show();
        } else {
            filterDate.hide();
        }
    });

    function formatDate(dateString) {
        const options = { day: '2-digit', month: 'short', year: 'numeric' };
        const date = new Date(dateString);
        return date.toLocaleDateString('es-ES', options).toUpperCase();
    }

    function loadInformes(page) {
        const customerId = $('#midocdoc-informes-container').data('customer-id');
        const doctorFilter = filterDoctor.val();
        const dateRangeFilter = filterDateRange.val();
        const dateFilter = filterDate.val();

        informesContainer.empty(); // Vaciar el contenedor de informes
        spinner.show(); // Mostrar el spinner

        $.ajax({
            url: midocdocAjax.ajaxurl,
            method: 'POST',
            data: {
                action: 'midocdoc_get_informes',
                nonce: midocdocAjax.nonce,
                customer_id: customerId,
                doctor_id: doctorFilter,
                date_range: dateRangeFilter,
                date: dateFilter,
                page: page
            },
            success: function(response) {
                console.log('Response:', response);
                
                if (!response || !response.success || !response.data) {
                    console.error('Invalid response format');
                    informesContainer.html('<p>Error al cargar informes</p>');
                    spinner.hide(); // Ocultar el spinner
                    return;
                }

                const informes = response.data.informes.informes; // Ajuste aquí para acceder al array correcto
                
                if (!Array.isArray(informes)) {
                    console.error('Informes is not an array:', informes);
                    informesContainer.html('<p>Formato de datos inválido</p>');
                    spinner.hide(); // Ocultar el spinner
                    return;
                }

                if (informes.length === 0) {
                    informesContainer.html('<p>No se encontraron informes médicos</p>');
                    spinner.hide(); // Ocultar el spinner
                    return;
                }

                const informesHTML = informes.map(informe => {
                    if (!informe || typeof informe !== 'object') {
                        console.error('Invalid informe:', informe);
                        return '';
                    }

                    const doctor = informe.doctor_name || 'Doctor no disponible';
                    const diagnosis = informe.detalles?.citasMedicas?.[0]?.diagnosis || 'Diagnóstico no disponible';
                    const formattedDate = formatDate(informe.report_date);
                    
                    return `
                        <div class="informe-item" data-doctor="${informe.id_doctor || ''}" data-date="${informe.report_date || ''}">
                            <div class="informe-header">
                                <h4>${formattedDate || 'Fecha no disponible'}</h4>
                                <p>Dr: ${doctor}</p>
                            </div>
                            <div class="informe-body">
                                <p>${diagnosis}</p>
                            </div>
                            <div class="informe-actions">
                                <a href="?midocdoc_generar_pdf=${informe.id || ''}" class="button-action" target="_blank">
                                    <i class="latepoint-icon latepoint-icon-paperclip"></i> Ver PDF
                                </a>
                            </div>
                        </div>
                    `;
                }).join('');

                informesContainer.html(informesHTML);
                currentPage = page; // Actualizar la página actual
                updatePagination(response.data.total || 0);
                spinner.hide(); // Ocultar el spinner
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', {xhr, status, error});
                informesContainer.html('<p>Error en la conexión</p>');
                spinner.hide(); // Ocultar el spinner
            }
        });
    }

    function updatePagination(totalItems) {
        const totalPages = Math.ceil(totalItems / itemsPerPage);
        paginationNumbers.empty();

        for (let i = 1; i <= totalPages; i++) {
            const pageButton = $('<button>')
                .addClass('pagination-number')
                .text(i)
                .on('click', function() {
                    loadInformes(i);
                });

            if (i === currentPage) {
                pageButton.addClass('active');
            }

            paginationNumbers.append(pageButton);
        }

        prevPageButton.prop('disabled', currentPage === 1);
        nextPageButton.prop('disabled', currentPage === totalPages);
    }

    if (toggleGridButton.length && toggleListButton.length) {
        toggleGridButton.on('click', function() {
            informesContainer.removeClass('list-view').addClass('grid-view');
        });

        toggleListButton.on('click', function() {
            informesContainer.removeClass('grid-view').addClass('list-view');
        });
    }

    applyFiltersButton.on('click', function() {
        loadInformes(1);
    });

    resetFiltersButton.on('click', function() {
        filterDoctor.val('');
        filterDateRange.val('');
        filterDate.val('');
        filterDate.hide();
        loadInformes(1);
    });

    prevPageButton.on('click', function() {
        if (currentPage > 1) {
            loadInformes(currentPage - 1);
        }
    });

    nextPageButton.on('click', function() {
        loadInformes(currentPage + 1);
    });

    loadInformes(1);
});
</script>

<style>
.filter-bar {
    margin: 20px 0;
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

.filter-select,
.filter-input,
.filter-button,
.view-button {
    padding: 8px 12px;
    border: 1px solid #ddd;
    border-radius: 4px;
    flex: 1;
    min-width: 150px;
}

#informes-container {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
    margin: 20px 0;
}

#informes-container.grid-view {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 20px;
}

#informes-container.list-view {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.informe-item {
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 15px;
    background: #fff;
}

.button-action {
    padding: 8px 12px;
    background: #007bff;
    color: white;
    border-radius: 4px;
    text-decoration: none;
}

.button-action:hover {
    background: #0056b3;
}

.pagination {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 10px;
    margin-top: 20px;
}

.pagination-button,
.pagination-number {
    padding: 8px 12px;
    border: 1px solid #ddd;
    border-radius: 4px;
    background: #fff;
    cursor: pointer;
}

.pagination-button:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.pagination-number.active {
    background: #007bff;
    color: white;
    border-color: #007bff;
}

.spinner-form {
  width: 50px;
  height: 50px;
  border: 5px solid #f3f3f3;
  border-top: 5px solid #3498db;
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin: 50px auto;
  display: block;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}
</style>