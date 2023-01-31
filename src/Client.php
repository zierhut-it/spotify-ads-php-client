<?php

    namespace Spotify\Api\Marketing;

    class Client {

        use Report;
        use Request;

        public Authorization $auth;

        public function __construct(
            string $clientId,
            string $clientSecret,
            ?string $redirectUrl = null,
            ?string $refreshToken = null,
        ) {
            $this->auth = new Authorization();
            $this->auth->setAuth($clientId, $clientSecret);

            if(is_null($redirectUrl)) $redirectUrl = "http://localhost";
            $this->auth->setRedirectUrl($redirectUrl);

            if(!is_null($refreshToken)) {
                $this->auth->setRefreshToken($refreshToken);
            }
        }

    }

?>