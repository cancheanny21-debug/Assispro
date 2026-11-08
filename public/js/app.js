/**
 * AsistePro - JavaScript Principal
 * 
 * Gestión del sidebar responsive y funciones de utilidad.
 */

document.addEventListener('DOMContentLoaded', function() {

    // ======== SIDEBAR TOGGLE (MOBILE) ========
    const sidebar     = document.getElementById('sidebar');
    const btnToggle   = document.getElementById('btn-toggle-sidebar');
    let   overlay     = document.querySelector('.sidebar-overlay');

    // Crear overlay si no existe
    if (sidebar && !overlay) {
        overlay = document.createElement('div');
        overlay.className = 'sidebar-overlay';
        document.body.appendChild(overlay);
    }

    if (btnToggle && sidebar) {
        btnToggle.addEventListener('click', function() {
            sidebar.classList.toggle('active');
            overlay.classList.toggle('active');
        });

        overlay.addEventListener('click', function() {
            sidebar.classList.remove('active');
            overlay.classList.remove('active');
        });
    }

    // ======== AUTO-CERRAR ALERTAS ========
    const alertas = document.querySelectorAll('.alert-dismissible');
    alertas.forEach(function(alerta) {
        setTimeout(function() {
            const bsAlert = bootstrap.Alert.getOrCreateInstance(alerta);
            bsAlert.close();
        }, 5000);
    });

    // ======== CONFIRMAR ELIMINACIÓN ========
    document.querySelectorAll('[data-confirm]').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            const mensaje = this.getAttribute('data-confirm') || '¿Está seguro de realizar esta acción?';
            if (!confirm(mensaje)) {
                e.preventDefault();
            }
        });
    });

    // ======== TOOLTIP BOOTSTRAP ========
    const tooltipTriggers = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    tooltipTriggers.forEach(function(el) {
        new bootstrap.Tooltip(el);
    });

});

/**
 * Realiza una petición Fetch con token CSRF
 *
 * @param {string} url     URL del endpoint
 * @param {object} data    Datos a enviar
 * @param {string} method  Método HTTP (POST por defecto)
 * @returns {Promise}
 */
async function fetchConCSRF(url, data = {}, method = 'POST') {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content
                   || document.querySelector('input[name="csrf_token"]')?.value
                   || '';

    const formData = new FormData();
    formData.append('csrf_token', csrfToken);
    for (const [key, value] of Object.entries(data)) {
        formData.append(key, value);
    }

    const response = await fetch(url, {
        method: method,
        body: formData,
        credentials: 'same-origin'
    });

    return response.json();
}

