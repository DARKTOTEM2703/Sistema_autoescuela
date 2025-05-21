document.addEventListener('DOMContentLoaded', function() {
    // Inicializar animaciones de scroll
    const animatedElements = document.querySelectorAll('.animate');
    
    // Función para verificar si un elemento está en el viewport
    function checkIfInView() {
        animatedElements.forEach(element => {
            const elementTop = element.getBoundingClientRect().top;
            const elementBottom = element.getBoundingClientRect().bottom;
            
            // Si el elemento está al menos parcialmente visible
            if (elementTop < window.innerHeight && elementBottom > 0) {
                element.classList.add('visible');
            }
        });
    }
    
    // Verificar elementos visibles al cargar la página
    checkIfInView();
    
    // Verificar elementos visibles al hacer scroll
    window.addEventListener('scroll', checkIfInView);
    
    // Aplicar clase "animate" a los elementos en secciones específicas
    function setupAnimations() {
        // Características (Why Us)
        const features = document.querySelectorAll('.feature-card');
        features.forEach((feature, index) => {
            feature.classList.add('animate', 'slide-up');
            feature.classList.add('delay-' + (index + 1));
        });
        
        // Secciones principales
        const sections = document.querySelectorAll('section');
        sections.forEach(section => {
            const title = section.querySelector('.section-title');
            if (title) {
                title.classList.add('animate', 'fade-in');
            }
        });
        
        // Elementos de contacto
        const contactItems = document.querySelectorAll('.contact-list li');
        contactItems.forEach((item, index) => {
            item.classList.add('animate', 'slide-left');
            item.classList.add('delay-' + (index + 1));
        });
        
        // Columnas del footer
        const footerColumns = document.querySelectorAll('.footer-column');
        footerColumns.forEach((column, index) => {
            column.classList.add('animate', 'slide-up');
            column.classList.add('delay-' + (index + 1));
        });
    }
    
    // Configurar animaciones
    setupAnimations();
    
    // Recheck en caso de cambio de tamaño de ventana
    window.addEventListener('resize', checkIfInView);
});
