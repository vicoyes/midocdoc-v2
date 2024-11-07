console.log('Script table_new.js cargado correctamente.');
let customerId;

function addNewColumn() {
    const currentUrl = new URL(window.location.href);
    if (currentUrl.searchParams.get('route_name') !== 'customers__index') {
        console.log('La función addNewColumn no se ejecutará en esta URL.');
        return;
    }

    const table = document.querySelector('table');
    if (!table) return;

    const thead = table.querySelector('thead');
    const tbody = table.querySelector('tbody');
    const tfoot = table.querySelector('tfoot');

    /**
     * Agrega una nueva columna a una fila de la tabla.
     * @param {HTMLTableRowElement} row - La fila a la que se agregará la columna.
     * @param {boolean} isHeader - Indica si la fila es de encabezado.
     */
    const addColumnToRow = (row, isHeader = false) => {
        // Crear la celda usando insertCell para mayor eficiencia
        const cell = row.insertCell(2); // Insertar en el índice 2 (tercera columna)
        cell.textContent = isHeader ? 'Informes Médicos' : 'Ver Informes Médicos';
        cell.classList.add('informes-medicos');
        return cell;
    };

    /**
     * Procesa una colección de filas para agregar la nueva columna si no existe.
     * @param {NodeListOf<HTMLTableRowElement>} rows - Las filas a procesar.
     * @param {boolean} isHeader - Indica si las filas son de encabezado o pie.
     */
    const processRows = (rows, isHeader = false) => {
        rows.forEach(row => {
            if (!row.querySelector('.informes-medicos')) {
                addColumnToRow(row, isHeader);
            }
        });
    };

    // Agregar columnas al encabezado
    if (thead) {
        const headerRows = thead.querySelectorAll('tr');
        processRows(headerRows, true);
    }

    // Agregar columnas al cuerpo
    if (tbody) {
        const bodyRows = tbody.querySelectorAll('tr');
        processRows(bodyRows, false);
    }

    // Agregar columnas al pie
    if (tfoot) {
        const footerRows = tfoot.querySelectorAll('tr');
        processRows(footerRows, true);
    }

    // Implementar Delegación de Eventos para manejar clics en las nuevas celdas
    table.removeEventListener('click', handleCellClick); // Evitar múltiples listeners
    table.addEventListener('click', handleCellClick);
}


 //Maneja el evento de clic en las celdas "Ver Informes Médicos".

function handleCellClick(event) {
    const cell = event.target.closest('.informes-medicos');
    if (cell) {
        const row = cell.parentElement;
        const dataParams = row.getAttribute('data-os-params');
        const customerIdMatch = dataParams?.match(/customer_id=(\d+)/);
        const customerId = customerIdMatch ? customerIdMatch[1] : null;

        if (customerId) {
            event.stopPropagation();
            event.preventDefault();
            showMedicalReports(customerId);
        }
    }
}

// Inicializar la función al cargar el DOM
document.addEventListener('DOMContentLoaded', () => {
    addNewColumn();

    // Observador para detectar cambios en la tabla y re-ejecutar addNewColumn si es necesario
    const observer = new MutationObserver((mutationsList) => {
        for (const mutation of mutationsList) {
            if (mutation.type === 'childList') {
                addNewColumn();
                break; // Evita múltiples ejecuciones innecesarias
            }
        }
    });

    const tableContainer = document.querySelector('table');
    if (tableContainer) {
        observer.observe(tableContainer, { childList: true, subtree: true });
    }
});



function showMedicalReports(customerId) {
    const newUrl = new URL(window.location.href);
    newUrl.searchParams.set('id', customerId);
    window.history.pushState({}, '', newUrl);

    const sidebar = document.getElementById('medicalReportsSidebar');
    const overlay = document.querySelector('.sidebar-overlay');

    if (overlay) {
        overlay.style.display = 'block';
        setTimeout(() => overlay.classList.add('active'), 10);
    }

    if (sidebar) {
        sidebar.style.display = 'block';
        setTimeout(() => sidebar.classList.add('active'), 10);
        const medicalReportsContent = document.getElementById('medicalReportsContent');
        if (medicalReportsContent) {
            medicalReportsContent.innerHTML = `
  <div class="center-content">
    <div class="spinner-form"></div>
  </div>
`;
            fetch(`${datosAjax.ajaxurl}?action=cargar_inform_content&id=${customerId}`)
                .then(response => response.text())
                .then(data => medicalReportsContent.innerHTML = data)
                .catch(() => medicalReportsContent.innerHTML = '<p>Error al cargar el informe.</p>');
        }
    } else {
        console.error('El elemento #medicalReportsSidebar no se encontró en el DOM.');
    }
}


function closeSidebar() {
    const sidebar = document.getElementById('medicalReportsSidebar');
    const overlay = document.querySelector('.sidebar-overlay');

    if (sidebar) {
        sidebar.classList.remove('active');
        if (overlay) {
            overlay.classList.remove('active');
            setTimeout(() => {
                sidebar.style.display = 'none';
                overlay.style.display = 'none';
            }, 300);
        }
    }
}

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
    document.getElementById('guardar-informe').style.display = 'block';
}

function firstab() {
    const tabcontent = document.querySelector('#tabs-citas-medicas');
    if (tabcontent) {
        tabcontent.click();
    } else {
        console.log('El elemento no fue encontrado');
    }
}

document.body.addEventListener('click', event => {
    if (event.target.classList.contains('ver-detalles')) {
        event.preventDefault();
        const buttonGuardar = document.getElementById('enviar-form-citas-medicas-abajo');
        //buttonGuardar.style.display = "block";
        
        const appointmentId = event.target.getAttribute('data-appointment-id');
        fetch(`${datosAjax.ajaxurl}?action=cargar_form_medical_content&id=${customerId}`)
            .then(response => response.text())
            .then(data => {
                const container = document.getElementById('container-para-formulario');
                if (container) {
                    container.innerHTML = data;
                    container.style.display = 'flex';
                }
                firstab();
                contador();
            })
            .catch(error => console.error('Error:', error));
    }
});

function agregarMiElemento() {
    const emailInput = document.getElementById('customer_email');
    const emailValue = emailInput.value;
    let customerId = null;

    document.querySelectorAll('.customer-option').forEach(customerOption => {
        const customerEmail = customerOption.querySelector('strong').textContent;
        if (customerEmail === emailValue) {
            const customerParams = customerOption.getAttribute('data-os-params');
            customerId = customerParams.split('=')[1];
            return;
        }
    });

    const contenedor = document.querySelector('.os-form-content');
    if (contenedor) {
        const nuevoElemento = document.createElement('a');
        nuevoElemento.textContent = 'Ver Informes';
        nuevoElemento.href = `${window.location.origin}/wp-admin/admin.php?page=latepoint&route_name=customers__index`;
        contenedor.insertBefore(nuevoElemento, contenedor.firstChild);
    }
}

document.querySelectorAll('[data-os-action="bookings__quick_edit"]').forEach(elemento => {
    elemento.addEventListener('click', () => {
        observer.observe(document.body, { childList: true, subtree: true });
    });
});

function iniciarAgregarMedicamento() {
    // Establecer fecha actual
      const fechaRecetaInput = document.getElementById('fecha_receta');
      console.log('Fecha receta:', fechaRecetaInput);
      const fechaActual = new Date();
      const fechaFormatted = fechaActual.toISOString().slice(0, 10);
      fechaRecetaInput.value = fechaFormatted;
  }
  
  
  
  function agregarMedicamento() {
      console.log('Agregando medicamento...');
      if (!window.medicamentoIdCounter) {
          window.medicamentoIdCounter = 1;
      }
      const idActual = window.medicamentoIdCounter++;
      const nuevoMedicamento = document.createElement('div');
      nuevoMedicamento.className = 'medicamento';
  
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
  
      const btnEliminar = document.createElement('button');
      btnEliminar.textContent = 'Eliminar';
      btnEliminar.classList.add('btn-eliminar');
      btnEliminar.addEventListener('click', () => {
          const listaMedicamentos = document.getElementById('listaMedicamentos');
          console.log('Lista de medicamentos antes de eliminar:', window.medicamentos);
          listaMedicamentos.removeChild(nuevoMedicamento);
          window.medicamentos = window.medicamentos.filter(
              medicamento => medicamento.id !== idActual
          );
          console.log('Medicamentos después de eliminar:', window.medicamentos);
      });
  
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
  


function citasmedicasform() {
    //console.log('Medicamentos antes de enviar:', JSON.stringify(window.medicamentos));
    const buttonGuardar = document.getElementById('guardar-informe');
    const buttonCancelar = document.getElementById('cancelar-informe');
    const loadingMessage = document.getElementById('loading');
    const mensajeRespuesta = document.getElementById('mensajeRespuesta');

    // Deshabilitar botones y mostrar feedback visual
    buttonGuardar.disabled = true;
    buttonCancelar.disabled = true;
    buttonGuardar.value = "Guardando...";
    buttonGuardar.style.backgroundColor = "#ccc"; // Cambiar color de fondo
    buttonGuardar.style.cursor = "not-allowed"; // Cambiar cursor

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
        console.log(pair[0], pair[1]);
    }

    // Verificar el contenido de window.medicamentos antes de enviarlo
    console.log('Medicamentos antes de enviar:', JSON.stringify(window.medicamentos));

    formDataCompleto.append('medicamentos', JSON.stringify(window.medicamentos));
    formDataCompleto.append('fecha_receta', fechaReceta);

    const xhr = new XMLHttpRequest();
    xhr.open('POST', '../wp-content/plugins/midocdoc/procces/formulario-medical.php', true);
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4) {
            loadingMessage.style.display = 'none'; // Ocultar mensaje de carga
            buttonGuardar.disabled = false;
            buttonCancelar.disabled = false;
            buttonGuardar.value = "Guardar";
            buttonGuardar.style.backgroundColor = ""; // Restaurar color de fondo
            buttonGuardar.style.cursor = ""; // Restaurar cursor
            if (xhr.status === 200) {
                mensajeRespuesta.innerHTML = xhr.responseText;
                const a = document.createElement("a");
                a.id = "btn-listo";
                a.innerHTML = "Volver";
                a.href = `${window.location.origin}/wp-admin/admin.php?page=latepoint&route_name=customers__index`;
                mensajeRespuesta.appendChild(a);
                document.getElementById('contenedor-botones-guarda').style.display = "none";
                document.querySelector('span.cerrar').style.display = "none";
            } else {
                console.error('Error al enviar datos:', xhr.statusText);
            }
        }
    };

    // Verificar el contenido de formDataCompleto antes de enviarlo
    for (const pair of formDataCompleto.entries()) {
        console.log(pair[0] + ': ' + pair[1]);
    }

    xhr.send(formDataCompleto);
}



function mostrarPopup() {
    const popup = document.getElementById('miPopup');
    popup.classList.add('mostrado');
}

function cerrarPopup() {
    const popup = document.getElementById('miPopup');
    popup.classList.remove('mostrado');
}

function generarPDF() {
    const informeId = '';
    const xhrPDF = new XMLHttpRequest();
    xhrPDF.open('GET', `?midocdoc_generar_pdf=${informeId}`, true);
    xhrPDF.onreadystatechange = function() {
        if (xhrPDF.readyState == 4 && xhrPDF.status == 200) {
            console.log('PDF generado');
        }
    };
    xhrPDF.send();
}

function showPopup(message) {
    document.getElementById('popup-message').textContent = message;
    document.getElementById('popup').style.display = 'block';
}

function closePopup() {
    document.getElementById('popup').style.display = 'none';
}

function contador() {
    const campos = document.querySelectorAll('.campo-con-contador');
    campos.forEach(campo => {
        const maxLength = campo.getAttribute('maxlength');
        const contador = document.createElement('div');
        contador.classList.add('contador-caracteres');
        contador.textContent = `0/${maxLength}`;
        campo.parentNode.insertBefore(contador, campo.nextSibling);

        campo.oninput = function() {
            const caracteresUsados = campo.value.length;
            contador.textContent = `${caracteresUsados}/${maxLength}`;
            if (maxLength - caracteresUsados <= 10) {
                contador.classList.add('bajo');
            } else {
                contador.classList.remove('bajo');
            }
        };
    });
}