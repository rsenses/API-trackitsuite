<?php

namespace App\Traits;

use GuzzleHttp\Client;
use Exception;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;

trait GuzzleRequest
{
    /**
     * Send POST request to external source
     *
     * @param  $baseUri
     * @param  $path
     * @param  array $params
     * @return mixed
     */
    protected function sendPostRequest($baseUri, $path, array $params)
    {
        $client = new Client([
            'base_uri' => $baseUri
        ]);

        try {
            $response = $client->post($path, [
                'form_params' => $params
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
