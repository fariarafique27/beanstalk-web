<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Log;
use App\Http\Traits\CommonService;

class GuzzleApiService
{
    use CommonService;

    protected $client;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => config('api.base_url'), // Define in config/services.php
            // 'timeout'  => 10.0,
        ]);
    }

    public function request($method, $uri, $data = [], $isMultipart=false)
    {
        $token = session()->get('user.token');
        try {
        
            $options = [
                'headers' => [
                    'Authorization' => "Bearer {$token}",
                    'Accept'        => 'application/json',
                    'apikey'        => config('api.apikey'),
                ],
            ];
            
            // Forward decrypt token from web session if present
            if (session()->has('decrypt_token')) {
                $options['headers']['X-Decrypt-Token'] = session('decrypt_token');
            }

            if ($isMultipart) {
                $multipartData = [];

                foreach ($data as $name => $contents) {
                    if ($contents instanceof \Illuminate\Http\UploadedFile || $contents instanceof \Illuminate\Http\File) {
                        $multipartData[] = [
                            'name'     => $name,
                            'contents' => fopen($contents->getRealPath(), 'r'),
                            'filename' => $contents->getClientOriginalName(),
                        ];
                    } elseif (is_array($contents)) {
                        $multipartData[] = [
                            'name'     => $name,
                            'contents' => json_encode($contents),
                        ];
                    } elseif (is_string($contents)) {
                        $multipartData[] = [
                            'name'     => $name,
                            'contents' => $contents,
                        ];
                    } else {
                        $multipartData[] = [
                            'name'     => $name,
                            'contents' => $contents,
                        ];
                    }
                }

                $options['multipart'] = $multipartData;
            } elseif (isset($data['query'])) {
                $options['query'] = $data['query'];
            } else {
                $options['json'] = $data;
            }


            $response = $this->client->request($method, $uri, $options);

            // dd($response);
            $responseBody = $response->getBody();
            $status = $response->getStatusCode();

            if ($status == 200) {

                $contentType = $response->getHeader('Content-Type')[0] ?? '';

                // if (\Str::contains($contentType, [
                //     'application/vnd.ms-excel',
                //     'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                //     'application/zip',
                //     'application/pdf',

                // ])) {
                //     return $response;
                // }
                return $response;
                return json_decode($response->getBody()->getContents(), true);
            }
            return $this->errorResponse('Server responded with a status code of ' . $status, [], $status);

        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $statusCode = $e->getResponse()->getStatusCode();
            $body = json_decode($e->getResponse()->getBody()->getContents(), true);
            
            if ($statusCode === 401) {
                session()->forget('user');

                if(request()->ajax()){
                    return response()->json([
                        'success' => false,
                        'message' => 'Session expired. Please log in again.',
                        'url'     => route('login'),  
                    ], 401);
                }
                
                return redirect()->route('login')
                    ->withErrors(['error' => $body['message'] ?? 'Session expired. Please login again.']);
            }

            if ($statusCode === 403 && isset($body['message'])) {
                session()->forget('user');
                session()->forget('decrypt_token');

                if (request()->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => $body['message'],
                        'url'     => route('login'),
                    ], 403);
                }

                return redirect()->route('login')
                    ->withErrors(['error' => $body['message']]);
            }
            
            $returnResponse = [
                'success' => false,
                'message' => $body['message'] ?? 'Client error',
                // 'data' => $body['data'] ?? [],
                // 'errors' => $body['errors'] ?? [],
                'status_code' => $statusCode
            ];

            if(isset($body['data'])){
                $returnResponse['data'] = $body['data'];
            }
            if(isset($body['errors'])){
                $returnResponse['errors'] = $body['errors'];
            }

            return $returnResponse;
    
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            $statusCode = $e->getResponse()->getStatusCode();
            $body = json_decode($e->getResponse()->getBody()->getContents(), true);
            $returnResponse = [
                'success' => false,
                'message' => $body['message'] ?? 'Client error',
                // 'data' => $body['data'] ?? [],
                // 'errors' => $body['errors'] ?? [],
                'status_code' => $statusCode
            ];

            if(isset($body['data'])){
                $returnResponse['data'] = $body['data'];
            }
            if(isset($body['errors'])){
                $returnResponse['errors'] = $body['errors'];
            }

            return $returnResponse;
        }
    }

    public function get($uri, $params = [])
    {
        Log::info('GuzzleApiService [GET Method Called]', [
        'uri'    => $uri,
        'params' => $params, // Will be empty array [] if not passed
    ]);

        return $this->request('GET', $uri, ['query' => $params]);
    }

    public function post($uri, $data = [], $isMultipart=false)
    {
        return $this->request('POST', $uri, $data, $isMultipart);
    }

    // Add PUT, DELETE etc. if needed
}


?>
