# Docker Image for OpenSwoole + Laravel Octane
updated by Rendy
Linked In : https://www.linkedin.com/in/rendybiz/

Swoole Version  : 4.11.1
PHP Version     : 8.1
Composer Version: 2.1.6
Websocket       : ON

###How to get started
up your docker compose
``` sudo docker-compose up ``` or 
``` sudo docker-compose up -d ``` instead

In the container, it will run the laravel octane in port 9502 with host 0.0.0.0. You can take a look in the **Dockerfile** file

in our docker-compose.yml you will find out that port 9502 will be mapped to port 81. Modify it as necessary.

### Navigation
by default, you can try to use Websocket URL ws://127.0.0.1:82
you can test it using Simple Websocket client from Chrome extension to test