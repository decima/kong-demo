Kong Demo
==================

This platform is a proof of concept about using Kong with multiple services


## Getting started

### Start Kong

In order to get this project working, you will need php8 with Symfony CLI tool, and docker with docker-compose.

first launch kong:
```shell
docker-compose -f kong/docker-compose.yml up -d
```

This will start Kong on port 8000, and admin part on 8001. it will also start konga, an admin interface on port 1337.
On first access to Konga, il will ask you the url of kong admin just speficy :
```
http://kong:8001
```

### Start example Services
run the following command to start two services A & B

```shell
docker-compose -f kong/services.yml up -d

```

To test if the service is up, go to konga (http://localhost:1337) and add a new service which named serviceA,
and in the field Url, specify : http://serviceA/.
Save the input, then click on the freshly created service, go to routes, and add the following route : 
```
name: service_A
paths: /service_a (don't forget to press enter to add the path)
```
Then save the new route and you are ready to test on http://localhost:8000/service_a.
If it works, you should see something really similar : 
```
Name: serviceA
Hostname: 353feffe1d87
IP: 127.0.0.1
IP: 172.19.0.6
....
```