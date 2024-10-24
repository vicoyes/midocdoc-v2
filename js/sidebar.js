console.log('El script sidebar.js se ha cargado correctamente.');
// Incluir el archivo wp-content/plugins/midocdoc-1/js/latepoint_inform.js

jQuery(document).ready(function($) {
    // Función para abrir el sidebar
    function openSidebar(content) {
        $('#medicalReportsSidebar').show();
        $('#medicalReportsContent').html(content); // Cargar contenido dinámico
    }

    // Función para cerrar el sidebar
    function closeSidebar() {
        $('#medicalReportsSidebar').hide();
    }

    // Manejar el clic en los botones de "Ver Informes Médicos"
    $('.ver-informes-medicos').on('click', function(e) {
        e.preventDefault();
        const customerId = $(this).data('customer-id'); // Suponiendo que tienes el ID del cliente en el botón
        console.log('Mostrando informes médicos para el cliente con ID:', customerId);
        //console.log(datosAjax.ajaxurl);
        console.log(datosAjax.ajaxurl);
        console.log('.Ver-informes-medicos');
        // Realizar una solicitud AJAX para obtener el informe médico
        $.ajax({
            url: datosAjax.ajaxurl,
            data: { action: 'cargar_inform_content', id: customerId },
            success: function(response) {
                openSidebar(response); // Mostrar el contenido en el sidebar
            },
            error: function() {
                alert('Error al cargar el informe médico.');
            }
    });

    // Manejar el clic en el botón de cierre
    $('#closeSidebar').on('click', function() {
        closeSidebar();
    });

    let edit = document.querySelectorAll('.edit-medical-button')
    console.log(edit);

    // carga el elemento que busca el usuario
    
});


// nuevo elemento 
// Manejar el clic en los botones de "Ver Detalles"

document.body.addEventListener('click', function(event) {
    if (event.target.classList.contains('ver-detalles')) {
        event.preventDefault();  // Prevenir cualquier comportamiento predeterminado

        let appointmentId = event.target.getAttribute('data-appointment-id');
        
        // Realizar la solicitud fetch
        fetch(datosAjax.ajaxurl + '?action=cargar_form_medical_content&id=' + appointmentId)
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
});

});



