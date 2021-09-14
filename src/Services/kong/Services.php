<?php


namespace App\Services\kong;


class Services extends AbstractManager
{

    public function create($name, $url)
    {
        return $this->client->request("POST", "/services", [
            "body" => ["name" => $name, "url" => $url],
        ])->toArray();
    }

    public function addACL($serviceId, $allow)
    {
        return $this->client->request("POST", "/services/$serviceId/plugins", [
            "body" => ["name" => "acl", "config.allow" => $allow, "config.hide_groups_header" => "true"],
        ])->toArray();

    }

    public function addRoute($serviceId, $path)
    {
        return $this->client->request("POST", "/services/$serviceId/routes", [
            "body" => ["paths[]" => $path],
        ])->toArray();
    }

    public function addAuth($serviceId)
    {
        switch ($this->authType) {
            case "key-auth":
                return $this->client->request("POST", "/services/$serviceId/plugins/", [
                    "body" => [
                        "name" => "key-auth",
                        "config.key_names" => "key",
                        "config.hide_credentials" => "true",
                    ],
                ])->toArray();
            case "basic-auth":
                $this->client->request("POST", "/services/$serviceId/plugins/", [
                    "body" => [
                        "name" => "basic-auth",
                        "config.hide_credentials" => "true",
                    ],
                ])->toArray();
            case "oauth2":
                $this->client->request("POST", "/services/$serviceId/plugins/", [
                    "body" => [
                        "name" => "oauth2",
                        "config.mandatory_scope" => "false",
                        "config.token_expiration" => 7200,
                        "config.enable_client_credentials" => "true",

                    ],
                ])->toArray();
        }

    }
}