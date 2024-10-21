<?php

namespace App\Controllers;

use App\System\Core\Controller;
use App\Models\Users;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\ClientException;

class HomeController extends Controller
{
    public function __construct() {
        parent::__construct();
        $this->checkAuth();
    }

    public function index()
    {
        $client = new Client();        
        $user_obj = new Users;
        $id_user = $_SESSION['user_id'];
        $user = $user_obj->where('id',$id_user)->first();
        $synonyms = null;
        $error = null;
        try {
            // Send request to WordsAPI
            $response = $client->request('GET', 'https://wordsapiv1.p.rapidapi.com/words/' . $user->user_describe . '/synonyms', [
                'headers' => [
                    'X-RapidAPI-Host' => 'wordsapiv1.p.rapidapi.com',
                    'X-RapidAPI-Key' => config('apiKey')
                ]
            ]);
            $body = $response->getBody();
            $data = json_decode($body, true);
            if (isset($data['synonyms']) && is_array($data['synonyms'])) {
                $synonyms = $data['synonyms'];
            }
        } catch (ClientException $e) {
            $responseBody = $e->getResponse()->getBody()->getContents();
            $responseData = json_decode($responseBody, true);
            if (isset($responseData['message'])) {
                $synonyms = ['API Response: ' . $responseData['message']];
            } else {
                $error = 'Client error: ' . $e->getMessage();
            }
        } catch (ConnectException $e) {
            $error = 'No internet connection or cannot reach API. Please try again later.';
        } catch (RequestException $e) {
            $error = '<b> API request failed: </b>' . $e->getMessage();
        } catch (\Exception $e) {
            $error = '<b> An unexpected error occurred: </b>' . $e->getMessage();
        }

        $this->render('home/index', compact('user','synonyms', 'error'));
    }
}