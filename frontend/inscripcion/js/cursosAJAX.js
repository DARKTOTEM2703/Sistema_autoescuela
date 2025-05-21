document.addEventListener('DOMContentLoaded', function() {
    // Referencias a elementos del DOM
    const cursosTableBody = document.getElementById('cursosTableBody');
    const filtroHorario = document.getElementById('filtroHorario');
    const horariosInfo = document.getElementById('horariosInfo');
    
    // Cache para almacenar datos cargados
    let cursosData = [];
    let horariosData = [];
    
    // Mostrar animación de carga
    function showLoading(element) {
        element.innerHTML = `
            <tr>
                <td colspan="6" class="loading-message">
                    <div class="spinner"></div>
                    <p>Cargando datos...</p>
                </td>
            </tr>
        `;
    }
    
    // Mostrar mensaje de error
    function showError(element, message) {
        element.innerHTML = `
            <tr>
                <td colspan="6" class="error-message">
                    <i class="fas fa-exclamation-circle"></i>
                    ${message}
                    <button class="btn-retry" onclick="location.reload()">Reintentar</button>
                </td>
            </tr>
        `;
    }
    
    // Formatear hora (HH:MM:SS -> HH:MM)
    function formatHora(hora) {
        return hora ? hora.substring(0, 5) : '';
    }
    
    // Cargar todos los datos de la página
    function cargarDatos() {
        showLoading(cursosTableBody);
        
        fetch('../../backend/api/landing_data.php')
            .then(response => {
                if (!response.ok) {
                    throw new Error('Error al cargar los datos');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Guardar datos en cache
                    cursosData = data.data.cursos;
                    horariosData = data.data.horarios;
                    
                    // Actualizar interfaz
                    actualizarTabla(cursosData);
                    actualizarFiltrosHorarios(horariosData);
                    actualizarInfoHorarios(horariosData);
                } else {
                    showError(cursosTableBody, data.mensaje || 'Error al cargar datos');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showError(cursosTableBody, error.message);
            });
    }
    
    // Actualizar tabla de cursos
    function actualizarTabla(cursos, filtroHorarioId = null) {
        if (!cursos || cursos.length === 0) {
            cursosTableBody.innerHTML = `
                <tr>
                    <td colspan="6" class="empty-table">No hay cursos disponibles</td>
                </tr>
            `;
            return;
        }
        
        cursosTableBody.innerHTML = '';
        
        // Aplicar filtro por horario si está definido
        const cursosFiltrados = filtroHorarioId 
            ? cursos.filter(c => c.horarios && c.horarios.some(h => h.id == filtroHorarioId))
            : cursos;
        
        if (cursosFiltrados.length === 0) {
            cursosTableBody.innerHTML = `
                <tr>
                    <td colspan="6" class="empty-table">No hay cursos disponibles para este horario</td>
                </tr>
            `;
            return;
        }
        
        cursosFiltrados.forEach(curso => {
            const row = document.createElement('tr');
            
            // Generar HTML para horarios
            let horariosHtml = '';
            if (curso.horarios && curso.horarios.length > 0) {
                horariosHtml = curso.horarios
                    .map(h => {
                        // Si hay un filtro de horario, resaltar el horario filtrado
                        const destacado = filtroHorarioId && h.id == filtroHorarioId ? 'class="horario-destacado"' : '';
                        return `<span ${destacado}>${h.nombre} (${formatHora(h.hora_inicio)}-${formatHora(h.hora_fin)})</span>`;
                    })
                    .join(', ');
            } else {
                horariosHtml = 'No disponible';
            }
            
            // Crear la fila con los datos
            row.innerHTML = `
                <td>${curso.nombre}</td>
                <td>${curso.duracion}</td>
                <td>${curso.clases_practicas}</td>
                <td>${curso.clases_teoricas}</td>
                <td class="horarios-cell">${horariosHtml}</td>
                <td>$${parseFloat(curso.precio).toLocaleString('es-MX')}</td>
            `;
            
            cursosTableBody.appendChild(row);
        });
    }
    
    // Actualizar el dropdown de filtro de horarios
    function actualizarFiltrosHorarios(horarios) {
        if (!horarios || horarios.length === 0) return;
        
        filtroHorario.innerHTML = '<option value="">Todos los horarios</option>';
        
        horarios.forEach(h => {
            const option = document.createElement('option');
            option.value = h.id;
            option.textContent = `${h.nombre} (${formatHora(h.hora_inicio)}-${formatHora(h.hora_fin)})`;
            filtroHorario.appendChild(option);
        });
    }
    
    // Actualizar información de horarios en la sección "Horarios Flexibles"
    function actualizarInfoHorarios(horarios) {
        if (!horarios || horarios.length === 0) {
            horariosInfo.textContent = 'Ofrecemos horarios adaptados a tus necesidades, incluyendo fines de semana.';
            return;
        }
        
        // Crear texto con todos los horarios disponibles
        let texto = 'Ofrecemos horarios flexibles: ';
        horarios.forEach((h, index) => {
            if (index > 0) texto += ', ';
            texto += `${h.nombre} (${formatHora(h.hora_inicio)} a ${formatHora(h.hora_fin)}, ${h.dias})`;
        });
        
        horariosInfo.textContent = texto;
    }
    
    // Evento para filtrar cursos por horario
    filtroHorario.addEventListener('change', function() {
        actualizarTabla(cursosData, this.value);
    });
    
    // Iniciar carga de datos
    cargarDatos();
});