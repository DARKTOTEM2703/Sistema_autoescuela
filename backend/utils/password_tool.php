<?php
// Mostrar errores para depuración
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../autoload.php';

use Backend\Config\Database;

/**
 * Utilidad para verificar y actualizar contraseñas en la base de datos
 */
class PasswordTool
{
    private $db;
    private $result = null;

    public function __construct()
    {
        try {
            $this->db = Database::getInstance()->getConnection();
        } catch (\PDOException $e) {
            throw new \Exception("Error de conexión: " . $e->getMessage());
        }
    }

    /**
     * Verifica si una contraseña coincide con el hash almacenado para un usuario
     */
    public function verificarPassword($username, $password)
    {
        try {
            $stmt = $this->db->prepare("SELECT password FROM usuarios WHERE username = :username");
            $stmt->bindParam(':username', $username, PDO::PARAM_STR);
            $stmt->execute();

            $user = $stmt->fetch();

            if (!$user) {
                return ['success' => false, 'mensaje' => "El usuario '$username' no existe."];
            }

            $hashAlmacenado = $user['password'];
            $coincide = password_verify($password, $hashAlmacenado);

            return [
                'success' => true,
                'coincide' => $coincide,
                'mensaje' => "Contraseña para '$username': " . ($coincide ? "CORRECTA" : "INCORRECTA"),
                'hash' => $hashAlmacenado
            ];
        } catch (\PDOException $e) {
            return ['success' => false, 'mensaje' => "Error al verificar: " . $e->getMessage()];
        }
    }

    /**
     * Actualiza la contraseña de un usuario con un nuevo hash
     */
    public function actualizarPassword($username, $newPassword)
    {
        try {
            // Primero verificamos si el usuario existe
            $stmt = $this->db->prepare("SELECT id FROM usuarios WHERE username = :username");
            $stmt->bindParam(':username', $username, PDO::PARAM_STR);
            $stmt->execute();

            if (!$stmt->fetch()) {
                return ['success' => false, 'mensaje' => "El usuario '$username' no existe."];
            }

            // Generar el hash con bcrypt
            $hash = password_hash($newPassword, PASSWORD_BCRYPT);

            $stmt = $this->db->prepare("UPDATE usuarios SET password = :password WHERE username = :username");
            $stmt->bindParam(':password', $hash, PDO::PARAM_STR);
            $stmt->bindParam(':username', $username, PDO::PARAM_STR);

            if ($stmt->execute()) {
                return [
                    'success' => true,
                    'mensaje' => "Contraseña actualizada correctamente para '$username'",
                    'hash' => $hash
                ];
            } else {
                return ['success' => false, 'mensaje' => "Error al actualizar la contraseña."];
            }
        } catch (\PDOException $e) {
            return ['success' => false, 'mensaje' => "Error al actualizar: " . $e->getMessage()];
        }
    }

    /**
     * Genera un nuevo hash para una contraseña (sin guardarla)
     */
    public function generarHash($password)
    {
        try {
            $hash = password_hash($password, PASSWORD_BCRYPT);
            return [
                'success' => true,
                'mensaje' => "Hash generado para la contraseña",
                'hash' => $hash
            ];
        } catch (\Exception $e) {
            return ['success' => false, 'mensaje' => "Error al generar hash: " . $e->getMessage()];
        }
    }

    // Procesar formulario si se envía
    public function processRequest()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';

            switch ($action) {
                case 'verificar':
                    $response = $this->verificarPassword($username, $password);
                    $this->result = [
                        'success' => $response['coincide'] ?? false,
                        'result' => $response['mensaje'] ?? 'Error al verificar la contraseña',
                        'hash' => $response['hash'] ?? '',
                    ];
                    break;
                case 'actualizar':
                    $response = $this->actualizarPassword($username, $password);
                    $this->result = [
                        'success' => $response['success'] ?? false,
                        'result' => $response['mensaje'] ?? 'Error al actualizar la contraseña',
                        'hash' => $response['hash'] ?? '',
                    ];
                    break;
                case 'generar':
                    $response = $this->generarHash($password);
                    $this->result = [
                        'success' => $response['success'] ?? false,
                        'result' => $response['mensaje'] ?? 'Error al generar el hash',
                        'hash' => $response['hash'] ?? '',
                    ];
                    break;
            }

            // Si es una petición AJAX, devolver JSON
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
                strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                header('Content-Type: application/json');
                echo json_encode($this->result);
                exit;
            }
        }
    }

    public function getResult() {
        return $this->result;
    }
}

// Función para renderizar la UI de la herramienta
function renderPasswordToolUI() {
    $passwordTool = new PasswordTool();
    $passwordTool->processRequest();
    $resultado = $passwordTool->getResult();

    // Renderizar HTML del formulario
    ?>
    <!DOCTYPE html>
    <html lang="es">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Herramienta de Contraseñas</title>
        <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background: #f5f5f5;
        }

        h1 {
            color: #333;
        }

        .card {
            background: #fff;
            border-radius: 5px;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        form {
            margin-top: 20px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        input,
        select {
            width: 100%;
            padding: 8px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }

        button {
            background: #4CAF50;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 4px;
            cursor: pointer;
        }

        button:hover {
            background: #45a049;
        }

        .result {
            margin-top: 20px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            background: #f9f9f9;
            white-space: pre-wrap;
        }

        .warning {
            color: red;
            margin-bottom: 20px;
        }

        .success {
            color: green;
        }

        .error {
            color: red;
        }
        </style>
    </head>

    <body>
        <h1>Herramienta de Gestión de Contraseñas</h1>
        <div class="warning">
            <strong>Advertencia:</strong> Esta herramienta debe usarse solo por administradores del sistema y debe estar
            protegida.
        </div>

        <div class="card">
            <h2>Gestión de Contraseñas</h2>
            <form id="passwordForm">
                <label for="action">Acción:</label>
                <select id="action" name="action">
                    <option value="verificar">Verificar Contraseña</option>
                    <option value="actualizar">Actualizar Contraseña</option>
                    <option value="generar">Generar Hash</option>
                </select>

                <div id="usernameField">
                    <label for="username">Nombre de Usuario:</label>
                    <input type="text" id="username" name="username" required>
                </div>

                <label for="password">Contraseña:</label>
                <input type="password" id="password" name="password" required>

                <button type="submit">Ejecutar</button>
            </form>

            <div id="result" class="result" style="display: none;"></div>
        </div>

        <script>
        document.getElementById('passwordForm').addEventListener('submit', function(e) {
            e.preventDefault();

            document.getElementById('result').textContent = 'Procesando...';
            document.getElementById('result').className = 'result';
            document.getElementById('result').style.display = 'block';

            const formData = new FormData(this);

            fetch(window.location.href, {
                    method: 'POST',
                    body: formData
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Error en la respuesta del servidor');
                    }
                    return response.json();
                })
                .then(data => {
                    const resultDiv = document.getElementById('result');
                    resultDiv.textContent = data.result;
                    resultDiv.className = data.success ? 'result success' : 'result error';
                    resultDiv.style.display = 'block';
                })
                .catch(error => {
                    console.error('Error:', error);
                    const resultDiv = document.getElementById('result');
                    resultDiv.textContent = 'Error al procesar la solicitud: ' + error.message;
                    resultDiv.className = 'result error';
                    resultDiv.style.display = 'block';
                });
        });

        // Ocultar el campo de usuario cuando se selecciona "generar"
        document.getElementById('action').addEventListener('change', function() {
            const usernameField = document.getElementById('usernameField');
            const usernameInput = document.getElementById('username');
            if (this.value === 'generar') {
                usernameInput.required = false;
                usernameField.style.display = 'none';
            } else {
                usernameInput.required = true;
                usernameField.style.display = 'block';
            }
        });
        </script>

        <div style="margin-top: 30px; text-align: center;">
            <a href="../../frontend/admin/init_link.php" style="text-decoration: none; color: #4CAF50;">Inicializar Base de
                Datos</a> |
            <a href="../../frontend/admin/login/index.php" style="text-decoration: none; color: #4CAF50;">Ir al Login</a>
        </div>
    </body>

    </html>
    <?php
}

// Si este archivo se llama directamente (no incluido)
if (basename($_SERVER['SCRIPT_FILENAME']) == basename(__FILE__)) {
    header('Location: ../../frontend/admin/tools/password_manager.php');
    exit;
}

// Ejemplo de estructura de respuesta esperada para generación de hash
return [
    'success' => true,
    'hash' => '$2y$10$YrpF8lcWblsE8mOtKVzAkOD...', // El hash generado
    'result' => 'Hash generado para la contraseña'
];