<?php

namespace App\Utils;

class Translator
{
    public static function addBracketToParameters(array $parameters = null): array
    {
        $newParams = [];

        if ($parameters) {
            foreach ($parameters as $key => $value) {
                if (!str_contains($key, '{{')) {
                    $key = "{{ $key }}";
                }
                $newParams[$key] = $value;
            }
        }

        return $newParams;
    }
}
