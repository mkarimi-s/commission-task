<?php

namespace CommissionTask\Service\DataAdapter\Traits;

use CommissionTask\Service\DataAdapter\Validator\MethodCheckValidator;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request as GuzzleRequest;

const ADAPTER_TIMEOUT = 25;

trait HttpAdapter
{
    private $baseUri;
    private $requestedUrl;
    private $client;
    private $headers = [];
    private $headersToPass = [];
    private $guzzleParamsToPass = [];

    public function makeRequest(string $method, string $route)
    {
        $this->setConfigs();
        $this->setClient();
        return $this->requester($method, $route);
    }

    public function requester(string $method, string $route)
    {
        $this->validateMethod($method);
        $route = $this->baseUri . $route;

        $requestData = new GuzzleRequest($method, $route, $this->headers);
        $response = $this->client->send($requestData);

        return $this->decorateContent($response->getHeaderLine('content-type'), $response->getBody()->getContents());
    }

    private function setConfigs(): void
    {
        $this->baseUri = 'https://developers.paysera.com/'; //TODO: read from env
        $this->validateConfigs();
    }

    private function setClient(): void
    {
        $this->client = new Client([
            'timeout' => ADAPTER_TIMEOUT,
            'http_errors' => false
        ]);
    }


    private function validateMethod(string $method): void
    {
        $validator = new MethodCheckValidator();
        $validator->validate($method);
    }

    private function validateConfigs(): void
    {
        if ($this->baseUri == null) {
            throw new \RuntimeException("base_url doesnt exist", 503);
        }
    }

    private function decorateContent(string $contentType, $content)
    {
        if (preg_match('/^(application\/json.*)/i', $contentType)) {
            $content = json_decode($content);
        }

        return $content;
    }

}