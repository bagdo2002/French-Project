<?php
session_start();        // ferme la session existante
session_destroy();       // vide et supprime les données

// Redirige vers la page d'accueil de l'application
header('Location: index.php');
exit;
