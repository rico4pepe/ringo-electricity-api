<?php

require_once 'ElectricalPaymentService.php';
require_once 'RequestValidator.php';

header('Content-Type: application/json');

// Capture the incoming request data
$requestMethod = $_SERVER['REQUEST_METHOD'];
$requestData = json_decode(file_get_contents('php://input'), true);

if ($requestMethod == 'POST' && isset($requestData['mobile'], $requestData['amount'], $requestData['opt3'])) {
    // Validate the request
    $validator = new RequestValidator();
    if (!$validator->validate($requestData)) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid request data']);
        exit;
    }

    // Inject dependencies
    $paymentService = new ElectricalPaymentService();
    
    // Call the payment processing method
    try {
        $response = $paymentService->processElectricPayment($requestData);
        echo json_encode($response);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
    }
} else {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['error' => 'Invalid request method or missing parameters']);
}