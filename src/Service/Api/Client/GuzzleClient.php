<?php
declare(strict_types=1);

namespace App\Service\Api\Client;

use GuzzleHttp\Client;

class GuzzleClient implements ClientInterface
{
    /**
     * To get the client, always use getClient() method
     * @var GuzzleHttp
     */
    private $client;

    public function post(string $url, array $header = [], array $body = []): Response
    {
        $this->getClient();
        $options = [];

        if (!empty($header)) {
            $options['headers'] = $header;
        }

        if (!empty($body)) {
            $options['form_params'] = $body;
        }

        $response = $this->client->request('POST', $url, $options);

        $response = new Response(
            $response->getStatusCode(),
            $response->getHeaders(),
            $response->getBody()->getContents()
        );

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
