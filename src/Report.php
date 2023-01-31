<?php

    # https://developer.spotify.com/documentation/ads-api/reference/#/operations/get_report

    namespace Spotify\Api\Marketing;

    trait Report {

        public static function aa(){
            return "aa";
        }

        public function createReport(string $adAccountId): array {
            $results = [];
            $continuationToken = null;
            do {
                $result = $this->api("report", [
                    "dimensions" => [
                        "CAMPAIGN",
                        "AD_SET"
                    ],
                    "fields" => [
                        "CLICKS",
                        "IMPRESSIONS",
                        "CTR",
                        "SPEND",
                    ],
                    "ad_account_ids" => [
                        $adAccountId,
                    ],
                    "page_size" => 500,
                    "continuation_token" => $continuationToken,
                ]);
                $continuationToken = $result->continuation_token ?? null;
                if(!isset($result->page)) throw new \Exception(json_encode($result, JSON_PRETTY_PRINT));
                $newResults = array_map(function($result) {
                    //echo json_encode($result, JSON_PRETTY_PRINT);exit;
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
                $results = array_merge($results, $newResults);
            } while(!is_null($continuationToken));

            echo json_encode($results, JSON_PRETTY_PRINT);exit;

            return $results;
        }

    }

?>