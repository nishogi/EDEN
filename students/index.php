<html>

<head>
    <title>Eden students page</title>
    <!-- Favicon-->
    <link rel="icon" type="image/x-icon" href="assets/favicon.ico" />

    <!-- Core theme CSS (includes Bootstrap)-->
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
                        location.reload();
                    }
                };
                xhr.send("action=createVM&name=" + encodeURIComponent(vmName) + "&ssh_public_key=" + encodeURIComponent(sshPublicKey));
            }
        }
    </script>
</head>

<body>

    <div class="d-flex" id="wrapper">

        <!-- Sidebar-->
        <div class="border-end bg-white" id="sidebar-wrapper">
            <div class="sidebar-heading border-bottom bg-light" align="center">
                <?php
                echo ($_SERVER['REMOTE_USER'] ?? '') . PHP_EOL;
                ?>
                &nbsp;
            </div>
            <div class="list-group list-group-flush">
                <a class="list-group-item list-group-item-action list-group-item-light p-3" href="">Documentation</a>

                <a class="list-group-item list-group-item-action list-group-item-light p-3" href="../index.php">Home</a>
                <a class="list-group-item list-group-item-action list-group-item-light p-3"
                    href="/teachers/index.php">Teachers pages</a>
                <a class="list-group-item list-group-item-action list-group-item-light p-3"
                    href="/students/index.php">Students pages</a>
                <a class="list-group-item list-group-item-action list-group-item-light p-3"
                    href="/admin/index.php">Admin pages</a>
                <a class="list-group-item list-group-item-action list-group-item-light p-3"
                    href="/fbertin/index.php">Perso fbertin (tests CAS)</a>

                <a class="list-group-item list-group-item-action list-group-item-light p-3" href="">
                    <table width="100%">
                        <tr>
                            <td width="100%" align="center"><b>2024</b></td>
                        </tr>
                    </table>
                </a>
            </div>
        </div>

        <!--  Page content wrapper - Top navigation -->
        <div id="page-content-wrapper">
            <nav class="navbar navbar-expand-lg navbar-light bg-light border-bottom">
                <div class="container-fluid">
                    <button class="btn btn-primary" id="sidebarToggle">
                        << Menu>>
                    </button>
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                        data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                        aria-expanded="false" aria-label="Toggle navigation"><span
                            class="navbar-toggler-icon"></span></button>
                    <ul class="navbar-nav ms-auto mt-2 mt-lg-0">
                        <li class="nav-item active"><a class="nav-link" href="https://gitlabev.imtbs-tsp.eu"
                                target="_blank">GitLabEv</a></li>
                        <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button"
                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Team</a>
                        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                            <a class="dropdown-item" href="#!">franz.bertin@imtbs-tsp.eu</b></a>
                            <a class="dropdown-item" href="#!">christophe.gaboret@imtbs-tsp.eu</b></a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="#!">tom.burellier@telecom-sudparis.eu</a>
                            <a class="dropdown-item" href="#!">nicolas.rocq@telecom-sudparis.eu</a>
                            <a class="dropdown-item" href="#!">mathis.williot@telecom-sudparis.eu</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="#!">olivier.berger@telecom-sudparis.eu</a>
                            <a class="dropdown-item" href="#!">arthur.jovart@telecom-sudparis.eu</a>
                            <a class="dropdown-item" href="#!"></a>
                        </div>
                        <li class="nav-item dropdown">
                        </li>
                    </ul>
                </div>
            </nav>


            <!-- Page content-->
            <div class="container-fluid">
                <p>&nbsp;</p>
                <table width="100%" border="0">
                    <tr>
                        <h1>Eden students page:
                            <?php echo $_SERVER['REMOTE_USER']; ?>
                        </h1>
                    </tr>
                </table>

                <div align="center">
                    <hr style="height:5px; width:100%; border-width:0; color:blue; background-color:blue" />
                </div>


                <p><b>Mes VM :</b></p>
                <ul>
                    <?php


                    // For debug
                    error_reporting(E_ALL);
                    ini_set('display_errors', 1);



                    include 'students_functions.php';

                    // Format des noms de VM recherchés
                    $vmNamePattern = strval($_SERVER['REMOTE_USER']) . '-1';

                    $userVMs = getVMNames($vmNamePattern);

                    echo "<ul>";
                    $ind = 0;
                    foreach ($userVMs as $vmName) {
                        $VMId = getVMId($vmName);
                        $status = checkVMStatus($VMId);
                        echo "<li><b>$vmName</b>";
                        echo "<p>Statut : $status</p>";
                        echo '<button class="boutonVM">Accéder</button>';
                        echo "<button class='boutonVM' onclick=\"confirmStartVM('$vmName')\">Allumer</button>";
                        echo "<button class='boutonVM' onclick=\"confirmStopVM('$vmName')\">Eteindre</button>";
                        echo "<button class='boutonVM' onclick=\"confirmDeleteVM('$vmName')\">Supprimer</button>";
                        echo "</li>";
                        $ind = $ind + 1;
                        if ($ind != count($userVMs)) {
                            echo '<div align="left">
                                <hr style="height:2px; width:50%; border-width:0; color:blue; background-color:blue" />
                                </div>';
                        }
                    }
                    echo "</ul>";
                    ?>
                </ul>

                <div align="center">
                    <hr style="height:5px; width:100%; border-width:0; color:blue; background-color:blue" />
                </div>

                <p><b>Mes VM possibles à créer :</b></p>
                <ul>
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

                    echo "<ul>";
                    $ind = 0;
                    foreach ($lignes as $ligne) {
                        // On vérifie si la VM n'existe pas déjà
                        $VMname = substr($ligne, 0, -1) . "-" . $_SERVER['REMOTE_USER'] . "-1";
                        if (existingVM($VMname) == false) {
                            echo "<li>$ligne";
                            echo "<button class='boutonVM' onclick=\"confirmCreateVM('$vmName')\">Créer</button>";
                            echo "<br></br>";
                            echo "</li>";
                            $ind = $ind + 1;
                            if ($ind != $taille) {
                                echo '<div align="left">
                                    <hr style="height:2px; width:50%; border-width:0; color:blue; background-color:blue" />
                                    </div>';
                            }
                        }
                    }
                    echo "</ul>";
                    ?>
                </ul>

                <div align="center">
                    <hr style="height:5px; width:100%; border-width:0; color:blue; background-color:blue" />
                </div>

                <p><b>A implementer</b></p>
                <ul>
                    <li>Creer sa VM pour un module particulier</li>
                </ul>

                <p>1 module = 1 cours</p>

                <p>Identification VM => IdDuCours_UidStudent_1</p>

                <p>1 etudiant ne peut que creer 1 VM par module</p>

                <p>Duree de vie limitee des VM</p>

            </div>
        </div>

        <!-- Bootstrap core JS-->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>

        <!-- Core theme JS-->
        <script src="../js/scripts.js"></script>

</body>

</html>
