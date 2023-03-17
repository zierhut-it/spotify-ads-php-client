<?php

    namespace Spotify\Api\Marketing;

    trait IterableResult {

        public function getIterator(): \Traversable {
            return new \ArrayIterator($this->data);
        }
    }

?>