<?php
// File: templates/public/informes-medicos.php

if (!defined('ABSPATH')) exit; ?>

<div class="midocdoc-informes-lista">
    <h3>Mis Informes Médicos</h3>
    <div class="filter-bar">
        <select id="filter-doctor" class="filter-select">
            <option value=""><?php esc_html_e('Todos los Doctores', 'midocdoc'); ?></option>
            <?php
            $doctors = get_users(array('role' => 'doctor'));
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
    <div id="informes-container" class="list-view">
        <?php foreach ($informes->informes as $informe): ?>
            <?php 
            $doctor = get_userdata($informe->id_doctor);
            $doctor_name = $doctor ? $doctor->display_name : 'Doctor no disponible';
            ?>
            <div class="informe-item" data-doctor="<?php echo esc_attr($informe->id_doctor); ?>" data-date="<?php echo esc_attr($informe->report_date); ?>">
                <div class="informe-header">
                    <h4><?php echo esc_html(wp_date('d/m/Y', strtotime($informe->report_date))); ?></h4>
                    <p><?php echo esc_html($doctor_name); ?></p>
                </div>
                <div class="informe-body">
                    <p><?php echo esc_html($informe->detalles->citasMedicas[0]->diagnosis); ?></p>
                </div>
                <div class="informe-actions">
                    <a href="?midocdoc_enviar_email=<?php echo esc_attr($informe->id_patient); ?>&informe_id=<?php echo esc_attr($informe->id); ?>" 
                       class="button-action">
                        <i class="latepoint-icon latepoint-icon-message-square"></i> Enviar
                    </a>
                    <a href="?midocdoc_generar_pdf=<?php echo esc_attr($informe->id); ?>" 
                       class="button-action" 
                       target="_blank">
                        <i class="latepoint-icon latepoint-icon-paperclip"></i> Ver PDF
                    </a>
                    <a class="button-action edit_form_button" onclick="buttonEditForm(event, <?php echo esc_attr($informe->id); ?>)">
                        <i class="latepoint-icon latepoint-icon-edit-3"></i> Editar
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <div class="pagination">
        <button id="prev-page" class="pagination-button"><?php esc_html_e('Anterior', 'midocdoc'); ?></button>
        <button id="next-page" class="pagination-button"><?php esc_html_e('Siguiente', 'midocdoc'); ?></button>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const toggleGridButton = document.getElementById('toggle-grid');
    const toggleListButton = document.getElementById('toggle-list');
    const informesContainer = document.getElementById('informes-container');
    const filterDoctor = document.getElementById('filter-doctor');
    const filterDateRange = document.getElementById('filter-date-range');
    const filterDate = document.getElementById('filter-date');
    const applyFiltersButton = document.getElementById('apply-filters');
    const resetFiltersButton = document.getElementById('reset-filters');
    const prevPageButton = document.getElementById('prev-page');
    const nextPageButton = document.getElementById('next-page');

    let currentPage = 1;
    const itemsPerPage = 20;

    function showPage(page) {
        const items = informesContainer.querySelectorAll('.informe-item');
        const totalItems = items.length;
        const totalPages = Math.ceil(totalItems / itemsPerPage);

        if (page < 1) page = 1;
        if (page > totalPages) page = totalPages;

        items.forEach((item, index) => {
            item.style.display = (index >= (page - 1) * itemsPerPage && index < page * itemsPerPage) ? 'block' : 'none';
        });

        currentPage = page;
    }

    toggleGridButton.addEventListener('click', function() {
        informesContainer.classList.remove('list-view');
        informesContainer.classList.add('grid-view');
    });

    toggleListButton.addEventListener('click', function() {
        informesContainer.classList.remove('grid-view');
        informesContainer.classList.add('list-view');
    });

    filterDateRange.addEventListener('change', function() {
        if (filterDateRange.value === 'custom') {
            filterDate.style.display = 'block';
        } else {
            filterDate.style.display = 'none';
        }
    });

    applyFiltersButton.addEventListener('click', function() {
        const doctor = filterDoctor.value;
        const dateRange = filterDateRange.value;
        const customDate = filterDate.value;
        const items = informesContainer.querySelectorAll('.informe-item');

        const now = new Date();
        const startOfWeek = new Date(now.setDate(now.getDate() - now.getDay()));
        const startOfMonth = new Date(now.getFullYear(), now.getMonth(), 1);

        items.forEach(function(item) {
            const itemDoctor = item.getAttribute('data-doctor');
            const itemDate = new Date(item.getAttribute('data-date'));

            let show = true;

            if (doctor && itemDoctor !== doctor) {
                show = false;
            }

            if (dateRange === 'this_week' && itemDate < startOfWeek) {
                show = false;
            }

            if (dateRange === 'this_month' && itemDate < startOfMonth) {
                show = false;
            }

            if (dateRange === 'custom' && customDate && itemDate.toISOString().split('T')[0] !== customDate) {
                show = false;
            }

            if (show) {
                item.style.display = 'block';
            } else {
                item.style.display = 'none';
            }
        });

        showPage(1);
    });

    resetFiltersButton.addEventListener('click', function() {
        filterDoctor.value = '';
        filterDateRange.value = '';
        filterDate.value = '';
        filterDate.style.display = 'none';

        const items = informesContainer.querySelectorAll('.informe-item');
        items.forEach(function(item) {
            item.style.display = 'block';
        });

        showPage(1);
    });

    prevPageButton.addEventListener('click', function() {
        showPage(currentPage - 1);
    });

    nextPageButton.addEventListener('click', function() {
        showPage(currentPage + 1);
    });

    showPage(1);
});
</script>