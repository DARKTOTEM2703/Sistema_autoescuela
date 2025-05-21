document.addEventListener("DOMContentLoaded", function () {
  // Referencias DOM
  const tableBody = document.querySelector(".data-table tbody");
  const btnNuevoHorario = document.getElementById("btnNuevoHorario");
  const modal = document.getElementById("horarioModal");
  const modalTitle = document.getElementById("modalTitle");
  const horarioForm = document.getElementById("horarioForm");
  const horarioIdInput = document.getElementById("horarioId");
  const loadingSpinner = document.createElement("div");
  loadingSpinner.className = "loading-spinner";

  // Animación de carga
  function showLoading() {
    tableBody.innerHTML = "";
    tableBody.appendChild(loadingSpinner);
  }

  // Sistema de notificaciones
  function mostrarNotificacion(mensaje, tipo) {
    // Crear contenedor si no existe
    let notificacionContainer = document.getElementById(
      "notificacion-container"
    );
    if (!notificacionContainer) {
      notificacionContainer = document.createElement("div");
      notificacionContainer.id = "notificacion-container";
      document.body.appendChild(notificacionContainer);
    }

    const notificacion = document.createElement("div");
    notificacion.className = `notificacion ${tipo}`;

    const contenido = document.createElement("div");
    contenido.className = "notificacion-content";
    contenido.textContent = mensaje;

    const closeBtn = document.createElement("button");
    closeBtn.innerHTML = "&times;";
    closeBtn.className = "close-notif";
    closeBtn.onclick = function () {
      notificacion.classList.remove("mostrar");
      setTimeout(() => notificacion.remove(), 300);
    };

    notificacion.appendChild(contenido);
    notificacion.appendChild(closeBtn);
    notificacionContainer.appendChild(notificacion);

    // Animación de entrada
    setTimeout(() => notificacion.classList.add("mostrar"), 10);

    // Autocierre después de 5 segundos
    setTimeout(() => {
      if (notificacion.parentNode) {
        notificacion.classList.remove("mostrar");
        setTimeout(() => {
          if (notificacion.parentNode) notificacion.remove();
        }, 300);
      }
    }, 5000);
  }

  // Mejora para cargar horarios
  function cargarHorarios() {
    showLoading();

    fetch("../../backend/api/horarios.php")
      .then((response) => {
        if (!response.ok) {
          throw new Error(`Error de red: ${response.status}`);
        }
        
        // Comprueba el tipo de contenido antes de parsear
        const contentType = response.headers.get("content-type");
        if (!contentType || !contentType.includes("application/json")) {
          return response.text().then(text => {
            console.error("Respuesta no JSON:", text);
            throw new Error("La respuesta no es un JSON válido");
          });
        }
        
        return response.json();
      })
      .then((data) => {
        console.log("Datos de horarios recibidos:", data);
        tableBody.innerHTML = "";

        if (!Array.isArray(data) || data.length === 0) {
          tableBody.innerHTML = `
                <tr>
                    <td colspan="7" class="empty-table">No hay horarios configurados</td>
                </tr>
            `;
          return;
        }

        // Llenar la tabla con los datos
        data.forEach((horario) => {
          const row = document.createElement("tr");
          row.dataset.id = horario.id;
          row.className = "fade-in-row";

          // Formatear horas para mostrar solo HH:MM
          const horaInicio = horario.hora_inicio
            ? horario.hora_inicio.substring(0, 5)
            : "00:00";
          const horaFin = horario.hora_fin
            ? horario.hora_fin.substring(0, 5)
            : "00:00";

          row.innerHTML = `
            <td>${horario.id}</td>
            <td>${ucFirst(horario.nombre || "")}</td>
            <td>${horaInicio}</td>
            <td>${horaFin}</td>
            <td>${horario.dias || ""}</td>
            <td>${horario.capacidad || 0}</td>
            <td class="actions-cell">
                <button class="btn-icon btn-edit" title="Editar horario" data-id="${horario.id}">
                    <i class="fas fa-edit"></i>
                </button>
                <button class="btn-icon btn-delete" title="Eliminar horario" data-id="${horario.id}">
                    <i class="fas fa-trash-alt"></i>
                </button>
            </td>
          `;

          tableBody.appendChild(row);
        });

        // Añadir listeners a los botones de acciones
        initActionButtons();
      })
      .catch((error) => {
        console.error("Error al cargar horarios:", error);
        tableBody.innerHTML = `
            <tr>
                <td colspan="7" class="error-message">
                    <i class="fas fa-exclamation-circle"></i> 
                    Error al cargar horarios: ${error.message}<br>
                    <button class="btn-retry" onclick="cargarHorarios()">
                        <i class="fas fa-sync"></i> Reintentar
                    </button>
                </td>
            </tr>
        `;
      });
  }

  // Inicializar botones de acción (editar/eliminar)
  function initActionButtons() {
    // Botones de editar
    document.querySelectorAll(".btn-edit").forEach((btn) => {
      btn.addEventListener("click", function () {
        editarHorario(this.dataset.id);
      });
    });

    // Botones de eliminar
    document.querySelectorAll(".btn-delete").forEach((btn) => {
      btn.addEventListener("click", function () {
        confirmarEliminarHorario(this.dataset.id);
      });
    });
  }

  // Función para editar horario
  function editarHorario(id) {
    // Mostrar spinner en el botón de editar
    const btnEdit = document.querySelector(`.btn-edit[data-id="${id}"]`);
    const originalBtnContent = btnEdit.innerHTML;
    btnEdit.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    btnEdit.disabled = true;

    fetch(`../../backend/api/horarios.php?id=${id}`)
      .then((response) => {
        if (!response.ok) throw new Error("Error al obtener datos del horario");
        return response.json();
      })
      .then((horario) => {
        // Restaurar botón
        btnEdit.innerHTML = originalBtnContent;
        btnEdit.disabled = false;

        // Llenar el formulario con los datos
        modalTitle.textContent = "Editar Horario";
        horarioIdInput.value = horario.id;
        document.getElementById("nombre").value = horario.nombre;
        document.getElementById("hora_inicio").value =
          horario.hora_inicio.substring(0, 5);
        document.getElementById("hora_fin").value = horario.hora_fin.substring(
          0,
          5
        );
        document.getElementById("dias").value = horario.dias;
        document.getElementById("capacidad").value = horario.capacidad;

        // Mostrar modal con animación
        modal.classList.add("show");
      })
      .catch((error) => {
        // Restaurar botón
        btnEdit.innerHTML = originalBtnContent;
        btnEdit.disabled = false;

        console.error("Error:", error);
        mostrarNotificacion("Error al cargar datos del horario", "error");
      });
  }

  // Función para confirmar eliminación de horario
  function confirmarEliminarHorario(id) {
    // Obtener datos del horario para mostrar información en el modal
    const row = document.querySelector(`tr[data-id="${id}"]`);
    const nombreHorario = row.querySelectorAll("td")[1].textContent;

    // Mostrar modal de confirmación personalizado
    const confirmarModalHTML = `
            <div class="modal" id="confirmarEliminarModal">
                <div class="modal-content modal-sm">
                    <div class="modal-header">
                        <h3>Confirmar Eliminación</h3>
                        <button class="close-btn" id="closeConfirmModal"><i class="fas fa-times"></i></button>
                    </div>
                    <div class="modal-body">
                        <p>¿Está seguro de que desea eliminar el horario <strong>${nombreHorario}</strong>?</p>
                        <p class="warning-text">Esta acción no se puede deshacer y podría afectar a los estudiantes asignados.</p>
                        <div class="form-actions">
                            <button type="button" class="btn-secondary" id="cancelarEliminarBtn">Cancelar</button>
                            <button type="button" class="btn-danger" id="confirmarEliminarBtn" data-id="${id}">
                                <i class="fas fa-trash-alt"></i> Eliminar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;

    // Insertar el modal en el DOM
    if (!document.getElementById("confirmarEliminarModal")) {
      document.body.insertAdjacentHTML("beforeend", confirmarModalHTML);
    } else {
      document.getElementById("confirmarEliminarModal").outerHTML =
        confirmarModalHTML;
    }

    // Mostrar con animación
    const confirmarModal = document.getElementById("confirmarEliminarModal");
    confirmarModal.classList.add("show");

    // Configurar eventos para el modal
    document
      .getElementById("closeConfirmModal")
      .addEventListener("click", cerrarModalConfirmacion);
    document
      .getElementById("cancelarEliminarBtn")
      .addEventListener("click", cerrarModalConfirmacion);

    document
      .getElementById("confirmarEliminarBtn")
      .addEventListener("click", function () {
        eliminarHorario(this.dataset.id);
        cerrarModalConfirmacion();
      });

    function cerrarModalConfirmacion() {
      confirmarModal.classList.remove("show");
      setTimeout(() => {
        confirmarModal.remove();
      }, 300);
    }
  }

  // Función para eliminar horario
  function eliminarHorario(id) {
    const row = document.querySelector(`tr[data-id="${id}"]`);
    row.classList.add("deleting");

    fetch(`../../backend/api/horarios.php?id=${id}`, {
      method: "DELETE",
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          // Animación para eliminar la fila
          row.classList.add("fade-out-row");

          setTimeout(() => {
            row.remove();
            mostrarNotificacion("Horario eliminado correctamente", "success");

            // Verificar si quedaron horarios
            if (tableBody.children.length === 0) {
              tableBody.innerHTML = `
                            <tr>
                                <td colspan="7" class="empty-table">No hay horarios configurados</td>
                            </tr>
                        `;
            }
          }, 500);
        } else {
          row.classList.remove("deleting");
          mostrarNotificacion(
            data.mensaje || "Error al eliminar el horario",
            "error"
          );
        }
      })
      .catch((error) => {
        row.classList.remove("deleting");
        console.error("Error:", error);
        mostrarNotificacion("Error al eliminar el horario", "error");
      });
  }

  // Función para crear o actualizar horario
  function guardarHorario(event) {
    event.preventDefault();

    // Obtener datos del formulario
    const id = horarioIdInput.value;
    const isEditing = id !== "";

    const horarioData = {
      nombre: document.getElementById("nombre").value,
      hora_inicio: document.getElementById("hora_inicio").value,
      hora_fin: document.getElementById("hora_fin").value,
      dias: document.getElementById("dias").value,
      capacidad: document.getElementById("capacidad").value,
    };

    // Validación básica
    if (
      !horarioData.nombre ||
      !horarioData.hora_inicio ||
      !horarioData.hora_fin ||
      !horarioData.dias
    ) {
      mostrarNotificacion(
        "Por favor complete todos los campos obligatorios",
        "error"
      );
      return;
    }

    // Deshabilitar botón y mostrar indicador
    const submitBtn = horarioForm.querySelector('button[type="submit"]');
    const originalBtnText = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Guardando...';

    // Configurar la petición según sea creación o actualización
    const url = isEditing
      ? `../../backend/api/horarios.php?id=${id}`
      : "../../backend/api/horarios.php";

    const method = isEditing ? "PUT" : "POST";

    fetch(url, {
      method: method,
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify(horarioData),
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          cerrarModal();
          mostrarNotificacion(
            isEditing
              ? "Horario actualizado correctamente"
              : "Horario creado correctamente",
            "success"
          );

          // Recargar la tabla con animación
          cargarHorarios();
        } else {
          mostrarNotificacion(
            data.mensaje || "Error al guardar el horario",
            "error"
          );
        }
      })
      .catch((error) => {
        console.error("Error:", error);
        mostrarNotificacion("Error al guardar el horario", "error");
      })
      .finally(() => {
        // Restaurar estado del botón
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalBtnText;
      });
  }

  // Funciones para manejar el modal
  function abrirModalNuevoHorario() {
    modalTitle.textContent = "Nuevo Horario";
    horarioForm.reset();
    horarioIdInput.value = "";
    modal.classList.add("show");

    // Enfocar el primer campo
    setTimeout(() => document.getElementById("nombre").focus(), 300);
  }

  function cerrarModal() {
    modal.classList.remove("show");
  }

  // Utility function para capitalizar primera letra
  function ucFirst(string) {
    return string.charAt(0).toUpperCase() + string.slice(1);
  }

  // Event Listeners
  if (btnNuevoHorario) {
    btnNuevoHorario.addEventListener("click", abrirModalNuevoHorario);
  }

  document.querySelectorAll("#closeModal, #cancelarBtn").forEach((el) => {
    if (el) el.addEventListener("click", cerrarModal);
  });

  if (horarioForm) {
    horarioForm.addEventListener("submit", guardarHorario);
  }

  // Cargar horarios al iniciar
  cargarHorarios();
});
