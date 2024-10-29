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

    const addColumnToRow = (row, isHeader = false) => {
        const cell = document.createElement(isHeader ? 'th' : 'td');
        cell.textContent = isHeader ? 'Informes Médicos' : 'Ver Informes Médicos';
        cell.classList.add('informes-medicos');
        row.insertBefore(cell, row.children[2]);
        return cell;
    };

    if (thead) {
        const headerRow = thead.querySelector('tr');
        if (headerRow && !headerRow.querySelector('.informes-medicos')) {
            addColumnToRow(headerRow, true);
        }
    }

    if (tbody) {
        tbody.querySelectorAll('tr').forEach(row => {
            if (!row.querySelector('.informes-medicos')) {
                const cell = addColumnToRow(row);
                const customerId = row.getAttribute('data-os-params')?.match(/customer_id=(\d+)/)?.[1];
                if (customerId) {
                    cell.addEventListener('click', event => {
                        event.stopPropagation();
                        event.preventDefault();
                        showMedicalReports(customerId);
                    });
                }
            }
        });
    }

    if (tfoot) {
        const footerRow = tfoot.querySelector('tr');
        if (footerRow && !footerRow.querySelector('.informes-medicos')) {
            addColumnToRow(footerRow, true);
        }
    }
}

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

    sidebar.style.display = 'block';
    setTimeout(() => sidebar.classList.add('active'), 10);

    const medicalReportsContent = document.getElementById('medicalReportsContent');
    medicalReportsContent.innerHTML = '<p>Cargando informe...</p>';

    fetch(`${datosAjax.ajaxurl}?action=cargar_inform_content&id=${customerId}`)
        .then(response => response.text())
        .then(data => medicalReportsContent.innerHTML = data)
        .catch(() => medicalReportsContent.innerHTML = '<p>Error al cargar el informe.</p>');
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

document.addEventListener('DOMContentLoaded', () => {
    addNewColumn();

    const pageSelector = document.getElementById('tablePaginationPageSelector');
    if (pageSelector) {
        pageSelector.addEventListener('change', addNewColumn);
    }

    const observer = new MutationObserver(mutationsList => {
        mutationsList.forEach(mutation => {
            if (mutation.type === 'childList') {
                addNewColumn();
            }
        });
    });

    const tableContainer = document.querySelector('table');
    if (tableContainer) {
        observer.observe(tableContainer, { childList: true, subtree: true });
    }

    const closeSidebarButton = document.getElementById('closeSidebar');
    if (closeSidebarButton) {
        closeSidebarButton.addEventListener('click', closeSidebar);
    }

    const overlay = document.querySelector('.sidebar-overlay');
    if (overlay) {
        overlay.addEventListener('click', closeSidebar);
    }

    const currentUrl = new URL(window.location.href);
    const customerId = currentUrl.searchParams.get("id");
    window.medicamentos = window.medicamentos || [];

    if (currentUrl.pathname.includes('customers__index')) {
        const tabsContainer = document.createElement('div');
        const tabButtons = ['Datos Básicos', 'Historial Médico', 'Informes Médicos'].map(text => {
            const button = document.createElement('button');
            button.textContent = text;
            button.classList.add('midocdoc-category-filter-trigger');
            return button;
        });

        const actualizarSeleccion = tabButton => {
            tabButtons.forEach(button => button.classList.remove('is-selected'));
            tabButton.classList.add('is-selected');
        };

        tabButtons[0].addEventListener('click', event => {
            event.preventDefault();
            actualizarSeleccion(event.target);
            document.querySelectorAll('.white-box')[0].style.display = 'block';
            document.querySelectorAll('.white-box')[1].style.display = 'none';
            document.getElementById('tab3-content').style.display = 'none';
        });

        tabButtons[1].addEventListener('click', event => {
            event.preventDefault();
            actualizarSeleccion(event.target);
            document.querySelectorAll('.white-box')[0].style.display = 'none';
            document.querySelectorAll('.white-box')[1].style.display = 'block';
            document.getElementById('tab3-content').style.display = 'none';
        });

        tabButtons[2].addEventListener('click', event => {
            event.preventDefault();
            actualizarSeleccion(event.target);
            document.querySelectorAll('.white-box').forEach(box => box.style.display = 'none');
            fetch(`${datosAjax.ajaxurl}?action=cargar_inform_content&id=${customerId}`)
                .then(response => response.text())
                .then(data => {
                    const tab3Content = document.getElementById('tab3-content');
                    tab3Content.innerHTML = data;
                    tab3Content.style.display = 'block';
                });
        });

        tabButtons.forEach(button => tabsContainer.appendChild(button));
        document.querySelector('form').parentNode.insertBefore(tabsContainer, document.querySelector('form'));

        const tab3Content = document.createElement('div');
        tab3Content.id = 'tab3-content';
        tab3Content.style.display = 'none';
        document.querySelector('form').parentNode.insertBefore(tab3Content, document.querySelector('form'));

        actualizarSeleccion(tabButtons[0]);
        document.querySelectorAll('.white-box')[0].style.display = 'block';
        document.querySelectorAll('.white-box')[1].style.display = 'none';
    }

    const logoW = document.querySelector('.logo-w');
    if (logoW) {
        logoW.innerHTML = '';
        const nuevaImagen = document.createElement('img');
        nuevaImagen.src = '../wp-content/plugins/midocdoc/img/logo-midocdoc.svg';
        nuevaImagen.alt = 'logo midocdoc';
        nuevaImagen.classList.add('logo-midocdoc');
        logoW.appendChild(nuevaImagen);
    }
});

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
    alert('Medicamentos antes de enviar:', window.medicamentos);

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