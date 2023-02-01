<?php

    namespace Spotify\Api\Marketing;

    class Resource {

        protected Client $client;

        public function __construct(Client $client) {
            $this->client = $client;
        }

    }

?>