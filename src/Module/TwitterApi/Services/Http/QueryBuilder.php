<?php
declare(strict_types=1);

namespace App\Module\TwitterApi\Services\Http;

/**
 * Class QueryBuilder
 */
class QueryBuilder
{
    public function createUrl(string $baseUrl, array $parameters): string
    {
        $queryParameters = $this->createUrlParameters($parameters);
        return $baseUrl . '?' . $queryParameters;
    }

    public function createUrlParameters(array $parameters): string
    {
        $queryVariables = [];

        foreach ($parameters as $key => $parameter) {
            $queryVariables[] = $key . '=' . rawurlencode($parameter);
        }

        return implode('&', $queryVariables);
    }
}
