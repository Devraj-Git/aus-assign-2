<?php

namespace App\Controllers;

use App\System\Core\Controller;

class DiscordController extends Controller
{
    private $clientId = '1286016772432068719'; 
    private $client_secret = '3ejOTNM6aarzQRkQdKuxhjiJRUmqe3gy';
    private $redirectUri = 'http://127.1.0.1:7000/discord'; 
    private $scopes = 'identify guilds'; 
    private $discordAuthUrl = 'https://discord.com/oauth2/authorize';

    public function __construct() {
        parent::__construct();
        $this->checkAuth();
    }

    public function connect() {
        $state = bin2hex(random_bytes(16));
        $_SESSION['oauth2_state'] = $state;
        $authUrl = $this->discordAuthUrl . '?' . http_build_query([
            'client_id' => $this->clientId,
            'redirect_uri' => $this->redirectUri,
            'response_type' => 'code',
            'scope' => $this->scopes,
            'state' => $state
        ]);
        if (is_null(user()->discord_access_token)) {
            redirect($authUrl);
        }
        else{
            redirect(url('discord/showProfile'));
        }
    }

    public function callback() {
        if (!isset($_GET['state']) || $_GET['state'] !== $_SESSION['oauth2_state']) {
            echo "Invalid state token. Possible CSRF attack.";
            exit;
        }
        unset($_SESSION['oauth2_state']);
        if (isset($_GET['code'])) {
            $code = $_GET['code'];
            try {
                $client = new \GuzzleHttp\Client();
                $response = $client->post('https://discord.com/api/oauth2/token', [
                    'form_params' => [
                        'client_id' => $this->clientId,
                        'client_secret' => $this->client_secret,
                        'grant_type' => 'authorization_code',
                        'code' => $code,
                        'redirect_uri' => $this->redirectUri
                    ]
                ]);
    
                // $statusCode = $response->getStatusCode();
                $body = $response->getBody(); // Get the response body
                $tokenData = json_decode($body, true);
    
                // echo "Status Code: " . $statusCode . "<br>";
                // echo "Response Body: " . $body . "<br>";
    
                if ($tokenData && isset($tokenData['access_token'])) {
                    $accessToken = $tokenData['access_token'];
                    $user = user();
                    $user->discord_access_token = $accessToken;
                    $user->save();
                    redirect(url('discord/showProfile'));
                    // echo "Access Token: " . $accessToken;
                } else {
                    echo "Error: Access token not found in response.";
                }
    
            } catch (\GuzzleHttp\Exception\RequestException $e) {
                echo "Request failed: " . $e->getMessage();
            }
        } else {
            echo "Authorization failed. Please try again.";
        }
    }

    public function showProfile() {
        $accessToken = user()->discord_access_token;
        
        $client = new \GuzzleHttp\Client();
        $response = $client->get('https://discord.com/api/users/@me', [
            'headers' => [
                'Authorization' => 'Bearer ' . $accessToken
            ]
        ]);
        $guilds_response = $client->get('https://discord.com/api/users/@me/guilds', [
            'headers' => [
                'Authorization' => 'Bearer ' . $accessToken
            ]
        ]);

        $userInfo = json_decode($response->getBody(), true);
        $guildsInfo = json_decode($guilds_response->getBody(), true);
        view('discord/index',['userInfo'=>$userInfo,'guildsInfo'=>$guildsInfo,'user'=>user()]);
    }
}