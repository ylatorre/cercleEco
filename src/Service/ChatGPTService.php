<?php

namespace App\Service;

use OpenAI;

class ChatGPTService
{
    private $client;

    public function __construct()
    {
        // Récupère la clé API depuis l'environnement
        $apiKey = $_ENV['OPENAI_API_KEY'] ?? null;

        if (!$apiKey) {
            throw new \RuntimeException('L’API Key OpenAI est manquante. Veuillez la définir dans le fichier .env');
        }
        $this->client = OpenAI::client($apiKey);
    }

    public function generateResponse(string $prompt): string
    {
        try 
        {
            $response = $this->client->chat()->create([
                'model' => 'gpt-3.5-turbo',
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => $prompt,
                    ],
                ],
            ]);

            // Vérifier la structure de la réponse et extraire le texte
            if (isset($response['choices'][0]['message']['content'])) {
                return $response['choices'][0]['message']['content'];
            } else {
                return 'No response content available.';
            }

        } 
        catch (Exception $e) 
        {
            return 'API Error: ' . $e->getMessage();
        }
    }
}