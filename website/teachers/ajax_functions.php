<?php

include 'teachers_functions.php';

// Traitement de la requête AJAX
if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET["action"])) {
    $action = $_GET["action"];
    $name = $_GET["name"];

    if ($action === "startVM") {
        startVM($name);
    } elseif ($action === "stopVM") {
        stopVM($name);
    } elseif ($action === "deleteVM") {
        deleteVM($name);
    } elseif ($action === "checkVMStatus") {
        $VMId = getVMId($name);
        $status = checkVMStatus($VMId);
        echo json_encode(['status' => $status]);
    }
}


if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $action = $_POST["action"];
    $name = $_POST["name"];
    $sshPublicKey = $_POST["ssh_public_key"];

    if ($action === "createVM") {
        $result = createVM($name, $sshPublicKey);
        echo $result; // Ceci enverra la réponse au client
    }
}