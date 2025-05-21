document.addEventListener("DOMContentLoaded", function () {
  const form = document.getElementById("passwordForm");
  const resultDiv = document.getElementById("result");

  form.addEventListener("submit", function (e) {
    e.preventDefault();

    // Mostrar indicador de carga con animación
    resultDiv.innerHTML =
      '<div class="loading-dot-container"><div class="loading-dot"></div><div class="loading-dot"></div><div class="loading-dot"></div></div>';
    resultDiv.className = "result processing";
    resultDiv.style.display = "block";

    // Deshabilitar el botón durante el procesamiento
    const submitButton = this.querySelector('button[type="submit"]');
    const originalText = submitButton.innerHTML;
    submitButton.disabled = true;
    submitButton.innerHTML =
      '<i class="fas fa-spinner fa-spin"></i> Procesando...';

    const formData = new FormData(this);

    // Añadir un pequeño delay para mostrar la animación
    setTimeout(() => {
      fetch(window.location.href, {
        method: "POST",
        body: formData,
      })
        .then((response) => {
          if (!response.ok) {
            throw new Error("Error en la respuesta del servidor");
          }
          return response.json();
        })
        .then((data) => {
          // Preparar animación
          resultDiv.style.opacity = "0";

          setTimeout(() => {
            // Actualizar contenido
            resultDiv.innerHTML = `
                        <div class="result-icon ${
                          data.success ? "success-icon" : "error-icon"
                        }">
                            <i class="fas ${
                              data.success
                                ? "fa-check-circle"
                                : "fa-times-circle"
                            }"></i>
                        </div>
                        <div class="result-content">${data.result}</div>
                    `;
            resultDiv.className = data.success
              ? "result success animated"
              : "result error animated";

            // Mostrar con animación
            resultDiv.style.opacity = "1";

            // Animar el scroll hasta el resultado
            resultDiv.scrollIntoView({ behavior: "smooth", block: "center" });
          }, 300);
        })
        .catch((error) => {
          console.error("Error:", error);

          // Preparar animación
          resultDiv.style.opacity = "0";

          setTimeout(() => {
            // Mostrar error
            resultDiv.innerHTML = `
                        <div class="result-icon error-icon">
                            <i class="fas fa-exclamation-circle"></i>
                        </div>
                        <div class="result-content">Error al procesar la solicitud: ${error.message}</div>
                    `;
            resultDiv.className = "result error animated";

            // Mostrar con animación
            resultDiv.style.opacity = "1";
          }, 300);
        })
        .finally(() => {
          // Restaurar botón
          submitButton.disabled = false;
          submitButton.innerHTML = originalText;
        });
    }, 600); // Delay para mostrar la animación
  });

  // Ocultar el campo de usuario cuando se selecciona "generar"
  document.getElementById("action").addEventListener("change", function () {
    const usernameField = document.getElementById("usernameField");
    const usernameInput = document.getElementById("username");

    // Animar el cambio
    if (this.value === "generar") {
      usernameField.style.opacity = "1";

      // Fade out
      let fadeOutInterval = setInterval(() => {
        usernameField.style.opacity -= 0.1;
        if (usernameField.style.opacity <= 0) {
          clearInterval(fadeOutInterval);
          usernameInput.required = false;
          usernameField.style.display = "none";
        }
      }, 30);
    } else {
      if (usernameField.style.display === "none") {
        usernameField.style.opacity = "0";
        usernameField.style.display = "block";
        usernameInput.required = true;

        // Fade in
        let fadeInInterval = setInterval(() => {
          usernameField.style.opacity =
            parseFloat(usernameField.style.opacity) + 0.1;
          if (usernameField.style.opacity >= 1) {
            clearInterval(fadeInInterval);
          }
        }, 30);
      }
    }
  });

  // Efecto hover para botones
  const buttons = document.querySelectorAll("button");
  buttons.forEach((button) => {
    button.addEventListener("mouseenter", function () {
      this.classList.add("btn-hover");
    });

    button.addEventListener("mouseleave", function () {
      this.classList.remove("btn-hover");
    });
  });
});
