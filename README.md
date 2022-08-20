# Introduction

This server is build for MQTT Server, 

under app folder we have clientjs(nodejs based) and clients-php

## Clients, try to publish and subscribe to our MQTT Server

### Clientjs
``` cd clientjs ```
``` yarn start ```

### Clients-php
- #### Using Workerman (Non-Swoole / without coroutine) 
  - ```cd clients-php```
  - ```php client.php start```

- #### Using Swoole coroutine
  - ```cd clients-php```
  - ```php client-swoole.php```

You can test it