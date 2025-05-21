<?php
// frontend/admin/cursos.php
session_start();

// Verificar autenticación
require_once '../../backend/autoload.php';

use Backend\Services\AuthService;

$authService = new AuthService();
if (!$authService->isAuthenticated()) {
    header('Location: login/index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Cursos - AutoEscuela Segura</title>
    <link rel="stylesheet" href="css/admin.css">
    <link rel="stylesheet" href="css/animations.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body class="admin-body">
    <div class="admin-container">
        <!-- Barra lateral actualizada -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <div class="logo">
                    <span class="icon"><i class="fas fa-car"></i></span>
                    <h1>Panel Admin</h1>
                </div>
            </div>
            <nav class="sidebar-nav">
                <ul>
                    <li><a href="index.php"><i class="fas fa-user-graduate"></i> Estudiantes</a></li>
                    <li class="active"><a href="cursos.php"><i class="fas fa-graduation-cap"></i> Cursos</a></li>
                    <li><a href="horarios.php"><i class="fas fa-clock"></i> Horarios</a></li>
                    <li><a href="configuracion.php"><i class="fas fa-cog"></i> Configuración</a></li>
                </ul>
            </nav>
            <div class="sidebar-footer">
                <a href="logout.php" class="logout-btn">
                    <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
                </a>
            </div>
        </aside>

        <!-- Contenido principal -->
        <main class="main-content">
            <header class="content-header">
                <div class="page-title">
                    <i class="fas fa-graduation-cap"></i>
                    <h2>Gestión de Cursos</h2>
                </div>
                <div class="user-info">
                    <span><?php echo $_SESSION['usuario_nombre'] ?? 'Admin'; ?></span>
                    <a href="logout.php" class="logout-link">
                        <i class="fas fa-sign-out-alt"></i> Salir
                    </a>
                </div>
            </header>

            <div class="content-wrapper">
                <div class="content-header-actions">
                    <h3>Cursos Disponibles</h3>
                    <div class="actions-container">
                        <button class="btn-primary" id="btnNuevoCurso">
                            <i class="fas fa-plus"></i> Nuevo Curso
                        </button>
                    </div>
                </div>

                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Duración</th>
                                <th>Clases P.</th>
                                <th>Clases T.</th>
                                <th>Precio</th>
                                <th>Horarios</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="cursosTableBody">
                            <!-- Datos cargados dinámicamente con JS -->
                            <tr>
                                <td colspan="8" class="loading-message">
                                    <div class="loading-spinner"></div>
                                    <p>Cargando cursos...</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <!-- Modal para Crear/Editar Curso -->
    <div class="modal" id="cursoModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="modalTitle">Nuevo Curso</h3>
                <button class="close-btn" id="closeModal"><i class="fas fa-times"></i></button>
            </div>
            <div class="modal-body">
                <form id="cursoForm">
                    <input type="hidden" id="cursoId" name="cursoId" value="">

                    <div class="form-group">
                        <label for="nombre">Nombre del Curso</label>
                        <input type="text" id="nombre" name="nombre" placeholder="Ej: Curso Básico" required>
                    </div>

                    <div class="form-group">
                        <label for="duracion">Duración</label>
                        <input type="text" id="duracion" name="duracion" placeholder="Ej: 4 semanas" required>
                    </div>

                    <div class="form-row">
                        <div class="form-group form-col">
                            <label for="clases_practicas">Clases Prácticas</label>
                            <input type="text" id="clases_practicas" name="clases_practicas" placeholder="Ej: 10 horas"
                                required>
                        </div>

                        <div class="form-group form-col">
                            <label for="clases_teoricas">Clases Teóricas</label>
                            <input type="text" id="clases_teoricas" name="clases_teoricas" placeholder="Ej: 5 horas"
                                required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="precio">Precio ($)</label>
                        <input type="number" id="precio" name="precio" min="1" step="0.01" placeholder="Ej: 2500.00"
                            required>
                    </div>

                    <div class="form-group">
                        <label for="descripcion">Descripción</label>
                        <textarea id="descripcion" name="descripcion" rows="3"
                            placeholder="Describe el curso brevemente"></textarea>
                    </div>

                    <!-- Horarios disponibles para este curso -->
                    <div class="form-group horarios-disponibles">
                        <label>Horarios disponibles</label>
                        <div class="horarios-list" id="horariosDisponibles">
                            <!-- Cargados dinámicamente -->
                            <div class="loading-spinner small"></div>
                            <p class="loading-text">Cargando horarios...</p>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="button" class="btn-secondary" id="cancelarBtn">Cancelar</button>
                        <button type="submit" class="btn-primary">
                            <i class="fas fa-save"></i> Guardar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="js/admin.js"></script>
    <script src="js/cursosAJAX.js"></script>
</body>

</html>