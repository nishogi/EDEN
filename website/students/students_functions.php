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

    curl_exec($curl);

    curl_close($curl);
}
// Function to check if the VM already exists
function existingVM($VMname) {
    $url = "https://atlantis.int-evry.fr:8006/api2/json/nodes/atlantis/qemu";
    $headers = array(
        'Authorization: PVEAPIToken=web@pve!web_token=d0ea3d01-dbd5-4cf0-a534-19b508cd81f7',
    );

    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

    $vmListResult = curl_exec($curl);
    if ($vmListResult === false) {
        error_log("CURL error: " . curl_error($curl));
        curl_close($curl);
        return false;
    }
    curl_close($curl);

    $vmListData = json_decode($vmListResult, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        error_log("JSON decode error: " . json_last_error_msg());
        return false;
    }

    foreach ($vmListData['data'] as $vm) {
        if (strcmp($vm['name'], $VMname) == 0) {
            return true;
        }
    }

    return false;
}

// Function to get the next available VM ID
function getNextAvailableVMID() {
    $url = 'https://atlantis.int-evry.fr:8006/api2/json/nodes/atlantis/qemu';
    $headers = array(
        'Authorization: PVEAPIToken=web@pve!web_token=d0ea3d01-dbd5-4cf0-a534-19b508cd81f7',
    );

    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

    $vmListResult = curl_exec($curl);
    if ($vmListResult === false) {
        error_log("CURL error: " . curl_error($curl));
        curl_close($curl);
        return false;
    }
    curl_close($curl);

    $data = json_decode($vmListResult, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        error_log("JSON decode error: " . json_last_error_msg());
        return false;
    }

    $existingVMIDs = [];
    foreach ($data['data'] as $vm) {
        $existingVMIDs[] = (int)$vm['vmid'];
    }

    $nextAvailableID = 100;
    while (in_array($nextAvailableID, $existingVMIDs)) {
        $nextAvailableID++;
    }

    return $nextAvailableID;
}

// Function to create a VM for a student by cloning the professor's VM
function createVM($VMname, $sshPublicKey) {
    $userName = $_SERVER['REMOTE_USER'];
    $tofu_file_name = "/var/www/tofu/variables.auto.tfvars";

    $key = ftok($tofu_file_name, 'a');
    $sem_id = sem_get($key, 1, 0666, 1);
    if (!$sem_id) {
        error_log("Unable to get semaphore");
        return false;
    }

    if (!sem_acquire($sem_id)) {
        error_log("Unable to acquire semaphore");
        return false;
    }

    try {
        $folderPath = "/var/www/tofu";
        $filePath = '/var/www/tofu/variables.auto.tfvars';
        $vmID = getNextAvailableVMID();
        if ($vmID === false) {
            throw new Exception("Failed to get next available VM ID");
        }

        $VMname_clone = substr($VMname, 0, 7);
        $cloneID = getVMId($VMname_clone);
        if ($cloneID === false) {
            throw new Exception("Failed to get VM ID for cloning");
        }

        modifyVariablesFile($filePath, $cloneID, $sshPublicKey, $userName, $vmID, $VMname);
        executeTofuCommands($filePath, $folderPath);

    } catch (Exception $e) {
        error_log("Error: " . $e->getMessage());
        return false;
    } finally {
        sem_release($sem_id);
    }

    return $userName . " / " . $VMname . " / " . $sshPublicKey;
}

// Function to modify the variables in the tofu configuration file
function modifyVariablesFile($filePath, $cloneID, $sshPublicKey, $userName, $vmID, $VMname) {
    $file = fopen($filePath, "r");
    if (!$file) {
        throw new Exception("Unable to open file: " . $filePath);
    }

    $lines = [];
    while (($line = fgets($file)) !== false) {
        $lines[] = $line;
    }
    fclose($file);

    $lines[2]  = "clone_vm_id            = $cloneID\n";
    $lines[5]  = "cloudinit_ssh_keys     = [\"$sshPublicKey\"]\n";
    $lines[6]  = "cloudinit_user_account = \"$userName\"\n";
    $lines[18] = "vm_id                  = $vmID\n";
    $lines[21] = "vm_name                = \"$VMname\"\n";

    $file = fopen($filePath, "w");
    if (!$file) {
        throw new Exception("Unable to open file for writing: " . $filePath);
    }

    foreach ($lines as $line) {
        fwrite($file, $line);
    }
    fclose($file);
}

// Function to execute tofu commands to create the VM
function executeTofuCommands($filePath, $folderPath) {
    $initOutput = shell_exec("tofu -chdir=$folderPath init 2>&1");
    if ($initOutput === null) {
        throw new Exception("Error during tofu initialization.");
    }

    $planOutput = shell_exec("tofu -chdir=$folderPath plan -lock=false 2>&1");
    if ($planOutput === null) {
        throw new Exception("Error during tofu planning.");
    }

    $applyOutput = shell_exec("echo yes | tofu -chdir=$folderPath apply -lock=false 2>&1");
    if ($applyOutput === null) {
        throw new Exception("Error during tofu apply.");
    }

    echo "VM created successfully. Result: <pre>$initOutput</pre>";
    echo "---------------------------------------------------------------------------------------------------------------------------------";
    echo "VM created successfully. Result: <pre>$planOutput</pre>";
    echo "---------------------------------------------------------------------------------------------------------------------------------";
    echo "VM created successfully. Result: <pre>$applyOutput</pre>";
}