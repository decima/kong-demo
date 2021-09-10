<?php


namespace App\Services\kong;


class Consumers extends AbstractManager
{

    public function createConsumer($companyName)
    {
        return $this->client->request("POST", "/consumers", [
            "body" => ['custom_id' => $companyName,],
        ])->toArray();
    }

    public function createCredentials(string $name, string $type, string $kongId)
    {
        $body = [];
        $fieldsToExtract = ["key"];
        switch ($type) {
            case "basic-auth":
                $body = ["username" => md5($name . uniqid()), "password" => md5(uniqid())];
                $fieldsToExtract = ["username", "password"];
                break;
            case "oauth2":
                $body = ["name" => $name . time(), "redirect_uris" => "https://localhost/null", "hash_secret" => "false",];
                $fieldsToExtract = ["client_id", "client_secret"];
                break;
        }

        $e = $this->client->request("POST", "/consumers/$kongId/$type", [
            "body" => $body,

        ])->toArray(false);
        $res = ["id" => $e["id"]];
        foreach ($fieldsToExtract as $field) {
            $res[$field] = $e[$field];
        }
        return $res;

    }

    public function dropCredentials(string $consumerId, string $kongId, string $type):bool
    {
        return $this->client->request("DELETE", "/consumers/$consumerId/$type/$kongId")->getStatusCode()==204;
    }

}