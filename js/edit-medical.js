// Asegurarnos de que el script se carga después del DOM
document.addEventListener('DOMContentLoaded', function() {
    console.log('Script de edición médica cargado');
    window.medicamentos = window.medicamentos || [];

    // Función para cargar el formulario de edición en el sidebar
    function cargarFormularioEdicion(informeId, customerId) {
        const sidebar = document.getElementById('medicalReportsSidebar');
        const overlay = document.querySelector('.sidebar-overlay');
        const medicalReportsContent = document.getElementById('medicalReportsContent');

        if (!sidebar || !overlay || !medicalReportsContent) {
            console.error('Elementos del sidebar no encontrados');
            return;
        }

        // Mostrar overlay y sidebar
        overlay.style.display = 'block';
        setTimeout(() => overlay.classList.add('active'), 10);
        
        sidebar.style.display = 'block';
        setTimeout(() => sidebar.classList.add('active'), 10);

        medicalReportsContent.innerHTML = '<p>Cargando formulario de edición...</p>';

        // Cargar el contenido del formulario
        fetch(`${midocdocAjax.ajaxurl}?action=cargar_edit_medical_content&security=${midocdocAjax.nonce}&informe_id=${informeId}&customer_id=${customerId}`)
            .then(response => response.text())
            .then(data => {
                medicalReportsContent.innerHTML = data;
                inicializarFormularioEdicion();
            })
            .catch(error => {
                console.error('Error al cargar el formulario:', error);
                medicalReportsContent.innerHTML = '<p>Error al cargar el formulario de edición.</p>';
            });
    }

    // Inicializar el formulario una vez cargado
    function inicializarFormularioEdicion() {
        firstab();
        contador();
        inicializarMedicamentosExistentes();
        configurarManejadoresEventos();
    }

    // Inicializar medicamentos existentes si los hay
    function inicializarMedicamentosExistentes() {
        const listaMedicamentos = document.getElementById('listaMedicamentos');
        if (!listaMedicamentos) return;

        const medicamentosExistentes = listaMedicamentos.querySelectorAll('.medicamento-item');
        window.medicamentos = Array.from(medicamentosExistentes).map(med => ({
            id: med.dataset.id,
            descricion: med.querySelector('[data-field="descricion"]').textContent,
            presentation: med.querySelector('[data-field="presentation"]').textContent,
            concentration: med.querySelector('[data-field="concentration"]').textContent,
            administration_route: med.querySelector('[data-field="administration_route"]').textContent,
            quantity: med.querySelector('[data-field="quantity"]').textContent,
            dosage: med.querySelector('[data-field="dosage"]').textContent
        }));
    }

    // Configurar manejadores de eventos
    function configurarManejadoresEventos() {
        const btnGuardar = document.getElementById('guardar-informe');
        if (btnGuardar) {
            btnGuardar.addEventListener('click', manejarEnvioFormulario);
        }

        const btnAgregar = document.getElementById('btnAgregar');
        if (btnAgregar) {
            btnAgregar.addEventListener('click', agregarNuevoMedicamento);
        }
    }

    // Manejar el envío del formulario
    function manejarEnvioFormulario(e) {
        e.preventDefault();

        const formData = new FormData();
        formData.append('action', 'actualizar_informe_medico');
        formData.append('security', midocdocAjax.nonce);

        // Agregar datos de los formularios
        const forms = {
            'citas-medicas': document.getElementById('citas-medicas'),
            'antecedentes-medicos': document.getElementById('formulario-antecedentes-medicos'),
            'recetas': document.getElementById('formReceta')
        };

        Object.entries(forms).forEach(([key, form]) => {
            if (form) {
                new FormData(form).forEach((value, key) => {
                    formData.append(key, value);
                });
            }
        });

        // Agregar medicamentos
        formData.append('medicamentos', JSON.stringify(window.medicamentos || []));

        // Enviar actualización
        fetch(midocdocAjax.ajaxurl, {
            method: 'POST',
            body: formData,
            credentials: 'same-origin'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                mostrarMensaje('Informe actualizado correctamente', 'success');
                // Recargar la lista de informes después de un tiempo
                setTimeout(() => {
                    const customerId = document.querySelector('[name="customer_id"]')?.value;
                    if (customerId) {
                        cargarListaInformes(customerId);
                    }
                }, 2000);
            } else {
                mostrarMensaje(data.message || 'Error al actualizar el informe', 'error');
            }
        })
        .catch(error => {
            console.error('Error en la actualización:', error);
            mostrarMensaje('Error al actualizar el informe', 'error');
        });
    }

    // Agregar nuevo medicamento
    function agregarNuevoMedicamento() {
        const campos = {
            descricion: document.getElementById('descricion'),
            presentation: document.getElementById('presentation'),
            concentration: document.getElementById('concentration'),
            administration_route: document.getElementById('administration_route'),
            quantity: document.getElementById('quantity'),
            dosage: document.getElementById('dosage')
        };

        // Validar campos
        for (const [key, campo] of Object.entries(campos)) {
            if (!campo || !campo.value.trim()) {
                mostrarMensaje(`El campo ${obtenerEtiquetaCampo(key)} es requerido`, 'error');
                return;
            }
        }

        const medicamento = {
            id: Date.now(), // ID temporal único
            ...Object.fromEntries(
                Object.entries(campos).map(([key, campo]) => [key, campo.value.trim()])
            )
        };

        // Agregar a la lista global
        window.medicamentos.push(medicamento);

        // Crear elemento visual
        const listaMedicamentos = document.getElementById('listaMedicamentos');
        const elementoMedicamento = crearElementoMedicamento(medicamento);
        listaMedicamentos.appendChild(elementoMedicamento);

        // Limpiar campos
        Object.values(campos).forEach(campo => campo.value = '');
        actualizarContadores();
    }

    // Crear elemento visual para medicamento
    function crearElementoMedicamento(medicamento) {
        const div = document.createElement('div');
        div.className = 'medicamento-item';
        div.dataset.id = medicamento.id;

        const html = `
            <div class="medicamento-content">
                <p><strong>Descripción:</strong> <span data-field="descricion">${medicamento.descricion}</span></p>
                <p><strong>Presentación:</strong> <span data-field="presentation">${medicamento.presentation}</span></p>
                <p><strong>Concentración:</strong> <span data-field="concentration">${medicamento.concentration}</span></p>
                <p><strong>Vía de Administración:</strong> <span data-field="administration_route">${medicamento.administration_route}</span></p>
                <p><strong>Cantidad:</strong> <span data-field="quantity">${medicamento.quantity}</span></p>
                <p><strong>Dosificación:</strong> <span data-field="dosage">${medicamento.dosage}</span></p>
            </div>
            <button type="button" class="btn-eliminar">
                <i class="latepoint-icon latepoint-icon-x"></i>
            </button>
        `;

        div.innerHTML = html;

        // Agregar manejador para eliminar
        div.querySelector('.btn-eliminar').addEventListener('click', () => {
            div.remove();
            window.medicamentos = window.medicamentos.filter(m => m.id !== medicamento.id);
        });

        return div;
    }

    // Funciones auxiliares
    function mostrarMensaje(mensaje, tipo) {
        const mensajeElement = document.getElementById('mensajeRespuesta');
        if (mensajeElement) {
            mensajeElement.textContent = mensaje;
            mensajeElement.className = `mensaje-${tipo}`;
            mensajeElement.style.display = 'block';
        }
    }

    function obtenerEtiquetaCampo(key) {
        const etiquetas = {
            descricion: 'Descripción',
            presentation: 'Presentación',
            concentration: 'Concentración',
            administration_route: 'Vía de Administración',
            quantity: 'Cantidad',
            dosage: 'Dosificación'
        };
        return etiquetas[key] || key;
    }

    function actualizarContadores() {
        const campos = document.querySelectorAll('.campo-recetas');
        campos.forEach(campo => {
            const contador = campo.nextElementSibling;
            if (contador && contador.classList.contains('contador-caracteres')) {
                const maxLength = campo.getAttribute('maxlength');
                contador.textContent = `0/${maxLength}`;
                contador.classList.remove('bajo');
            }
        });
    }

    // Event Listeners globales
    document.body.addEventListener('click', event => {
        if (event.target.matches('a[href*="midocdoc_editar"]')) {
            event.preventDefault();
            const url = new URL(event.target.href);
            const informeId = url.searchParams.get('midocdoc_editar');
            const customerId = url.searchParams.get('id') || 
                             document.querySelector('[name="customer_id"]')?.value;
            
            if (informeId && customerId) {
                cargarFormularioEdicion(informeId, customerId);
            }
        }
    });
});