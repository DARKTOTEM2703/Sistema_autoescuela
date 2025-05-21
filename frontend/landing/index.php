<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AutoEscuela Segura</title>
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body>
    <header>
        <div class="logo">
            <span class="icon"><i class="fas fa-car"></i></span>
            <h1>AutoEscuela Segura</h1>
        </div>
        <nav>
            <ul>
                <li><a href="#">Inicio</a></li>
                <li><a href="#">Nosotros</a></li>
                <li><a href="#">Cursos</a></li>
                <li><a href="#">Contacto</a></li>
                <li><a href="../admin/index.php" class="login">Iniciar Sesión</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <section class="hero">
            <div class="hero-text">
                <h2>Aprende a conducir con seguridad y confianza</h2>
                <p>Instructores certificados, vehículos modernos y horarios flexibles para adaptarse a tu ritmo de vida.
                </p>
                <div class="cta-buttons">
                    <a href="../inscripcion/inscripcion.php" class="btn btn-primary">Inscríbete Ahora</a>
                    <a href="#" class="btn btn-secondary">Ver Cursos</a>
                </div>
            </div>
            <div class="hero-image">
                <!-- Aquí iría la imagen principal -->
                <div class="placeholder-image">
                    <img src="https://www.autoescuelarubio.com/images/gal3.jpg" alt="Autoescuela"
                        style="max-width:100%; height:auto; border-radius:10px;">
                </div>
            </div>
        </section>

        <section class="why-us">
            <h2 class="section-title">¿Por qué elegirnos?</h2>
            <div class="features-container">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-car"></i>
                    </div>
                    <h3>Vehículos Modernos</h3>
                    <p>Aprende en vehículos nuevos equipados con la última tecnología de seguridad.</p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <h3>Horarios Flexibles</h3>
                    <p>Ofrecemos horarios adaptados a tus necesidades, incluyendo fines de semana.</p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-calendar"></i>
                    </div>
                    <h3>Cursos Personalizados</h3>
                    <p>Programas adaptados a tu nivel de experiencia y objetivos específicos.</p>
                </div>
            </div>
        </section>

        <section class="courses">
            <h2 class="section-title">Nuestros Cursos</h2>
            <div class="courses-table-container">
                <table class="courses-table">
                    <thead>
                        <tr>
                            <th>Curso</th>
                            <th>Duración</th>
                            <th>Clases Prácticas</th>
                            <th>Clases Teóricas</th>
                            <th>Precio</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Curso Básico</td>
                            <td>4 semanas</td>
                            <td>10 horas</td>
                            <td>5 horas</td>
                            <td>$2,500</td>
                        </tr>
                        <tr>
                            <td>Curso Intermedio</td>
                            <td>6 semanas</td>
                            <td>15 horas</td>
                            <td>8 horas</td>
                            <td>$3,800</td>
                        </tr>
                        <tr>
                            <td>Curso Avanzado</td>
                            <td>8 semanas</td>
                            <td>20 horas</td>
                            <td>10 horas</td>
                            <td>$4,500</td>
                        </tr>
                        <tr>
                            <td>Curso Intensivo</td>
                            <td>2 semanas</td>
                            <td>12 horas</td>
                            <td>6 horas</td>
                            <td>$3,200</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>

        <!-- Sección Aprende con nosotros -->
        <section class="learn-with-us">
            <h2 class="section-title">Aprende con nosotros</h2>
            <div class="video-container">
                <iframe src="https://www.youtube.com/embed/W-Cxhnb-0Jo?si=UXd2gYBBmG-soP_h" title="YouTube video player"
                    frameborder="0"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                    allowfullscreen></iframe>
            </div>
        </section>

        <!-- Sección Ubicación y Contacto -->
        <div class="location-contact-wrapper">
            <section class="location-section">
                <h2 class="section-title">Nuestra Ubicación</h2>
                <div class="location-contact-container">
                    <div class="map-container">
                        <div class="map-embed">
                            <iframe
                                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d29798.518637280728!2d-89.65770731450192!3d20.97163499595841!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x8f5673b3246ae82d%3A0x1317b111951f3bc8!2zTcOpcmlkYSwgWXVjLiwgTcOpeGljbw!5e0!3m2!1ses-419!2smx!4v1621461470114!5m2!1ses-419!2smx"
                                style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                        </div>
                    </div>
                    <div class="contact-info">
                        <h2 class="contact-title">Contáctanos</h2>
                        <ul class="contact-list">
                            <li>
                                <i class="fas fa-map-marker-alt"></i>
                                <span>Calle 60 x 45 y 47, Centro, 97000 Mérida, Yucatán</span>
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
                    </div>
                </div>
            </section>
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
</body>

</html>