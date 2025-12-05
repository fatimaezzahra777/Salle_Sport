<?php

require __DIR__ . "/conexion.php";


$totalCours = $conn->query("SELECT COUNT(*) AS total FROM cours")->fetch_assoc()['total'];
$totalEquip = $conn->query("SELECT COUNT(*) AS total FROM `Équipement`")->fetch_assoc()['total'];

$editCours = null; 

if($_SERVER['REQUEST_METHOD']=='POST'){

    //ajouter un cours
    if(isset($_POST["form"]) && $_POST["form"]=="cours"){
        $nom = $_POST['nom'];
        $categorie = $_POST['categorie'];
        $date_c = $_POST['date_c'];
        $heur = $_POST['heur'];
        $duree = $_POST['duree'];
        $nombre_m = $_POST['nombre_m'];

        $sql = "INSERT INTO cours (nom, `categorie`, date_c, heur, duree, nombre_m)
                VALUES ('$nom', '$categorie', '$date_c', '$heur', '$duree', '$nombre_m')";


        if ($conn->query($sql) === TRUE) {
            header("Location: ".$_SERVER['PHP_SELF']); 
            exit;
        } else {
            echo "Erreur : " . $conn->error;
        }
    }

    if (isset($_POST["form"]) && $_POST["form"] === "editCours") {

        $id = intval($_POST["edit_id"]);
        $sql = "SELECT * FROM cours WHERE id_c = $id";
        $result = $conn->query($sql);

        if ($result && $result->num_rows > 0) {
            $editCours = $result->fetch_assoc();
        }
    }

    if (isset($_POST["form"]) && $_POST["form"] === "updateCours") {

        $id = intval($_POST["id_c"]);
        $nom = $_POST['nom'];
        $categorie = $_POST['categorie'];
        $date_c = $_POST['date_c'];
        $heur = $_POST['heur'];
        $duree = $_POST['duree'];
        $nombre_m = $_POST['nombre_m'];

        $sql = "UPDATE cours 
                SET nom='$nom', categorie='$categorie', date_c='$date_c',
                    heur='$heur', duree='$duree', nombre_m='$nombre_m'
                WHERE id_c = $id";
        $conn->query($sql);
    }


    //suppromer un cours
    if (isset($_POST["form"]) && $_POST["form"] == "deleteCours") {
    $id_c = $_POST["delete_id"];
    $stmt = $conn->prepare("DELETE FROM cours WHERE id_c = ?");
    $stmt->bind_param("i", $id_c);
        if ($stmt->execute()) {
            header("Location: " . $_SERVER['PHP_SELF']);
            exit;
        } else {
            echo "Erreur suppression cours : " . $stmt->error;
        }
        $stmt->close();
    }
  
    //ajouter un equipement
    if(isset($_POST["form"]) && $_POST["form"]=="Équipement"){
        $nom = $_POST['nom'];
        $type = $_POST['type'];
        $quantite_d = $_POST['quantite_d'];
        $etat = $_POST['etat'];

        $sql = "INSERT INTO `Équipement` (nom, type, quantite_d, etat)
                VALUES ('$nom', '$type', '$quantite_d', '$etat')";

        if ($conn->query($sql) === TRUE) {
            header("Location: ".$_SERVER['PHP_SELF']); 
            exit;
        } else {
            echo "Erreur : " . $conn->error;
        }
    }

    //supprimer un equipement
    if (isset($_POST["form"]) && $_POST["form"] == "deletEquip") {
    $id_é = $_POST["delete_id"];

    $sql = "DELETE FROM `Équipement` WHERE id_é = $id_é";

    if ($conn->query($sql) === TRUE) {
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    } else {
        echo "Erreur: " . $conn->error;
    }
}
}
    
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <title>Gestion Salle de Sport - Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <!-- Bootstrap CSS -->
    <link
            href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
            rel="stylesheet"
    />

    <!-- Icons (Bootstrap Icons) -->
    <link
            rel="stylesheet"
            href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"
    />

    <!-- Chart.js pour les graphiques -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

    <style>
        body {
            background-color: #f5f6fa;
        }
        .navbar-brand {
            font-weight: 700;
            letter-spacing: 0.5px;
        }
        .nav-link {
            cursor: pointer;
        }
        .section {
            display: none;
            padding-top: 2rem;
        }
        .section.active {
            display: block;
        }
        .card-kpi {
            box-shadow: 0 2px 6px rgba(0,0,0,0.05);
        }
        .table-actions i {
            cursor: pointer;
            margin-right: 8px;
        }
        footer {
            margin-top: 3rem;
            padding: 1.5rem 0;
            text-align: center;
            font-size: 0.9rem;
            color: #888;
        }
    </style>
</head>
<body>
<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">Salle de Sport</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="mainNavbar">
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                <li class="nav-item"><span class="nav-link active" data-section="dashboard">Dashboard</span></li>
                <li class="nav-item"><span class="nav-link" data-section="cours">Cours</span></li>
                <li class="nav-item"><span class="nav-link" data-section="equipements">Équipements</span></li>
                <li class="nav-item"><span class="nav-link" data-section="associations">Associations</span></li>
                <li class="nav-item"><span class="nav-link" data-section="auth">Auth</span></li>
            </ul>
        </div>
    </div>
</nav>

<main class="container my-4">
    <section id="dashboard" class="section active">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Dashboard</h2>
            <span class="badge text-bg-secondary">Vue globale</span>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card card-kpi">
                    <div class="card-body">
                        <h6 class="card-title text-muted">Nombre total de cours</h6>
                        <h3><?= $totalCours ?></h3>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card card-kpi">
                    <div class="card-body">
                        <h6 class="card-title text-muted">Nombre total d'équipements</h6>
                        <h3><?= $totalEquip ?></h3>
                    </div>
                </div>
            </div>

        </div>

        <div class="row g-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        Répartition des cours par catégorie
                    </div>
                    <div class="card-body">
                        <canvas id="chartCoursCategories"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        Répartition des équipements par type
                    </div>
                    <div class="card-body">
                        <canvas></canvas>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- COURS -->
    <section id="cours" class="section">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2>Gestion des cours</h2>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalCours" onclick="openAddCours()">
                <i class="bi bi-plus-lg"></i> Ajouter un cours
            </button>
        </div>

        <!-- Filtres simples (front uniquement) -->
        <div class="row g-3 mb-3">
            <div class="col-md-4">
                <input
                        type="text"
                        class="form-control"
                        id="filterCoursNom"
                        placeholder="Filtrer par nom..."
                        oninput="renderCoursTable()"
                />
            </div>
            <div class="col-md-4">
                <select class="form-select" id="filterCoursCategorie" onchange="renderCoursTable()">
                    <option value="">Toutes les catégories</option>
                    <option value="Yoga">Yoga</option>
                    <option value="Musculation">Musculation</option>
                    <option value="Cardio">Cardio</option>
                </select>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-striped align-middle">
                <thead>
                <tr>
                    <th>Nom</th>
                    <th>Catégorie</th>
                    <th>Date</th>
                    <th>Heure</th>
                    <th>Durée (min)</th>
                    <th>Max participants</th>
                    <th style="width: 120px;">Actions</th>
                </tr>
                </thead>
               <tbody>
                    <?php
                    $result = $conn->query("SELECT * FROM cours ORDER BY date_c ASC");

                    if ($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            echo "<tr>
                                    <td>{$row['nom']}</td>
                                    <td>{$row['categorie']}</td>
                                    <td>{$row['date_c']}</td>
                                    <td>{$row['heur']}</td>
                                    <td>{$row['duree']} min</td>
                                    <td>{$row['nombre_m']}</td>
                                    <td class='actions'>
                                    <form method='POST' style='display:inline;' action=''>
                                            <input type='hidden' name='edit_id' value='{$row['id_c']}'>
                                            <input type='hidden' value='editCours' name='form' />
                                            <a  data-bs-toggle='modal' data-bs-target='#modalCours' href='index.php?id={$row['id_c']}'>Modifier</a>
                                        </form>
                                        <form method='POST' style='display:inline;' action=''>
                                            <input type='hidden' name='delete_id' value='{$row['id_c']}'>
                                            <input type='hidden' name='form' value='deleteCours'>
                                            <button type='submit' class='btn btn-sm btn-danger'>Supprimer</button>
                                        </form>
                                    </td>
                                </tr>";
                        }
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </section>

    <!-- MODAL COURS -->
    <div class="modal fade" id="modalCours" tabindex="-1">
        <div class="modal-dialog">
            <form class="modal-content" id="formCours" method="Post" action="">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalCoursTitle">Ajouter un cours</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="form" value="<?= $editCours ? 'updateCours' : 'cours' ?>">
                    <input type="hidden" name="id_c" value="<?= $editCours['id_c'] ?? '' ?>">

                    <div class="mb-3">
                        <label class="form-label">Nom du cours *</label>
                        <input type="text" id="coursNom" name="nom" class="form-control" value="<?= $editCours['nom'] ?? '' ?>" required />
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Catégorie *</label>
                        <select id="coursCategorie" class="form-select" name="categorie" value="<?= $editCours['categorie'] ?? '' ?>"  required>
                            <option value="">Choisir...</option>
                            <option value="Yoga">Yoga</option>
                            <option value="Musculation">Musculation</option>
                            <option value="Cardio">Cardio</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Date *</label>
                        <input type="date" id="coursDate" class="form-control" name="date_c" value="<?= $editCours['date_c'] ?? '' ?>" required />
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Heure *</label>
                        <input type="time" id="coursHeure" class="form-control" name="heur" value="<?= $editCours['heur'] ?? '' ?>" required />
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Durée (minutes) *</label>
                        <input type="number" id="coursDuree" class="form-control" name="duree" value="<?= $editCours['duree'] ?? '' ?>" required />
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Nombre maximum de participants *</label>
                        <input type="number" id="coursMaxParticipants" class="form-control" name="nombre_m" min="1" value="<?= $editCours['nombre_m'] ?? '' ?>" required />
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary" value="<?= $editCours ? "Modifier" : "Ajouter" ?>">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
    

    <!-- EQUIPEMENTS -->
    <section id="equipements" class="section">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2>Gestion des équipements</h2>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalEquipement" onclick="openAddEquipement()">
                <i class="bi bi-plus-lg"></i> Ajouter un équipement
            </button>
        </div>

        <!-- Filtres -->
        <div class="row g-3 mb-3">
            <div class="col-md-4">
                <input
                        type="text"
                        class="form-control"
                        id="filterEquipNom"
                        placeholder="Filtrer par nom..."
                        oninput="renderEquipementsTable()"
                />
            </div>
            <div class="col-md-4">
                <select class="form-select" id="filterEquipType" onchange="renderEquipementsTable()">
                    <option value="">Tous les types</option>
                    <option value="Tapis de course">Tapis de course</option>
                    <option value="Haltères">Haltères</option>
                    <option value="Ballons">Ballons</option>
                </select>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-striped align-middle">
                <thead>
                <tr>
                    <th>Nom</th>
                    <th>Type</th>
                    <th>Quantité</th>
                    <th>État</th>
                    <th style="width: 120px;">Actions</th>
                </tr>
                </thead>
                <tbody>
                    <?php
                    $result = $conn->query("SELECT * FROM Équipement");

                    if ($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            echo "<tr>
                                    <td>{$row['nom']}</td>
                                    <td>{$row['type']}</td>
                                    <td>{$row['quantite_d']}</td>
                                    <td>{$row['etat']}</td>
                                    <td class='actions'>
                                        <button class='btn btn-sm btn-primary' data-bs-toggle='modal' data-bs-target='#modalEquipement' onclick=oppenEditEquipement(
                                                    '{$row['id_é']}',
                                                    '{$row['nom']}',
                                                    '{$row['type']}',
                                                    '{$row['quantite_d']}',
                                                    '{$row['etat']}'                                       
                        )>
                                        Modifier
                                        </button>
                                        <form method='POST' style='display:inline;'>
                                            <input type='hidden' name='delete_id' value='{$row['id_é']}'>
                                            <input type='hidden' name='form' value='deletEquip'>
                                            <button type='submit' class='btn btn-sm btn-danger'>Supprimer</button>
                                        </form>
                                    </td>
                                </tr>";
                        }
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </section>

    <!-- MODAL EQUIPEMENT -->
    <div class="modal fade" id="modalEquipement" tabindex="-1">
        <div class="modal-dialog">
            <form class="modal-content" id="formEquipement" method="Post" action="">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalEquipementTitle">Ajouter un équipement</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="form" value="<?= $editEquipement ? 'updateEquipement' : 'Equipement' ?>"/>
                    <input type="hidden" name="id_é" value="<?= $editEquipement['id_é'] ?? '' ?>">

                    <div class="mb-3">
                        <label class="form-label">Nom *</label>
                        <input type="text" name="nom" id="equipementNom" class="form-control" value="<?= $editEquipement['nom'] ?? '' ?>" required />
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Type *</label>
                        <select id="equipementType" name="type" class="form-select" value="<?= $editEquipement['type'] ?? '' ?>" required>
                            <option value="">Choisir...</option>
                            <option value="Tapis de course">Tapis de course</option>
                            <option value="Haltères">Haltères</option>
                            <option value="Ballons">Ballons</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Quantité disponible *</label>
                        <input type="number" id="equipementQuantite" name="quantite_d" class="form-control" min="0" value="<?= $editEquipement['quantite_d'] ?? '' ?>" required />
                    </div>

                    <div class="mb-3">
                        <label class="form-label">État *</label>
                        <select id="equipementEtat" name="etat" class="form-select" value="<?= $editEquipement['etat'] ?? '' ?>" required>
                            <option value="">Choisir...</option>
                            <option value="Bon">Bon</option>
                            <option value="Moyen">Moyen</option>
                            <option value="À remplacer">À remplacer</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary" value="<?= $editEquipement ? "Modifier" : "Ajouter" ?>">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>

    <!-- ASSOCIATIONS COURS / EQUIPEMENTS -->
    <section id="associations" class="section">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2>Association Cours / Équipements</h2>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-md-4">
                <label class="form-label">Cours</label>
                <select id="assocCours" class="form-select"></select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Équipement</label>
                <select id="assocEquipement" class="form-select"></select>
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <button class="btn btn-primary me-2" onclick="addAssociation()">
                    Associer
                </button>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-striped align-middle">
                <thead>
                <tr>
                    <th>Cours</th>
                    <th>Équipement</th>
                    <th style="width: 120px;">Actions</th>
                </tr>
                </thead>
                <tbody id="associationsTableBody"></tbody>
            </table>
        </div>
    </section>

    <!-- AUTH (Login / Register simple front) -->
    <section id="auth" class="section">
        <div class="row">
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header">Login</div>
                    <div class="card-body">
                        <form onsubmit="event.preventDefault(); alert('Login côté front uniquement. À connecter au backend plus tard.');">
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" required />
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Mot de passe</label>
                                <input type="password" class="form-control" required />
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Se connecter</button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header">Register</div>
                    <div class="card-body">
                        <form onsubmit="event.preventDefault(); alert('Inscription côté front uniquement. À connecter au backend plus tard.');">
                            <div class="mb-3">
                                <label class="form-label">Nom complet</label>
                                <input type="text" class="form-control" required />
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" required />
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Mot de passe</label>
                                <input type="password" class="form-control" required />
                            </div>
                            <button type="submit" class="btn btn-outline-primary w-100">Créer un compte</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </section>

    <footer>
        Projet Salle de Sport.
    </footer>
</main>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.addEventListener("DOMContentLoaded", () => {
    // ---------------------------
    // Navigation entre sections
    // ---------------------------
    const sections = document.querySelectorAll(".section");
    const navLinks = document.querySelectorAll(".nav-link");

    navLinks.forEach((link) => {
        link.addEventListener("click", () => {
            const target = link.getAttribute("data-section");

            // Activer le lien cliqué
            navLinks.forEach((lnk) => lnk.classList.remove("active"));
            link.classList.add("active");

            // Afficher la section correspondante
            sections.forEach((sec) => sec.classList.remove("active"));
            document.getElementById(target).classList.add("active");
        });
    });
    var modal = new bootstrap.Modal(document.getElementById('modalCours'));
    modal.show();
    // ---------------------------
    // Gestion des modals
    // ---------------------------
    // Fonction générique pour ouvrir un modal Bootstrap
    function openModal(modalId) {
        const modalEl = document.getElementById(modalId);
        const modal = new bootstrap.Modal(modalEl);
        modal.show();
    }

    // Exemple : ouvrir modals avec des boutons
    const addCoursBtn = document.getElementById("btnAddCours");
    if (addCoursBtn) {
        addCoursBtn.addEventListener("click", () => openModal("modalCours"));
    }

    const addEquipBtn = document.getElementById("btnAddEquip");
    if (addEquipBtn) {
        addEquipBtn.addEventListener("click", () => openModal("modalEquipement"));
    }
});
</script>

</body>
</html>



