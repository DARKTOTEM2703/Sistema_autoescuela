<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configuración - AutoEscuela Segura</title>
    <link rel="stylesheet" href="css/admin.css">
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
                    <li>
                        <a href="horarios.php"><i class="fas fa-clock"></i> Horarios</a>
                    </li>
                    <li class="active">
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
                    <i class="fas fa-cog"></i>
                    <h2>Configuración del Sistema</h2>
                </div>
                <div class="user-info">
                    <span>Admin</span>
                    <a href="logout.php" class="logout-link">
                        <i class="fas fa-sign-out-alt"></i> Salir
                    </a>
                </div>
            </header>

            <div class="content-wrapper">
                <div class="configuration-container">
                    <form class="config-form">
                        <div class="config-section">
                            <h3 class="section-title">Información General</h3>

                            <div class="form-group">
                                <label for="nombreEscuela">Nombre de la Autoescuela</label>
                                <input type="text" id="nombreEscuela" name="nombreEscuela" value="AutoEscuela Segura">
                            </div>

                            <div class="form-group">
                                <label for="direccionEscuela">Dirección</label>
                                <input type="text" id="direccionEscuela" name="direccionEscuela"
                                    value="Calle 60 x 45 y 47, Centro, 97000 Mérida, Yucatán">
                            </div>

                            <div class="form-group">
                                <label for="telefonoEscuela">Teléfono</label>
                                <input type="text" id="telefonoEscuela" name="telefonoEscuela" value="(999) 123-4567">
                            </div>

                            <div class="form-group">
                                <label for="emailEscuela">Email</label>
                                <input type="email" id="emailEscuela" name="emailEscuela"
                                    value="info@autoescuelasegura.com">
                            </div>
                        </div>

                        <div class="config-section">
                            <h3 class="section-title">Redes Sociales</h3>

                            <div class="form-group">
                                <label for="facebookUrl">URL de Facebook</label>
                                <input type="text" id="facebookUrl" name="facebookUrl"
                                    value="https://facebook.com/autoescuelasegura">
                            </div>

                            <div class="form-group">
                                <label for="instagramUrl">URL de Instagram</label>
                                <input type="text" id="instagramUrl" name="instagramUrl"
                                    value="https://instagram.com/autoescuelasegura">
                            </div>

                            <div class="form-group">
                                <label for="youtubeUrl">URL de YouTube</label>
                                <input type="text" id="youtubeUrl" name="youtubeUrl"
                                    value="https://youtube.com/autoescuelasegura">
                            </div>
                        </div>

                        <div class="config-section">
                            <h3 class="section-title">Configuración de Cursos</h3>

                            <div class="form-group">
                                <label for="maxEstudiantesPorInstructor">Máximo de estudiantes por instructor</label>
                                <input type="number" id="maxEstudiantesPorInstructor" name="maxEstudiantesPorInstructor"
                                    value="10">
                            </div>

                            <div class="form-group">
                                <label for="duracionClaseEstandar">Duración de clase estándar (minutos)</label>
                                <input type="number" id="duracionClaseEstandar" name="duracionClaseEstandar" value="60">
                            </div>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn-primary">Guardar Configuración</button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>

    <script>
    document.querySelector('.config-form').addEventListener('submit', function(e) {
        e.preventDefault();
        // Aquí iría el código para guardar la configuración
        alert('Configuración guardada correctamente');
    });
    </script>
</body>

</html>