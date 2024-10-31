// Arrays globales para almacenar los IDs de los medicamentos eliminados y los nuevos medicamentos agregados
window.medicamentosEliminados = [];
window.nuevosMedicamentos = [];

function buttonEditForm(informeId) {
    console.log('buttonEditForm called with informeId:', informeId);
    const ajaxUrl = `${datosAjax.ajaxurl}?action=cargar_edit_medical_content&idinform=${informeId}`;
    console.log('AJAX URL:', ajaxUrl);

    // Realizar la solicitud fetch
    fetch(ajaxUrl)
        .then(response => response.text())
        .then(data => {
            // Encuentra el contenedor del sidebar
            let sidebar = document.getElementById('sidebar-form');
            let container = document.getElementById('container-para-formulario');

            // Inserta los datos en el contenedor del sidebar
            if (container) {
                container.innerHTML = data;
            }

            // Mostrar el sidebar deslizando
            if (sidebar) {
                sidebar.classList.add('show');
            }

            // Llama a las funciones adicionales si es necesario
            firstab();
            contador();
        })
        .catch(error => console.error('Error:', error));
}

// Método para cerrar el sidebar
function closeSidebarForm() {
    console.log('Cerrando el sidebar...');
    let sidebar = document.getElementById('sidebar-form');
    if (sidebar) {
        sidebar.classList.remove('show'); // Esconde el sidebar deslizando
    }
}

// Cerrar el sidebar cuando se presiona el botón "Cerrar"
document.querySelector('#close-btn-inform').addEventListener('click', closeSidebarForm);

// Función para agregar medicamentos a la receta
function agregarMedicamentoEdit() {
    console.log('Agregando medicamento...');
    if (!window.medicamentoIdCounter) {
        window.medicamentoIdCounter = 1;
    }
    const idActual = 'nuevo_' + window.medicamentoIdCounter++;
    const nuevoMedicamento = document.createElement('div');
    nuevoMedicamento.className = 'medicamento';
    nuevoMedicamento.setAttribute('data-id', idActual);

    const ulMedicamento = document.createElement('ul');
    
    const propiedades = [
        'descricion',
        'presentation', 
        'concentration',
        'administration_route',
        'quantity',
        'dosage'
    ];

    const medicamentoActual = {};

    const obtenerTextoEnEspanol = propiedad => {
        const traducciones = {
            'descricion': 'Descripción',
            'presentation': 'Presentación',
            'concentration': 'Concentración',
            'administration_route': 'Vía de Administración',
            'quantity': 'Cantidad',
            'dosage': 'Dosificación'
        };
        return traducciones[propiedad] || propiedad;
    };

    // Inicializar el array de medicamentos si no existe
    if (!window.medicamentos) {
        window.medicamentos = [];
    }

    propiedades.forEach(propiedad => {
        const label = obtenerTextoEnEspanol(propiedad);
        const input = document.getElementById(propiedad);
        const valor = input ? input.value : '';
        
        medicamentoActual[label.toLowerCase()] = valor;

        const li = document.createElement('li');
        li.textContent = `${label}: ${valor}`;
        li.className = propiedad;
        ulMedicamento.appendChild(li);
    });

    medicamentoActual.id = idActual;
    window.medicamentos.push(medicamentoActual);
    window.nuevosMedicamentos.push(medicamentoActual); // Agregar a nuevos medicamentos

    const btnEliminar = document.createElement('button');
    btnEliminar.textContent = 'Eliminar';
    btnEliminar.classList.add('btn-eliminar-edit');
    btnEliminar.addEventListener('click', (event) => eliminarMedicamentoEdit(event, idActual));

    nuevoMedicamento.appendChild(ulMedicamento);
    nuevoMedicamento.appendChild(btnEliminar);
    const listaMedicamentos = document.getElementById('listaMedicamentos');
    listaMedicamentos.appendChild(nuevoMedicamento);

    // Limpiar campos
    propiedades.forEach(propiedad => {
        const input = document.getElementById(propiedad);
        if (input) {
            input.value = '';
        }
    });

    // Reiniciar contadores
    const campos = document.querySelectorAll('.campo-recetas');
    campos.forEach(campo => {
        campo.value = '';
        const contador = campo.nextElementSibling;
        if (contador) {
            const maxLength = campo.getAttribute('maxlength');
            contador.textContent = `0/${maxLength}`;
            contador.classList.remove('bajo');
        }
    });
}

// Función para eliminar medicamentos de la receta
function eliminarMedicamentoEdit(e,idMedicamento) {
   e.preventDefault(); // Prevenir el comportamiento predeterminado del evento
   e.stopPropagation(); // Detener la propagación del evento
   console.log(e);

    const medicamentoDiv = document.querySelector(`.medicamento[data-id="${idMedicamento}"]`);
    if (medicamentoDiv) {
        medicamentoDiv.remove();
        // Eliminar el medicamento del array global
        window.medicamentos = window.medicamentos.filter(medicamento => medicamento.id !== idMedicamento);
        if (!idMedicamento.startsWith('nuevo_')) {
            window.medicamentosEliminados.push(idMedicamento); // Agregar a medicamentos eliminados si no es un nuevo medicamento
        }
        console.log(`Medicamento con ID ${idMedicamento} eliminado.`);
    }
}

// funcion para mostra el elemento el boton

function openForm(evt, formName) {
    const tabcontent = document.getElementsByClassName("tabcontent");
    const tablinks = document.getElementsByClassName("tablinks");

    for (let i = 0; i < tabcontent.length; i++) {
        tabcontent[i].style.display = "none";
    }

    for (let i = 0; i < tablinks.length; i++) {
        tablinks[i].className = tablinks[i].className.replace(" active", "");
    }

    if (formName) {
        document.getElementById(formName).style.display = "block";
    }
    evt.currentTarget.className += " active";
    const actualizarInformeBtn = document.getElementById('actualizar-informe');
    if (actualizarInformeBtn) {
        actualizarInformeBtn.style.display = 'block';
    }
}

//funcion para actulizar el registro en la base de datos

function actualizarCitasMedicasForm() {
   console.log('Medicamentos:', window.medicamentos);
    const buttonActualizar = document.getElementById('actualizar-informe');
    const buttonCancelar = document.getElementById('cancelar-informe');
    const loadingMessage = document.getElementById('loading');
    const mensajeRespuesta = document.getElementById('mensajeRespuesta');

    // Deshabilitar botones y mostrar feedback visual
    buttonActualizar.disabled = true;
    buttonCancelar.disabled = true;
    buttonActualizar.value = "Actualizando...";
    buttonActualizar.style.backgroundColor = "#ccc"; // Cambiar color de fondo
    buttonActualizar.style.cursor = "not-allowed"; // Cambiar cursor

    // Mostrar mensaje de carga con spinner
    loadingMessage.style.display = 'flex';
    mensajeRespuesta.innerHTML = ''; // Limpiar mensaje de respuesta anterior

    const formularioCitasform = document.getElementById('citas-medicas');
    const formularioAntecedentesMedicos = document.getElementById('formulario-antecedentes-medicos');
    const formularioRecetasMedicas = document.getElementById('formReceta');

    const formDataCitasMedicas = new FormData(formularioCitasform);
    const formDataAntecedentesMedicos = new FormData(formularioAntecedentesMedicos);
    const formDataRecetasMedicas = new FormData(formularioRecetasMedicas);
    const fechaReceta = document.getElementById('fecha_receta').value;

    const formDataCompleto = new FormData();
    for (const pair of formDataCitasMedicas.entries()) {
        formDataCompleto.append(pair[0], pair[1]);
    }
    for (const pair of formDataAntecedentesMedicos.entries()) {
        formDataCompleto.append(pair[0], pair[1]);
    }
    for (const pair of formDataRecetasMedicas.entries()) {
        formDataCompleto.append(pair[0], pair[1]);
    }

    formDataCompleto.append('medicamentos', JSON.stringify(window.medicamentos));
    formDataCompleto.append('fecha_receta', fechaReceta);

    // Añadir el ID del registro que se va a actualizar
    const registroId = document.getElementById('registro_id').value;
    formDataCompleto.append('registro_id', registroId);

    const xhr = new XMLHttpRequest();
    xhr.open('POST', '../wp-content/plugins/midocdoc/procces/formulario-medical-update.php', true);
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4) {
            loadingMessage.style.display = 'none'; // Ocultar mensaje de carga
            buttonActualizar.disabled = false;
            buttonCancelar.disabled = false;
            buttonActualizar.value = "Actualizar";
            buttonActualizar.style.backgroundColor = ""; // Restaurar color de fondo
            buttonActualizar.style.cursor = ""; // Restaurar cursor
            if (xhr.status === 200) {
                mensajeRespuesta.innerHTML = xhr.responseText;
                const a = document.createElement("a");
                a.id = "btn-listo";
                a.innerHTML = "Volver";
                a.href = `${window.location.origin}/wp-admin/admin.php?page=latepoint&route_name=customers__index`;
                mensajeRespuesta.appendChild(a);
                document.getElementById('contenedor-botones-update').style.display = "none";
                document.querySelector('span.cerrar').style.display = "none";
            } else {
                console.error('Error al enviar datos:', xhr.statusText);
            }
        }
    };

    xhr.send(formDataCompleto);
}