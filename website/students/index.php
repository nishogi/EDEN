<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>EDEN - Mes VMs</title>
  <link rel="icon" type="image/x-icon" href="assets/favicon.ico">
  <link href="https://stackpath.bootstrapcdn.com/bootstrap/5.2.3/css/bootstrap.min.css" rel="stylesheet">
  <link href="../css/styles.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script>
    function showLoadingSwal(title, text) {
        Swal.fire({
            title: title,
            text: text,
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
    }

    function showSuccessSwal(text) {
        Swal.fire({
            title: 'Succès!',
            text: text,
            icon: 'success',
            timer: 5000
        }).then(() => {
            location.reload();
        });
    }

    function pollVMStatus(vmName, expectedStatus) {
        return new Promise((resolve) => {
            const checkStatus = setInterval(() => {
                fetch(`ajax_functions.php?action=checkVMStatus&name=${encodeURIComponent(vmName)}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === expectedStatus) {
                            clearInterval(checkStatus);
                            resolve();
                        }
                    });
            }, 2000); // Check status every 2 seconds
        });
    }

    function confirmStartVM(name) {
    Swal.fire({
        title: 'Confirmation',
        text: "Souhaitez-vous vraiment allumer la VM : " + name + " ?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Oui',
        cancelButtonText: 'Non, annuler'
    }).then((result) => {
        if (result.isConfirmed) {
            showLoadingSwal('Veuillez patienter', 'La VM est en cours de démarrage...');
            const timeout = setTimeout(() => {
                Swal.close();
                showErrorSwal('Le démarrage de la VM a pris trop de temps.');
            }, 60000); // Timeout set to 60 seconds

            fetch(`ajax_functions.php?action=startVM&name=${encodeURIComponent(name)}`)
                .then(() => {
                    return pollVMStatus(name, 'running');
                })
                .then(() => {
                    clearTimeout(timeout);
                    Swal.close();
                    showSuccessSwal('VM démarrée !');
                })
                .catch((error) => {
                    clearTimeout(timeout);
                    Swal.close();
                    showErrorSwal('Erreur lors du démarrage de la VM.');
                    console.error('Error:', error);
                });
        }
    });
}

function confirmStopVM(name) {
    Swal.fire({
        title: 'Confirmation',
        text: "Souhaitez-vous vraiment arrêter la VM : " + name + " ?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Oui',
        cancelButtonText: 'Non, annuler'
    }).then((result) => {
        if (result.isConfirmed) {
            showLoadingSwal('Veuillez patienter', 'La VM est en cours d\'arrêt...');
            const timeout = setTimeout(() => {
                Swal.close();
                showErrorSwal('L\'arrêt de la VM a pris trop de temps.');
            }, 120000); // Timeout set to 120 seconds

            fetch(`ajax_functions.php?action=stopVM&name=${encodeURIComponent(name)}`)
                .then(() => {
                    return pollVMStatus(name, 'stopped');
                })
                .then(() => {
                    clearTimeout(timeout);
                    Swal.close();
                    showSuccessSwal('VM arrêtée !');
                })
                .catch((error) => {
                    clearTimeout(timeout);
                    Swal.close();
                    showErrorSwal('Erreur lors de l\'arrêt de la VM.');
                    console.error('Error:', error);
                });
        }
    });
}

    function confirmAccessVM(VMname, port, username) {
        Swal.fire({
            title: "Avant de tenter d'accéder à votre VM, vérifier que son statut est running si ce n'est pas le cas alors allumez votre VM. Voici la commande à exécuter pour vous connecter à la VM :",
            text: "ssh -p " + port + " " + username + "@" + "vm.eden.telecom-sudparis.eu",
            icon: 'info',
        });
    }

    function confirmDeleteVM(name) {
    Swal.fire({
        title: 'Confirmation',
        text: "Souhaitez-vous vraiment supprimer la VM : " + name + " ?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Oui',
        cancelButtonText: 'Non, annuler'
    }).then((result) => {
        if (result.isConfirmed) {
            showLoadingSwal('Veuillez patienter', 'La VM est en cours de suppression...');
            const timeout = setTimeout(() => {
                Swal.close();
                showErrorSwal('La suppression de la VM a pris trop de temps.');
            }, 120000); // Timeout set to 120 seconds

            fetch(`ajax_functions.php?action=deleteVM&name=${encodeURIComponent(name)}`)
                .then(() => {
                    return (existingVM($VMname) == false);
                })
                .then(() => {
                    clearTimeout(timeout);
                    Swal.close();
                    showSuccessSwal('VM supprimée !');
                })
                .catch((error) => {
                    clearTimeout(timeout);
                    Swal.close();
                    showErrorSwal('Erreur lors de la suppression de la VM.');
                    console.error('Error:', error);
                });
        }
    });
}

function confirmCreateVM(vmName) {
    Swal.fire({
        title: 'Clé SSH',
        padding: '1rem',
        input: 'textarea',
        inputLabel: 'Veuillez nous transmettre votre clé ssh publique afin qu\'on puisse vous transmettre vos identifiants :',
        inputPlaceholder: 'Entrez votre clé ssh publique ici...',
        inputAttributes: {
            'aria-label': 'Entrez votre clé ssh publique ici'
        },
        showCancelButton: true,
    }).then((result) => {
        if (result.isConfirmed && result.value) {
            const sshPublicKey = result.value;
            showLoadingSwal('Veuillez patienter', 'La VM est en cours de création...');
            const timeout = setTimeout(() => {
                Swal.close();
                showErrorSwal('La création de la VM a pris trop de temps.');
            }, 60000); // Timeout set to 60 seconds

            fetch("ajax_functions.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded"
                },
                body: `action=createVM&name=${encodeURIComponent(vmName)}&ssh_public_key=${encodeURIComponent(sshPublicKey)}`
            }).then(() => {
                return pollVMStatus(vmName, 'stopped');
            }).then(() => {
                clearTimeout(timeout);
                Swal.close();
                showSuccessSwal('VM créée avec succès, merci de l\'allumer pour y accéder !');
            }).catch((error) => {
                clearTimeout(timeout);
                Swal.close();
                showErrorSwal('Erreur lors de la création de la VM.');
                console.error('Error:', error);
            });
        }
    });
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
            margin: 0 0.5rem 0 0.5rem;
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 0.5rem;
        }

        .separator {
            padding: 0.5rem 0;
            background-color: transparent;
            border: none;
        }
  </style>
</head>
<body>
  <div class="d-flex" id="wrapper">
    <div class="border-end bg-white" id="sidebar-wrapper">
      <div class="sidebar-heading border-bottom bg-light text-center">
        <?php
                echo ($_SERVER['REMOTE_USER'] ?? '') . PHP_EOL;
                ?>
      </div>
      <div class="list-group list-group-flush">
        <a class="list-group-item list-group-item-action list-group-item-light p-3" href="">Documentation</a> <a class="list-group-item list-group-item-action list-group-item-light p-3" href="../index.php">Home</a> <a class="list-group-item list-group-item-action list-group-item-light p-3" href="/teachers/index.php">Teachers pages</a> <a class="list-group-item list-group-item-action list-group-item-light p-3" href="/students/index.php">Students pages</a> <a class="list-group-item list-group-item-action list-group-item-light p-3" href="/admin/index.php">Admin pages</a> <a class="list-group-item list-group-item-action list-group-item-light p-3" href="/fbertin/index.php">Perso fbertin (tests CAS)</a> <a class="list-group-item list-group-item-action list-group-item-light p-3" href="">
        <div class="text-center">
          <b>2024</b>
        </div></a>
      </div>
    </div>
    <div id="page-content-wrapper">
      <nav class="navbar navbar-expand-lg navbar-light bg-light border-bottom">
        <div class="container-fluid">
          <button class="btn btn-primary" id="sidebarToggle">&lt;&lt; Menu &gt;&gt;</button>
          <ul class="navbar-nav ms-auto mt-2 mt-lg-0">
            <li class="nav-item active">
              <a class="nav-link" href="https://github.com/Nishogi/EDEN" target="_blank">lien vers le code source</a>
            </li>
            <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Team</a>
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
                    $userName = $_SERVER['REMOTE_USER'];

                    foreach ($userVMs as $vmName) {
                        $VMId = getVMId($vmName);
                        $status = checkVMStatus($VMId);
                        $port = getPortfromID($VMId);

                        echo "<li class='list-group-item'>";
                        echo "<b>$vmName</b>";
                        echo "<p>Statut : $status</p>";
                        echo "<button class='btn btn-primary boutonVM' onclick=\"confirmAccessVM('$vmName', '$port', '$userName')\">Accéder</button>";
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

                    // Chemin vers le fichier de conf des cours
                    $cheminFichier = '../config_cours/liste_cours.conf';

                    // Lire les lignes du fichier et les stocker dans un tableau
                    $lignes = file($cheminFichier);

                    echo "<ul>";
                    $cours = [];
                    foreach ($lignes as $ligne) {
                        // On vérifie si l'utilisateur est dans le cours
                        $ligne = substr($ligne, 0, -1);
                        $fichierCours = "../config_cours/cours/$ligne.conf";
                        $users = file($fichierCours);
                        foreach($users as $user) {
                            $user = substr($user, 0, -1);
                            if ($user == $_SERVER['REMOTE_USER']) {
                                $cours[] = $ligne;
                            }
                        }
                    }

                    foreach ($cours as $cour) {
                        // On vérifie si la VM n'existe pas déjà
                        $VMname = $cour . "-" . $_SERVER['REMOTE_USER'] . "-1";
                        if (existingVM($VMname) == false) {
                            # On vérifie si le template du cours existe
                            if (existingVM($cour) == true) {
                                echo "<li class='list-group-item'>$VMname";
                                echo "<button class='btn btn-success boutonVM' onclick=\"confirmCreateVM('$VMname')\">Créer</button>";
                                echo "</li>";
                                echo "<div class='separator'></div>";
                            }
                        }
                    }

                ?>
        </ul>
        <hr class="my-4">
      </div>
    </div>
  </div>
  
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script> 
  <script src="../js/scripts.js"></script>
</body>
</html>