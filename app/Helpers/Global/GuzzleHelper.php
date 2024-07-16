<?php

use App\Exceptions\GeneralException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Message;
use Illuminate\Support\Facades\Log;

if (!function_exists('callGuzzle')) {
    /**
     * Helper to call http
     *
     * @param string $method
     * @param string $endPoint
     * @param array $option
     * @param array $extraHeaders
     *
     * @return mixed
     * @throws GeneralException
     */
    function callGuzzleHttp(string $method, string $endPoint, array $option = [], array $extraHeaders = [])
    {
        try {
            $headers = [
                'Accept' => 'application/json',
                // 'Content-Type' => 'application/json'
            ];
            
            $headers = array_merge($headers, $extraHeaders);
            $client = new Client([
                'headers' => $headers,
            ]);

            $response = $client->request($method, $endPoint, $option);
            
            return json_decode($response->getBody(), true);
        } catch (RequestException $e) {
            if ($e->hasResponse()) {
                Log::info($e->getMessage());
            }
            throw new GeneralException('Call Guzzle Request Failed', $e->getCode());
        } catch (GuzzleException $e) {
            throw new GeneralException('Call Guzzle Request Failed', $e->getCode());
        }
    }
}