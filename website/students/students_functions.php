<?php

// Function to retrieve VM names matching the given pattern
function getVMNames($pattern) {
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
        return array();
    }
    curl_close($curl);

    $vmListData = json_decode($vmListResult, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        error_log("JSON decode error: " . json_last_error_msg());
        return array();
    }

    $userVMs = array();
    foreach ($vmListData['data'] as $vm) {
        if (strpos($vm['name'], $pattern) !== false) {
            $userVMs[] = $vm['name'];
        }
    }

    return $userVMs;
}

// Function to get the VM ID given its name
function getVMId($name) {
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

    $vmListData = json_decode($vmListResult, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        error_log("JSON decode error: " . json_last_error_msg());
        return false;
    }

    foreach ($vmListData['data'] as $vm) {
        if (isset($vm['name']) && strcmp($vm['name'], $name) == 0) {
            return $vm['vmid'];
        }
    }

    return false; // Return false if no matching VM is found
}

// Function to start the VM by its name
function startVM($name) {
    $VMId = getVMId($name);
    if ($VMId === false) {
        error_log("Failed to get VM ID for VM: $name");
        return;
    }

    $path = "https://atlantis.int-evry.fr:8006/api2/json/nodes/atlantis/qemu/$VMId/status/start";

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

// Function to stop the VM by its name
function stopVM($name) {
    $VMId = getVMId($name);
    if ($VMId === false) {
        error_log("Failed to get VM ID for VM: $name");
        return;
    }

    $path = "https://atlantis.int-evry.fr:8006/api2/json/nodes/atlantis/qemu/$VMId/status/stop";

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

// Function to check the status of the VM
function checkVMStatus($VMId) {
    $url = "https://atlantis.int-evry.fr:8006/api2/json/nodes/atlantis/qemu/$VMId/status/current";
    $headers = array(
        'Authorization: PVEAPIToken=web@pve!web_token=d0ea3d01-dbd5-4cf0-a534-19b508cd81f7',
    );

    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

    $vmStatusResult = curl_exec($curl);
    if ($vmStatusResult === false) {
        error_log("CURL error: " . curl_error($curl));
        curl_close($curl);
        return false;
    }
    curl_close($curl);

    $statusData = json_decode($vmStatusResult, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        error_log("JSON decode error: " . json_last_error_msg());
        return false;
    }

    return $statusData['data']['status'];
}

// Function to delete the VM by its name
function deleteVM($name) {
    $VMId = getVMId($name);
    if ($VMId === false) {
        error_log("Failed to get VM ID for VM: $name");
        return;
    }

    stopVM($name);

    while (checkVMStatus($VMId) != 'stopped') {
        sleep(1);
    }

    $path = "https://atlantis.int-evry.fr:8006/api2/json/nodes/atlantis/qemu/$VMId";

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

    $pathScript = "../proxy/script.sh";
    $userName = $_SERVER['REMOTE_USER'];
    $ip = getIPfromID($VMId);
    $port = getPortfromID($VMId);

    $command = $pathScript . " rm " . $userName . " " . $ip . " " . $port;

    shell_exec($command);
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

    sort($existingVMIDs);

    $nextAvailableID = 5000; // Starting ID for VMs
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

        $firstDashPos = strpos($VMname, '-'); // Find the position of the first dash
        $VMname_clone = substr($VMname, 0 , $firstDashPos); 
        
        $cloneID = getVMId($VMname_clone);

        if ($cloneID === false) {
            throw new Exception("Failed to get VM ID for cloning");
        }
        modifyVariablesFile($filePath, $cloneID, $sshPublicKey, $userName, $vmID, $VMname);
        executeTofuCommands($filePath, $folderPath);

        $pathScript = "../proxy/script.sh";
        $ip = getIPfromID($vmID);
        $port = getPortfromID($vmID);

        $command = $pathScript . " add " . $userName . " " . $ip . " " . $port;
        echo $command;
        $result = shell_exec($command);

    } catch (Exception $e) {
        error_log("Error: " . $e->getMessage());
        return false;
    } finally {
        sem_release($sem_id);
    }

    $ID = getIPfromID($vmID);
    $port = getPortfromID($vmID);

    return $userName . " / " . $VMname . " / " . $sshPublicKey . " / " . $ID . " / " . $port . " / " . $result;
}


// Function to obtain the private IP from a VM link to the ID
function getIPfromID($vmID) {
    $totID = $vmID - 4000;

    $ip24 = floor($totID / 256) + 2;

    $ip32 = $totID - floor($totID / 256) * 256;

    $IP = "192.168." . $ip24 . "." . $ip32;

    return $IP;
}

// Function to obtain the port from a VM link to the ID
function getPortfromID($vmID) {
    $port = 10000 + $vmID;
    return $port;
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

    $IP = getIPfromID($vmID);

    $IP = $IP . "/16";

    $lines[2]  = "clone_vm_id            = $cloneID\n";
    $lines[5]  = "cloudinit_ssh_keys     = [\"$sshPublicKey\"]\n";
    $lines[6]  = "cloudinit_user_account = \"$userName\"\n";
    $lines[18] = "vm_id                  = $vmID\n";
    $lines[21] = "vm_name                = \"$VMname\"\n";
    $lines[24] = "ip                     = \"$IP\"";

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

}
?>