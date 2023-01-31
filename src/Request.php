<?php

    namespace Spotify\Api\Marketing;

    use GuzzleHttp\Exception\ClientException;
    use GuzzleHttp\Client as GuzzleClient;

    trait Request {

        public string $endpoint = "https://api-partner.spotify.com/ads";
        public string $version = "v1.4";

        public function getVersionedEndpoint(): string {
            return "{$this->endpoint}/{$this->version}/";
        }

        public GuzzleClient $api;
        public function api(string $action, array $params) {
            $this->api = new GuzzleClient([
                "base_uri" => $this->getVersionedEndpoint(),
                'allow_redirects' => true,
            ]);

            try {
                $response = $this->api->post(
                    $action, [
                    'headers' => [
                        'Authorization' => "Bearer ".$this->auth->getAccessToken(),
                        'Content-Type' => 'application/json',
                    ],
                    'body' => json_encode($params),
                ]);
            } catch (ClientException $e) {
                $response = $e->getResponse();
            }
            return json_decode($response->getBody()->getContents());
        }

    }

?>