<?php

Class ElectricalPaymentService{
    private string $token;
    private string $authToken;
    private string $apiUrl;

    public function __construct(){
           // Load credentials from environment or config file if we want to extent and give granularity in the future 
           $this->token = '6234bb3a-1138-4bb9-9825-4c63ac10';
           $this->authToken = 'Elg2P0hV8thhGRPQsBj5/ijF4xdnpr1xLh/atAFr9JPToynvl2/QYYivHz763zTUwJJsYFSt+u64JqRC61H/qA==';
           $this->apiUrl = 'https://api.onecardnigeria.com/rest/doPayment';  // Replace with actual URL
    }

    public function processElectricPayment(array $data) : array{

        //generate 16 digit number for reference
        $referenceNumber = $this->generateReference();

          // Prepare headers and form data
          $headers = [
            'token' => $this->token,
            'authtoken' => $this->authToken,
        ];

        $formData = [
            //'product_id' => 'W9XgeJNGktFEs3KJ4NiTUA==',
            //'subscriber' => 'EFQcPKjZtZnI8sYuDLUVxg==',
            'reference' => $referenceNumber,  // Use the plain reference number
            'request' => [
                [
                    'product_id' => $data['product_id'],
                    'mobile' => $data['mobile'],
                    'amount' => $data['amount'],
                    'params' => [
                        'opt3' => $data['opt3']
                    ],
                    'plan_params' => ''
                ]
            ]
        ];

             // Send request to external API
             return $this->sendRequest($headers, $formData);
        
    }


    public function generateReference(){

       
        return bin2hex(random_bytes(8));  // Generate a 16-digit hexadecimal string

        
    }

    public function sendRequest(array $headers, array $formData): array{
            $curl = curl_init();

            curl_setopt_array($curl, [
                CURLOPT_URL => $this->apiUrl,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_HTTPHEADER => [
                    "token: {$headers['token']}",
                    "authtoken: {$headers['authtoken']}",
                    'Content-Type: application/x-www-form-urlencoded',
                ],
                CURLOPT_POSTFIELDS => http_build_query($formData),
            ]);
    
            $response = curl_exec($curl);
            $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    
            curl_close($curl);
    
            if ($httpCode === 200) {
                $decodedResponse = json_decode($response, true);
                
                if (json_last_error() === JSON_ERROR_NONE) {
                    return $decodedResponse;
                } else {
                    throw new Exception("Failed to decode JSON response. Response: $response");
                }
            } else {
                throw new Exception("Failed to process payment. HTTP code: $httpCode. Response: $response");
            }
    }
}

