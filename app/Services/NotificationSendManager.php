<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use App\Models\User;
use Google\Client;


class NotificationSendManager
{
    private $client;

    public function __construct()
    {
        $this->initializeClient();
    }

    private function initializeClient()
    {
        $credentialsFilePath = config('app')['google_api_file'];

        // Validate file existence (optional)
        if (!file_exists($credentialsFilePath)) {
            Log::error('Google credentials file not found: ' . $credentialsFilePath);
            throw new \Exception('Google credentials file not found.');
        }

        $this->client = new Client();
        $this->client->setAuthConfig($credentialsFilePath);
        $this->client->addScope('https://www.googleapis.com/auth/firebase.messaging');
    }


    public function sendNotification(string $title, string $message, string $token)
    {
        // Log::info('title: '   . $title);
        // Log::info('message: ' . $message);
        // Log::info('token: '   . $token);

        $notificationData = [
            "title" => $title,
            "body" => $message,
        ];

        $apiUrl = 'https://fcm.googleapis.com/v1/projects/shojonsltexon/messages:send';

        $dataPayload = [
            // "topic"=> "Bonus Points",
            "notification" => $notificationData,
            'data'  => $notificationData,
            'token' => $token,
        ];

        $payload = ['message' => $dataPayload];

        try {
            $this->client->refreshTokenWithAssertion();

            $tokenData = $this->client->getAccessToken();
            Log::info('Token data:', $tokenData);
            $accessToken = $tokenData['access_token'];

            $headers = [
                "Authorization: Bearer {$accessToken}",
                'Content-Type: application/json',
            ];

            $encodedPayload = json_encode($payload);

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $apiUrl);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $encodedPayload);
            curl_exec($ch);

            // Execute the cURL request
            $response = curl_exec($ch);

            // Check for errors
            if ($response === FALSE) {
                $errorMessage = curl_error($ch);
                Log::error('cURL Error: ' . $errorMessage);
                throw new \Exception('cURL Error: ' . $errorMessage);
            }

            // Close the cURL session
            curl_close($ch);

            if ($response) {
                Log::info('Notification sent successfully:', [
                    'title' => $title,
                    'message' => $message,
                    'response' => $response,
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Failed to send notification:', [
                'title' => $title,
                'message' => $message,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }
}

