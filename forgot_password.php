<?php
session_start();
require 'config.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);

    if (!$email) {
        $message = "Veuillez entrer une adresse e-mail valide.";
    } else {
        // Step 1: Check if email exists in the database
        $sql = "SELECT id FROM users WHERE email = :email";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch();

        if ($user) {
            $user_id = $user['id'];
            
            // Step 2: Generate a secure, unique token
            $token = bin2hex(random_bytes(32)); // 64 characters
            $expires = new DateTime();
            $expires->modify('+1 hour'); // Token valid for 1 hour
            
            // Step 3: Store the token and expiration in the database
            $sql_update = "UPDATE users SET reset_token = :token, reset_token_expires_at = :expires WHERE id = :id";
            $stmt_update = $pdo->prepare($sql_update);
            $stmt_update->execute([
                ':token' => $token,
                ':expires' => $expires->format('Y-m-d H:i:s'),
                ':id' => $user_id
            ]);
            
            // Step 4: Send the password reset email using PHPMailer
            require 'vendor/autoload.php';

            $mail = new PHPMailer\PHPMailer\PHPMailer(true);

            try {
                // Hostinger SMTP settings (replace with your actual values)
                $mail->isSMTP();
                $mail->Host       = 'smtp.hostinger.com'; // e.g., smtp.hostinger.com or mail.trinidad-betting.com
                $mail->SMTPAuth   = true;
                $mail->Username   = 'no-reply@trinidad-betting.com'; // your Hostinger email
                $mail->Password   = 'YOUR_EMAIL_PASSWORD'; // your Hostinger email password
                $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS; // or ENCRYPTION_STARTTLS for port 587
                $mail->Port       = 465; // 465 for SSL, 587 for TLS

                // Sender and recipient
                $mail->setFrom('no-reply@trinidad-betting.com', 'Trinidad Betting Support');
                $mail->addAddress($email);

                // Email content
                $mail->isHTML(true);
                $mail->Subject = 'Password Reset Request for Trinidad Betting';
                // IMPORTANT: Change this to your live domain when hosted
                $reset_link = "https://trinidad-betting.com/reset_password.php?token=" . $token;
                $mail->Body    = 'Hello,<br><br>You requested a password reset for your Trinidad Betting account. Please click on the following link to reset your password: <a href="' . $reset_link . '">' . $reset_link . '</a><br><br>This link will expire in 1 hour. If you did not request a password reset, please ignore this email.<br><br>Thanks,<br>The Trinidad Betting Team';
                $mail->AltBody = 'Hello, You requested a password reset for your Trinidad Betting account. Please copy and paste the following link into your browser to reset your password: ' . $reset_link . ' This link will expire in 1 hour. If you did not request a password reset, please ignore this email. Thanks, The Trinidad Betting Team';

                $mail->send();
                $message = "Si votre adresse e-mail est enregistrée chez nous, un lien de réinitialisation de mot de passe vous a été envoyé. Veuillez vérifier votre boîte de réception (et vos spams).";

            } catch (Exception $e) {
                error_log("Message could not be sent. Mailer Error: {$mail->ErrorInfo}");
                $message = "Si votre adresse e-mail est enregistrée chez nous, un lien de réinitialisation de mot de passe vous a été envoyé. Veuillez vérifier votre boîte de réception (et vos spams).";
            }
            
        } else {
            // To prevent email enumeration attacks, always give a generic success message
            $message = "Si votre adresse e-mail est enregistrée chez nous, un lien de réinitialisation de mot de passe vous a été envoyé. Veuillez vérifier votre boîte de réception (et vos spams).";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mot de passe oublié</title>
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

        .forgot-password-container {
            max-width: 460px;
            width: 100%;
            background: var(--bg-card);
            padding: 2.5rem 2.5rem 3rem;
            border-radius: 12px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.6);
            text-align: center;
        }

        .forgot-password-container h2 {
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
            margin-top: 1.5rem;
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
    <div class="forgot-password-container">
        <h2>Mot de passe oublié ?</h2>
        <?php if ($message): ?>
            <div class="message <?= (strpos($message, 'Erreur') !== false || strpos($message, 'invalide') !== false) ? 'error' : 'success' ?>">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>
        <form action="forgot_password.php" method="POST">
            <div class="form-group">
                <label for="email">Adresse e-mail</label>
                <input type="email" id="email" name="email" required>
            </div>
            <button type="submit" class="btn">Réinitialiser le mot de passe</button>
        </form>
        <div class="back-to-login">
            <a href="login.html">Retour à la connexion</a>
        </div>
    </div>
</body>
</html> 