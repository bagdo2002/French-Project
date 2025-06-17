<?php
session_start();
require 'config.php';

$message = '';
$token = $_GET['token'] ?? '';

$user_id = null;

// Step 1: Validate the token from the URL
if (empty($token)) {
    $message = "Jeton de réinitialisation manquant.";
} else {
    $sql = "SELECT id, reset_token_expires_at FROM users WHERE reset_token = :token";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':token' => $token]);
    $user = $stmt->fetch();

    if (!$user) {
        $message = "Jeton de réinitialisation invalide ou déjà utilisé.";
    } else {
        $expires = new DateTime($user['reset_token_expires_at']);
        $now = new DateTime();

        if ($now > $expires) {
            $message = "Jeton de réinitialisation expiré. Veuillez refaire une demande.";
        } else {
            $user_id = $user['id']; // Token is valid and not expired
        }
    }
}

// Handle new password submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $user_id) {
    $new_password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (empty($new_password) || empty($confirm_password)) {
        $message = "Veuillez remplir tous les champs de mot de passe.";
    } elseif ($new_password !== $confirm_password) {
        $message = "Les mots de passe ne correspondent pas.";
    } elseif (strlen($new_password) < 6) { // Example: minimum 6 characters
        $message = "Le mot de passe doit contenir au moins 6 caractères.";
    } else {
        $password_hash = password_hash($new_password, PASSWORD_DEFAULT);

        try {
            // Update password and clear token
            $sql_update = "UPDATE users SET password_hash = :password_hash, reset_token = NULL, reset_token_expires_at = NULL WHERE id = :id";
            $stmt_update = $pdo->prepare($sql_update);
            $stmt_update->execute([
                ':password_hash' => $password_hash,
                ':id' => $user_id
            ]);

            $message = "Votre mot de passe a été réinitialisé avec succès. Vous pouvez maintenant vous connecter.";
            // Redirect to login page after a short delay, or directly.
            header('Refresh: 3; URL=login.html'); // Redirect after 3 seconds
            exit;
        } catch (PDOException $e) {
            $message = "Erreur lors de la mise à jour du mot de passe.";
            // For debugging: error_log("Password reset error: " . $e->getMessage());
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réinitialiser le mot de passe</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-dark: #0d1117;
            --bg-card: #161b22;
            --text-light: #f2f2f2;
            --primary: #ffc845;
            --secondary: #1e8e3e;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Montserrat', Arial, sans-serif;
            color: var(--text-light);
        }

        body {
            background: var(--bg-dark);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .reset-password-container {
            max-width: 460px;
            width: 100%;
            background: var(--bg-card);
            padding: 2.5rem 2.5rem 3rem;
            border-radius: 12px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.6);
            text-align: center;
        }

        .reset-password-container h2 {
            margin-bottom: 1.5rem;
            color: var(--text-light);
        }

        .form-group {
            margin-bottom: 1.2rem;
            text-align: left;
        }

        .form-group label {
            display: block;
            font-size: 0.9rem;
            margin-bottom: 0.3rem;
            font-weight: 600;
        }

        .form-group input {
            width: 100%;
            padding: 0.65rem 0.8rem;
            border: 1px solid #444c56;
            border-radius: 6px;
            background: #0d1117;
            color: var(--text-light);
        }

        .btn {
            cursor: pointer;
            padding: 0.6rem 1.2rem;
            border: none;
            border-radius: 6px;
            font-weight: 600;
            background: var(--primary);
            color: #000;
            transition: transform 0.15s;
            width: 100%;
            font-size: 1rem;
        }

        .btn:hover {
            transform: scale(1.05);
        }

        .message {
            margin-bottom: 1.5rem;
            padding: 10px;
            border-radius: 5px;
            font-size: 0.9rem;
        }

        .message.success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .message.error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .back-to-login {
            margin-top: 1.5rem;
            font-size: 0.85rem;
        }

        .back-to-login a {
            color: var(--secondary);
            text-decoration: none;
            font-weight: 600;
        }

        .back-to-login a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="reset-password-container">
        <h2>Réinitialiser le mot de passe</h2>
        <?php if ($message): ?>
            <div class="message <?= (strpos($message, 'succès') !== false) ? 'success' : 'error' ?>">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <?php if ($user_id && empty($message)): // Only show form if token is valid and no error yet ?>
            <form action="reset_password.php?token=<?= htmlspecialchars($token) ?>" method="POST">
                <div class="form-group">
                    <label for="password">Nouveau mot de passe</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <div class="form-group">
                    <label for="confirm_password">Confirmer le mot de passe</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                </div>
                <button type="submit" class="btn">Mettre à jour le mot de passe</button>
            </form>
        <?php elseif (!empty($message)): // Show link back to login if there was an error ?>
            <div class="back-to-login">
                <a href="login.html">Retour à la connexion</a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html> 