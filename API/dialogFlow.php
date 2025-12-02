<?php
require '../includes/functions.php';
session_start();
header('Content-Type: application/json');

$json = file_get_contents('php://input');
$data = json_decode($json, true);

// extract intent name
$intent = $data['queryResult']['intent']['displayName'] ?? null;

// map intents to functions WITHOUT calling them
$functions = [
    "traitement des demandes" => "Valider_demande",
    "latest demande" => "getLatestDemandeAPI"
];

// check if the intent exists in mapping
if (isset($functions[$intent])) {
    $func = $functions[$intent];
    $func($data);    // call function and send response
} else {
    // fallback
    echo json_encode([
        "fulfillmentText" => "Intent not recognized: $intent"
    ]);
    exit;
}

// --------------Functions To Be Called-----------------------------

function Valider_demande($data) {
    // extract parameters
    $number = $data['queryResult']['parameters']['number'] ?? null;
    $response = [
        'fulfillmentText' => "Please tell me the number you want to use: $number"
    ];
    echo json_encode($response);
    exit;
}

function getLatestDemandeAPI($data){
    $response = getLatestDemande()[0];

    $text = "La dernière demande est #{$response['id']}. Elle a été faite par {$response['demandeur']} et son niveau d'urgence est {$response['urgence']}. Si tu veux, je peux l’accepter, la refuser ou l’envoyer à l’administrateur. Dis-moi simplement ! Je peux t’aider.";

    echo json_encode([
        'fulfillmentText' => $text
    ]);

    exit;
}