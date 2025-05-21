document.addEventListener("DOMContentLoaded", function () {
  // Referencias a elementos del DOM
  const tableBody = document.getElementById("cursosTableBody");
  const btnNuevoCurso = document.getElementById("btnNuevoCurso");
  const modal = document.getElementById("cursoModal");
  const modalTitle = document.getElementById("modalTitle");
  const cursoForm = document.getElementById("cursoForm");
  const cursoIdInput = document.getElementById("cursoId");
  const closeModalBtn = document.getElementById("closeModal");
  const cancelarBtn = document.getElementById("cancelarBtn");
  const horariosDisponibles = document.getElementById("horariosDisponibles");
  
  // Crear elemento de carga
  const loadingSpinner = document.createElement("div");
  loadingSpinner.className = "loading-spinner";

  // Sistema de notificaciones
  function mostrarNotificacion(mensaje, tipo) {
    // Crear contenedor si no existe
    let notificacionContainer = document.getElementById("notificacion-container");
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
    setTimeout(() => {
      notificacion.classList.add("mostrar");
    }, 10);

    // Eliminar después de 3 segundos
    setTimeout(() => {
      notificacion.classList.remove("mostrar");
      setTimeout(() => {
        notificacion.remove();
      }, 300);
    }, 3000);
  }

  // Función para mostrar animación de carga
  function showLoading() {
    tableBody.innerHTML = "";
    const loadingRow = document.createElement("tr");
    loadingRow.innerHTML = `
      <td colspan="8" class="loading-message">
        <div class="loading-spinner"></div>
        <p>Cargando cursos...</p>
      </td>
    `;
    tableBody.appendChild(loadingRow);
  }

  // Cargar cursos
  function cargarCursos() {
    showLoading();

    fetch("../../backend/api/cursos.php")
      .then(response => {
        if (!response.ok) {
          throw new Error(`Error de red: ${response.status}`);
        }
        return response.json();
      })
      .then(data => {
        tableBody.innerHTML = "";

        if (!Array.isArray(data) || data.length === 0) {
          tableBody.innerHTML = `
            <tr>
              <td colspan="8" class="empty-table">No hay cursos configurados</td>
            </tr>
          `;
          return;
        }

        // Llenar la tabla con los datos
        data.forEach(curso => {
          const row = document.createElement("tr");
          row.dataset.id = curso.id;
          row.className = "fade-in-row";

          // Formatear precio
          const precio = parseFloat(curso.precio).toLocaleString('es-MX', {
            style: 'currency',
            currency: 'MXN'
          });

          // Obtener horarios asociados
          let horariosHTML = "No disponible";
          if (curso.horarios && curso.horarios.length > 0) {
            horariosHTML = curso.horarios.map(h => 
              `<span class="horario-tag">${h.nombre}</span>`
            ).join(' ');
          }

          row.innerHTML = `
            <td>${curso.id}</td>
            <td>${curso.nombre || ""}</td>
            <td>${curso.duracion || ""}</td>
            <td>${curso.clases_practicas || ""}</td>
            <td>${curso.clases_teoricas || ""}</td>
            <td>${precio}</td>
            <td class="horarios-cell">${horariosHTML}</td>
            <td class="actions-cell">
              <button class="btn-icon btn-edit" title="Editar curso" data-id="${curso.id}">
                <i class="fas fa-edit"></i>
              </button>
              <button class="btn-icon btn-delete" title="Eliminar curso" data-id="${curso.id}">
                <i class="fas fa-trash-alt"></i>
              </button>
            </td>
          `;

          tableBody.appendChild(row);
        });

        // Inicializar botones de acción
        initActionButtons();
      })
      .catch(error => {
        console.error("Error al cargar cursos:", error);
        tableBody.innerHTML = `
          <tr>
            <td colspan="8" class="error-message">
              <i class="fas fa-exclamation-circle"></i> 
              Error al cargar cursos: ${error.message}<br>
              <button class="btn-retry" onclick="cargarCursos()">
                <i class="fas fa-sync"></i> Reintentar
              </button>
            </td>
          </tr>
        `;
      });
  }

  // Inicializar botones de acción
  function initActionButtons() {
    document.querySelectorAll(".btn-edit").forEach(btn => {
      btn.addEventListener("click", function() {
        editarCurso(this.dataset.id);
      });
    });

    document.querySelectorAll(".btn-delete").forEach(btn => {
      btn.addEventListener("click", function() {
        confirmarEliminar(this.dataset.id);
      });
    });
  }

  // Función para editar curso
  function editarCurso(id) {
    modalTitle.textContent = "Editar Curso";
    cursoIdInput.value = id;
    
    // Resetear el formulario y cargar los datos del curso
    cursoForm.reset();
    
    fetch(`../../backend/api/cursos.php?id=${id}`)
      .then(response => response.json())
      .then(curso => {
        document.getElementById("nombre").value = curso.nombre || "";
        document.getElementById("duracion").value = curso.duracion || "";
        document.getElementById("clases_practicas").value = curso.clases_practicas || "";
        document.getElementById("clases_teoricas").value = curso.clases_teoricas || "";
        document.getElementById("precio").value = curso.precio || "";
        document.getElementById("descripcion").value = curso.descripcion || "";
        
        // Cargar horarios y marcar los asociados
        cargarHorariosEdicion(curso.horarios || []);
        
        // Mostrar el modal
        modal.classList.add("show");
      })
      .catch(error => {
        console.error("Error al cargar datos del curso:", error);
        mostrarNotificacion("Error al cargar datos del curso", "error");
      });
  }

  // Cargar horarios
  function cargarHorarios() {
    horariosDisponibles.innerHTML = `
      <div class="loading-spinner small"></div>
      <p class="loading-text">Cargando horarios...</p>
    `;

    fetch("../../backend/api/horarios.php")
      .then(response => {
        if (!response.ok) {
          throw new Error(`Error de red: ${response.status}`);
        }
        return response.json();
      })
      .then(data => {
        if (!Array.isArray(data) || data.length === 0) {
          horariosDisponibles.innerHTML = `
            <p class="no-data">No hay horarios disponibles</p>
          `;
          return;
        }

        // Crear checkboxes para cada horario
        horariosDisponibles.innerHTML = '';
        data.forEach(horario => {
          const horaInicio = horario.hora_inicio ? horario.hora_inicio.substring(0, 5) : "00:00";
          const horaFin = horario.hora_fin ? horario.hora_fin.substring(0, 5) : "00:00";
          
          const checkbox = document.createElement("div");
          checkbox.className = "checkbox-wrapper";
          checkbox.innerHTML = `
            <input type="checkbox" id="horario_${horario.id}" name="horarios[]" value="${horario.id}">
            <label for="horario_${horario.id}">${horario.nombre} (${horaInicio}-${horaFin})</label>
          `;
          horariosDisponibles.appendChild(checkbox);
        });
      })
      .catch(error => {
        console.error("Error al cargar horarios:", error);
        horariosDisponibles.innerHTML = `
          <p class="error-text">
            <i class="fas fa-exclamation-circle"></i> 
            Error al cargar horarios: ${error.message}
          </p>
        `;
      });
  }

  // Cargar horarios para edición y marcar los asociados
  function cargarHorariosEdicion(horariosAsociados) {
    fetch("../../backend/api/horarios.php")
      .then(response => response.json())
      .then(horarios => {
        horariosDisponibles.innerHTML = '';
        
        if (!Array.isArray(horarios) || horarios.length === 0) {
          horariosDisponibles.innerHTML = '<p class="no-data">No hay horarios disponibles</p>';
          return;
        }
        
        // Crear checkboxes y marcar los asociados
        horarios.forEach(horario => {
          const horaInicio = horario.hora_inicio ? horario.hora_inicio.substring(0, 5) : "00:00";
          const horaFin = horario.hora_fin ? horario.hora_fin.substring(0, 5) : "00:00";
          
          const checked = horariosAsociados.some(h => h.id == horario.id) ? "checked" : "";
          
          const checkbox = document.createElement("div");
          checkbox.className = "checkbox-wrapper";
          checkbox.innerHTML = `
            <input type="checkbox" id="horario_${horario.id}" name="horarios[]" value="${horario.id}" ${checked}>
            <label for="horario_${horario.id}">${horario.nombre} (${horaInicio}-${horaFin})</label>
          `;
          horariosDisponibles.appendChild(checkbox);
        });
      })
      .catch(error => {
        console.error("Error al cargar horarios:", error);
        horariosDisponibles.innerHTML = `
          <p class="error-text">Error al cargar horarios: ${error.message}</p>
        `;
      });
  }

  // Función para confirmar eliminación
  function confirmarEliminar(id) {
    if (confirm('¿Está seguro de que desea eliminar este curso? Esta acción no se puede deshacer.')) {
      eliminarCurso(id);
    }
  }

  // Función para eliminar curso
  function eliminarCurso(id) {
    const row = document.querySelector(`tr[data-id="${id}"]`);
    row.classList.add("deleting");

    fetch(`../../backend/api/cursos.php?id=${id}`, {
      method: "DELETE",
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        // Animación para eliminar la fila
        row.classList.add("fade-out-row");

        setTimeout(() => {
          row.remove();
          mostrarNotificacion("Curso eliminado correctamente", "success");

          // Verificar si quedaron cursos
          if (tableBody.children.length === 0) {
            tableBody.innerHTML = `
              <tr>
                <td colspan="8" class="empty-table">No hay cursos configurados</td>
              </tr>
            `;
          }
        }, 500);
      } else {
        row.classList.remove("deleting");
        mostrarNotificacion(data.mensaje || "Error al eliminar el curso", "error");
      }
    })
    .catch(error => {
      console.error("Error:", error);
      row.classList.remove("deleting");
      mostrarNotificacion("Error al eliminar el curso", "error");
    });
  }

  // Manejo del modal
  function abrirModalNuevo() {
    modalTitle.textContent = "Nuevo Curso";
    cursoForm.reset();
    cursoIdInput.value = "";
    
    // Cargar horarios disponibles vacíos
    cargarHorarios();
    
    // Mostrar modal
    modal.classList.add("show");
  }

  function cerrarModal() {
    modal.classList.remove("show");
  }

  // Función para guardar curso (crear o actualizar)
  function guardarCurso(event) {
    event.preventDefault();
    
    // Preparar datos del formulario
    const formData = new FormData(cursoForm);
    const cursoId = cursoIdInput.value;
    
    // Convertir FormData a objeto para enviar como JSON
    const cursoData = {};
    formData.forEach((value, key) => {
      if (key === 'horarios[]') {
        if (!cursoData.horarios) cursoData.horarios = [];
        cursoData.horarios.push(value);
      } else {
        cursoData[key] = value;
      }
    });
    
    // Deshabilitar botón durante el envío
    const submitBtn = cursoForm.querySelector('button[type="submit"]');
    const originalBtnText = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Guardando...';

    const method = cursoId ? "PUT" : "POST";
    const url = cursoId ? `../../backend/api/cursos.php?id=${cursoId}` : "../../backend/api/cursos.php";

    fetch(url, {
      method: method,
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify(cursoData)
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        mostrarNotificacion(cursoId ? "Curso actualizado correctamente" : "Curso creado correctamente", "success");
        cerrarModal();
        
        // Recargar la tabla con animación
        cargarCursos();
      } else {
        mostrarNotificacion(data.mensaje || "Error al guardar el curso", "error");
      }
    })
    .catch(error => {
      console.error("Error:", error);
      mostrarNotificacion("Error al guardar el curso", "error");
    })
    .finally(() => {
      // Restaurar estado del botón
      submitBtn.disabled = false;
      submitBtn.innerHTML = originalBtnText;
    });
  }

  // Event Listeners
  btnNuevoCurso.addEventListener("click", abrirModalNuevo);
  closeModalBtn.addEventListener("click", cerrarModal);
  cancelarBtn.addEventListener("click", cerrarModal);
  cursoForm.addEventListener("submit", guardarCurso);

  // Cerrar modal al hacer clic fuera de él
  window.addEventListener("click", function(event) {
    if (event.target === modal) {
      cerrarModal();
    }
  });

  // Primera carga de cursos
  cargarCursos();
});

// Función global para permitir recargar desde cualquier parte
window.cargarCursos = function() {
  document.dispatchEvent(new Event('DOMContentLoaded'));
};
