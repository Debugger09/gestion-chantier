<?php
require_once '../../includes/config.php';
require_once '../../includes/auth.php';
requireAuth();

// Récupération des chantiers avec jointures
$query = "SELECT c.*, cl.nom as client_nom, u.nom as chef_nom 
          FROM chantiers c
          JOIN clients cl ON c.client_id = cl.id
          LEFT JOIN users u ON c.id_chef = u.id
          ORDER BY c.date_debut DESC";

$stmt = $db->query($query);
$chantiers = $stmt->fetchAll();

require_once '../../includes/header.php';
?>

<link rel="stylesheet" href="../../assets/css/liste.css">
<link rel="stylesheet" href="../../assets/css/style.css">

<div class="user-management-container">
    <h2>Gestion des chantiers</h2>
    <?php if (isAdmin()): ?>
        <a href="ajouter.php" class="add-user-btn">Ajouter un chantier</a>
    <?php endif; ?>

    <?php if (isset($_GET['success'])): ?>
        <div class="alert success">Opération effectuée avec succès</div>
    <?php endif; ?>

    <div class="table-responsive">
        <table class="users-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Localisation</th>
                    <th>Client</th>
                    <th>Chef de projet</th>
                    <th>Dates</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($chantiers as $chantier): ?>
                <tr>
                    <td><?= $chantier['id'] ?></td>
                    <td><?= htmlspecialchars($chantier['nom_chantier']) ?></td>
                    <td><?= htmlspecialchars($chantier['localisation']) ?></td>
                    <td><?= htmlspecialchars($chantier['client_nom']) ?></td>
                    <td>
                        <?= $chantier['chef_nom'] ? htmlspecialchars($chantier['chef_nom']) : '<span class="status warning">Non attribué</span>' ?>
                    </td>
                    <td>
                        <?= date('d/m/Y', strtotime($chantier['date_debut'])) ?>
                        <?= $chantier['date_fin'] ? ' - '.date('d/m/Y', strtotime($chantier['date_fin'])) : '' ?>
                    </td>
                    <td>
                        <div class="action-buttons">
                            <a href="details.php?id=<?= $chantier['id'] ?>" class="btn view-btn">Détails</a>
                            <?php if (isAdmin()): ?>
                                <a href="modifier.php?id=<?= $chantier['id'] ?>" class="btn edit-btn">Modifier</a>
                                <?php if (!$chantier['id_chef']): ?>
                                    <a href="attribuer.php?id=<?= $chantier['id'] ?>" class="btn assign-btn">Attribuer</a>
                                <?php endif; ?>
                                <a href="supprimer.php?id=<?= $chantier['id'] ?>" class="btn delete-btn" onclick="return confirm('Êtes-vous sûr ?')">Supprimer</a>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>