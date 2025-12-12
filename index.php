<?php
session_start();
require_once __DIR__ . "/connexion.php";


if(isset($_POST['login'])){
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM personne WHERE username=? LIMIT 1");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result && $result->num_rows === 1){
        $user = $result->fetch_assoc();
        if(password_verify($password, $user['password'])){
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            header("Location: ".$_SERVER['PHP_SELF']);
            exit;
        } else {
            $error = "Mot de passe incorrect";
        }
    } else {
        $error = "Utilisateur non trouvé";
    }
}

if(isset($_GET['logout'])){
    session_unset();
    session_destroy();
    header("Location: ".$_SERVER['PHP_SELF']);
    exit;
}

if (isset($_POST['signup'])) {

    $username = trim($_POST['new_username']);
    $password = $_POST['new_password'];
    $confirm  = $_POST['confirm_password'];

    // Vérifications
    if ($password !== $confirm) {
        $error = "Les mots de passe ne correspondent pas";
    } else {

        // Vérifier si l'utilisateur existe déjà
        $check = $conn->prepare("SELECT id FROM personne WHERE username = ?");
        $check->bind_param("s", $username);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $error = "Nom d'utilisateur déjà utilisé";
        } else {
            // Hash du mot de passe
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Insertion
            $stmt = $conn->prepare("INSERT INTO personne (username, password) VALUES (?, ?)");
            $stmt->bind_param("ss", $username, $hashedPassword);

            if ($stmt->execute()) {
                $success = "Compte créé avec succès. Vous pouvez vous connecter.";
            } else {
                $error = "Erreur lors de la création du compte";
            }
        }
    }
}


if(!isset($_SESSION['user_id'])) {
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Login - Salle de sport</title>
    <style>
    body{
        margin:0;
        min-height:100vh;
        display:flex;
        align-items:center;
        justify-content:center;
        background:linear-gradient(135deg,#0ea5a4,#7c3aed);
        font-family: Arial, Helvetica, sans-serif;
    }

    .auth-container{
        width:100%;
        max-width:420px;
        background:#fff;
        border-radius:12px;
        padding:30px;
        box-shadow:0 20px 40px rgba(0,0,0,.15);
    }

    .auth-container h2{
        text-align:center;
        margin-bottom:15px;
        color:#111827;
    }

    .auth-container hr{
        margin:25px 0;
        border:none;
        border-top:1px solid #e5e7eb;
    }

    .field{
        display:flex;
        flex-direction:column;
        margin-bottom:15px;
    }

    .field label{
        font-size:14px;
        margin-bottom:5px;
        color:#374151;
    }

    .field input{
        padding:10px 12px;
        border-radius:8px;
        border:1px solid #d1d5db;
        font-size:14px;
        outline:none;
    }

    .field input:focus{
        border-color:#0ea5a4;
        box-shadow:0 0 0 2px rgba(14,165,164,.2);
    }

    .btn-auth{
        width:100%;
        padding:12px;
        border:none;
        border-radius:8px;
        background:#111827;
        color:#fff;
        font-size:15px;
        cursor:pointer;
        transition:.2s;
    }

    .btn-auth:hover{
        background:#0ea5a4;
    }

    .message-error{
        background:#fee2e2;
        color:#991b1b;
        padding:10px;
        border-radius:8px;
        font-size:14px;
        margin-bottom:15px;
        text-align:center;
    }

    .message-success{
        background:#dcfce7;
        color:#166534;
        padding:10px;
        border-radius:8px;
        font-size:14px;
        margin-bottom:15px;
        text-align:center;
    }
</style>

</head>
<body>

<div class="auth-container">

    <?php if(isset($error)): ?>
        <div class="message-error"><?= $error ?></div>
    <?php endif; ?>

    <?php if(isset($success)): ?>
        <div class="message-success"><?= $success ?></div>
    <?php endif; ?>

    <h2>Créer un compte</h2>

    <form method="POST">
        <div class="field">
            <label>Nom d'utilisateur</label>
            <input type="text" name="new_username" required>
        </div>

        <div class="field">
            <label>Mot de passe</label>
            <input type="password" name="new_password" required>
        </div>

        <div class="field">
            <label>Confirmer le mot de passe</label>
            <input type="password" name="confirm_password" required>
        </div>

        <button type="submit" name="signup" class="btn-auth">
            S'inscrire
        </button>
    </form>

    <hr>

    <h2>Connexion</h2>

    <form method="POST">
        <div class="field">
            <label>Nom d'utilisateur</label>
            <input type="text" name="username" required>
        </div>

        <div class="field">
            <label>Mot de passe</label>
            <input type="password" name="password" required>
        </div>

        <button type="submit" name="login" class="btn-auth">
            Se connecter
        </button>
    </form>

</div>

</body>

</html>
<?php
exit;
}


$totalCours = $conn->query("SELECT COUNT(*) AS total FROM cours")->fetch_assoc()['total'];
$totalEquip = $conn->query("SELECT COUNT(*) AS total FROM `Équipement`")->fetch_assoc()['total'];

$editCours = null; 
$editEquipement = null;

if($_SERVER['REQUEST_METHOD']=='POST'){

    //ajouter un cours
    if(isset($_POST["form"]) && $_POST["form"]=="cours"){
        $nom = $_POST['nom'];
        $categorie = $_POST['categorie'];
        $date_c = $_POST['date_c'];
        $heur = $_POST['heur'];
        $duree = $_POST['duree'];
        $nombre_m = $_POST['nombre_m'];

        $sql = "INSERT INTO cours (nom, categorie, date_c, heur, duree, nombre_m)
                VALUES ('$nom', '$categorie', '$date_c', '$heur', '$duree', '$nombre_m')";


        $conn->query($sql);
        header("Location: ".$_SERVER['PHP_SELF']);
        exit;
    }

    //modifier un cours
    if (isset($_POST["form"]) && $_POST["form"] === "updateCours") {
        $id = intval($_POST["id_c"]);
        $nom = $_POST["nom"];
        $categorie = $_POST["categorie"];
        $date_c = $_POST["date_c"];
        $heur = $_POST["heur"];
        $duree = $_POST["duree"];
        $nombre_m = $_POST["nombre_m"];

        $sql = "UPDATE cours SET 
                nom='$nom',
                categorie='$categorie',
                date_c='$date_c',
                heur='$heur',
                duree='$duree',
                nombre_m='$nombre_m'
                WHERE id_c = $id";

        $conn->query($sql);
        header("Location: ".$_SERVER['PHP_SELF']);
        exit;

    }

    /* Récupérer un cours pour édition */
    if (isset($_POST["form"]) && $_POST["form"] === "editCours") {
        $id = intval($_POST["edit_id"]);
        $result = $conn->query("SELECT * FROM cours WHERE id_c = $id");

        if ($result && $result->num_rows > 0) {
            $editCours = $result->fetch_assoc();
        }
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

    //modifier un equipement
    if (isset($_POST["form"]) && $_POST["form"] === "editEquipement") {
        $id = intval($_POST["edit_id"]);
        $result = $conn->query("SELECT * FROM Équipement WHERE id_é = $id");

        if ($result && $result->num_rows > 0) {
            $editEquipement = $result->fetch_assoc();
        }
    }

    if (isset($_POST["form"]) && $_POST["form"] === "updateEquipement") {
        $id = intval($_POST["id_é"]);
        $nom = $_POST['nom'];
        $type = $_POST['type'];
        $quantite_d = $_POST['quantite_d'];
        $etat = $_POST['etat'];

        $sql = "UPDATE Équipement
                SET nom='$nom', type='$type', quantite_d='$quantite_d', etat='$etat'
                WHERE id_é = $id";
        $conn->query($sql);
        header("Location: ".$_SERVER['PHP_SELF']);
        exit;
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

<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Dashboard - Salle de sport</title>
  <style>
    /* Simple, modern CSS template - replace variables as needed */
    :root{
      --bg:#f6f7fb;
      --card:#ffffff;
      --muted:#6b7280;
      --accent:#0f172a;
      --accent-2:#0ea5a4;
      --radius:12px;
      --shadow: 0 6px 18px rgba(12, 12, 12, 0.08);
      font-family: Inter, ui-sans-serif, system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial;
    }
    body{margin:0;background:var(--bg);color:var(--accent);}
    .container{max-width:1100px;margin:28px auto;padding:18px;}
    header{display:flex;align-items:center;justify-content:space-between;margin-bottom:18px}
    .brand{display:flex;gap:12px;align-items:center}
    .logo{width:46px;height:46px;border-radius:10px;background:linear-gradient(135deg,var(--accent-2),#7c3aed);display:flex;align-items:center;justify-content:center;color:white;font-weight:700}
    h1{font-size:20px;margin:0}
    .grid{display:grid;grid-template-columns:repeat(3,1fr);gap:16px;margin-bottom:18px}
    .card{background:var(--card);border-radius:var(--radius);padding:16px;box-shadow:var(--shadow)}
    .card h3{margin:0 0 8px 0;font-size:14px;color:var(--muted)}
    .big-num{font-size:28px;font-weight:700}

    /* lists and tables */
    .table{width:100%;border-collapse:collapse}
    .table th, .table td{padding:10px;border-bottom:1px solid #eef2f6;text-align:left}
    .controls{display:flex;gap:8px;flex-wrap:wrap}
    .btn{display:inline-block;padding:8px 12px;border-radius:8px;background:#111827;color:#fff;text-decoration:none;font-size:13px}
    .btn.secondary{background:#e5e7eb;color:#111827}

    /* Responsive */
    @media (max-width:900px){
      .grid{grid-template-columns:repeat(1,1fr)}
    }

    /* Forms */
    form .row{display:flex;gap:8px;margin-bottom:8px}
    .field{flex:1;display:flex;flex-direction:column}
    .field label{font-size:13px;color:var(--muted);margin-bottom:6px}
    .field input,.field select,.field textarea{padding:8px;border:1px solid #e6eef6;border-radius:8px}

    footer{margin-top:20px;text-align:center;color:var(--muted);font-size:13px}
  </style>
</head>
<body>
  <div class="container">
    <header>
      <div class="brand">
        <div class="logo">SS</div>
        <div>
          <h1>Plateforme - Salle de sport</h1>
          <div style="color:var(--muted);font-size:13px">Dashboard de gestion des cours & équipements</div>
        </div>
      </div>
      <div class="controls">
        <a class="btn" href="?logout=1">Se déconnecter</a>
        <a class="btn secondary" href="#forms">Ajouter</a>
      </div>
    </header>

    <!-- Dashboard cards -->
    <div class="grid">
      <div class="card">
        <h3>Total de cours</h3>
        <div class="big-num" id="total-cours"><?= $totalCours ?></div>
      </div>
      <div class="card">
        <h3>Total d'équipements</h3>
        <div class="big-num" id="total-equipements"><?= $totalEquip ?></div>
      </div>
    </div>

    <!-- Charts -->
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:18px">
      <div class="card">
        <canvas id="chartCours" width="300" height="300"></canvas>
      </div>
      <div class="card">

        <canvas id="chartEquip" width="300" height="300"></canvas>
      </div>
    </div>

    <!-- Tables: Courses -->
    <div class="card" style="margin-bottom:12px">
      <h3>Liste des cours</h3>
      <table class="table" id="table-cours">
        <thead>
          <tr><th>Nom</th><th>Catégorie</th><th>Date</th><th>Heure</th><th>Durée (min)</th><th>Max</th><th>Actions</th></tr>
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
                                            <button type='submit' class='btn btn-sm btn-primary'>Modifier</button>
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

    <!-- Tables: Equipements -->
    <div class="card" style="margin-bottom:12px">
      <h3>Liste des équipements</h3>
      <table class="table" id="table-equipements">
        <thead>
          <tr><th>Nom</th><th>Type</th><th>Quantité</th><th>État</th><th>Actions</th></tr>
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
                                        <form method='POST' style='display:inline;' action=''>
                                            <input type='hidden' name='edit_id' value='{$row['id_é']}'>
                                            <input type='hidden' value='editEquipement' name='form' />
                                            <button type='submit' class='btn btn-sm btn-primary'>Modifier</button>
                                        </form>
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

    <!-- Forms: Add / Edit (point to PHP endpoints) -->
    <div id="forms" class="card">
      <h4>Ajouter un cours</h4>
      <form class="modal-content" id="formCours" action="" method="Post">
        <input type="hidden" name="form" value="<?= $editCours ? 'updateCours' : 'cours' ?>">
        <input type="hidden" name="id_c" value="<?= $editCours['id_c'] ?? '' ?>">
        <div class="row">
          <div class="field"><label>Nom*</label><input type="text" id="coursNom" name="nom" class="form-control" value="<?= $editCours['nom'] ?? '' ?>"  required></div>
          <div class="field"><label>Catégorie*</label><select name="categorie" value="<?= $editCours['categorie'] ?? '' ?>" required>
            <option value="Yoga">Yoga</option>
            <option value="Cardio">Cardio</option>
            <option value="Musculation">Musculation</option>
          </select></div>
        </div>
        <div class="row">
          <div class="field"><label>Date*</label><input type="date" name="date_c" value="<?= $editCours['date_c'] ?? '' ?>" required></div>
          <div class="field"><label>Heure*</label><input type="time" name="heur" value="<?= $editCours['heur'] ?? '' ?>"  required></div>
        </div>
        <div class="row">
          <div class="field"><label>Durée (minutes)*</label><input type="number" name="duree" value="<?= $editCours['duree'] ?? '' ?>"  required></div>
          <div class="field"><label>Max participants*</label><input type="number" name="nombre_m" min="1" value="<?= $editCours['nombre_m'] ?? '' ?>" required></div>
        </div>
        <div style="margin-top:8px"><button class="btn" type="submit" value="<?= $editCours ? "Modifier" : "Ajouter" ?>" >Enregistrer</button></div>
      </form>

      <hr style="margin:12px 0">

      <h4>Ajouter un équipement</h4>
      <form action="" method="post">
        <input type="hidden" name="form" value="<?= $editEquipement ? 'updateEquipement' : 'Équipement' ?>"/>
        <input type="hidden" name="id_é" value="<?= $editEquipement['id_é'] ?? '' ?>">
        <div class="row">
          <div class="field"><label>Nom*</label><input name="nom" value="<?= $editEquipement['nom'] ?? '' ?>" required></div>
          <div class="field"><label>Type*</label><input name="type" value="<?= $editEquipement['type'] ?? '' ?>" required></div>
        </div>
        <div class="row">
          <div class="field"><label>Quantité*</label><input type="number" name="quantite_d" class="form-control" min="0" value="<?= $editEquipement['quantite_d'] ?? '' ?>" required></div>
          <div class="field"><label>État*</label><select name="etat" value="<?= $editEquipement['etat'] ?? '' ?>"  required>
            <option value="bon">Bon</option>
            <option value="moyen">Moyen</option>
            <option value="a_remplacer">À remplacer</option>
          </select></div>
        </div>
        <div style="margin-top:8px"><button class="btn" type="submit" value="<?= $editEquipement ? "Modifier" : "Ajouter" ?>">Enregistrer</button></div>
        </form>

        <?php if(isset($_GET['associer'])): ?>
        <h3>Associer un équipement au cours #<?= $_GET['associer'] ?></h3>

        <form method="POST">
            <input type="hidden" name="id_c" value="<?= $_GET['associer'] ?>">

            <select name="id_é" required>
                <?php
                $eq = $conn->query("SELECT * FROM Équipement");
                while($e = $eq->fetch_assoc()){
                    echo "<option value='{$e['id_é']}'>{$e['nom']}</option>";
                }
                ?>
            </select>

            <button class="btn" type="submit" name="add_equip">Associer</button>
        </form>
        <?php endif; ?>




    </div>


    <footer>
      Template fournie — intégrez vos scripts PHP (connexion, CRUD) et exécutez `database.sql` pour créer les tables.
    </footer>
  </div>
<!-- CHARTS JS UNIQUE -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    // Totaux venant de PHP
    const totalCours = <?= $totalCours ?>;
    const totalEquip = <?= $totalEquip ?>;

    console.log("Total Cours =", totalCours);
    console.log("Total Equipements =", totalEquip);

    // Chart cours
    new Chart(document.getElementById('chartCours').getContext('2d'), {
        type: 'doughnut',
        data: {
            labels: ['Cours'],
            datasets: [{
                data: [totalCours],
                backgroundColor: ['#0EA5A4'],
                borderWidth: 0
            }]
        },
        options: {
            plugins: {
                legend: { display: false },
                title: {
                    display: true,
                    text: 'Total des Cours'
                }
            },
            cutout: '60%'
        }
    });

    // Chart équipements
    new Chart(document.getElementById('chartEquip').getContext('2d'), {
        type: 'doughnut',
        data: {
            labels: ['Équipements'],
            datasets: [{
                data: [totalEquip],
                backgroundColor: ['#7C3AED'],
                borderWidth: 0
            }]
        },
        options: {
            plugins: {
                legend: { display: false },
                title: {
                    display: true,
                    text: 'Total des Équipements'
                }
            },
            cutout: '60%'
        }
    });
</script>

</body>
</html>
