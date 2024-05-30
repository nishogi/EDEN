<?php

include 'teachers   _functions.php';

// Traitement de la requête AJAX
if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET["action"])) {
    $action = $_GET["action"];
    $name = $_GET["name"];

    if ($action === "startVM") {
        startVM($name);
    }

    if ($action === "stopVM") {
        stopVM($name);
    }

    if ($action === "deleteVM") {
        deleteVM($name);
    }
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST["action"];
    $name = $_POST["name"];
    $sshPublicKey = $_POST["ssh_public_key"];

    if ($action === "createVM") {
        //createVM($name, $sshPublicKey);
        $result = createVM($name, $sshPublicKey);
        echo $result; // Ceci enverra la réponse au client
    }
}