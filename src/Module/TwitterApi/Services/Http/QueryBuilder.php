<?php
declare(strict_types=1);

namespace App\Module\TwitterApi\Services\Http;

class QueryBuilder
{
    /**
     * Append parameters to a base url
     */
    public function createUrl(string $baseUrl, array $parameters): string
    {
        $queryParameters = $this->createUrlParameters($parameters);
        return $baseUrl . '?' . $queryParameters;
    }

    /**
     * Create correctly formated url parameters from an array
     */
    public function createUrlParameters(array $parameters): string
    {
        $queryVariables = [];

        foreach ($parameters as $key => $parameter) {
            $queryVariables[] = $key . '=' . rawurlencode($parameter);
        }

        return implode('&', $queryVariables);
    }
}
