// File: assets/js/informes.js

jQuery(document).ready(function($) {
    const informesContainer = $('#informes-container');
    const filterDoctor = $('#filter-doctor');
    const filterDateRange = $('#filter-date-range');
    const filterDate = $('#filter-date');
    const applyFiltersButton = $('#apply-filters');
    const resetFiltersButton = $('#reset-filters');
    const prevPageButton = $('#prev-page');
    const nextPageButton = $('#next-page');
    const currentPageSpan = $('#current-page');
    const totalPagesSpan = $('#total-pages');

    let currentPage = 1;
    const itemsPerPage = 20;

    function loadInformes(page) {
        const customerId = $('#midocdoc-informes-container').data('customer-id');

        $.ajax({
            url: midocdocAjax.ajaxurl,
            method: 'POST',
            data: {
                action: 'midocdoc_get_informes',
                nonce: midocdocAjax.nonce,
                customer_id: customerId,
                page: page
            },
            success: function(response) {
                if (response.success) {
                    const informes = response.data.informes;
                    informesContainer.empty();

                    if (Array.isArray(informes)) {
                        informes.forEach(function(informe) {
                            const doctor = informe.doctor_name || 'Doctor no disponible';
                            const item = `
                                <div class="informe-item" data-doctor="${informe.id_doctor}" data-date="${informe.report_date}">
                                    <div class="informe-header">
                                        <h4>${informe.report_date}</h4>
                                        <p>${doctor}</p>
                                    </div>
                                    <div class="informe-body">
                                        <p>${informe.diagnosis}</p>
                                    </div>
                                    <div class="informe-actions">
                                        <a href="?midocdoc_enviar_email=${informe.id_patient}&informe_id=${informe.id}" class="button-action">
                                            <i class="latepoint-icon latepoint-icon-message-square"></i> Enviar
                                        </a>
                                        <a href="?midocdoc_generar_pdf=${informe.id}" class="button-action" target="_blank">
                                            <i class="latepoint-icon latepoint-icon-paperclip"></i> Ver PDF
                                        </a>
                                        <a class="button-action edit_form_button" onclick="buttonEditForm(event, ${informe.id})">
                                            <i class="latepoint-icon latepoint-icon-edit-3"></i> Editar
                                        </a>
                                    </div>
                                </div>
                            `;
                            informesContainer.append(item);
                        });

                        currentPageSpan.text(page);
                        totalPagesSpan.text(Math.ceil(response.data.total / itemsPerPage));
                    } else {
                        informesContainer.append('<p>No se encontraron informes médicos.</p>');
                    }
                }
            }
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