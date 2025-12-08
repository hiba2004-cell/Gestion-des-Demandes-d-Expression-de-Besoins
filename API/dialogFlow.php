<?php
require '../includes/functions.php';
session_start();
header('Content-Type: application/json');

$json = file_get_contents('php://input');
$data = json_decode($json, true);

// extract intent name
$intent = $data['queryResult']['intent']['displayName'] ?? null;


// Extract session ID (unique per user)
$session = $data['session'] ?? null;

// map intents to functions WITHOUT calling them
$functions = [
    "traitement des demandes" => "Valider_demande",
    "latest demande" => "getLatestDemandeAPI"
];

// check if the intent exists in mapping
if (isset($functions[$intent])) {
    $func = $functions[$intent];
    $func($data, $session);
} else {
    // fallback
    echo json_encode([
        "fulfillmentText" => "Intent not recognized: $intent"
    ]);
    exit;
}

// --------------Functions To Be Called-----------------------------

function Valider_demande($data, $session) {
    // extract parameters
    $demandeId = $data['queryResult']['parameters']['number'] ?? null;
    $action = $data['queryResult']['parameters']['action'] ?? [];


    if (!$demandeId) {
        foreach ($data['queryResult']['outputContexts'] as $ctx) {
            if (strpos($ctx['name'], 'last_demande_context') !== false) {
                $demandeId = $ctx['parameters']['last_demande_id'] ?? null;
            }
        }
    }

    $fulfillmentText = "";
    if (count($action) > 1 || $action[0] === "sent-to-admin"){
        createNotification($demandeId,1,1,"validateur 2 a envoyé la demande #{$demandeId} à l'administrateur pour révision. Automatiqumenet par Maliiia",2);
        $fulfillmentText = "Cette Demande etait envoyez a l'adminitrateur";
    }else{
        $action = $action[0];
        $arr = processBesoinAction($demandeId, 2, "Le Validateur 2 a {$action} cette demande {$demandeId} via Maliiia", $action);
        $fulfillmentText = $arr['success'] ?? $arr['error'];
    }

    
    $response = [
        'fulfillmentText' => $fulfillmentText
    ];
    echo json_encode($response);
    exit;
}

function getLatestDemandeAPI($data, $session){
    $response = getLatestDemande()[0];

    $demandeId = $response['id'];
    $text = "La dernière demande est #{$response['id']}. Elle a été faite par {$response['demandeur']} et son niveau d'urgence est {$response['urgence']}. Si tu veux, je peux l’accepter, la refuser ou l’envoyer à l’administrateur. Dis-moi simplement ! Je peux t’aider.";

     // Create output context with memory
    $outputContext = [
        [
            "name" => $session . "/contexts/last_demande_context",
            "lifespanCount" => 5,
            "parameters" => [
                "last_demande_id" => $demandeId
            ]
        ]
    ];

    echo json_encode([
        "fulfillmentText" => $text,
        "outputContexts" => $outputContext
    ]);

    exit;
}