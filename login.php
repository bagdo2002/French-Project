<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
require 'config.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    /* 1. Récupération des champs */
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $pass  = $_POST['password'] ?? '';

    if (!$email || !$pass) {
        $error = 'Merci de remplir tous les champs.';
    } else {
        /* 2. Cherche l'utilisateur par e-mail */
        $sql  = "SELECT id, password_hash FROM users WHERE email = :mail";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':mail' => $email]);
        $user = $stmt->fetch();

        /* 3. Vérification du mot de passe */
        if ($user && password_verify($pass, $user['password_hash'])) {
            $_SESSION['user_id'] = $user['id'];      // on stocke l'ID en session
            header('Location: index.php'); // page protégée à créer
            exit;
        } else {
            $error = 'E-mail ou mot de passe incorrect.';
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Connexion</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 500px;
            margin: 0 auto;
            padding: 20px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
        }
        input {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background-color: #45a049;
        }
        .error {
            color: red;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <h1>Connexion</h1>
    <?php if ($error) echo "<p class='error'>$error</p>"; ?>
    
    <form method="POST">
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
        </div>
        <div class="form-group">
            <label for="password">Mot de passe:</label>
            <input type="password" id="password" name="password" required>
        </div>
        <button type="submit">Se connecter</button>
    </form>
    <p>Pas encore de compte? <a href="register.php">Inscrivez-vous ici</a></p>
</body>
</html>
