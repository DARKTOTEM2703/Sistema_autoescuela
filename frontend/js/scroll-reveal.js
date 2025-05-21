/**
 * Script mejorado para animaciones de scroll con transiciones más evidentes
 * AutoEscuela Segura - Efectos avanzados de scroll
 */

document.addEventListener("DOMContentLoaded", function () {
  // Inicializar elementos con efecto reveal
  const revealElements = document.querySelectorAll(".reveal");

  // Función para preparar TODOS los elementos para animación
  const prepareElements = () => {
    // Hacemos que TODOS los elementos se preparen para animarse
    revealElements.forEach((element) => {
      // Añadimos la clase ready a todos los elementos con reveal
      element.classList.add("ready");
    });
  };

  // Inicializar elementos con stagger effect
  const setupStaggeredElements = () => {
    const staggerContainers = document.querySelectorAll(".stagger-container");

    staggerContainers.forEach((container) => {
      const items = container.querySelectorAll(
        ".feature-card, .contact-list li, .footer-column"
      );
      items.forEach((item, index) => {
        item.classList.add("stagger-item");
        item.style.transitionDelay = `${0.1 * (index + 1)}s`;
      });
    });
  };

  // Configurar elementos con efectos de scroll reveal
  const setupRevealElements = () => {
    // Asignamos efectos más variados a diferentes elementos
    document.querySelectorAll(".section-title").forEach((el) => {
      el.classList.add("reveal", "reveal-fade-scale");
    });

    document.querySelectorAll(".feature-card").forEach((el, index) => {
      // Alternamos efectos para más variedad
      if (index % 2 === 0) {
        el.classList.add("reveal", "reveal-left");
      } else {
        el.classList.add("reveal", "reveal-right");
      }
    });

    document.querySelectorAll(".contact-list li").forEach((el, index) => {
      el.classList.add("reveal", "reveal-left");
      el.style.transitionDelay = `${0.15 * index}s`; // Incrementado para mayor efecto
    });

    document.querySelectorAll(".footer-column").forEach((el, index) => {
      el.classList.add("reveal", "reveal-bottom");
      el.style.transitionDelay = `${0.15 * index}s`; // Incrementado para mayor efecto
    });

    document.querySelectorAll(".courses-table").forEach((el) => {
      el.classList.add("reveal", "reveal-zoom");
    });

    document.querySelectorAll(".video-container").forEach((el) => {
      el.classList.add("reveal", "reveal-flip"); // Nuevo efecto más dramático
    });

    document.querySelectorAll(".map-container").forEach((el) => {
      el.classList.add("reveal", "reveal-right");
    });

    document.querySelectorAll(".contact-info").forEach((el) => {
      el.classList.add("reveal", "reveal-left");
    });

    // Efectos para secciones completas
    document.querySelectorAll("section").forEach((el) => {
      el.classList.add("section-transition");
    });

    // Después de configurar todos los elementos, los preparamos para animación
    prepareElements();
  };

  // Función para verificar si un elemento es visible en el viewport
  // con mayor sensibilidad para activar antes las animaciones
  const checkIfInView = () => {
    // Reducimos el umbral para que las animaciones se activen antes
    const triggerBottom = window.innerHeight * 0.75; // Más alto en la pantalla

    revealElements.forEach((element) => {
      const elementTop = element.getBoundingClientRect().top;

      if (elementTop < triggerBottom) {
        element.classList.add("active");
        element.classList.remove("ready");
      }
    });
  };

  // Evento de desplazamiento para activar las animaciones
  // Añadimos throttling para mejor rendimiento
  let scrollTimeout;
  window.addEventListener("scroll", function () {
    if (scrollTimeout) {
      window.cancelAnimationFrame(scrollTimeout);
    }

    scrollTimeout = window.requestAnimationFrame(function () {
      checkIfInView();
    });
  });

  // Configurar la página
  setupStaggeredElements();
  setupRevealElements();

  // Comprobar elementos visibles al cargar
  // Retrasamos ligeramente para permitir que la página se renderice primero
  setTimeout(checkIfInView, 400);

  // Suavizar el desplazamiento para enlaces internos
  document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
    anchor.addEventListener("click", function (e) {
      e.preventDefault();

      const targetId = this.getAttribute("href");
      if (targetId === "#") return;

      const targetElement = document.querySelector(targetId);
      if (targetElement) {
        targetElement.scrollIntoView({
          behavior: "smooth",
          block: "start",
        });

        // Actualizar URL sin recargar
        history.pushState(null, null, targetId);
      }
    });
  });

  // Marca el enlace activo según la sección visible
  const markActiveNavLink = () => {
    const sections = document.querySelectorAll("section[id], div[id]");
    let currentActive = "";

    sections.forEach((section) => {
      const sectionTop = section.offsetTop - 100;
      const sectionBottom = sectionTop + section.offsetHeight;

      if (window.scrollY >= sectionTop && window.scrollY < sectionBottom) {
        currentActive = section.getAttribute("id");
      }
    });

    document.querySelectorAll("nav ul li a").forEach((link) => {
      link.classList.remove("active");
      if (link.getAttribute("href") === "#" + currentActive) {
        link.classList.add("active");
      }
    });
  };

  window.addEventListener("scroll", markActiveNavLink);
});
