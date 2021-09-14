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

    public function modifyHeaderWithCompanyName($kongId, $companyName, bool $isAdmin)
    {
        $body = [
            "name" => "request-transformer",


        ];
        if ($isAdmin) {
            $body = array_merge($body, [
                "config.add.headers[1]" => "OriginalCompany: $companyName",
                "config.add.headers[2]" => "Role: ADMIN",
                "config.add.headers[3]" => "Company: $companyName",

            ]);
        } else {
            $body = array_merge($body, [
                "config.add.headers[1]" => "Company: $companyName",
                "config.add.headers[2]" => "Role: USER",
                "config.replace.headers" => "Company: $companyName",
            ]);
        }

        return $this->client->request("POST", "/consumers/$kongId/plugins", [
            "body" => $body,

        ])->toArray();

    }

    public function addGroup($kongId, $group)
    {
        return $this->client->request("POST", "/consumers/$kongId/acls", [
            "body" => ["group" => $group],

        ])->toArray();
    }

    public function removeGroup($kongId, $group)
    {
        $acls = $this->client->request("GET", "/consumers/$kongId/acls")->toArray();

        foreach ($acls["data"] as $acl) {
            if ($acl["group"] === $group) {
                $this->client->request("DELETE", "/consumers/$kongId/acls/" . $acl["id"]);
                return;
            }
        }

    }

    public function createCredentials(string $name, string $kongId)
    {
        $type = $this->authType;
        $body = [];
        $fieldsToExtract = ["key"];
        switch ($this->authType) {
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

    public function dropCredentials(string $consumerId, string $kongId, string $type): bool
    {
        return $this->client->request("DELETE", "/consumers/$consumerId/$type/$kongId")->getStatusCode() == 204;
    }

}