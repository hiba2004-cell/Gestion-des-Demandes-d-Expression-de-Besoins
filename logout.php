<?php
session_start();

// Détruire toutes les variables de session
$_SESSION = [];

// Détruire la session
session_destroy();

// Rediriger vers la page de connexion ou l'accueil
header("Location: index.php");
exit;