document.addEventListener("DOMContentLoaded", function () {
    // Referencias a elementos del DOM
    const cursosTableBody = document.getElementById("cursosTableBody");
    const filtroHorario = document.getElementById("filtroHorario");
    const horariosInfo = document.getElementById("horariosInfo");

    // Variable para almacenar todos los cursos
    let todosLosCursos = [];
    
    // Carga los horarios para el filtro
    function cargarHorarios() {
        fetch("../../backend/api/horarios.php")
            .then(response => response.json())
            .then(horarios => {
                // Actualizar el selector de filtro
                horarios.forEach(horario => {
                    const option = document.createElement("option");
                    option.value = horario.id;
                    option.textContent = `${horario.nombre} (${horario.hora_inicio.substring(0, 5)} - ${horario.hora_fin.substring(0, 5)})`;
                    filtroHorario.appendChild(option);
                });

                // Crear texto para la sección "Horarios flexibles"
                if (horarios.length > 0) {
                    const horariosTexto = horarios
                        .map(h => `${h.nombre}: ${h.hora_inicio.substring(0, 5)} - ${h.hora_fin.substring(0, 5)}`)
                        .join("<br>");
                    horariosInfo.innerHTML = `Ofrecemos diversos horarios para que puedas aprender cuando más te convenga.<br>${horariosTexto}`;
                } else {
                    horariosInfo.textContent = "Consulta nuestros horarios disponibles llamando a nuestro centro.";
                }
            })
            .catch(error => {
                console.error("Error al cargar horarios:", error);
                horariosInfo.textContent = "Consulta nuestros horarios disponibles llamando a nuestro centro.";
            });
    }

    // Carga los cursos desde el backend
    function cargarCursos() {
        cursosTableBody.innerHTML = `
            <tr>
                <td colspan="6" class="loading-message">
                    <div class="spinner"></div>
                    <p>Cargando cursos...</p>
                </td>
            </tr>
        `;

        fetch("../../backend/api/cursos.php")
            .then(response => {
                if (!response.ok) {
                    throw new Error(`Error de red: ${response.status}`);
                }
                return response.json();
            })
            .then(cursos => {
                todosLosCursos = cursos;
                mostrarCursos(cursos);
            })
            .catch(error => {
                console.error("Error al cargar cursos:", error);
                cursosTableBody.innerHTML = `
                    <tr>
                        <td colspan="6" class="error-message">
                            <i class="fas fa-exclamation-circle"></i> 
                            Error al cargar cursos: ${error.message}
                        </td>
                    </tr>
                `;
            });
    }

    // Muestra los cursos en la tabla
    function mostrarCursos(cursos) {
        cursosTableBody.innerHTML = "";

        if (!Array.isArray(cursos) || cursos.length === 0) {
            cursosTableBody.innerHTML = `
                <tr>
                    <td colspan="6" class="empty-table">No hay cursos disponibles</td>
                </tr>
            `;
            return;
        }

        // Ordenar cursos por precio (de menor a mayor)
        cursos.sort((a, b) => parseFloat(a.precio) - parseFloat(b.precio));

        // Mostrar cada curso en la tabla
        cursos.forEach(curso => {
            const row = document.createElement("tr");
            row.className = "fade-in-row";
            
            // Formatear precio
            const precio = parseFloat(curso.precio).toLocaleString('es-MX', {
                style: 'currency',
                currency: 'MXN'
            });

            // Formato de horarios
            let horariosHTML = "No disponible";
            if (curso.horarios && curso.horarios.length > 0) {
                horariosHTML = curso.horarios
                    .map(h => `<span class="horario-badge">${h.nombre} (${h.hora_inicio.substring(0, 5)}-${h.hora_fin.substring(0, 5)})</span>`)
                    .join(' ');
            }

            row.innerHTML = `
                <td><strong>${curso.nombre || ""}</strong></td>
                <td>${curso.duracion || ""}</td>
                <td>${curso.clases_practicas || ""}</td>
                <td>${curso.clases_teoricas || ""}</td>
                <td class="horarios-cell">${horariosHTML}</td>
                <td class="precio-cell"><strong>${precio}</strong></td>
            `;

            cursosTableBody.appendChild(row);
        });
    }

    // Filtrar cursos por horario
    filtroHorario.addEventListener("change", function() {
        const horarioId = this.value;
        
        if (!horarioId) {
            // Si no hay horario seleccionado, mostrar todos los cursos
            mostrarCursos(todosLosCursos);
            return;
        }

        // Filtrar cursos que tienen el horario seleccionado
        const cursosFiltrados = todosLosCursos.filter(curso => {
            if (!curso.horarios || curso.horarios.length === 0) return false;
            return curso.horarios.some(h => h.id == horarioId);
        });

        mostrarCursos(cursosFiltrados);
    });

    // Inicializar la carga de datos
    cargarHorarios();
    cargarCursos();
});