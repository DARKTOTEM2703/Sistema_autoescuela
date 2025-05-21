<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicializar Base de Datos</title>
    <style>
    body {
        font-family: Arial, sans-serif;
        text-align: center;
        padding: 20px;
        background-color: #f4f4f4;
    }

    .container {
        max-width: 500px;
        margin: 0 auto;
        background: white;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    h2 {
        color: #e91e63;
    }

    p {
        margin-bottom: 20px;
        color: #333;
    }

    .btn {
        display: inline-block;
        background: #e91e63;
        color: white;
        padding: 10px 20px;
        text-decoration: none;
        border-radius: 4px;
        font-weight: bold;
        margin-top: 10px;
    }

    .warning {
        color: #e91e63;
        font-weight: bold;
    }
    </style>
</head>

<body>
    <div class="container">
        <h2>Inicializar Base de Datos</h2>
        <p>Si estás experimentando problemas con la autenticación o la herramienta de gestión de contraseñas, es posible
            que necesites inicializar la base de datos.</p>
        <p class="warning">Advertencia: Este proceso eliminará todos los datos existentes y creará una nueva base de
            datos.</p>
        <a href="../../backend/database/init_db.php" class="btn">Inicializar Base de Datos</a>
        <p><a href="../landing/index.php">Volver a la página principal</a></p>
    </div>
</body>

</html>