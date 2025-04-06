<?php
require_once '../../includes/config.php';
require_once '../../includes/auth.php';
requireAuth();

$stmt = $db->query("SELECT * FROM materiels ORDER BY nom_materiel ASC");
$materiels = $stmt->fetchAll();

require_once '../../includes/header.php';
?>

<link rel="stylesheet" href="../../assets/css/liste.css">
<link rel="stylesheet" href="../../assets/css/style.css">

<div class="user-management-container">
    <h2>Gestion du matériel</h2>
    <a href="ajouter.php" class="add-user-btn">Ajouter un matériel</a>

    <?php if (isset($_GET['success'])): ?>
        <div class="alert success">Opération effectuée avec succès</div>
    <?php endif; ?>

    <div class="table-responsive">
        <table class="users-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Description</th>
                    <th>Quantité</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($materiels as $materiel): ?>
                <tr>
                    <td><?= $materiel['id'] ?></td>
                    <td><?= htmlspecialchars($materiel['nom_materiel']) ?></td>
                    <td><?= htmlspecialchars($materiel['description']) ?></td>
                    <td><?= $materiel['quantite_disponible'] ?></td>
                    <td>
                        <div class="action-buttons">
                            <a href="modifier.php?id=<?= $materiel['id'] ?>" class="btn edit-btn">Modifier</a>
                            <a href="supprimer.php?id=<?= $materiel['id'] ?>" class="btn delete-btn" onclick="return confirm('Êtes-vous sûr ?')">Supprimer</a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>