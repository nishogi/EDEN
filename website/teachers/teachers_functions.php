<?php

// Fonction pour récupérer les noms de VM correspondant au motif donné en argument de la fonction
function getVMNames($pattern) {
    // URL de l'API et paramètres
    $url = 'https://atlantis.int-evry.fr:8006/api2/json/nodes/atlantis/qemu';
    $headers = array(
        'Authorization: PVEAPIToken=web@pve!web_token=d0ea3d01-dbd5-4cf0-a534-19b508cd81f7',
    );
    $cert_file = "/etc/certs/activ/eden.imtbs-tsp.eu/fullchain.pem";

    // Configuration de la requête Curl
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); // Ignorer la vérification SSL pour l'instant, trouver comment faire
    //curl_setopt($curl, CURLOPT_CAINFO, $cert_file); // Spécifier le chemin vers le certificat SSL

    $vmListResult = curl_exec($curl);

    // Vérification des erreurs Curl
    if ($vmListResult === false) {
        echo 'Erreur Curl : ' . curl_error($curl);
    }

    // Fermeture de la session Curl
    curl_close($curl);

    //echo "$vmListResult\n";

    // Décodage des données brutes afin d'en extraite le nom des VM
    $vmListData = json_decode($vmListResult, true);

    $userVMs = array();

    // Permet de sauvegarde le nom des VM correspondant au pattern
    // On enlève au début 8 caractère pour l'id du cours ex : CSC3101_
    foreach ($vmListData['data'] as $vm) {
        if (strcmp(substr($vm['name'], 8),$pattern)==0) {
            // Si même nom, ajouter le nom de la VM au tableau
            $userVMs[] = $vm['name'];
        }
    }

    return $userVMs;
}

// Fonction pour obtenir l'Id de la VM en ayant son nom en paramètre
function getVMId($name) {
    // On obtient l'ensemble des données au format JSON
    $url = 'https://atlantis.int-evry.fr:8006/api2/json/nodes/atlantis/qemu';
    $headers = array(
        'Authorization: PVEAPIToken=web@pve!web_token=d0ea3d01-dbd5-4cf0-a534-19b508cd81f7',
    );

    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

    $vmListResult = curl_exec($curl);
    curl_close($curl);
    //echo "$vmListResult\n";

    // Décodage des données brutes afin d'en extraite le nom des VM
    $vmListData = json_decode($vmListResult, true);


    // Permet de sauvegarde le nom des VM correspondant au pattern
    // On enlève au début 8 caractère pour l'id du cours ex : CSC3101_
    foreach ($vmListData['data'] as $vm) {
        if (strcmp($vm['name'],$name)==0) {
            // Si même nom, obtenir l'id de la VM
            $VMId = $vm['vmid'];
            break;
        }
    }

    return $VMId;
}

// Permet d'allumer la VM via son nom
function startVM($name) {
    $VMId = getVMId($name);
    $path = "https://atlantis.int-evry.fr:8006/api2/json/nodes/atlantis/qemu/$VMId/status/start";

    # Envoi de la demande d'allumage avec curl en utilisant le path mentionné au dessus et l'id de la VM
    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => $path,
        CURLOPT_RETURNTRANSFER => false,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_HTTPHEADER => array(
            "Authorization: PVEAPIToken=web@pve!web_token=d0ea3d01-dbd5-4cf0-a534-19b508cd81f7"
        ),
    ));

    curl_exec($curl);

    curl_close($curl);

    header("Location: {$_SERVER['PHP_SELF']}");
    exit;
}

// Permet d'arrêter la VM via son nom
function stopVM($name) {
    $VMId = getVMId($name);

    $path = "https://atlantis.int-evry.fr:8006/api2/json/nodes/atlantis/qemu/$VMId/status/stop";

    # Envoi de la demande d'arrêt avec curl en utilisant le path mentionné au dessus et l'id de la VM
    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => $path,
        CURLOPT_RETURNTRANSFER => false,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_HTTPHEADER => array(
            "Authorization: PVEAPIToken=web@pve!web_token=d0ea3d01-dbd5-4cf0-a534-19b508cd81f7"
        ),
    ));

    curl_exec($curl);

    curl_close($curl);
}

// Fonction pour vérifier l'état de la machine virtuelle
function checkVMStatus($VMId) {
    // On obtient l'ensemble des données au format JSON
    $url = "https://atlantis.int-evry.fr:8006/api2/json/nodes/atlantis/qemu/$VMId/status/current";
    $headers = array(
        'Authorization: PVEAPIToken=web@pve!web_token=d0ea3d01-dbd5-4cf0-a534-19b508cd81f7',
    );

    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

    $vmStatusResult = curl_exec($curl);
    curl_close($curl);

    $statusData = json_decode($vmStatusResult, true);

    // Accéder à la valeur de la clé "status"
    $status = $statusData['data']['status'];

    return $status;
}

// Permet de supprimer la VM via son nom
function deleteVM($name) {
    $VMId = getVMId($name);

    stopVM($name);

    // Attendre que la machine virtuelle soit arrêtée
    while (checkVMStatus($VMId) != 'stopped') {
        // Attendre 2 secondes avant de vérifier à nouveau
        sleep(1);
    }

    $path = "https://atlantis.int-evry.fr:8006/api2/json/nodes/atlantis/qemu/$VMId";

    # Envoi de la demande de suppression avec curl en utilisant le path mentionné au dessus et l'id de la VM
    $curl = curl_init();    

    curl_setopt_array($curl, array(
        CURLOPT_URL => $path,
        CURLOPT_RETURNTRANSFER => false,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_CUSTOMREQUEST => "DELETE",
        CURLOPT_HTTPHEADER => array(
            "Authorization: PVEAPIToken=web@pve!web_token=d0ea3d01-dbd5-4cf0-a534-19b508cd81f7"
        ),
    ));

    $response = curl_exec($curl);

    curl_close($curl);

    echo $response;
}

// Fonction pour savoir si la VM existe déjà ou pas
function existingVM($VMname) {
    // On obtient l'ensemble des données au format JSON
    $url = "https://atlantis.int-evry.fr:8006/api2/json/nodes/atlantis/qemu";
    $headers = array(
        'Authorization: PVEAPIToken=web@pve!web_token=d0ea3d01-dbd5-4cf0-a534-19b508cd81f7',
    );

    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

    $vmListResult = curl_exec($curl);
    curl_close($curl);

    // Décodage des données brutes afin d'en extraite le nom des VM
    $vmListData = json_decode($vmListResult, true);

    $result = false;

    foreach ($vmListData['data'] as $vm) {
        if (strcmp($vm['name'], $VMname)==0) {
            // Si même nom, alors resultat à true car la VM existe déjà pour cet user
            $result = true;
        }
    }

    return $result;
}

// Fonction permettant d'obtenir le prochain ID disponible pour la création de VM
function getNextAvailableVMID() {
    // On obtient l'ensemble des données au format JSON
    $url = 'https://atlantis.int-evry.fr:8006/api2/json/nodes/atlantis/qemu';
    $headers = array(
        'Authorization: PVEAPIToken=web@pve!web_token=d0ea3d01-dbd5-4cf0-a534-19b508cd81f7',
    );

    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

    $vmListResult = curl_exec($curl);
    curl_close($curl);
    //echo "$vmListResult\n";

    // Décodage des données brutes afin d'en extraite le nom des VM
    $data = json_decode($vmListResult, true);


    // Extraire les IDs des VMs existantes
    $existingVMIDs = [];
    foreach ($data['data'] as $vm) {
        $existingVMIDs[] = (int)$vm['vmid'];
    }

    // Trouver le premier ID disponible
    $nextAvailableID = 4000; // Début à partir de 4000 pour les VM de prof
    while (in_array($nextAvailableID, $existingVMIDs)) {
        $nextAvailableID++;
    }

    return $nextAvailableID;
}

// Fonction permettant de créer une VM pour un professeur (cloner la VM template de base)
function createVM($VMname, $sshPublicKey) {
    $userName = $_SERVER['REMOTE_USER'];

    $tofu_file_name = "variables.auto.tfvars";

    // Création de la clé de sémaphore
    $key = ftok($tofu_file_name, 'a');
    
    // Obtention d'un sémaphore
    $sem_id = sem_get($key, 1, 0666, 1);
    
    if (!$sem_id) {
        die("Impossible d'obtenir un sémaphore\n");
    }

    // Acquisition du sémaphore (attendre si nécessaire)
    if (!sem_acquire($sem_id)) {
        die("Impossible d'acquérir le sémaphore\n");
    }

    try {
        // Modifier le fichier variables.auto.tfvars
        $folderPath = "../../tofu";
        $filePath = '../../tofu/variables.auto.tfvars';
        $vmID = getNextAvailableVMID();

        // Nom de la VM template de base qui est sur Proxmox
        $VMname_clone = "template";
        $cloneID = getVMId($VMname_clone);

        modifyVariablesFile($filePath, $cloneID, $sshPublicKey, $userName, $vmID, $VMname);

        // Exécuter les commandes tofu
        executeTofuCommands($filePath, $folderPath);

    } catch (Exception $e) {
        echo "Erreur : " . $e->getMessage();
    } finally {
        // Libération du sémaphore
        sem_release($sem_id);
    }

    return $userName . " / " . $VMname . " / " . $sshPublicKey;
}

// Fonction permettant de modifier les variables dans le fichier de conf de tofu
function modifyVariablesFile($filePath, $cloneID, $sshPublicKey, $userName, $vmID, $VMname) {
    // Lire le fichier ligne par ligne
    $file = fopen($filePath, "r");
    if (!$file) {
        throw new Exception("Impossible d'ouvrir le fichier : " . $filePath);
    }

    $lines = [];
    while (($line = fgets($file)) !== false) {
        $lines[] = $line;
    }
    fclose($file);

    // Modifier les lignes spécifiques
    $lines[2]  = "clone_vm_id            = $cloneID\n";
    $lines[5]  = "cloudinit_ssh_keys     = [\"$sshPublicKey\"]\n";
    $lines[6]  = "cloudinit_user_account = \"$userName\"\n";
    $lines[18] = "vm_id                  = $vmID\n";
    $lines[21] = "vm_name                = \"$VMname\"\n";

    // Écrire les lignes modifiées dans le fichier
    $file = fopen($filePath, "w");
    if (!$file) {
        throw new Exception("Impossible d'ouvrir le fichier en écriture : " . $filePath);
    }

    foreach ($lines as $line) {
        fwrite($file, $line);
    }
    fclose($file);
}

// Fonction permettant d'exécuter les commandes de tofu afin de lancer la création
function executeTofuCommands($filePath, $folderPath) {
    // Initialiser la configuration tofu
    $initOutput = shell_exec("tofu -chdir=$folderPath init 2>&1");
    if ($initOutput === null) {
        throw new Exception("Erreur lors de l'initialisation de tofu.");
    }

    // Planifier les changements
    $planOutput = shell_exec("tofu -chdir=$folderPath plan -lock=false 2>&1");
    if ($planOutput === null) {
        throw new Exception("Erreur lors de la planification de tofu.");
    }

    // Appliquer les changements et confirmer avec 'yes'
    $applyOutput = shell_exec("echo yes | tofu -chdir=$folderPath apply -lock=false 2>&1");
    if ($applyOutput === null) {
        throw new Exception("Erreur lors de l'application de tofu.");
    }
    echo "VM créée avec succès. Résultat : <pre>$initOutput</pre>";
    echo "---------------------------------------------------------------------------------------------------------------------------------";
    echo "VM créée avec succès. Résultat : <pre>$planOutput</pre>";
    echo "---------------------------------------------------------------------------------------------------------------------------------";
    echo "VM créée avec succès. Résultat : <pre>$applyOutput</pre>";
}

