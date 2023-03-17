<?php

    # https://developer.spotify.com/documentation/ads-api/reference/#/operations/get_report

    namespace Spotify\Api\Marketing;

    class Report extends Resource implements \IteratorAggregate {

        use IterableResult;

        public array $fields = [];
        public function addField(string|array $fields): Report {
            if(!is_array($fields)) $fields = [$fields, ...func_get_args()];
            $this->fields = array_merge($this->fields, $fields);
            $this->fields = array_unique($this->fields);
            $this->fields = array_values($this->fields);
            return $this;
        }

        public array $dimensions = [];
        public function addDimension(string|array $dimensions): Report {
            if(!is_array($dimensions)) $dimensions = [$dimensions, ...func_get_args()];
            $this->dimensions = array_merge($this->dimensions, $dimensions);
            $this->dimensions = array_unique($this->dimensions);
            $this->dimensions = array_values($this->dimensions);
            return $this;
        }

        public array $adAccountIds = [];
        public function addAdAccountId(string|array $adAccountIds): Report {
            if(!is_array($adAccountIds)) $adAccountIds = [$adAccountIds, ...func_get_args()];
            $this->adAccountIds = array_merge($this->adAccountIds, $adAccountIds);
            $this->adAccountIds = array_unique($this->adAccountIds);
            $this->adAccountIds = array_values($this->adAccountIds);
            return $this;
        }

        public int $pageSize = 500;

        public array $data = [];

        public function run(): array {
            $continuationToken = null;

            do {
                $result = $this->client->api(
                    "POST", "report",
                    $this->payload($continuationToken),
                );

                $continuationToken = $result->continuation_token ?? null;
                if(!isset($result->page)) throw new \Exception(json_encode($result, JSON_PRETTY_PRINT));

                $newResults = array_map(function($result) {
                    $flatResult = new \stdClass;
                    foreach($result->dimensions as $dimension) {
                        // Extract dimension value name
                        $dimensionValueName = array_keys(get_object_vars($dimension))[1];
                        $type = strtolower($dimension->type);
                        foreach($dimension->{$dimensionValueName} as $key => $value) {
                            $flatResult->{"{$type}_$key"} = $value;
                        }

                        foreach($result->fields as $field) {
                            $fieldType = strtolower($field->type);
                            $flatResult->{$fieldType} = $field->value;
                        }
                    }
                    return $flatResult;
                }, $result->page->rows);

                $this->data = array_merge($this->data, $newResults);
            } while(!is_null($continuationToken));

            return $this->data;
        }

        private function payload(?string $continuationToken): array {
            $payload = [];

            if(!is_null($continuationToken)) {
                $payload["continuation_token"] = $continuationToken;
            }

            if(empty($this->fields)) {
                throw new \Exception("Every Report needs at least one field.");
            }
            $payload["fields"] = $this->fields;

            if(empty($this->dimensions)) {
                throw new \Exception("Every Report needs at least one dimension.");
            }
            $payload["dimensions"] = $this->dimensions;

            if(!empty($this->adAccountIds)) {
                $payload["ad_account_ids"] = $this->adAccountIds;
            }

            $payload["page_size"] = $this->pageSize;

            return $payload;
        }

    }

?>