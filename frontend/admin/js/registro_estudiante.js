document.addEventListener("DOMContentLoaded", function () {
  const form = document.getElementById("formNuevoEstudiante");
  if (!form) return;

  // Mostrar nombre del archivo seleccionado
  const fileInput = document.getElementById("recibo");
  const fileInfo = document.querySelector(".file-info");
  const fileBtn = document.querySelector(".file-upload-btn");
  if (fileBtn && fileInput) {
    fileBtn.addEventListener("click", () => fileInput.click());
    fileInput.addEventListener("change", function () {
      fileInfo.textContent = fileInput.files[0]
        ? fileInput.files[0].name
        : "Ningún archivo seleccionado";
    });
  }

  // Enviar formulario por AJAX
  form.addEventListener("submit", function (e) {
    e.preventDefault();
    const formData = new FormData(form);

    fetch("../../backend/inscripcion/procesar_inscripcion.php", {
      method: "POST",
      body: formData,
    })
      .then((res) => res.json())
      .then((data) => {
        if (data.success) {
          mostrarMensaje("¡Estudiante registrado correctamente!", true);
          form.reset();
          fileInfo.textContent = "Ningún archivo seleccionado";
          document.getElementById("modalEstudiante").style.display = "none";
          // Si tienes función para recargar la tabla, llámala aquí
        } else {
          mostrarMensaje(
            "Error: " + (data.message || "No se pudo registrar"),
            false
          );
        }
      })
      .catch(() => {
        mostrarMensaje("Error al registrar estudiante", false);
      });
  });

  function mostrarMensaje(mensaje, exito) {
    const div = document.createElement("div");
    div.textContent = mensaje;
    div.style.position = "fixed";
    div.style.top = "30px";
    div.style.left = "50%";
    div.style.transform = "translateX(-50%)";
    div.style.background = exito ? "#4caf50" : "#f44336";
    div.style.color = "#fff";
    div.style.padding = "15px 30px";
    div.style.borderRadius = "8px";
    div.style.zIndex = 9999;
    document.body.appendChild(div);
    setTimeout(() => div.remove(), 2500);
  }
});
