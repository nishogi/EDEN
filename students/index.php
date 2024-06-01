<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EDEN - Mes VMs</title>
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="assets/favicon.ico" />
    <!-- Core theme CSS (includes Bootstrap) -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/5.2.3/css/bootstrap.min.css" rel="stylesheet" />
    <link href="../css/styles.css" rel="stylesheet" />
    <script>
        function confirmStartVM(name) {
            var confirmation = confirm("Souhaitez-vous vraiment allumer la VM : " + name + " ?");
            if (confirmation) {
                // Envoi d'une requête AJAX pour démarrer la VM
                var xhr = new XMLHttpRequest();
                xhr.open("GET", "ajax_functions.php?action=startVM&name=" + encodeURIComponent(name), true);
                xhr.send();

                // Afficher un message "Veuillez patienter" pendant 2 secondes
                alert("Après avoir appuyer sur ok, veuillez patienter 2 secondes jusqu'au rechargement de la page, la VM est en cours de démarrage.");

                // Lancer le rechargement de la page après 2 secondes
                setTimeout(function () {
                    location.reload();
                }, 2000); // Temps d'attente en millisecondes (ici 2 secondes)
            }
        }

        function confirmStopVM(name) {
            var confirmation = confirm("Souhaitez-vous vraiment arrêter la VM : " + name + " ?");
            if (confirmation) {
                // Envoi d'une requête AJAX pour arrêter la VM
                var xhr = new XMLHttpRequest();
                xhr.open("GET", "ajax_functions.php?action=stopVM&name=" + encodeURIComponent(name), true);
                xhr.send();

                // Afficher un message "Veuillez patienter" pendant 2 secondes
                alert("Après avoir appuyer sur ok, veuillez patienter 2 secondes jusqu'au rechargement de la page, la VM est en cours d'arrêt.");

                // Lancer le rechargement de la page après 2 secondes
                setTimeout(function () {
                    location.reload();
                }, 2000); // Temps d'attente en millisecondes (ici 2 secondes)
            }
        }

        function confirmDeleteVM(name) {
            var confirmation = confirm("Souhaitez-vous vraiment supprimer la VM : " + name + " ?");
            if (confirmation) {
                // Envoi d'une requête AJAX pour arrêter la VM
                var xhr = new XMLHttpRequest();
                xhr.open("GET", "students_functions.php?action=deleteVM&name=" + encodeURIComponent(name), true);
                xhr.send();

                // Afficher un message "Veuillez patienter" pendant 2 secondes
                alert("Après avoir appuyer sur ok, veuillez patienter 2 secondes jusqu'au rechargement de la page, la VM est en cours de suppression.");

                // Lancer le rechargement de la page après 2 secondes
                setTimeout(function () {
                    location.reload();
                }, 2000); // Temps d'attente en millisecondes (ici 2 secondes)
            }
        }

        function confirmCreateVM(vmName) {
            var sshPublicKey = prompt("Veuillez nous transmettre votre clé ssh publique afin qu'on puisse vous transmettre vos identifiants :");
            if (sshPublicKey != null) {
                var xhr = new XMLHttpRequest();
                xhr.open("POST", "ajax_functions.php", true);
                xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                xhr.onreadystatechange = function() {
                    if (this.readyState === XMLHttpRequest.DONE && this.status === 200) {
                        // Il faudra modifier ici pour par la suite envoyer les identifiants de connexion à la VM 
                        // et d'ailleurs les afficher autre part pour les avoir à chaque fois 
                        // par exemple en dessous de statut avoir ssh login
                        alert("VM créée avec succès !");
                        alert("Réponse du serveur : " + this.responseText); // Affiche la réponse dans une alerte
                        document.getElementById('debugInfo').innerText = this.responseText; // Ou l'affiche dans le DOM
                        location.reload();
                    }
                };
                xhr.send("action=createVM&name=" + encodeURIComponent(vmName) + "&ssh_public_key=" + encodeURIComponent(sshPublicKey));
            }
        }
    </script>
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        .sidebar-heading {
            font-weight: bold;
        }

        .btn {
            margin: 0.2rem;
        }

        ul {
            list-style-type: none;
            padding: 0;
        }

        li {
            padding: 0.5rem;
            margin: 0 0.5rem 0.5rem 0.5rem;
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 0.5rem;
        }
    </style>
</head>

<body>
    <div class="d-flex" id="wrapper">
        <!-- Sidebar -->
        <div class="border-end bg-white" id="sidebar-wrapper">
            <div class="sidebar-heading border-bottom bg-light text-center">
                <?php
                echo ($_SERVER['REMOTE_USER'] ?? '') . PHP_EOL;
                ?>
            </div>
            <div class="list-group list-group-flush">
                <a class="list-group-item list-group-item-action list-group-item-light p-3" href="">Documentation</a>
                <a class="list-group-item list-group-item-action list-group-item-light p-3" href="../index.php">Home</a>
                <a class="list-group-item list-group-item-action list-group-item-light p-3" href="/teachers/index.php">Teachers pages</a>
                <a class="list-group-item list-group-item-action list-group-item-light p-3" href="/students/index.php">Students pages</a>
                <a class="list-group-item list-group-item-action list-group-item-light p-3" href="/admin/index.php">Admin pages</a>
                <a class="list-group-item list-group-item-action list-group-item-light p-3" href="/fbertin/index.php">Perso fbertin (tests CAS)</a>
                <a class="list-group-item list-group-item-action list-group-item-light p-3" href="">
                    <div class="text-center"><b>2024</b></div>
                </a>
            </div>
        </div>
        <!-- Page content wrapper -->
        <div id="page-content-wrapper">
            <nav class="navbar navbar-expand-lg navbar-light bg-light border-bottom">
                <div class="container-fluid">
                    <button class="btn btn-primary" id="sidebarToggle">&lt;&lt; Menu &gt;&gt;</button>
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
                        aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <ul class="navbar-nav ms-auto mt-2 mt-lg-0">
                        <li class="nav-item active">
                            <a class="nav-link" href="https://github.com/Nishogi/EDEN" target="_blank">lien vers le code source</a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown"
                                aria-haspopup="true" aria-expanded="false">Team</a>
                            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                <a class="dropdown-item" href="mailto:franz.bertin@imtbs-tsp.eu">franz.bertin@imtbs-tsp.eu</a>
                                <a class="dropdown-item" href="mailto:christophe.gaboret@imtbs-tsp.eu">christophe.gaboret@imtbs-tsp.eu</a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="mailto:tom.burellier@telecom-sudparis.eu">tom.burellier@telecom-sudparis.eu</a>
                                <a class="dropdown-item" href="mailto:nicolas.rocq@telecom-sudparis.eu">nicolas.rocq@telecom-sudparis.eu</a>
                                <a class="dropdown-item" href="mailto:mathis.williot@telecom-sudparis.eu">mathis.williot@telecom-sudparis.eu</a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="mailto:olivier.berger@telecom-sudparis.eu">olivier.berger@telecom-sudparis.eu</a>
                                <a class="dropdown-item" href="mailto:arthur.jovart@telecom-sudparis.eu">arthur.jovart@telecom-sudparis.eu</a>
                            </div>
                        </li>
                    </ul>
                </div>
            </nav>
                
            <!-- Page content -->
            <div class="container-fluid">
                <h1 class="mt-4">Bienvenue sur la page de gestion de vos VM, <?php echo $_SERVER['REMOTE_USER']; ?></h1>
                <hr class="my-4">

                <p><b>Mes VM :</b></p>
                <ul class="list-group">
                    <?php
                    // For debug
                    error_reporting(E_ALL);
                    ini_set('display_errors', 1);

                    include 'students_functions.php';

                    // Format des noms de VM recherchés
                    $vmNamePattern = strval($_SERVER['REMOTE_USER']) . '-1';

                    $userVMs = getVMNames($vmNamePattern);

                    foreach ($userVMs as $vmName) {
                        $VMId = getVMId($vmName);
                        $status = checkVMStatus($VMId);
                        echo "<li class='list-group-item'>";
                        echo "<b>$vmName</b>";
                        echo "<p>Statut : $status</p>";
                        echo "<button class='btn btn-primary boutonVM'>Accéder</button>";
                        echo "<button class='btn btn-success boutonVM' onclick=\"confirmStartVM('$vmName')\">Allumer</button>";
                        echo "<button class='btn btn-warning boutonVM' onclick=\"confirmStopVM('$vmName')\">Éteindre</button>";
                        echo "<button class='btn btn-danger boutonVM' onclick=\"confirmDeleteVM('$vmName')\">Supprimer</button>";
                        echo "</li>";
                    }
                    ?>
                </ul>

                <hr class="my-4">

                <p><b>VM disponibles :</b></p>
                <ul class="list-group">
                    <?php
                    // For debug
                    error_reporting(E_ALL);
                    ini_set('display_errors', 1);

                    // Chemin vers le fichier de conf temporaire, à l'avenir cela sera changé
                    $cheminFichier = 'cours.conf';

                    // Lire les lignes du fichier et les stocker dans un tableau
                    $lignes = file($cheminFichier);

                    // Fonction servant juste pour la suite pour l'affichage de la barre dans les VM à créer
                    $taille = 0;
                    foreach ($lignes as $ligne) {
                        $VMname = substr($ligne, 0, -1) . "-" . $_SERVER['REMOTE_USER'] . "-1";
                        if (existingVM($VMname) == false) {
                            $taille = $taille + 1;
                        }
                    }

                    foreach ($lignes as $ligne) {
                        // On vérifie si la VM n'existe pas déjà
                        $VMname = substr($ligne, 0, -1) . "-" . $_SERVER['REMOTE_USER'] . "-1";
                        if (existingVM($VMname) == false) {
                            echo "<li class='list-group-item'>$ligne";
                            echo "<button class='btn btn-success boutonVM' onclick=\"confirmCreateVM('$VMname')\">Créer</button>";
                            echo "</li>";
                        }
                    }
                    ?>
                </ul>

                <hr class="my-4">

            </div>
        </div>
    </div>
    <!-- Bootstrap core JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Core theme JS -->
    <script src="../js/scripts.js"></script>
</body>
</html>