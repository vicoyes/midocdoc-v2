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