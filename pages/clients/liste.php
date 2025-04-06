<?php
require_once '../../includes/config.php';
require_once '../../includes/auth.php';
requireAdmin();

// Récupération des clients
$stmt = $db->query("SELECT * FROM clients ORDER BY nom ASC");
$clients = $stmt->fetchAll();

require_once '../../includes/header.php';
?>

<link rel="stylesheet" href="../../assets/css/liste.css">
<link rel="stylesheet" href="../../assets/css/style.css">

<div class="user-management-container">
    <h2>Gestion des clients</h2>
    <a href="ajouter.php" class="add-user-btn">Ajouter un client</a>

    <?php if (isset($_GET['success'])): ?>
        <div class="alert success">Opération effectuée avec succès</div>
    <?php endif; ?>

    <div class="table-responsive">
        <table class="users-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Email</th>
                    <th>Téléphone</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($clients as $client): ?>
                <tr>
                    <td><?= $client['id'] ?></td>
                    <td><?= htmlspecialchars($client['nom']) ?></td>
                    <td><?= htmlspecialchars($client['email']) ?></td>
                    <td><?= htmlspecialchars($client['telephone']) ?></td>
                    <td>
                        <div class="action-buttons">
                            <a href="modifier.php?id=<?= $client['id'] ?>" class="btn edit-btn">Modifier</a>
                            <a href="supprimer.php?id=<?= $client['id'] ?>" class="btn delete-btn" onclick="return confirm('Êtes-vous sûr ?')">Supprimer</a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>