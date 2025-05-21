document.addEventListener("DOMContentLoaded", function () {
  // Referencias a elementos del DOM
  const tableBody = document.querySelector(".data-table tbody");
  const searchInput = document.getElementById("searchInput");
  const loadingSpinner = document.createElement("div");
  loadingSpinner.className = "loading-spinner";

  // Animación de carga
  function showLoading() {
    tableBody.innerHTML = "";
    tableBody.appendChild(loadingSpinner);
  }

  // Función para cargar estudiantes desde el backend
  function cargarEstudiantes() {
    showLoading();

    fetch("../../backend/api/estudiantes.php")
      .then((response) => {
        if (!response.ok) {
          throw new Error(`Error de red: ${response.status}`);
        }
        return response.json();
      })
      .then((data) => {
        tableBody.innerHTML = "";

        if (!data || data.length === 0) {
          tableBody.innerHTML = `
                        <tr>
                            <td colspan="7" class="empty-table">No hay estudiantes registrados</td>
                        </tr>
                    `;
          return;
        }

        // Llenar la tabla con los datos
        data.forEach((estudiante) => {
          const row = document.createElement("tr");
          row.dataset.id = estudiante.id;
          row.className = "fade-in-row";

          // Formatear fecha
          const fecha = new Date(estudiante.fecha_registro);
          const fechaFormateada = fecha.toLocaleDateString();

          row.innerHTML = `
                        <td>${estudiante.id}</td>
                        <td>${estudiante.nombre}</td>
                        <td>${estudiante.celular}</td>
                        <td>${estudiante.tipo_auto_nombre}</td>
                        <td>${
                          estudiante.horario_nombre
                        } (${estudiante.hora_inicio.substring(
            0,
            5
          )} - ${estudiante.hora_fin.substring(0, 5)})</td>
                        <td>${fechaFormateada}</td>
                        <td class="actions-cell">
                            <button class="btn-icon btn-edit" data-id="${
                              estudiante.id
                            }" title="Editar estudiante">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn-icon btn-delete" data-id="${
                              estudiante.id
                            }" title="Eliminar estudiante">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </td>
                    `;

          tableBody.appendChild(row);
        });

        // Añadir event listeners a los nuevos botones
        document.querySelectorAll(".btn-edit").forEach((btn) => {
          btn.addEventListener("click", function () {
            editarEstudiante(this.dataset.id);
          });
        });

        document.querySelectorAll(".btn-delete").forEach((btn) => {
          btn.addEventListener("click", function () {
            mostrarModalEliminar(this.dataset.id);
          });
        });
      })
      .catch((error) => {
        console.error("Error al cargar estudiantes:", error);
        tableBody.innerHTML = `
                    <tr>
                        <td colspan="7" class="error-message">
                            <i class="fas fa-exclamation-circle"></i>
                            Error al cargar estudiantes: ${error.message}
                            <div style="margin-top: 15px;">
                                <button class="btn-retry" onclick="cargarEstudiantes()">
                                    <i class="fas fa-sync"></i> Reintentar
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
      });
  }

  // Función para eliminar estudiante
  window.eliminarEstudiante = function (id) {
    fetch(`../../backend/api/eliminar_estudiante.php?id=${id}`, {
      method: "DELETE",
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          // Animación para eliminar la fila
          const row = document.querySelector(`tr[data-id="${id}"]`);
          row.classList.add("fade-out-row");

          // Esperar a que termine la animación antes de eliminar
          setTimeout(() => {
            row.remove();
            mostrarNotificacion(
              "Estudiante eliminado correctamente",
              "success"
            );

            // Si no quedan estudiantes, mostrar mensaje
            if (tableBody.children.length === 0) {
              tableBody.innerHTML = `
                            <tr>
                                <td colspan="7" class="empty-table">No hay estudiantes registrados</td>
                            </tr>
                        `;
            }
          }, 300);
        } else {
          mostrarNotificacion(
            data.mensaje || "Error al eliminar el estudiante",
            "error"
          );
        }
      })
      .catch((error) => {
        console.error("Error:", error);
        mostrarNotificacion("Error al eliminar el estudiante", "error");
      });
  };

  // Sistema de notificaciones
  function mostrarNotificacion(mensaje, tipo) {
    const notificacion = document.createElement("div");
    notificacion.className = `notificacion ${tipo}`;
    notificacion.textContent = mensaje;

    document.body.appendChild(notificacion);

    // Mostrar con animación
    setTimeout(() => {
      notificacion.classList.add("mostrar");
    }, 10);

    // Ocultar después de un tiempo
    setTimeout(() => {
      notificacion.classList.remove("mostrar");
      setTimeout(() => {
        notificacion.remove();
      }, 300);
    }, 3000);
  }

  // Buscar estudiantes
  searchInput.addEventListener("input", function () {
    const searchTerm = this.value.toLowerCase();
    const rows = document.querySelectorAll(".data-table tbody tr");

    rows.forEach((row) => {
      if (row.classList.contains("empty-table")) return;

      const nombre = row.children[1].textContent.toLowerCase();
      const telefono = row.children[2].textContent.toLowerCase();

      if (nombre.includes(searchTerm) || telefono.includes(searchTerm)) {
        row.style.display = "";
      } else {
        row.style.display = "none";
      }
    });
  });

  // Inicializar
  cargarEstudiantes();

  // Función para mostrar modal de eliminación
  window.mostrarModalEliminar = function (id) {
    document.getElementById("confirmarEliminarBtn").dataset.id = id;
    document.getElementById("confirmarEliminarModal").classList.add("show");
  };

  // Configurar modal de eliminación
  document
    .getElementById("confirmarEliminarBtn")
    .addEventListener("click", function () {
      const id = this.dataset.id;
      document
        .getElementById("confirmarEliminarModal")
        .classList.remove("show");
      eliminarEstudiante(id);
    });

  document
    .getElementById("cancelarEliminarBtn")
    .addEventListener("click", function () {
      document
        .getElementById("confirmarEliminarModal")
        .classList.remove("show");
    });

  document
    .getElementById("closeConfirmModal")
    .addEventListener("click", function () {
      document
        .getElementById("confirmarEliminarModal")
        .classList.remove("show");
    });
});

// Función cargarEstudiantes disponible globalmente para recargar datos
window.cargarEstudiantes = function () {
  document.dispatchEvent(new Event("DOMContentLoaded"));
};
