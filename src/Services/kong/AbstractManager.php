<?php


namespace App\Services\kong;


use Symfony\Contracts\HttpClient\HttpClientInterface;

class AbstractManager
{
public function __construct(protected HttpClientInterface $client){

}
}