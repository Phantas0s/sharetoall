<?php
declare(strict_types=1);

namespace App\Tests\Service\Api;

use App\Service\Api\LinkedinApi;
use App\Tests\Service\Api\Client\FakeClient;
use TestTools\TestCase\UnitTestCase;

class LinkedinApiTest extends UnitTestCase
{

    public function testGetNetworkSlug()
    {
        $response = [
            'oauth_token=token',
            'oauth_token_secret=secret'
        ];
        $linkedinApi = $this->getLinkedinApi($response);
        $networkSlug = $linkedinApi->getNetworkSlug();

        $this->assertEquals('linkedin', $networkSlug);
    }

    private function getLinkedinApi(array $fakeClientResponse): LinkedinApi
    {
        return new LinkedinApi(
            $this->getContainer()->get('cache'),
            new FakeClient($fakeClientResponse),
            'dummyKey',
            'dummySecret'
        );
    }
}
