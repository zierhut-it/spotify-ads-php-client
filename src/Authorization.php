<?php

    namespace Spotify\Api\Marketing;

    use GuzzleHttp\Exception\ClientException;
    use GuzzleHttp\Client as GuzzleClient;

    class Authorization {

        public string $endpoint = "https://accounts.spotify.com/";

        public string $clientId;
        public string $clientSecret;
        public string $authorizationBearer;

        public function setAuth(string $clientId, string $clientSecret): void {
            $this->clientId = $clientId;
            $this->clientSecret = $clientSecret;
            $this->authorizationBearer = base64_encode("$clientId:$clientSecret");
        }

        public string $redirectUrl;

        public function setRedirectUrl(string $redirectUrl): void {
            $this->redirectUrl = $redirectUrl;
        }

        public string $refreshToken;

        public function setRefreshToken(string $refreshToken): void {
            $this->refreshToken = $refreshToken;

            // Reset the access token as a new refresh token might be for a different user
            // and thus the access token needs to be renewed on the next request
            $this->expiresAt = 0;
        }

        public function getRedirectUrl() {
            return $this->endpoint."authorize?".http_build_query([
                "client_id" => $this->clientId,
                "response_type" => "code",
                "redirect_uri" => $this->redirectUrl,
            ]);
        }

        public function getRefreshToken(string $code) {
            $result = $this->request("api/token", [
                "grant_type" => "authorization_code",
                "code" => $code,
                "redirect_uri" => $this->redirectUrl,
            ]);

            $this->setAccessToken($result->access_token, $result->expires_in);
            $this->setRefreshToken($result->refresh_token);

            return $result->refresh_token;
        }

        private string $accessToken;
        private int $expiresAt;

        public function setAccessToken(string $accessToken, int $expiresIn): void {
            $this->accessToken = $accessToken;
            $this->expiresAt = time() + $expiresIn;
        }

        public int $expiryOffset = 60;

        public function getAccessToken() {
            // Only query for a new access token if the current one is expired or has not been set yet
            if(!empty($this->accessToken) && $this->expiresAt > time() + $this->expiryOffset) {
                return $this->accessToken;
            }

            $result = $this->request("api/token", [
                "grant_type" => "refresh_token",
                "refresh_token" => $this->refreshToken,
            ]);

            $this->setAccessToken($result->access_token, $result->expires_in);
            return $this->accessToken;
        }

        public GuzzleClient $guzzle;
        public function request(string $action, array $params) {
            $this->guzzle = new GuzzleClient([
                "base_uri" => $this->endpoint,
                'allow_redirects' => true,
            ]);

            try {
                $response = $this->guzzle->post(
                    $action, [
                    'headers' => [
                        'Authorization' => "Basic ".$this->authorizationBearer,
                    ],
                    'form_params' => $params,
                ]);
            } catch (ClientException $e) {
                $response = $e->getResponse();
            }
            return json_decode($response->getBody()->getContents());
        }

    }

?>