<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $password = $_POST['password'] ?? '';
    
    if ($email && $password) {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        
        try {
            $sql = "INSERT INTO users (email, password_hash) VALUES (:email, :password_hash)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':email' => $email,
                ':password_hash' => $password_hash
            ]);
            
            header('Location: login.php');
            exit;
        } catch (PDOException $e) {
            $error = "Erreur lors de l'inscription. L'email est peut-être déjà utilisé.";
        }
    } else {
        $error = "Veuillez remplir tous les champs correctement.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Inscription</title>
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
    <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
    
    <form method="POST">
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
        </div>
        <div class="form-group">
            <label for="password">Mot de passe:</label>
            <input type="password" id="password" name="password" required>
        </div>
        <button type="submit">S'inscrire</button>
    </form>
    <p>Déjà inscrit? <a href="login.php">Connectez-vous ici</a></p>
</body>
</html> 