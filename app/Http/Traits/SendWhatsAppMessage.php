<?php

namespace App\Http\Traits;

trait SendWhatsAppMessage
{
    public function sendMessage(array $payload)
    {

        $url = 'https://whatsb.in/api/adc77f35-25d5-4832-9651-7a253e08b2d5/contact/send-template-message';
        $token = 'FnUaZou2hyTTnspx5zfI16ENFRZ21SdNhETG7RHpaw54LJEZZ0TZ5ZfgSGucs6pq';

        $ch = curl_init($url);

        // Convert payload array to JSON
        $jsonPayload = json_encode($payload);

        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer $token",
            "Content-Type: application/json",
            "Accept: application/json",
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonPayload);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {

            curl_close($ch);
            return 'error';
        }

        curl_close($ch);

        $responseData = json_decode($response, true);

        
        if (
            isset($responseData['result']) && $responseData['result'] === 'success'
        ) {
            return 'success';
        }

        return 'error';
    }
}