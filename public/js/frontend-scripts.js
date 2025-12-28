/**
 * Scripts del frontend - Funcionalidad de la barra lateral
 */

(function($) {
    'use strict';
    
    $(document).ready(function() {
        
        var $sidebar = $('#wp-price-sidebar');
        var $toggle = $('.wp-price-sidebar-toggle');
        
        if (!$sidebar.length) {
            return;
        }
        
        // Toggle al hacer clic en el botón
        $toggle.on('click', function(e) {
            e.preventDefault();
            toggleSidebar();
        });
        
        // Función para abrir/cerrar
        function toggleSidebar() {
            $sidebar.toggleClass('collapsed');
            
            // Guardar estado en sessionStorage
            var isCollapsed = $sidebar.hasClass('collapsed');
            try {
                sessionStorage.setItem('wpPriceSidebarCollapsed', isCollapsed ? '1' : '0');
            } catch (e) {
                // Ignorar errores de storage
            }
        }
        
        // Restaurar estado al cargar la página
        try {
            var savedState = sessionStorage.getItem('wpPriceSidebarCollapsed');
            if (savedState === '0') {
                $sidebar.removeClass('collapsed');
            }
        } catch (e) {
            // Ignorar errores de storage
        }
        
        // Cerrar con tecla Escape
        $(document).on('keydown', function(e) {
            if (e.key === 'Escape' && !$sidebar.hasClass('collapsed')) {
                toggleSidebar();
            }
        });
        
        // Smooth scroll para el contenido
        $('.wp-price-sidebar-body').on('wheel', function(e) {
            var $this = $(this);
            var scrollTop = $this.scrollTop();
            var scrollHeight = $this[0].scrollHeight;
            var height = $this.height();
            var delta = e.originalEvent.deltaY;
            
            // Prevenir scroll del body cuando se llega al final
            if ((delta > 0 && scrollTop + height >= scrollHeight) || 
                (delta < 0 && scrollTop <= 0)) {
                e.preventDefault();
            }
        });
        
    });
    
})(jQuery);