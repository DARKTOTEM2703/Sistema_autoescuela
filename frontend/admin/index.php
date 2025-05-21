<?php
// Proteger la página para que solo sea accesible por usuarios autenticados
session_start();

// Incluir el autoloader personalizado para cargar las clases automáticamente
require_once '../../backend/autoload.php';

use Backend\Services\AuthService;

$authService = new AuthService();

// Verificar si el usuario está autenticado
if (!$authService->isAuthenticated()) {
    // Redirigir al login
    header('Location: login/index.php');
    exit;
}

// Si el usuario está autenticado, se muestra la página normalmente
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración - AutoEscuela Segura</title>
    <link rel="stylesheet" href="css/admin.css">
    <link rel="stylesheet" href="css/animations.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body class="admin-body">
    <div class="admin-container">
        <!-- Barra lateral -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <div class="logo">
                    <span class="icon"><i class="fas fa-car"></i></span>
                    <h1>Panel Admin</h1>
                </div>
            </div>
            <nav class="sidebar-nav">
                <ul>
                    <li class="active">
                        <a href="index.php"><i class="fas fa-user-graduate"></i> Estudiantes</a>
                    </li>
                    <li>
                        <a href="horarios.php"><i class="fas fa-clock"></i> Horarios</a>
                    </li>
                    <li>
                        <a href="configuracion.php"><i class="fas fa-cog"></i> Configuración</a>
                    </li>
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
                    <i class="fas fa-file-alt"></i>
                    <h2>Gestión de Estudiantes</h2>
                </div>
                <div class="user-info">
                    <span>Admin</span>
                    <a href="logout.php" class="logout-link">
                        <i class="fas fa-sign-out-alt"></i> Salir
                    </a>
                </div>
            </header>

            <div class="content-wrapper">
                <div class="content-header-actions">
                    <h3>Lista de Estudiantes</h3>
                    <div class="actions-container">
                        <div class="search-container">
                            <input type="text" placeholder="Buscar por nombre o teléfono" id="searchInput">
                            <i class="fas fa-search"></i>
                        </div>
                        <button class="btn-primary" id="btnNuevoEstudiante">
                            <i class="fas fa-plus"></i> Nuevo Estudiante
                        </button>
                    </div>
                </div>

                <!-- Reemplazar la sección de tabla estática por una tabla dinámica -->
                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Teléfono</th>
                                <th>Tipo Auto</th>
                                <th>Horario</th>
                                <th>Fecha Registro</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Los datos se cargarán dinámicamente con JavaScript -->
                            <tr>
                                <td colspan="7" class="loading-message">
                                    <div class="loading-spinner"></div>
                                    <p>Cargando estudiantes...</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <!-- Modal de Confirmación para Eliminar (estilo actualizado) -->
    <div class="modal" id="confirmarEliminarModal">
        <div class="modal-content modal-sm">
            <div class="modal-header">
                <h3>Confirmar Eliminación</h3>
                <button class="close-btn" id="closeConfirmModal"><i class="fas fa-times"></i></button>
            </div>
            <div class="modal-body">
                <p>¿Está seguro de que desea eliminar a este estudiante? Esta acción no se puede deshacer.</p>
                <div class="form-actions">
                    <button type="button" class="btn-secondary" id="cancelarEliminarBtn">Cancelar</button>
                    <button type="button" class="btn-danger" id="confirmarEliminarBtn">Eliminar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Otros modales... -->

    <script src="js/admin.js"></script>
    <script src="js/estudiantes.js"></script>
</body>

</html>