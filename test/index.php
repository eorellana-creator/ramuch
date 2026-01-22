<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MontainHandBook - Plataforma colaborativa de rutas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .hero-section {
            background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?ixlib=rb-4.0.3');
            background-size: cover;
            background-position: center;
            color: white;
            padding: 100px 0;
            margin-bottom: 30px;
        }
        .card {
            transition: transform 0.3s;
            margin-bottom: 20px;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        .navbar {
            background-color: #2c3e50 !important;
        }
        .btn-primary {
            background-color: #3498db;
            border-color: #3498db;
        }
        .btn-primary:hover {
            background-color: #2980b9;
            border-color: #2980b9;
        }
        .feature-icon {
            font-size: 2.5rem;
            color: #3498db;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="#">
                <img src="assets/img/logo.png" alt="Logo" height="40" class="d-inline-block align-text-top me-2">
                MontainHandBook
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item"><a class="nav-link active" href="#">Inicio</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Explorar</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Contribuir</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Comunidad</a></li>
                </ul>
                <div class="d-flex">
                    <a href="pages/login.php" class="btn btn-outline-light me-2">Iniciar sesión</a>
                    <a href="pages/register.php" class="btn btn-primary">Registrarse</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section text-center">
        <div class="container">
            <h1 class="display-4 fw-bold">Descubre y comparte rutas de montaña</h1>
            <p class="lead">Una plataforma colaborativa para amantes del senderismo y el montañismo</p>
            <a href="#browse" class="btn btn-primary btn-lg mt-3">Explorar Rutas</a>
        </div>
    </section>

    <!-- Continent/Country/Route Selector -->
    <section class="container mb-5" id="browse">
        <div class="card shadow">
            <div class="card-body p-4">
                <h3 class="card-title mb-4">Encuentra tu próxima aventura</h3>
                <form id="routeSelectorForm">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="continent" class="form-label">Continente</label>
                            <select class="form-select" id="continent" required>
                                <option value="" selected disabled>Selecciona un continente</option>
                                <option value="southamerica">América del Sur</option>
                                <option value="northamerica">América del Norte</option>
                                <option value="europe">Europa</option>
                                <option value="asia">Asia</option>
                                <option value="africa">África</option>
                                <option value="oceania">Oceanía</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="country" class="form-label">País</label>
                            <select class="form-select" id="country" disabled required>
                                <option value="" selected disabled>Primero selecciona un continente</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="routeType" class="form-label">Tipo de ruta</label>
                            <select class="form-select" id="routeType">
                                <option value="all" selected>Todas las rutas</option>
                                <option value="mountain">Cerros/Montañas</option>
                                <option value="valley">Valles</option>
                                <option value="trekking">Trekking</option>
                                <option value="climbing">Escalada</option>
                            </select>
                        </div>
                    </div>
                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary">Buscar Rutas</button>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="container mb-5">
        <h2 class="text-center mb-5">¿Por qué unirte a MontainHandBook?</h2>
        <div class="row">
            <div class="col-md-4 text-center">
                <div class="feature-icon">🗺️</div>
                <h4>Rutas Detalladas</h4>
                <p>Accede a información detallada de rutas, con mapas, elevación y datos técnicos.</p>
            </div>
            <div class="col-md-4 text-center">
                <div class="feature-icon">👥</div>
                <h4>Comunidad Activa</h4>
                <p>Conecta con otros entusiastas de la montaña y comparte tus experiencias.</p>
            </div>
            <div class="col-md-4 text-center">
                <div class="feature-icon">💾</div>
                <h4>Archivos GPS</h4>
                <p>Sube y descarga tracks GPS para usar en tus dispositivos y aplicaciones.</p>
            </div>
        </div>
    </section>

    <!-- Recent Routes -->
    <section class="container mb-5">
        <h2 class="mb-4">Rutas Recientes</h2>
        <div class="row" id="recentRoutes">
            <div class="col-md-4">
                <div class="card">
                    <img src="https://images.unsplash.com/photo-1662239090914-1da951eaeda4?fm=jpg&q=60&w=3000&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8OHx8YWNvbmNhZ3VhfGVufDB8fDB8fHww" class="card-img-top" alt="Cerro Aconcagua">
                    <div class="card-body">
                        <h5 class="card-title">Cerro Aconcagua</h5>
                        <p class="card-text">Ruta normal de ascenso al pico más alto de América.</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="badge bg-primary">Argentina</span>
                            <span class="text-muted">6962 m</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <img src="https://images.unsplash.com/photo-1589994965851-a8f479c573a9?ixlib=rb-4.0.3" class="card-img-top" alt="Torres del Paine">
                    <div class="card-body">
                        <h5 class="card-title">Torres del Paine</h5>
                        <p class="card-text">Circuito W en el parque nacional Torres del Paine.</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="badge bg-primary">Chile</span>
                            <span class="text-muted">Dificultad: Media</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <img src="https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?ixlib=rb-4.0.3" class="card-img-top" alt="Monte Fitz Roy">
                    <div class="card-body">
                        <h5 class="card-title">Monte Fitz Roy</h5>
                        <p class="card-text">Ascenso técnico a una de las montañas más desafiantes.</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="badge bg-primary">Argentina/Chile</span>
                            <span class="text-muted">3405 m</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Support Section -->
    <section class="bg-light py-5">
        <div class="container text-center">
            <h2 class="mb-4">¿Te gusta la plataforma?</h2>
            <p class="lead mb-4">Mantener este proyecto requiere recursos. Considera hacer una contribución voluntaria.</p>
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-body p-4">
                            <h4 class="card-title mb-4">Planes de apoyo</h4>
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <div class="card">
                                        <div class="card-body">
                                            <h5>Montañista</h5>
                                            <h3 class="my-3">$5/mes</h3>
                                            <ul class="list-unstyled">
                                                <li>✔️ Acceso completo</li>
                                                <li>✔️ Agradecimiento público</li>
                                                <li>✔️ Insignia en perfil</li>
                                            </ul>
                                            <button class="btn btn-outline-primary">Seleccionar</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <div class="card border-primary">
                                        <div class="card-body">
                                            <span class="badge bg-primary position-absolute top-0 end-0 m-2">Popular</span>
                                            <h5>Explorador</h5>
                                            <h3 class="my-3">$10/mes</h3>
                                            <ul class="list-unstyled">
                                                <li>✔️ Todo lo anterior</li>
                                                <li>✔️ Acceso prioritario a nuevas funciones</li>
                                                <li>✔️ Almacenamiento extra</li>
                                            </ul>
                                            <button class="btn btn-primary">Seleccionar</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <div class="card">
                                        <div class="card-body">
                                            <h5>Guía</h5>
                                            <h3 class="my-3">$25/mes</h3>
                                            <ul class="list-unstyled">
                                                <li>✔️ Todo lo anterior</li>
                                                <li>✔️ Listado destacado</li>
                                                <li>✔️ Soporte prioritario</li>
                                            </ul>
                                            <button class="btn btn-outline-primary">Seleccionar</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5>MontainHandBook</h5>
                    <p>Plataforma colaborativa para amantes de la montaña.</p>
                </div>
                <div class="col-md-3">
                    <h5>Enlaces</h5>
                    <ul class="list-unstyled">
                        <li><a href="#" class="text-white">Inicio</a></li>
                        <li><a href="#" class="text-white">Acerca de</a></li>
                        <li><a href="#" class="text-white">Contacto</a></li>
                    </ul>
                </div>
                <div class="col-md-3">
                    <h5>Legal</h5>
                    <ul class="list-unstyled">
                        <li><a href="#" class="text-white">Términos de uso</a></li>
                        <li><a href="#" class="text-white">Privacidad</a></li>
                    </ul>
                </div>
            </div>
            <hr>
            <p class="text-center mb-0">© 2025 MontainHandBook. Todos los derechos reservados.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Simulación de datos para los selectores
        document.getElementById('continent').addEventListener('change', function() {
            const countrySelect = document.getElementById('country');
            countrySelect.disabled = false;
            countrySelect.innerHTML = '<option value="" selected disabled>Selecciona un país</option>';
            
            // Simular carga de países según continente seleccionado
            const countries = {
                southamerica: ['Argentina', 'Chile', 'Perú', 'Bolivia', 'Ecuador', 'Colombia'],
                northamerica: ['Estados Unidos', 'Canadá', 'México'],
                europe: ['España', 'Francia', 'Italia', 'Suiza', 'Austria'],
                asia: ['Nepal', 'India', 'China', 'Japón'],
                africa: ['Tanzania', 'Kenia', 'Marruecos', 'Sudáfrica'],
                oceania: ['Nueva Zelanda', 'Australia', 'Papúa Nueva Guinea']
            };
            
            const selectedCountries = countries[this.value] || [];
            selectedCountries.forEach(country => {
                const option = document.createElement('option');
                option.value = country.toLowerCase();
                option.textContent = country;
                countrySelect.appendChild(option);
            });
        });

        // Simular envío del formulario
        document.getElementById('routeSelectorForm').addEventListener('submit', function(e) {
            e.preventDefault();
            alert('Funcionalidad de búsqueda en desarrollo. Pronto podrás explorar todas las rutas disponibles.');
        });
    </script>
</body>
</html>