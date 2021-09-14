<?php


namespace App\Services\kong;


use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class Kong
{
    private Consumers $consumers;
    private Services $services;
    private HttpClientInterface $client;
    public function __construct(
        private string $kongAdminUrl,
        private string $kongPublicUrl,
        private string $demoInternalUrl,
        public string $securityType,
    )
    {

        $this->client = HttpClient::createForBaseUri($this->kongAdminUrl);
        $this->consumers = new Consumers($this->client, $this->securityType);
        $this->services = new Services($this->client,$this->securityType);
    }

    public function getConsumerManager(): Consumers
    {
        return $this->consumers;
    }

    public function getServiceManager(): Services
    {
        return $this->services;
    }

    public function getPublicUrl(): string
    {
        return $this->kongPublicUrl;
    }

    public function installLogger(){
        return $this->client->request("POST", "/plugins", [
            "body" => [
                "name" => "http-log",
                "config.http_endpoint"=>$this->demoInternalUrl."usage",
                "config.method" => "POST",
                "config.timeout"=>1000,
                "config.keepalive"=>1000,
                "config.flush_timeout"=>2,
                "config.retry_count"=>15,
            ],
        ])->toArray();

    }

}