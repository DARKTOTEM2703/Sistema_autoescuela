<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solicitud de Inscripción - AutoEscuela Segura</title>
    <link rel="stylesheet" href="../css/inscripcion.css">
    <link rel="stylesheet" href="../css/inscripcion-animations.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body>
    <?php
    // Agregar al principio del archivo para cargar horarios de la base de datos
    require_once '../../backend/autoload.php';
    use Backend\Repositories\EstudianteRepository;

    // Obtener los horarios
    $repository = new EstudianteRepository();
    $horarios = $repository->getAllHorarios();
    ?>

    <header>
        <div class="logo">
            <span class="icon"><i class="fas fa-car"></i></span>
            <h1>AutoEscuela Segura</h1>
        </div>
        <nav>
            <ul>
                <li><a href="../landing/index.php">Inicio</a></li>
                <li><a href="#">Nosotros</a></li>
                <li><a href="#">Cursos</a></li>
                <li><a href="#">Contacto</a></li>
                <li><a href="#" class="login">Iniciar Sesión</a></li>
            </ul>
        </nav>
    </header>

    <main class="inscripcion-main">
        <h2 class="section-title">Solicitud de Inscripción</h2>

        <div class="inscripcion-container">
            <div class="formulario-inscripcion">
                <form id="inscripcionForm" action="../../backend/inscripcion/procesar_inscripcion.php" method="post"
                    enctype="multipart/form-data">
                    <!-- Contenedor para mostrar errores -->
                    <div id="erroresContainer" style="display:none;" class="error-container"></div>

                    <div class="form-group">
                        <label for="nombre">Nombre Completo</label>
                        <input type="text" id="nombre" name="nombre" placeholder="Ingrese su nombre completo" required>
                    </div>

                    <div class="form-group">
                        <label for="direccion">Dirección</label>
                        <textarea id="direccion" name="direccion" placeholder="Ingrese su dirección completa"
                            required></textarea>
                    </div>

                    <div class="form-group">
                        <label for="celular">Número de Celular</label>
                        <input type="tel" id="celular" name="celular" placeholder="(999) 123-4567" required>
                    </div>

                    <div class="form-group">
                        <label for="tipo_auto">Tipo de Auto</label>
                        <div class="select-wrapper">
                            <select id="tipo_auto" name="tipo_auto" required>
                                <option value="estandar">Estándar</option>
                                <option value="automatico">Automático</option>
                                <option value="ambos">Ambos</option>
                            </select>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="horario">Horario Preferido</label>
                        <div class="select-wrapper">
                            <select id="horario" name="horario" required>
                                <option value="" disabled selected>Seleccione un horario</option>
                                <?php foreach($horarios as $horario): ?>
                                    <?php 
                                        // Formatear las horas para mostrar HH:MM
                                        $horaInicio = substr($horario['hora_inicio'], 0, 5);
                                        $horaFin = substr($horario['hora_fin'], 0, 5);
                                    ?>
                                    <option value="<?php echo $horario['nombre']; ?>">
                                        <?php echo ucfirst($horario['nombre']) . " ({$horaInicio} - {$horaFin})"; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="recibo">Recibo de Pago</label>
                        <div class="file-upload">
                            <button type="button" class="file-upload-btn">Seleccionar archivo</button>
                            <div class="file-info">Ningún archivo seleccionado</div>
                            <input type="file" id="recibo" name="recibo" accept=".pdf,.jpg,.jpeg,.png">
                            <i class="fas fa-upload"></i>
                        </div>
                        <div class="file-instructions">Sube una imagen o PDF de tu comprobante de pago</div>
                    </div>

                    <div class="form-submit">
                        <button type="submit" class="btn-inscripcion">Enviar Solicitud</button>
                    </div>
                </form>
            </div>

            <div class="info-contacto">
                <h3>Información de Contacto</h3>
                <ul class="contact-list">
                    <li>
                        <i class="fas fa-map-marker-alt"></i>
                        <span>Calle 60 × 45 y 47, Centro, 97000 Mérida, Yucatán</span>
                    </li>
                    <li>
                        <i class="fas fa-phone"></i>
                        <span>(999) 123-4567</span>
                    </li>
                    <li>
                        <i class="fas fa-envelope"></i>
                        <span>info@autoescuelasegura.com</span>
                    </li>
                    <li>
                        <i class="fas fa-clock"></i>
                        <div class="horarios">
                            <div>Lunes a Viernes: 8:00 AM - 8:00 PM</div>
                            <div>Sábados: 9:00 AM - 2:00 PM</div>
                        </div>
                    </li>
                </ul>

                <div class="map-container">
                    <iframe
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d29798.518637280728!2d-89.65770731450192!3d20.97163499595841!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x8f5673b3246ae82d%3A0x1317b111951f3bc8!2zTcOpcmlkYSwgWXVjLiwgTcOpeGljbw!5e0!3m2!1ses-419!2smx!4v1621461470114!5m2!1ses-419!2smx"
                        style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                </div>
            </div>
        </div>
    </main>

    <footer>
        <div class="footer-container">
            <div class="footer-column">
                <div class="footer-logo">
                    <span class="icon"><i class="fas fa-car"></i></span>
                    <h3>AutoEscuela Segura</h3>
                </div>
                <p>Formando conductores responsables desde 2005. Nuestra misión es enseñar a conducir de manera segura y
                    responsable.</p>
            </div>

            <div class="footer-column">
                <h4>Enlaces</h4>
                <ul>
                    <li><a href="#">Inicio</a></li>
                    <li><a href="#">Nosotros</a></li>
                    <li><a href="#">Cursos</a></li>
                    <li><a href="#">Contacto</a></li>
                </ul>
            </div>

            <div class="footer-column">
                <h4>Servicios</h4>
                <ul>
                    <li><a href="#">Cursos para principiantes</a></li>
                    <li><a href="#">Cursos avanzados</a></li>
                    <li><a href="#">Clases particulares</a></li>
                    <li><a href="#">Evaluaciones</a></li>
                </ul>
            </div>

            <div class="footer-column">
                <h4>Legal</h4>
                <ul>
                    <li><a href="#">Política de privacidad</a></li>
                    <li><a href="#">Términos y condiciones</a></li>
                </ul>
            </div>
        </div>

        <div class="footer-bottom">
            <p>© 2025 AutoEscuela Segura. Todos los derechos reservados.</p>
        </div>
    </footer>

    <script src="js/inscripcion.js"></script>
    <script>
    // Script para mostrar el nombre del archivo cuando se selecciona
    document.getElementById('recibo').addEventListener('change', function(e) {
        const fileName = e.target.files[0] ? e.target.files[0].name : 'Ningún archivo seleccionado';
        document.querySelector('.file-info').textContent = fileName;
    });

    // Script para el botón de seleccionar archivo
    document.querySelector('.file-upload-btn').addEventListener('click', function() {
        document.getElementById('recibo').click();
    });
    </script>
</body>

</html>