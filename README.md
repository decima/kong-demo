Kong Demo
==================

This platform is a proof of concept about using Kong with multiple services


## Getting started

### Start Kong

In order to get this project working node with yarn for the front, and docker with docker-compose.

first launch kong:
```shell
docker-compose -f kong/docker-compose.yml up -d
```

This will start Kong on port 8000, and admin part on 8001. it will also start konga, an admin interface on port 1338.
On first access to Konga, il will ask you the url of kong admin just speficy :
```
http://kong:8001
```

### Start example Services
run the following command to start two services A & B

```shell
docker-compose -f kong/services.yml up -d

```

To test if the service is up, go to konga (http://localhost:1338) and add a new service which named myService,
and in the field Url, specify : http://serviceA/.
Save the input, then click on the freshly created service, go to routes, and add the following route : 
```
name: my_service
paths: /my_service (don't forget to press enter to add the path)
```
Then save the new route and you are ready to test on http://localhost:8000/my_service.
If it works, you should see something really similar : 
```
Name: serviceA
Hostname: 353feffe1d87
IP: 127.0.0.1
IP: 172.19.0.6
....
```

two services are existing : serviceA & serviceB. Both are containous/whoami which are documented on the [official repo](https://github.com/traefik/whoami).


### Start the project

Now that we have all started the config, we can start the symfony project. You will also need yarn in order to work with the project, but npm should be also enough.
If you have changed something on port configurations of kong, duplicate the .env file in the project into a .env.local file and edit it, this file won't be commited.

Install frontend and backend dependencies : 
```
yarn
docker-compose -f kong/docker-compose.yml exec demo composer install  
```
Then install the default kong config : 
```
docker-compose -f kong/docker-compose.yml exec demo bin/console install
```

This install command create 2 services in kong : serviceA and serviceB which should be accessible through localhost:8000/service_a and localhost:8000/service_b

Run the following commands to start the frontend on dev-server : 
```shell
yarn dev-server
```



once yarn is running and build is successful, you can use the project on port 8002 (http://localhost:8002).