<?php
// Proteger la página para que solo sea accesible por usuarios autenticados
session_start();

// Incluir el autoloader personalizado para cargar las clases automáticamente
require_once '../../backend/autoload.php';

use Backend\Services\AuthService;
use Backend\Repositories\EstudianteRepository;

$authService = new AuthService();
$repo = new EstudianteRepository();

// Verificar si el usuario está autenticado
if (!$authService->isAuthenticated()) {
    // Redirigir al login
    header('Location: login/index.php');
    exit;
}

// Obtener los tipos de auto y horarios para el formulario
$tiposAuto = $repo->getAllTiposAuto();
$horarios = $repo->getAllHorarios();
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
                        <a href="cursos.php"><i class="fas fa-graduation-cap"></i> Cursos</a>
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
                        <button id="btnNuevoEstudiante" class="btn btn-primary">Nuevo Estudiante</button>
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

    <!-- Ventana Modal para Nuevo Estudiante -->
    <div id="modalEstudiante" class="modal-estudiante">
        <div class="modal-contenido">
            <div class="modal-header">
                <h3 id="modalTitle">Registrar Nuevo Estudiante</h3>
                <button class="cerrar-modal" id="cerrarModal">&times;</button>
            </div>
            <div class="modal-body">
                <form id="formNuevoEstudiante" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="nombre">Nombre:</label>
                        <input type="text" id="nombre" name="nombre" placeholder="Ingrese el nombre completo" required>
                    </div>

                    <!-- <div class="form-group">
                        <label for="correo">Correo:</label>
                        <input type="email" id="correo" name="correo" placeholder="Ingrese el correo electrónico"
                            required>
                    </div> -->

                    <div class="form-group">
                        <label for="celular">Teléfono:</label>
                        <input type="text" id="celular" name="celular" placeholder="Ingrese el número de teléfono"
                            required>
                    </div>

                    <div class="form-group">
                        <label for="direccion">Dirección:</label>
                        <textarea id="direccion" name="direccion" placeholder="Ingrese la dirección completa"
                            required></textarea>
                    </div>

                    <div class="form-group">
                        <label for="tipo_auto">Tipo de Auto:</label>
                        <select id="tipo_auto" name="tipo_auto" required>
                            <option value="" disabled selected>Seleccione un tipo</option>
                            <?php foreach ($tiposAuto as $tipo): ?>
                                <option value="<?php echo htmlspecialchars($tipo['nombre']); ?>">
                                    <?php echo htmlspecialchars(ucfirst($tipo['nombre'])); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="horario">Horario Preferido:</label>
                        <select id="horario" name="horario" required>
                            <option value="" disabled selected>Seleccione un horario</option>
                            <?php foreach ($horarios as $horario): ?>
                                <?php
                                $horaInicio = substr($horario['hora_inicio'], 0, 5);
                                $horaFin = substr($horario['hora_fin'], 0, 5);
                                ?>
                                <option value="<?php echo htmlspecialchars($horario['nombre']); ?>">
                                    <?php echo htmlspecialchars(ucfirst($horario['nombre'])) . " ({$horaInicio} - {$horaFin})"; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="recibo">Recibo de Pago:</label>
                        <div class="file-upload">
                            <button type="button" class="file-upload-btn">Seleccionar archivo</button>
                            <input type="file" id="recibo" name="recibo" accept="image/*,application/pdf"
                                style="display: none;" required>
                            <div class="file-info">Ningún archivo seleccionado</div>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="button" class="btn-secondary" id="cancelarBtn">Cancelar</button>
                        <button type="submit" class="btn-primary">Registrar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Otros modales... -->

    <script src="js/admin.js"></script>
    <script src="js/estudiantes.js"></script>
    <script src="js/registro_estudiante.js"></script>
</body>

</html>