<?php

namespace App\Traits;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;

trait GuzzleRequest
{
    /**
     * Send POST request to external source
     *
     * @return mixed
     */
    protected function sendPostRequest($baseUri, $path, array $params)
    {
        // Check if the base URI contains smart word
        if (strpos($baseUri, 'smart') === false) {
            $client = new Client([
                'auth' => ['smart', 'eventosue'],
                'base_uri' => $baseUri,
                'verify' => ! env('APP_DEBUG', false),
            ]);
        } else {
            $client = new Client([
                'base_uri' => $baseUri,
                'verify' => ! env('APP_DEBUG', false),
            ]);
        }

        try {
            $response = $client->post($path, [
                'form_params' => $params,
            ]);

            return true;
        } catch (RequestException $e) {
            Log::notice($e->getMessage());

            return false;
        } catch (Exception $e) {
            Log::error($e->getMessage());

            return false;
        }
    }
}
