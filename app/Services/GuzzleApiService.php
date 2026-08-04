<?php

namespace App\Services;



use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use App\Http\Traits\CommonService;

class   GuzzleApiService
{
    use CommonService;

    protected $client;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => config('api.base_url') ?? env('API_BASE_URL', 'http://127.0.0.1:8000/api/'),
            'timeout'  => 30.0,
        ]);
    }

    public function request($method, $uri, $data = [])
    {
        $token = session()->get('auth_token') ?? session()->get('user.token');
        
        try {
            $options = [
                'headers' => [
                    'Accept' => 'application/json',
                    'apikey' => config('api.apikey'),
                ],
            ];
          
            
            // Only attach Bearer token if it exists in the session
            if (!empty($token)) {
                $options['headers']['Authorization'] = "Bearer {$token}";
            }

            if (isset($data['query'])) {
                $options['query'] = $data['query'];
            } else {
                $options['json'] = $data;
            }

            //just used inside a logger 
            $fullUrl = $this->client->getConfig('base_uri') . ltrim($uri, '/');
            logger('GUZZLE REQUEST HEADERS & TOKEN CHECK:', [
                'has_token' => !empty($token),
                'relative_uri' => $uri,
                'computed_full_url' => $fullUrl,
                'token_snippet' => $token ? substr($token, 0, 10) . '...' : 'NULL',
                'data send to backend' => $options
            ]);

            $response = $this->client->request($method, $uri, $options);
            $status = $response->getStatusCode();

            if ($status >= 200 && $status < 300) {
                // Return decoded JSON response safely
                return json_decode($response->getBody()->getContents(), true);
            }

            return $this->errorResponse('Server responded with a status code of ' . $status, [], $status);
        } catch (\GuzzleHttp\Exception\RequestException $e) {

        logger()->error("Guzzle Absolute Failure: " . $e->getMessage());

        // If the backend actually responded (4xx/5xx with a JSON body),
        // extract its real message/status instead of dumping the raw
        // Guzzle exception text -- that's what the user should see.
        if ($e->hasResponse()) {
            $body = json_decode($e->getResponse()->getBody()->getContents(), true);

            if (is_array($body) && isset($body['message'])) {
                return [
                    'success' => false,
                    'message' => $body['message'],
                    'status_code' => $e->getResponse()->getStatusCode(),
                ];
            }
        }

        // No usable response (connection refused, DNS failure, timeout
        // before the backend even responded) -- fall back to a generic,
        // user-safe message rather than the raw exception text.
        return [
            'success' => false,
            'message' => 'Unable to reach the server. Please try again.',
            'status_code' => 500
        ];

        } catch (\Exception $e) {

        logger()->error("Guzzle Absolute Failure: " . $e->getMessage());

        return [
            'success' => false,
            'message' => 'Something went wrong. Please try again.',
            'status_code' => 500
        ];
        }  
        
    }

    public function get($uri, $params = [])
    {
      logger("get -GuzzleApiService: {$uri}", [
            'params' => $params
        ]);

        return $this->request('GET', $uri, ['query' => $params]);
    }

    public function post($uri, $data = [], $isMultipart = false)
    {
        logger('Guzzle API Request sent to backend: ' . $uri, $data);
        return $this->request('POST', $uri, $data, $isMultipart);
    }

    public function put($uri, $data = [])
    {
        return $this->request('PUT', $uri, $data);
    }

    public function delete($uri, $data = [])
    {
        return $this->request('DELETE', $uri, $data);
    }
}
