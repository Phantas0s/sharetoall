<?php
declare(strict_types=1);

namespace App\Service\Api;

use App\Exception\NotFoundException;
use App\Service\Api\LinkedinApi;
use App\Service\Api\TwitterApi;

class NetworkFactory
{
    public function __construct(
        TwitterApi $twitterApi,
        LinkedinApi $linkedinApi
    ) {
        $this->twitterApi = $twitterApi;
        $this->linkedinApi = $linkedinApi;
    }

    public function create(string $networkSlug)
    {
        $propertyName = $networkSlug . 'Api';

        if (!property_exists($this, $propertyName)) {
            throw new NotFoundException(sprintf('The class %s doesn\'t exists', $propertyName));
        }

        return $this->$propertyName;
    }
}
