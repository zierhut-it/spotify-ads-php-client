<?php

    namespace Spotify\Api\Marketing;

    trait ResourceFactory {

        public function newReport(): Report {
            return new Report($this);
        }

    }

?>