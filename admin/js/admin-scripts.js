/**
 * Scripts del panel de administración
 */

(function($) {
    'use strict';
    
    $(document).ready(function() {
        
        // Confirmación antes de eliminar
        $('.button-link-delete').on('click', function(e) {
            if (!confirm('¿Estás seguro de que deseas eliminar este elemento? Esta acción no se puede deshacer.')) {
                e.preventDefault();
                return false;
            }
        });
        
        // Auto-foco en el primer campo del formulario
        if ($('.division-form, .item-form').length) {
            $('.division-form input[type="text"]:first, .item-form input[type="text"]:first').focus();
        }
        
        // Validación básica de formularios
        $('.division-form, .item-form').on('submit', function(e) {
            var requiredFields = $(this).find('[required]');
            var isValid = true;
            
            requiredFields.each(function() {
                if ($(this).val().trim() === '') {
                    isValid = false;
                    $(this).addClass('error-field');
                } else {
                    $(this).removeClass('error-field');
                }
            });
            
            if (!isValid) {
                e.preventDefault();
                alert('Por favor, completa todos los campos obligatorios.');
                return false;
            }
        });
        
        // Remover clase de error al escribir
        $('input[required]').on('input', function() {
            $(this).removeClass('error-field');
        });
        
        // Añadir estilo a campos con error
        var style = $('<style>.error-field { border-color: #dc3232 !important; }</style>');
        $('head').append(style);
        
    });
    
})(jQuery);