<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Reconocimiento Facial</title>
    <meta name="description" content="Sistema de Reconocimiento Facial para la gestión de usuarios.">
    <link rel="shortcut icon" href="{{asset('favicon.png') }}" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;700&display=swap" rel="stylesheet">
    <!-- <link rel="stylesheet" href="styles.css"> -->
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <i class="fas fa-id-card me-2"></i>
                Sistema de Reconocimiento Facial
            </a>
        </div>
    </nav>

  


    <div id="main-content" class="container mt-4" >
        <ul class="nav nav-pills nav-fill mb-3" id="myTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="reconocimiento-tab" data-bs-toggle="pill" data-bs-target="#reconocimiento" type="button" role="tab" aria-controls="reconocimiento" aria-selected="true">
                    <i class="fas fa-camera me-2"></i>Reconocimiento Facial
                </button>
            </li>
            <!-- <li class="nav-item" role="presentation">
                <button class="nav-link" id="registro-tab" data-bs-toggle="pill" data-bs-target="#registro" type="button" role="tab" aria-controls="registro" aria-selected="false">
                    <i class="fas fa-user-plus me-2"></i>Registro de Usuario
                </button>
            </li> -->
        </ul>
        <div class="tab-content" id="myTabContent">
            <div class="tab-pane fade show active" id="reconocimiento" role="tabpanel" aria-labelledby="reconocimiento-tab">
                <div class="card shadow">
                    <div class="card-body">
                        <h2 class="card-title text-center">Reconocimiento Facial</h2>
                        <div id="camera" class="mb-3">
                            <video id="video" class="w-100" autoplay muted></video>
                        </div>
                        <button id="start-camera" class="btn btn-primary w-100 mb-3" disabled>Activar Cámara</button>
                        <div id="recognition-result" class="alert alert-info" style="display: none;"></div>
                        <p id="loading-message" class="mt-3 alert alert-info" style="display: none;">Cargando datos, por favor espera...</p>
                    </div>
                </div>
            </div>
            <!-- <div class="tab-pane fade" id="registro" role="tabpanel" aria-labelledby="registro-tab">
                <div class="card shadow">
                    <div class="card-body">
                        <h2 class="card-title">Agregar Usuario</h2>
                        <form id="user-form" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="name" class="form-label">Nombre:</label>
                                <input type="text" id="name" name="name" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="cedula" class="form-label">Cédula:</label>
                                <input type="text" id="cedula" name="cedula" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="cargo" class="form-label">Cargo:</label>
                                <input type="text" id="cargo" name="cargo" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="photo" class="form-label">Foto:</label>
                                <input type="file" id="photo" name="photo" class="form-control" accept="image/*" required>
                            </div>
                            <button type="submit" id="submit-button" class="btn btn-success w-100">Agregar Usuario</button>
                        </form>
                        <p id="loading-message" class="mt-3" style="display: none;">Agregando usuario, por favor espera...</p>
                    </div>
                </div>
            </div> -->
            <!-- <p id="loading-message" class="mt-3" style="display: none;">cargando datos, por favor espera...</p> -->
       
        </div>
    </div>

    <div id="custom-alert" class="alert alert-success position-fixed top-50 start-50 translate-middle" style="display:none; z-index:1000;">
        Registro exitoso
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/face-api.js/dist/face-api.min.js"></script>
    <script src="{{ asset('js/script.js') }}"></script>

    <!-- Footer -->
    <footer class="footer mt-5 bg-dark text-light py-4">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-4 col-md-6">
                    <h5 class="mb-3">Acerca de Nosotros</h5>
                    <p class="mb-3">Somos un equipo dedicado a desarrollar soluciones innovadoras.</p>
                </div>
                <div class="col-lg-4 col-md-6">
                    <h5 class="mb-3">Soporte Técnico</h5>
                    <p class="mb-3">¿Necesitas ayuda? Contáctanos para soporte técnico especializado.</p>
                </div>
                <div class="col-lg-4 col-md-6">
                    <h5 class="mb-3">Síguenos</h5>
                    <a href="https://twitter.com" class="text-light text-decoration-none d-block mb-2 hover-effect" target="_blank">
                        <i class="fab fa-twitter me-2"></i>Twitter
                    </a>
                    <a href="https://facebook.com" class="text-light text-decoration-none d-block hover-effect" target="_blank">
                        <i class="fab fa-facebook me-2"></i>Facebook
                    </a>
                </div>
            </div>
            <div class="row mt-4">
                <div class="col-12 text-center">
                    <hr class="bg-light">
                    <p class="mb-0">&copy; 2025 Sistema de Reconocimiento Facial. Todos los derechos reservados.</p>
                </div>
            </div>
        </div>
    </footer>
</body>
</html>