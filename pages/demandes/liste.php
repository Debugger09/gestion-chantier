<?php
require_once '../../includes/config.php';
require_once '../../includes/auth.php';
requireAuth();

// Traitement des actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    
    $id = intval($_POST['id']);
    $action = $_POST['action'];
    $response = ['status' => 'error', 'message' => 'Action non autorisée'];

    try {
        // Récupérer la demande
        $stmt = $db->prepare("SELECT user_id, statut FROM demandes WHERE id = ?");
        $stmt->execute([$id]);
        $demande = $stmt->fetch();

        if (!$demande) {
            echo json_encode(['status' => 'error', 'message' => 'Demande introuvable']);
            exit;
        }

        // Vérification des droits
        $authorized = false;
        if ($action === 'supprimer') {
            $authorized = isAdmin() || (isChefChantier() && $demande['user_id'] == $_SESSION['user_id'] && $demande['statut'] == 'en_attente');
        } elseif (in_array($action, ['valider', 'refuser'])) {
            $authorized = isAdmin();
        }

        if (!$authorized) {
            echo json_encode(['status' => 'error', 'message' => 'Droits insuffisants']);
            exit;
        }

        // Exécution de l'action
        if ($action === 'supprimer') {
            $stmt = $db->prepare("DELETE FROM demandes WHERE id = ?");
            $stmt->execute([$id]);
            $response = ['status' => 'success', 'action' => 'supprimer'];
        } elseif ($action === 'valider' || $action === 'refuser') {
            $new_status = ($action === 'valider') ? 'validee' : 'refusee';
            $stmt = $db->prepare("
                UPDATE demandes 
                SET statut = ?, date_validation = NOW(), validateur_id = ?
                WHERE id = ? AND statut = 'en_attente'
            ");
            $stmt->execute([$new_status, $_SESSION['user_id'], $id]);
            $response = [
                'status' => 'success', 
                'action' => $action,
                'new_status' => ucfirst($new_status),
                'new_class' => $new_status
            ];
        }
    } catch (PDOException $e) {
        $response['message'] = 'Erreur technique';
        error_log("Erreur demande: " . $e->getMessage());
    }

    echo json_encode($response);
    exit;
}

// Récupération des demandes
if (isAdmin()) {
    $query = "
        SELECT d.*, m.nom_materiel, c.nom_chantier, u.nom AS demandeur 
        FROM demandes d
        JOIN materiels m ON d.materiel_id = m.id
        JOIN chantiers c ON d.chantier_id = c.id
        JOIN users u ON d.user_id = u.id
        ORDER BY d.date_demande DESC
    ";
    $stmt = $db->query($query);
} else {
    $query = "
        SELECT d.*, m.nom_materiel, c.nom_chantier 
        FROM demandes d
        JOIN materiels m ON d.materiel_id = m.id
        JOIN chantiers c ON d.chantier_id = c.id
        WHERE d.user_id = ?
        ORDER BY d.date_demande DESC
    ";
    $stmt = $db->prepare($query);
    $stmt->execute([$_SESSION['user_id']]);
}

$demandes = $stmt->fetchAll();

require_once '../../includes/header.php';
?>

<link rel="stylesheet" href="../../assets/css/liste.css">
<link rel="stylesheet" href="../../assets/css/style.css">

<div class="user-management-container">
    <h2>Gestion des demandes</h2>
    
    <?php if (isChefChantier()): ?>
        <a href="ajouter.php" class="add-user-btn">+ Nouvelle demande</a>
    <?php endif; ?>

    <?php if (isset($_GET['success'])): ?>
        <div class="alert success"><?= htmlspecialchars($_GET['success']) ?></div>
    <?php endif; ?>

    <div class="table-responsive">
        <table class="users-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Matériel</th>
                    <th>Chantier</th>
                    <th>Quantité</th>
                    <?= isAdmin() ? '<th>Demandeur</th>' : '' ?>
                    <th>Date</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($demandes as $demande): ?>
                <tr id="demande-<?= $demande['id'] ?>">
                    <td><?= $demande['id'] ?></td>
                    <td><?= htmlspecialchars($demande['nom_materiel']) ?></td>
                    <td><?= htmlspecialchars($demande['nom_chantier']) ?></td>
                    <td><?= $demande['quantite_demandee'] ?></td>
                    <?= isAdmin() ? '<td>'.htmlspecialchars($demande['demandeur'] ?? '').'</td>' : '' ?>
                    <td><?= date('d/m/Y H:i', strtotime($demande['date_demande'])) ?></td>
                    <td>
                        <span class="status-badge <?= $demande['statut'] ?>">
                            <?= ucfirst(str_replace('_', ' ', $demande['statut'])) ?>
                        </span>
                    </td>
                    <td>
                        <div class="action-buttons">
                            <?php if (isAdmin() && $demande['statut'] === 'en_attente'): ?>
                                <button class="btn validate-btn" 
                                        onclick="traiterDemande(<?= $demande['id'] ?>, 'valider')">
                                    Valider
                                </button>
                                <button class="btn refuse-btn" 
                                        onclick="traiterDemande(<?= $demande['id'] ?>, 'refuser')">
                                    Refuser
                                </button>
                            <?php endif; ?>
                            
                            <?php if ((isChefChantier() && $demande['user_id'] == $_SESSION['user_id'] && $demande['statut'] == 'en_attente') || isAdmin()): ?>
                                <button class="btn delete-btn" 
                                        onclick="traiterDemande(<?= $demande['id'] ?>, 'supprimer')">
                                    Supprimer
                                </button>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
async function traiterDemande(id, action) {
    if (!confirm(`Voulez-vous vraiment ${action} cette demande ?`)) return;
    
    try {
        const response = await fetch('liste.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `action=${action}&id=${id}`
        });
        
        const result = await response.json();
        
        if (result.status === 'success') {
            if (action === 'supprimer') {
                // Supprimer la ligne de la demande
                const row = document.querySelector(`#demande-${id}`);
                if (row) row.remove();
                alert(`Demande supprimée avec succès`);
            } else {
                // Mise à jour du statut
                const badge = document.querySelector(`#demande-${id} .status-badge`);
                badge.textContent = result.new_status;
                badge.className = `status-badge ${result.new_class}`;
                
                // Suppression des boutons Valider/Refuser
                document.querySelectorAll(`#demande-${id} .validate-btn, #demande-${id} .refuse-btn`).forEach(btn => {
                    btn.remove();
                });
                alert(`Demande ${action}ée avec succès`);
            }
        } else {
            alert(result.message || 'Erreur lors du traitement');
        }
    } catch (error) {
        alert('Erreur de connexion');
        console.error(error);
    }
}
</script>

<?php require_once '../../includes/footer.php'; ?>