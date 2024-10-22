
let currentUrl = new URL(window.location.href);
let customerId = currentUrl.searchParams.get("id");
let medicamentos = [];

// Inicializar la variable global medicamentos
window.medicamentos = window.medicamentos || [];
//carga las pestanas de la pagina superiores
document.addEventListener('DOMContentLoaded', function() {

    let currentUrl = window.location.href;
    console.log(currentUrl);

    if (currentUrl.includes('customers__index')) {
        let tabsContainer = document.createElement('div');
        let tabButton1 = document.createElement('button');
        let tabButton2 = document.createElement('button');
        let tabButton3 = document.createElement('button');
    

        tabButton1.textContent = 'Datos Básicos';
        tabButton2.textContent = 'Historial Médico';
        tabButton3.textContent = 'Informes Médicos';

        tabButton1.classList.add('midocdoc-category-filter-trigger');
        tabButton2.classList.add('midocdoc-category-filter-trigger');
        tabButton3.classList.add('midocdoc-category-filter-trigger');

        // Función para actualizar la selección del botón
        function actualizarSeleccion(tabButton) {
            [tabButton1, tabButton2, tabButton3].forEach(button => {
                button.classList.remove('is-selected');
            });
            tabButton.classList.add('is-selected');
        }

        tabButton1.addEventListener('click', function(event) {
            event.preventDefault();
            actualizarSeleccion(this);
            document.querySelectorAll('.white-box')[0].style.display = 'block';
            document.querySelectorAll('.white-box')[1].style.display = 'none';
            document.getElementById('tab3-content').style.display = 'none';
        });

        tabButton2.addEventListener('click', function(event) {
            event.preventDefault();
            actualizarSeleccion(this);
            document.querySelectorAll('.white-box')[0].style.display = 'none';
            document.querySelectorAll('.white-box')[1].style.display = 'block';
            document.getElementById('tab3-content').style.display = 'none';
        });

        tabButton3.addEventListener('click', function(event) {
            event.preventDefault();
            actualizarSeleccion(this);

            console.log(customerId);
            document.querySelectorAll('.white-box').forEach(box => box.style.display = 'none');
            fetch(datosAjax.ajaxurl + '?action=cargar_inform_content&id=' + customerId)
                .then(response => response.text())
                .then(data => {
                    let tab3Content = document.getElementById('tab3-content');
                    tab3Content.innerHTML = data;
                    tab3Content.style.display = 'block';
                });
        });

        tabsContainer.appendChild(tabButton1);
        tabsContainer.appendChild(tabButton2);
        tabsContainer.appendChild(tabButton3);

        

        let formElement = document.querySelector('form');
        formElement.parentNode.insertBefore(tabsContainer, formElement);

        let tab3Content = document.createElement('div');
        tab3Content.id = 'tab3-content';
        tab3Content.style.display = 'none';
        formElement.parentNode.insertBefore(tab3Content, formElement);

        // Inicializar la primera pestaña como seleccionada
        actualizarSeleccion(tabButton1);
        document.querySelectorAll('.white-box')[0].style.display = 'block';
        document.querySelectorAll('.white-box')[1].style.display = 'none';
    }

    let logoW = document.querySelector('.logo-w');

    // Elimina todos los elementos hijos de 'logo-w'
    while (logoW.firstChild) {
        logoW.removeChild(logoW.firstChild);
    }

    // Crea una nueva imagen
    let nuevaImagen = document.createElement('img');
    nuevaImagen.src = '../wp-content/plugins/midocdoc/img/logo-midocdoc.svg';
    nuevaImagen.alt = 'logo midocdoc';

    // Agrega la clase 'logo' al nuevo elemento <img>
    nuevaImagen.classList.add('logo-midocdoc');

    // Añade la nueva imagen al elemento 'logo-w'
    logoW.appendChild(nuevaImagen);


});


// cargar Formualario para medico pestana opciones  
function openForm(evt, formName) {
    console.log(evt, formName);
    let i, tabcontent, tablinks;
    tabcontent = document.getElementsByClassName("tabcontent");
    for (i = 0; i < tabcontent.length; i++) {
      tabcontent[i].style.display = "none";
    }
    tablinks = document.getElementsByClassName("tablinks");
    for (i = 0; i < tablinks.length; i++) {
      tablinks[i].className = tablinks[i].className.replace(" active", "");
    }
    if (formName) {
        document.getElementById(formName).style.display = "block"; 
    }
    evt.currentTarget.className += " active";
    document.getElementById('guardar-informe').style.display = 'block';
  }
  
  
  
  
function firstab(){
    // Seleccionar el elemento por su ID
    var tabcontent = document.querySelector('#tabs-citas-medicas');
    
    // Verificar si el elemento existe para evitar errores
    if (tabcontent) {
        // Simular un click en el elemento seleccionado
        tabcontent.click();
    } else {
        console.log('El elemento no fue encontrado');
    }
}
 
  


// cargar form_medical.php
    document.body.addEventListener('click', function(event) {
        if (event.target.classList.contains('ver-detalles')) {
            event.preventDefault();  // Prevenir cualquier comportamiento predeterminado

            let appointmentId = event.target.getAttribute('data-appointment-id');
            
            // Realizar la solicitud fetch
            fetch(datosAjax.ajaxurl + '?action=cargar_form_medical_content&id=' + customerId)
                .then(response => response.text())
                .then(data => {

                    
                    // Ocular todos los demás contenidos, si es necesario
                    document.querySelectorAll('.appointment-card').forEach(box => box.style.display = 'none');
                    // Encuentra el elemento en el que deseas mostrar los datos
                    let container = document.getElementById('container-para-formulario');
                    
                    // Inserta los datos en ese elemento
                    if (container) {
                        container.innerHTML = data;
                        container.style.display = 'flex'; // Asegúrate de que el contenedor sea visible
                    }
                    
                    
                    firstab()
                    contador()
                    
                })
                .catch(error => console.error('Error:', error));
        }
    });


  // boton de editar: 
// Función para añadir tu elemento personalizado
function agregarMiElemento() {

    // valor del id del cliente

    // Obtener el valor del campo de email
let emailInput = document.getElementById('customer_email');
let emailValue = emailInput.value;

// letiable para almacenar el ID del cliente encontrado
let customerId = null;

// Obtener todos los elementos de la opción de cliente
let customerOptions = document.querySelectorAll('.customer-option');

// Iterar sobre cada opción de cliente
customerOptions.forEach(function(customerOption) {
    // Obtener el email del cliente de la lista
    let customerEmail = customerOption.querySelector('strong').textContent;

    // Comparar si el email coincide
    if(customerEmail === emailValue) {
        // Extraer el customer_id
        let customerParams = customerOption.getAttribute('data-os-params');
        customerId = customerParams.split('=')[1];

        // Salir del bucle una vez que se encuentra la coincidencia
        return;
    }
});

// Ahora 'customerId' contiene el ID del cliente que coincide con el email, si existe una coincidencia
console.log(customerId);


    //END valor del id

    let contenedor = document.querySelector('.os-form-content');
    if (contenedor) {
        let nuevoElemento = document.createElement('a');
        nuevoElemento.textContent = 'Ver Informes';
        let urlBase = window.location.origin;
        nuevoElemento.href = `${urlBase}/wp-admin/admin.php?page=latepoint&route_name=customers__edit_form`
        contenedor.insertBefore(nuevoElemento, contenedor.firstChild);
    }



}

// Configuración del observer
let observer = new MutationObserver(function(mutations) {
    mutations.forEach(function(mutation) {
        if (mutation.type === 'childList') {
            let elementosAgregados = Array.from(mutation.addedNodes);
            let elementoRelevante = elementosAgregados.find(node => node.querySelector && node.querySelector('.os-form-content'));
            if (elementoRelevante) {
                agregarMiElemento();
            }
        }
    });
});

// Función para manejar clics en elementos específicos
function manejarClic() {
    observer.observe(document.body, { childList: true, subtree: true });
}

// Añadir event listeners a los elementos deseados
document.querySelectorAll('[data-os-action="bookings__quick_edit"]').forEach(function(elemento) {
    elemento.addEventListener('click', manejarClic);
});


// js que agrega un medicamento a la receta 

document.getElementById('btnAgregar').addEventListener('click', function() {
    let medicamento = document.getElementById('medicamento');
    let lista = document.getElementById('listaMedicamentos');
    let medicamentoSeleccionado = medicamento.options[medicamento.selectedIndex].text;
    
    let item = document.createElement('div');
    item.textContent = medicamentoSeleccionado;
    lista.appendChild(item);
    

    
    
    
});


function citasmedicasform() {
    let buttonGuardar = document.getElementById('enviar-form-citas-medicas-abajo');
    buttonGuardar.style.display = "block";

    let formularioCitasMedicas = document.getElementById('guardar-informe');
    let formularioCitasform = document.getElementById('citas-medicas');
    let formularioAntecedentesMedicos = document.getElementById('formulario-antecedentes-medicos');
    let formularioRecetasMedicas = document.getElementById('formReceta');

    formularioCitasMedicas.addEventListener('click', function (e) {
        e.preventDefault();

        var formDataCitasMedicas = new FormData(formularioCitasform);
        var formDataAntecedentesMedicos = new FormData(formularioAntecedentesMedicos);
        var formDataRecetasMedicas = new FormData(formularioRecetasMedicas);

        var fechaReceta = document.getElementById('fecha_receta').value;
        console.log('Hago click en el boton de guardar' + JSON.stringify(window.medicamentos));

        // Combina los datos de ambos formularios en un solo FormData
       var formDataCompleto = new FormData();
        for (var pair of formDataCitasMedicas.entries()) {
            formDataCompleto.append(pair[0], pair[1]);
        }
        for (var pair of formDataAntecedentesMedicos.entries()) {
            formDataCompleto.append(pair[0], pair[1]);
        }
        for (var pair of formDataRecetasMedicas.entries()) {
            formDataCompleto.append(pair[0], pair[1]);
        }

        // Agregar el arreglo de medicamentos como un string JSON
        formDataCompleto.append('medicamentos', JSON.stringify(window.medicamentos));
        formDataCompleto.append('fecha_receta', fechaReceta);
        

        var xhr = new XMLHttpRequest();
        xhr.open('POST', '../wp-content/plugins/midocdoc/procces/formulario-medical.php', true);
        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4) {
                if (xhr.status === 200) {
                    // Muestra la respuesta en el contenedor
document.getElementById('mensajeRespuesta').innerHTML = xhr.responseText;

// Crear un nuevo elemento 'a'
var a = document.createElement("a");
a.id = "btn-listo";
a.innerHTML = "Volver";

// Establecer la URL del elemento 'a'
var baseUrl = window.location.origin; // Esto obtendrá la parte base de la URL (http://midocdoc.local)
a.href = baseUrl + "/wp-admin/admin.php?page=latepoint&route_name=customers__edit_form&id=" + customerId;

// Agregar el elemento 'a' al elemento 'mensajeRespuesta'
document.getElementById('mensajeRespuesta').appendChild(a);

// Ocultar el elemento 'contenedor-botones-guarda'
document.getElementById('contenedor-botones-guarda').style.display = "none";
document.querySelector('span.cerrar').style.display = "none";
                } else {
                    console.error('Error al enviar datos:', xhr.statusText);
                }
            }
        };

        xhr.send(formDataCompleto); 
    });
}



// agrega la funcion de agregar medicamentos a la receta

function iniciarAgregarMedicamento(){

    let buttonGuardar =document.getElementById('enviar-form-citas-medicas-abajo')
    console.log(buttonGuardar.style.display);
    buttonGuardar.style.display = "block";

    // Obtener el elemento del campo de fecha
    var fechaRecetaInput = document.getElementById('fecha_receta');

    // Obtener la fecha actual
    var fechaActual = new Date();

    // Formatear la fecha actual como YYYY-MM-DD (formato esperado por el input type="date")
    var fechaFormatted = fechaActual.toISOString().slice(0, 10);

    // Asignar la fecha formateada al campo de fecha
    fechaRecetaInput.value = fechaFormatted;

    var btnAgregar = document.getElementById('btnAgregar');
    var listaMedicamentos = document.getElementById('listaMedicamentos');
    var idMedicamento = 0; // Identificador único para cada medicamento
    
    btnAgregar.addEventListener('click', function () {
        console.log('Agregando medicamento...');
    
        var idActual = idMedicamento++; // Asignar y aumentar el ID para el medicamento actual
    
        // Crear un div para el nuevo medicamento
        var nuevoMedicamento = document.createElement('div');
        nuevoMedicamento.className = 'medicamento';
    
        // Crear un ul para la información del medicamento
        var ulMedicamento = document.createElement('ul');
    
        // Array de propiedades del medicamento
        var propiedades = ['descricion', 'presentation', 'concentration', 'administration_route', 'quantity', 'dosage'];
    
        // Objeto para almacenar los valores de las propiedades del medicamento actual
        var medicamentoActual = {};
    
        // Función para obtener el texto en español de la propiedad
        function obtenerTextoEnEspanol(propiedad) {
            switch (propiedad) {
                case 'descricion':
                    return 'Descripción';
                case 'presentation':
                    return 'Presentación';
                case 'concentration':
                    return 'Concentración';
                case 'administration_route':
                    return 'Vía de Administración';
                case 'quantity':
                    return 'Cantidad';
                case 'dosage':
                    return 'Dosificación';
                default:
                    return propiedad;
            }
        }
    
        // Iterar sobre las propiedades y agregarlas al objeto medicamentoActual
        propiedades.forEach(function (propiedad) {
            var label = obtenerTextoEnEspanol(propiedad);
            var valor = document.getElementById(propiedad).value;
            
            // Agregar propiedad y valor al objeto medicamentoActual
            medicamentoActual[label.toLowerCase()] = valor;
    
            // Crear y añadir el li al ul
            var li = document.createElement('li');
            li.textContent = label + ': ' + valor;
            li.className = propiedad; // Mantener la clase correspondiente
            ulMedicamento.appendChild(li);
        });
    
        // Asignar un ID al medicamento actual antes de añadirlo al array
        medicamentoActual.id = idActual;
    
        // Añadir el medicamento actual al array de medicamentos
        if (!window.medicamentos) {
            window.medicamentos = [];
        }
        window.medicamentos.push(medicamentoActual);
    
        // Crear un botón para eliminar el medicamento
        var btnEliminar = document.createElement('button');
        btnEliminar.className = 'eliminar-btn';
        btnEliminar.textContent = 'Eliminar';
        btnEliminar.addEventListener('click', function () {
            // Eliminar visualmente el medicamento
            listaMedicamentos.removeChild(nuevoMedicamento);
            // Eliminar el medicamento del array
            window.medicamentos = window.medicamentos.filter(function(medicamento) {
                return medicamento.id !== idActual;
            });
            console.log('Medicamentos después de eliminar:', window.medicamentos);
        });
    
        // Agregar el ul y el botón de eliminar al nuevo medicamento
        nuevoMedicamento.appendChild(ulMedicamento);
        nuevoMedicamento.appendChild(btnEliminar);
    
        // Agregar el nuevo medicamento a la lista
        listaMedicamentos.appendChild(nuevoMedicamento);
    
        // Limpiar los campos del medicamento en el formulario principal
        propiedades.forEach(function (propiedad) {
            document.getElementById(propiedad).value = '';
        });
    
        // Mostrar los medicamentos en la consola
        //medicamentos.push(window.medicamentos);
        console.log('Medicamentos:', window.medicamentos);
        
        
            // restablece el contador de caracteres
    // Encuentra todos los campos de entrada y sus respectivos contadores
    let campos = document.querySelectorAll('.campo-recetas');
    
    campos.forEach(function(campo) {
        // Restablece el valor del campo de entrada
        campo.value = '';
        
        // Encuentra el contador de caracteres asociado y lo actualiza
        let contador = campo.nextElementSibling; // Asumiendo que el contador sigue al campo de entrada
        let maxLength = campo.getAttribute('maxlength');
        contador.textContent = `0/${maxLength}`;
        
        
        // Quita la clase .bajo si está presente
        if (contador.classList.contains('bajo')) {
            contador.classList.remove('bajo');
        }
        
    });
        
        //END
        
    });
    
    
};

//javascript para el formulario popup de citas medicas

function mostrarPopup() {
    var popup = document.getElementById('miPopup');
    popup.classList.add('mostrado');
}

function cerrarPopup() {
    var popup = document.getElementById('miPopup');
    popup.classList.remove('mostrado');
}


// funcion para generar el pdf de la receta medica
function generarPDF() {
    var informeId = '';
    var xhrPDF = new XMLHttpRequest();
    xhrPDF.open('GET', '?midocdoc_generar_pdf=' + informeId, true);
    xhrPDF.onreadystatechange = function() {
        if (xhrPDF.readyState == 4 && xhrPDF.status == 200) {
            console.log('PDF generado');
            // Aquí puedes manejar la respuesta de generación del PDF
        }
    };
    xhrPDF.send();
}


// popup de envio de email
function showPopup(message) {
    document.getElementById('popup-message').textContent = message;
    document.getElementById('popup').style.display = 'block';
}

function closePopup() {
    document.getElementById('popup').style.display = 'none';
}

fetch( midocdoc_vars.plugin_url + '../procces/send_email.php', {
    method: 'POST',
    body: new FormData(document.getElementById('tu-formulario'))
})
.then(response => response.json())
.then(data => {
    if (data.success) {
        showPopup(data.message);
    } else {
        showPopup(data.message);
    }
})
.catch(error => {
    showPopup('Ocurrió un error: ' + error);
});



// Contador de caracteres limitado

function contador(){
    
    let campos = document.querySelectorAll('.campo-con-contador');
     console.log(campos);
     
    campos.forEach(function(campo) {
        let maxLength = campo.getAttribute('maxlength');

        // Crea el elemento del contador y lo inserta en el DOM justo después del campo
        let contador = document.createElement('div');
        contador.classList.add('contador-caracteres');
        contador.textContent = `0/${maxLength}`;
        campo.parentNode.insertBefore(contador, campo.nextSibling);

        // Actualiza el contador en respuesta al evento de entrada
        campo.oninput = function() {
            let caracteresUsados = campo.value.length;
            contador.textContent = `${caracteresUsados}/${maxLength}`;
            
            // Añade o remueve clase para estilos, como el color, basado en los caracteres usados
            if(maxLength - caracteresUsados <= 10) {
                contador.classList.add('bajo');
            } else {
                contador.classList.remove('bajo');
            }
        };
    });
}
