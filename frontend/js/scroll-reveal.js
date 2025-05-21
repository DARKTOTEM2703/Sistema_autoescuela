/**
 * Script para controlar las animaciones de scroll reveal
 * AutoEscuela Segura - Efectos modernos de scroll
 */

document.addEventListener('DOMContentLoaded', function() {
    // Inicializar elementos con efecto reveal
    const revealElements = document.querySelectorAll('.reveal');
    
    // Función para preparar elementos para animación
    const prepareElements = () => {
        // Primero hacemos que todos los elementos estén visibles brevemente para evitar FOUC
        // (Flash of Unstyled Content)
        setTimeout(() => {
            revealElements.forEach(element => {
                // Solo preparamos los elementos que están fuera del viewport inicial
                const rect = element.getBoundingClientRect();
                // Si el elemento no está en la pantalla inicial, lo preparamos para animar
                if (rect.top > window.innerHeight) {
                    element.classList.add('ready');
                }
            });
        }, 100);
    };
    
    // Inicializar elementos con stagger effect
    const setupStaggeredElements = () => {
        const staggerContainers = document.querySelectorAll('.stagger-container');
        
        staggerContainers.forEach(container => {
            const items = container.querySelectorAll('.feature-card, .contact-list li, .footer-column');
            items.forEach((item, index) => {
                item.classList.add('stagger-item');
                item.style.transitionDelay = `${0.1 * (index + 1)}s`;
            });
        });
    };
    
    // Configurar elementos con efectos de scroll reveal
    const setupRevealElements = () => {
        // Agregar clases de reveal a elementos específicos
        document.querySelectorAll('.section-title').forEach(el => {
            el.classList.add('reveal', 'reveal-fade');
        });
        
        document.querySelectorAll('.feature-card').forEach(el => {
            el.classList.add('reveal', 'reveal-bottom');
        });
        
        document.querySelectorAll('.contact-list li').forEach((el, index) => {
            el.classList.add('reveal', 'reveal-left');
            el.style.transitionDelay = `${0.1 * index}s`;
        });
        
        document.querySelectorAll('.footer-column').forEach((el, index) => {
            el.classList.add('reveal', 'reveal-bottom');
            el.style.transitionDelay = `${0.1 * index}s`;
        });
        
        document.querySelectorAll('.courses-table').forEach(el => {
            el.classList.add('reveal', 'reveal-zoom');
        });
        
        document.querySelectorAll('.video-container').forEach(el => {
            el.classList.add('reveal', 'reveal-fade');
        });
        
        document.querySelectorAll('.map-container').forEach(el => {
            el.classList.add('reveal', 'reveal-right');
        });
        
        document.querySelectorAll('.contact-info').forEach(el => {
            el.classList.add('reveal', 'reveal-left');
        });
        
        // Después de configurar todos los elementos, los preparamos para animación
        prepareElements();
    };
    
    // Función para verificar si un elemento es visible en el viewport
    const checkIfInView = () => {
        const triggerBottom = window.innerHeight * 0.85;
        
        revealElements.forEach(element => {
            const elementTop = element.getBoundingClientRect().top;
            
            if (elementTop < triggerBottom) {
                element.classList.add('active');
                // Una vez que se activa, ya no es necesario mantener la clase 'ready'
                element.classList.remove('ready');
            }
        });
    };
    
    // Evento de desplazamiento para activar las animaciones
    window.addEventListener('scroll', checkIfInView);
    
    // Configurar la página
    setupStaggeredElements();
    setupRevealElements();
    
    // Comprobar elementos visibles al cargar (después de un pequeño retardo)
    setTimeout(checkIfInView, 300);
    
    // Suavizar el desplazamiento para enlaces internos
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            
            const targetId = this.getAttribute('href');
            if (targetId === '#') return;
            
            const targetElement = document.querySelector(targetId);
            if (targetElement) {
                targetElement.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
                
                // Actualizar URL sin recargar
                history.pushState(null, null, targetId);
            }
        });
    });
    
    // Marca el enlace activo según la sección visible
    const markActiveNavLink = () => {
        const sections = document.querySelectorAll('section[id], div[id]');
        let currentActive = '';
        
        sections.forEach(section => {
            const sectionTop = section.offsetTop - 100;
            const sectionBottom = sectionTop + section.offsetHeight;
            
            if (window.scrollY >= sectionTop && window.scrollY < sectionBottom) {
                currentActive = section.getAttribute('id');
            }
        });
        
        document.querySelectorAll('nav ul li a').forEach(link => {
            link.classList.remove('active');
            if (link.getAttribute('href') === '#' + currentActive) {
                link.classList.add('active');
            }
        });
    };
    
    window.addEventListener('scroll', markActiveNavLink);
});
