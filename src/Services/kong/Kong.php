<?php


namespace App\Services\kong;


use Symfony\Component\HttpClient\HttpClient;

class Kong
{
    private Consumers $consumers;

    public function __construct(
        private string $kongAdminUrl,
        public string $kongPublicUrl,
    )
    {

        $client = HttpClient::createForBaseUri($this->kongAdminUrl);
        $this->consumers = new Consumers($client);
    }

    public function getConsumerManager(): Consumers
    {
        return $this->consumers;
    }
}