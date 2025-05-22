// Script para el panel de administración

document.addEventListener("DOMContentLoaded", function () {
  // Referencias a elementos del DOM
  const modal = document.getElementById("estudianteModal");
  const confirmarModal = document.getElementById("confirmarEliminarModal");
  const btnNuevo = document.getElementById("btnNuevoEstudiante");
  const closeModal = document.getElementById("closeModal");
  const closeConfirmModal = document.getElementById("closeConfirmModal");
  const cancelarBtn = document.getElementById("cancelarBtn");
  const cancelarEliminarBtn = document.getElementById("cancelarEliminarBtn");
  const guardarBtn = document.getElementById("guardarBtn");
  const confirmarEliminarBtn = document.getElementById("confirmarEliminarBtn");
  const form = document.getElementById("estudianteForm");
  const searchInput = document.getElementById("searchInput");

  // ID del estudiante a eliminar
  let estudianteIdAEliminar = null;

  // Función para abrir el modal de nuevo estudiante
  btnNuevo &&
    btnNuevo.addEventListener("click", function () {
      document.getElementById("modalTitle").textContent = "Nuevo Estudiante";
      document.getElementById("estudianteId").value = "";
      form.reset();
      modal.classList.add("show");
    });

  // Función para cerrar el modal
  function cerrarModal() {
    modal.classList.remove("show");
  }

  closeModal && closeModal.addEventListener("click", cerrarModal);
  cancelarBtn && cancelarBtn.addEventListener("click", cerrarModal);

  // Función para cerrar el modal de confirmación
  function cerrarConfirmModal() {
    confirmarModal.classList.remove("show");
  }

  closeConfirmModal &&
    closeConfirmModal.addEventListener("click", cerrarConfirmModal);
  cancelarEliminarBtn &&
    cancelarEliminarBtn.addEventListener("click", cerrarConfirmModal);

  // Manejador para el botón de editar
  document.querySelectorAll(".btn-edit").forEach((btn) => {
    btn.addEventListener("click", function () {
      const id = this.getAttribute("data-id");
      editarEstudiante(id);
    });
  });

  // Manejador para el botón de eliminar
  document.querySelectorAll(".btn-delete").forEach((btn) => {
    btn.addEventListener("click", function () {
      const id = this.getAttribute("data-id");
      mostrarModalEliminar(id);
    });
  });

  // Función para editar un estudiante
  function editarEstudiante(id) {
    // Simulamos datos para este ejemplo
    const estudiantes = {
      1: {
        nombre: "Juan Pérez",
        telefono: "(999) 123-4567",
        direccion: "Calle 42 #123",
        tipo_auto: "estandar",
        horario: "manana",
      },
      2: {
        nombre: "María González",
        telefono: "(999) 234-5678",
        direccion: "Avenida 30 #456",
        tipo_auto: "automatico",
        horario: "tarde",
      },
      3: {
        nombre: "Carlos Rodríguez",
        telefono: "(999) 345-6789",
        direccion: "Plaza Principal #789",
        tipo_auto: "estandar",
        horario: "noche",
      },
      4: {
        nombre: "Ana Martínez",
        telefono: "(999) 456-7890",
        direccion: "Calle 15 #101",
        tipo_auto: "automatico",
        horario: "sabado",
      },
      5: {
        nombre: "Roberto Díaz",
        telefono: "(999) 567-8901",
        direccion: "Avenida Central #202",
        tipo_auto: "ambos",
        horario: "manana",
      },
    };

    const estudiante = estudiantes[id];
    if (estudiante && modal) {
      document.getElementById("modalTitle").textContent = "Editar Estudiante";
      document.getElementById("estudianteId").value = id;
      document.getElementById("nombre").value = estudiante.nombre;
      document.getElementById("telefono").value = estudiante.telefono;
      document.getElementById("direccion").value = estudiante.direccion;
      document.getElementById("tipo_auto").value = estudiante.tipo_auto;
      document.getElementById("horario").value = estudiante.horario;
      modal.classList.add("show");
    }
  }

  // Función para mostrar el modal de confirmación de eliminación
  function mostrarModalEliminar(id) {
    estudianteIdAEliminar = id;
    confirmarModal.classList.add("show");
  }

  // Manejador para el botón de confirmar eliminación
  confirmarEliminarBtn &&
    confirmarEliminarBtn.addEventListener("click", function () {
      // En una aplicación real, aquí enviarías una petición AJAX para eliminar al estudiante

      // Simulación de eliminación
      cerrarConfirmModal();

      // Aquí podrías eliminar la fila de la tabla o recargar los datos
      const fila = document.querySelector(
        `tr[data-id="${estudianteIdAEliminar}"]`
      );
      if (fila) {
        fila.remove();
      } else {
        // Alternativa: recargar la página
        // window.location.reload();
      }
    });

  // Funcionalidad de búsqueda
  searchInput &&
    searchInput.addEventListener("input", function () {
      const searchTerm = this.value.toLowerCase();
      const rows = document.querySelectorAll(".data-table tbody tr");

      rows.forEach((row) => {
        const nombre = row
          .querySelector("td:nth-child(2)")
          .textContent.toLowerCase();
        const telefono = row
          .querySelector("td:nth-child(3)")
          .textContent.toLowerCase();

        if (nombre.includes(searchTerm) || telefono.includes(searchTerm)) {
          row.style.display = "";
        } else {
          row.style.display = "none";
        }
      });
    });

  document.getElementById("btnNuevoEstudiante").onclick = function () {
    document.getElementById("modalEstudiante").style.display = "block";
  };
  document.getElementById("cerrarModal").onclick = function () {
    document.getElementById("modalEstudiante").style.display = "none";
  };
  // Cierra la ventana si se hace clic fuera del contenido
  window.onclick = function (event) {
    var modal = document.getElementById("modalEstudiante");
    if (event.target == modal) {
      modal.style.display = "none";
    }
  };
});

document.addEventListener("DOMContentLoaded", () => {
  const modal = document.getElementById("modalEstudiante");
  const abrirModalBtn = document.getElementById("btnNuevoEstudiante");
  const cerrarModalBtn = document.getElementById("cerrarModal");

  // Abrir la modal
  abrirModalBtn.addEventListener("click", () => {
    modal.style.display = "flex";
  });

  // Cerrar la modal
  cerrarModalBtn.addEventListener("click", () => {
    modal.style.display = "none";
  });

  // Cerrar la modal al hacer clic fuera del contenido
  window.addEventListener("click", (e) => {
    if (e.target === modal) {
      modal.style.display = "none";
    }
  });
});

document.addEventListener("DOMContentLoaded", () => {
  const fileInput = document.getElementById("recibo");
  const fileUploadBtn = document.querySelector(".file-upload-btn");
  const fileInfo = document.querySelector(".file-info");

  // Mostrar el nombre del archivo seleccionado
  fileInput.addEventListener("change", (e) => {
    const fileName = e.target.files[0]
      ? e.target.files[0].name
      : "Ningún archivo seleccionado";
    fileInfo.textContent = fileName;
  });

  // Abrir el selector de archivos al hacer clic en el botón
  fileUploadBtn.addEventListener("click", () => {
    fileInput.click();
  });
});
