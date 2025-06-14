<?php
session_start();                 // garde la session ouverte partout
require_once 'config.php';       // si tu as besoin d’interroger MySQL

// Récupère le pseudo si l’utilisateur est loggé
$user = null;
if (isset($_SESSION['user_id'])) {
  $stmt = $pdo->prepare('SELECT username FROM users WHERE id = ?');
  $stmt->execute([$_SESSION['user_id']]);
  $user = $stmt->fetchColumn();          // null si aucun résultat
}
?>
<header>
  <img style="border-radius: 50%; cursor: pointer" src="./logo.jpg" alt="Logo Trinidad">
  <h1>Trinidad Betting</h1>

  <div>
    <?php if ($user): ?>
      <!-- Affiche le pseudo + bouton Logout -->
      <span class="btn" style="background:#161B22;color:#FFC845;cursor:default;">
        <?= htmlspecialchars($user) ?>
      </span>
      <a href="logout.php" class="btn">Déconnexion</a>
    <?php else: ?>
      <!-- Version visiteur -->
      <a href="login.html" class="btn">Se connecter</a>
      <a href="inscription.html" class="btn">S’inscrire</a>
    <?php endif; ?>
  </div>
</header>