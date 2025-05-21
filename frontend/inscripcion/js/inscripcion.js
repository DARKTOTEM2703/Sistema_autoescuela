document.addEventListener("DOMContentLoaded", function () {
  // Seleccionar el formulario
  const form = document.getElementById("inscripcionForm");
  const formContainer = document.querySelector(".formulario-inscripcion");

  // Manejar el envío del formulario con AJAX
  form.addEventListener("submit", function (e) {
    e.preventDefault();

    // Limpiar mensajes de error anteriores
    const errorContainer = document.getElementById("erroresContainer");
    errorContainer.innerHTML = "";
    errorContainer.style.display = "none";

    // Mostrar animación de carga
    const loadingOverlay = document.createElement("div");
    loadingOverlay.className = "loading-overlay";
    loadingOverlay.innerHTML =
      '<div class="loading-spinner"></div><p>Enviando solicitud...</p>';
    formContainer.appendChild(loadingOverlay);

    // Recoger los datos del formulario
    const formData = new FormData(this);

    // Enviar los datos al servidor
    fetch("../../backend/inscripcion/procesar_inscripcion.php", {
      method: "POST",
      body: formData,
    })
      .then((response) => {
        console.log("Respuesta recibida:", response);

        if (!response.ok) {
          console.error(
            "Error en respuesta del servidor:",
            response.status,
            response.statusText
          );
          return response.text().then((text) => {
            console.error("Contenido de respuesta:", text);
            throw new Error(
              "Error en la respuesta del servidor: " + response.status
            );
          });
        }

        return response.json().catch((err) => {
          console.error("Error al parsear JSON:", err);
          throw new Error(
            "Error al procesar la respuesta del servidor (JSON inválido)"
          );
        });
      })
      .then((data) => {
        // Eliminar animación de carga
        loadingOverlay.remove();

        console.log("Respuesta recibida:", data); // Para debugging

        if (data.success) {
          // Mostrar animación de éxito
          formContainer.innerHTML = `
                    <div class="success-animation">
                        <div class="checkmark-circle">
                            <div class="checkmark draw"></div>
                        </div>
                        <h3 class="success-title">¡Inscripción Exitosa!</h3>
                        <p class="success-message">${data.mensaje}</p>
                        <div class="buttons-container">
                            <a href="../landing/index.php" class="btn btn-secondary">Volver al inicio</a>
                        </div>
                    </div>
                `;
        } else {
          // Mostrar errores
          errorContainer.innerHTML = "";

          (data.errores || ["Ocurrió un error desconocido"]).forEach(
            (error) => {
              const errorDiv = document.createElement("div");
              errorDiv.className = "error-item";
              errorDiv.textContent = error;
              errorContainer.appendChild(errorDiv);
            }
          );

          // Mostrar el contenedor de errores con animación
          errorContainer.style.display = "block";

          // Scroll suavemente para mostrar errores
          errorContainer.scrollIntoView({
            behavior: "smooth",
            block: "start",
          });
        }
      })
      .catch((error) => {
        // Eliminar animación de carga
        loadingOverlay.remove();

        console.error("Error:", error);

        // Mostrar error genérico
        const errorContainer = document.getElementById("erroresContainer");
        errorContainer.innerHTML = `<div class="error-item">Error al procesar la solicitud: ${error.message}. Por favor, inténtelo de nuevo más tarde.</div>`;
        errorContainer.style.display = "block";
      });
  });

  // Script para mostrar el nombre del archivo cuando se selecciona
  document.getElementById("recibo").addEventListener("change", function (e) {
    const fileName = e.target.files[0]
      ? e.target.files[0].name
      : "Ningún archivo seleccionado";
    document.querySelector(".file-info").textContent = fileName;

    // Añadir clase para animación
    document.querySelector(".file-upload").classList.add("file-selected");
  });

  // Script para el botón de seleccionar archivo
  document
    .querySelector(".file-upload-btn")
    .addEventListener("click", function () {
      document.getElementById("recibo").click();
    });

  // Animaciones para campos de formulario
  const formInputs = document.querySelectorAll("input, textarea, select");
  formInputs.forEach((input) => {
    input.addEventListener("focus", function () {
      this.closest(".form-group").classList.add("input-focus");
    });

    input.addEventListener("blur", function () {
      if (this.value === "") {
        this.closest(".form-group").classList.remove("input-focus");
      }
    });
  });
});
