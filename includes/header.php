<?php include_once __DIR__ . '/config.php'; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion de chantier</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/style.css">
</head>
<body>
    <header>
        <div class="container">
            <h1>Gestion de chantier</h1>
            <nav>
                <ul>
                    <li><a href="<?= BASE_URL ?>index.php">Accueil</a></li>
                    <li><a href="<?= BASE_URL ?>pages/chantiers/liste.php">Chantiers</a></li>
                    <?php if (isAdmin()): ?>
                        <li><a href="<?= BASE_URL ?>pages/clients/liste.php">Clients</a></li>
                    <?php endif; ?>
                    <li><a href="<?= BASE_URL ?>pages/materiels/liste.php">Matériels</a></li>
                    <li><a href="<?= BASE_URL ?>pages/demandes/liste.php">Demandes</a></li>
                    <?php if (isAdmin()): ?>
                        <li><a href="<?= BASE_URL ?>pages/utilisateurs/liste.php">Utilisateurs</a></li>
                    <?php endif; ?>
                    <li><a href="<?= BASE_URL ?>logout.php">Déconnexion</a></li>
                </ul>
            </nav>
        </div>
    </header>
    <main class="container">