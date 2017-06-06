<?php
declare(strict_types=1);

namespace App\Module\TwitterApi\Services\Http;

use GuzzleHttp\Client;
use App\Module\TwitterApi\Services\Http\ClientInterface;

class GuzzleClient implements ClientInterface
{
    private $username;
    private $password;
    private $url;
    private $baseUrl;

    /**
     * To get the client, always use getClient() method
     * @var GuzzleHttp
     */
    private $client;

    public function callPost(string $url, array $headers, string $content = '')
    {
        $this->getClient();

        $response = $this->client->request('POST', $url, $headers);
        $response = $response->getBody();

        return $response;
    }

    private function getClient()
    {
        if (null !== $this->client) {
            return $this->client();
        }

        $this->client = new Client();
    }
}
