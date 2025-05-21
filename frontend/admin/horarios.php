<?php
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
    <title>Gestión de Horarios - AutoEscuela Segura</title>
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
                    <li>
                        <a href="index.php"><i class="fas fa-user-graduate"></i> Estudiantes</a>
                    </li>
                    <li class="active">
                        <a href="horarios.php"><i class="fas fa-clock"></i> Horarios</a>
                    </li>
                    <li>
                        <a href="configuracion.php"><i class="fas fa-cog"></i> Configuración</a>
                    </li>
                    <!-- Sección de herramientas -->
                    <li class="nav-section">
                        <span>Herramientas</span>
                    </li>
                    <li>
                        <a href="tools/password_manager.php"><i class="fas fa-key"></i> Gestión Contraseñas</a>
                    </li>
                    <li>
                        <a href="tools/database_init.php"><i class="fas fa-database"></i> Inicializar BD</a>
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
                    <i class="fas fa-clock"></i>
                    <h2>Gestión de Horarios</h2>
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
                    <h3>Horarios Disponibles</h3>
                    <div class="actions-container">
                        <button class="btn-primary pulse-animation" id="btnNuevoHorario">
                            <i class="fas fa-plus"></i> Nuevo Horario
                        </button>
                    </div>
                </div>

                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Hora Inicio</th>
                                <th>Hora Fin</th>
                                <th>Días</th>
                                <th>Capacidad</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Datos cargados dinámicamente con JS -->
                            <tr>
                                <td colspan="7" class="loading-message">
                                    <div class="loading-spinner"></div>
                                    <p>Cargando horarios...</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <!-- Modal para Crear/Editar Horario -->
    <div class="modal" id="horarioModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="modalTitle">Nuevo Horario</h3>
                <button class="close-btn" id="closeModal"><i class="fas fa-times"></i></button>
            </div>
            <div class="modal-body">
                <form id="horarioForm">
                    <input type="hidden" id="horarioId" name="horarioId" value="">
                    
                    <div class="form-group">
                        <label for="nombre">Nombre del Horario</label>
                        <input type="text" id="nombre" name="nombre" placeholder="Ej: Mañana, Tarde, etc." required>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group form-col">
                            <label for="hora_inicio">Hora de Inicio</label>
                            <input type="time" id="hora_inicio" name="hora_inicio" required>
                        </div>
                        
                        <div class="form-group form-col">
                            <label for="hora_fin">Hora de Fin</label>
                            <input type="time" id="hora_fin" name="hora_fin" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="dias">Días disponibles</label>
                        <input type="text" id="dias" name="dias" placeholder="Ej: Lunes a Viernes" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="capacidad">Capacidad máxima</label>
                        <input type="number" id="capacidad" name="capacidad" min="1" max="50" value="10">
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
    <script src="js/horarios.js"></script>
</body>
</html>