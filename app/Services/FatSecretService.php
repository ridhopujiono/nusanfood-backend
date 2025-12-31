<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class FatSecretService
{
    protected string $baseUrl;
    protected string $consumerKey;
    protected string $consumerSecret;

    public function __construct()
    {
        $this->baseUrl = config('services.fatsecret.base_url');
        $this->consumerKey = config('services.fatsecret.key');
        $this->consumerSecret = config('services.fatsecret.secret');
    }

    public function searchFood(string $query): array
    {
        return $this->request([
            'method' => 'foods.search',
            'search_expression' => $query,
            'format' => 'json',
            'max_results' => 1,
        ]);
    }

    public function getFoodDetail(string $foodId): array
    {
        return $this->request([
            'method' => 'food.get',
            'food_id' => $foodId,
            'format' => 'json',
        ]);
    }

    protected function request(array $params): array
    {
        $oauth = [
            'oauth_consumer_key' => $this->consumerKey,
            'oauth_signature_method' => 'HMAC-SHA1',
            'oauth_timestamp' => time(),
            'oauth_nonce' => uniqid(),
            'oauth_version' => '1.0',
        ];

        $baseParams = array_merge($oauth, $params);
        ksort($baseParams);

        $baseString = 'GET&' . rawurlencode($this->baseUrl . '/rest/server.api') . '&' .
            rawurlencode(http_build_query($baseParams, '', '&'));

        $signingKey = rawurlencode($this->consumerSecret) . '&';
        $signature = base64_encode(hash_hmac('sha1', $baseString, $signingKey, true));

        $baseParams['oauth_signature'] = $signature;

        return Http::get($this->baseUrl . '/rest/server.api', $baseParams)->json();
    }
}
