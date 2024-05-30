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
        sleep(2);
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

    curl_exec($curl);

    curl_close($curl);
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

function createVM($VMname, $sshPublicKey) {
    $userName = $_SERVER['REMOTE_USER'];
    return $userName . $VMname . $sshPublicKey;
}