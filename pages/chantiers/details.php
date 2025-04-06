<?php
require_once '../../includes/config.php';
require_once '../../includes/auth.php';
requireAuth();

// Vérifier si l'ID du chantier est passé en paramètre
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: liste.php');
    exit();
}

$chantier_id = intval($_GET['id']);

// Récupérer les informations du chantier
$query = "SELECT c.*, cl.nom as client_nom, cl.email as client_email, cl.telephone as client_telephone, 
                 cl.adresse as client_adresse, u.nom as chef_nom, u.email as chef_email
          FROM chantiers c
          JOIN clients cl ON c.client_id = cl.id
          LEFT JOIN users u ON c.id_chef = u.id
          WHERE c.id = ?";

$stmt = $db->prepare($query);
$stmt->execute([$chantier_id]);
$chantier = $stmt->fetch();

// Si le chantier n'existe pas, rediriger
if (!$chantier) {
    header('Location: liste.php');
    exit();
}

// Récupérer la liste des matériels utilisés sur ce chantier
$query_materiels = "SELECT lm.*, m.nom_materiel, m.description 
                    FROM liste_materiel lm
                    JOIN materiels m ON lm.materiel_id = m.id
                    WHERE lm.chantier_id = ?";
$stmt_materiels = $db->prepare($query_materiels);
$stmt_materiels->execute([$chantier_id]);
$materiels = $stmt_materiels->fetchAll();

require_once '../../includes/header.php';
?>

<link rel="stylesheet" href="../../assets/css/details.css">

<div class="chantier-details-container">
    <h2>Détails du chantier: <?= htmlspecialchars($chantier['nom_chantier']) ?></h2>
    
    <a href="liste.php" class="back-btn">
        <span>←</span> Retour à la liste
    </a>
    
    <div class="chantier-info">
        <div class="info-section">
            <h3>Informations générales</h3>
            <p><strong>Localisation:</strong> <?= htmlspecialchars($chantier['localisation']) ?></p>
            <p><strong>Date de début:</strong> <?= date('d/m/Y', strtotime($chantier['date_debut'])) ?></p>
            <p><strong>Date de fin:</strong> <?= $chantier['date_fin'] ? date('d/m/Y', strtotime($chantier['date_fin'])) : 'Non définie' ?></p>
            <p><strong>Chef de chantier:</strong> 
                <?= $chantier['chef_nom'] ? htmlspecialchars($chantier['chef_nom'] . ' (' . $chantier['chef_email'] . ')') : 'Non attribué' ?>
            </p>
        </div>
        
        <div class="info-section">
            <h3>Client</h3>
            <p><strong>Nom:</strong> <?= htmlspecialchars($chantier['client_nom']) ?></p>
            <p><strong>Email:</strong> <?= htmlspecialchars($chantier['client_email']) ?></p>
            <p><strong>Téléphone:</strong> <?= htmlspecialchars($chantier['client_telephone']) ?></p>
            <p><strong>Adresse:</strong> <?= htmlspecialchars($chantier['client_adresse']) ?></p>
        </div>
    </div>
    
    <div class="materiel-section">
        <h3>Matériels utilisés</h3>
        
        <?php if (isAdmin() || $_SESSION['user_id'] == $chantier['id_chef']): ?>
            <a href="ajouter_materiel.php?chantier_id=<?= $chantier_id ?>" class="add-btn">Ajouter du matériel</a>
        <?php endif; ?>
        
        <?php if (empty($materiels)): ?>
            <p class="no-data">Aucun matériel n'a été enregistré pour ce chantier.</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="materiel-table">
                    <thead>
                        <tr>
                            <th>Matériel</th>
                            <th>Description</th>
                            <th>Quantité utilisée</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($materiels as $materiel): ?>
                        <tr>
                            <td><?= htmlspecialchars($materiel['nom_materiel']) ?></td>
                            <td><?= htmlspecialchars($materiel['description']) ?></td>
                            <td><?= $materiel['quantite_utilisee'] ?></td>
                            <td>
                                <?php if (isAdmin() || $_SESSION['user_id'] == $chantier['id_chef']): ?>
                                    <a href="modifier_materiel.php?id=<?= $materiel['id'] ?>" class="btn edit-btn">Modifier</a>
                                    <a href="supprimer_materiel.php?id=<?= $materiel['id'] ?>" class="btn delete-btn" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce matériel ?')">Supprimer</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>