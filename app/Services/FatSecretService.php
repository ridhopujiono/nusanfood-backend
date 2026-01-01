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

    /**
     * SEARCH FOOD (v5)
     * /rest/food/search/v5
     */
    public function searchFood(string $query): array
    {
        return $this->request(
            endpoint: '/rest/food/search/v5',
            params: [
                'method' => 'foods.search',
                'search_expression' => $query,
                'max_results' => 1,
                'format' => 'json',
            ]
        );
    }

    /**
     * FOOD DETAIL (v5)
     * /rest/food/v5
     */
    public function getFoodDetail(string $foodId): array
    {
        return $this->request(
            endpoint: '/rest/food/v5',
            params: [
                'food_id' => $foodId,
                'format' => 'json',
            ]
        );
    }

    /**
     * CORE REQUEST HANDLER (OAuth 1.0a)
     */
    protected function request(string $endpoint, array $params): array
    {
        $oauth = [
            'oauth_consumer_key' => $this->consumerKey,
            'oauth_signature_method' => 'HMAC-SHA1',
            'oauth_timestamp' => time(),
            'oauth_nonce' => uniqid('', true),
            'oauth_version' => '1.0',
        ];

        $baseParams = array_merge($oauth, $params);
        ksort($baseParams);

        $encodedParams = http_build_query(
            $baseParams,
            '',
            '&',
            PHP_QUERY_RFC3986
        );

        $baseString = 'GET&' .
            rawurlencode($this->baseUrl . $endpoint) .
            '&' .
            rawurlencode($encodedParams);

        $signingKey = rawurlencode($this->consumerSecret) . '&';

        $signature = base64_encode(
            hash_hmac('sha1', $baseString, $signingKey, true)
        );

        $baseParams['oauth_signature'] = $signature;

        return Http::get(
            $this->baseUrl . $endpoint,
            $baseParams
        )->json();
    }
}
