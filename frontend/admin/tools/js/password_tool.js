document.addEventListener("DOMContentLoaded", function () {
  const form = document.getElementById("passwordForm");
  const resultDiv = document.getElementById("result");

  form.addEventListener("submit", function (e) {
    e.preventDefault();

    // Mostrar indicador de carga con animación
    resultDiv.innerHTML =
      '<div class="loading-dot-container"><div class="loading-dot"></div><div class="loading-dot"></div><div class="loading-dot"></div></div>';
    resultDiv.className = "password-result processing";
    resultDiv.style.display = "block";
    resultDiv.style.opacity = "1";

    // Deshabilitar el botón durante el procesamiento
    const submitButton = this.querySelector('button[type="submit"]');
    const originalText = submitButton.innerHTML;
    submitButton.disabled = true;
    submitButton.innerHTML =
      '<i class="fas fa-spinner fa-spin"></i> Procesando...';

    // Preparar los datos del formulario como parámetros URL
    const formData = new URLSearchParams();
    const formElements = this.elements;
    for (let i = 0; i < formElements.length; i++) {
      const element = formElements[i];
      if (element.name) {
        formData.append(element.name, element.value);
      }
    }

    // Añadir pequeño delay para mostrar la animación
    setTimeout(() => {
      // Usar XMLHttpRequest en lugar de fetch para mejor compatibilidad
      const xhr = new XMLHttpRequest();
      xhr.open("POST", window.location.href);
      xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
      xhr.setRequestHeader("X-Requested-With", "XMLHttpRequest");

      xhr.onreadystatechange = function () {
        if (xhr.readyState === 4) {
          if (xhr.status === 200) {
            try {
              const data = JSON.parse(xhr.responseText);
              // Preparar animación
              resultDiv.style.opacity = "0";

              setTimeout(() => {
                // Actualizar contenido
                let resultContent = '';

                if (data.success) {
                  // Si tenemos un hash en la respuesta, lo mostramos claramente
                  if (data.hash) {
                    resultContent = `<strong>Hash generado:</strong><br><code class="hash-result">${data.hash}</code>`;
                  } else if (data.result) {
                    resultContent = data.result;
                  } else {
                    resultContent = "Operación realizada con éxito.";
                  }
                } else {
                  resultContent = data.mensaje || "Error desconocido";
                }

                resultDiv.innerHTML = `
                <div class="result-icon ${data.success ? "success-icon" : "error-icon"}">
                  <i class="fas ${data.success ? "fa-check-circle" : "fa-times-circle"}"></i>
                </div>
                <div class="result-content">${resultContent}</div>
                `;

                resultDiv.className = data.success
                  ? "password-result success"
                  : "password-result error";
                resultDiv.style.opacity = "1";
              }, 300);
            } catch (error) {
              // Error al parsear JSON
              console.error("Error al parsear respuesta:", xhr.responseText);
              resultDiv.style.opacity = "0";

              setTimeout(() => {
                resultDiv.innerHTML = `
                <div class="result-icon error-icon">
                  <i class="fas fa-exclamation-circle"></i>
                </div>
                <div class="result-content">Error al procesar la respuesta: No es un JSON válido</div>
                `;
                resultDiv.className = "password-result error";
                resultDiv.style.opacity = "1";
              }, 300);
            }
          } else {
            // Error de red
            resultDiv.style.opacity = "0";

            setTimeout(() => {
              resultDiv.innerHTML = `
              <div class="result-icon error-icon">
                <i class="fas fa-exclamation-circle"></i>
              </div>
              <div class="result-content">Error del servidor: ${xhr.status}</div>
              `;
              resultDiv.className = "password-result error";
              resultDiv.style.opacity = "1";
            }, 300);
          }

          // Restaurar botón en cualquier caso
          submitButton.disabled = false;
          submitButton.innerHTML = originalText;
        }
      };

      xhr.send(formData.toString());
    }, 600); // Delay para mostrar la animación
  });

  // Ocultar/mostrar el campo de usuario según la acción seleccionada
  document.getElementById("action").addEventListener("change", function () {
    const usernameField = document.getElementById("usernameField");
    const usernameInput = document.getElementById("username");

    if (this.value === "generar") {
      usernameField.style.opacity = "1";

      // Animación fade out
      let opacity = 1;
      const fadeOut = setInterval(() => {
        opacity -= 0.1;
        usernameField.style.opacity = opacity;

        if (opacity <= 0.1) {
          clearInterval(fadeOut);
          usernameField.style.display = "none";
          usernameInput.required = false;
        }
      }, 30);
    } else {
      if (usernameField.style.display === "none") {
        usernameField.style.display = "block";
        usernameInput.required = true;

        // Animación fade in
        let opacity = 0;
        usernameField.style.opacity = opacity;
        const fadeIn = setInterval(() => {
          opacity += 0.1;
          usernameField.style.opacity = opacity;

          if (opacity >= 1) {
            clearInterval(fadeIn);
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
