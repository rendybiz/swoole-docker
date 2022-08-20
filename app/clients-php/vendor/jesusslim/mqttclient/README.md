# jesusslim/mqttclient

PHP mqtt client

## usage

[English]
[Chinese](README_CN.md)

### Install

Install:

	composer require jesusslim/mqttclient

If your composer not allowed dev-master,add this config

	"minimum-stability": "dev"
	
into your composer.json.

### Require

	swoole 2.0.8+(swoole.so)
    mosquitto.so

### Example

There's MqttClient we can use.One is based on swoole(see swoole example,deprecated),another is based on mosquitto(see mosquitto example).

The difference:
* Requirements difference.
* Swoole has package-separation bug and crash bug.We should handle this exception,for example,monitor the process id and restart when is crash or receive error package.
* Swoole client can subscribe and publish in one client,use mosquitto we need two client.
* Some grammar difference between swoole and mosquitto.

#### Example base on mosquitoo

define your logger:

    class Logger implements \mqttclient\src\swoole\MqttLogInterface {

		public function log($type,$content,$params = []){
		        echo "$type : $content \r\n";
		 }
	}

use Mqttclient

    $host = '127.0.0.1';
	$port = 1883;
    $r = new \mqttclient\src\mosquitto\MqttClient($host,$port,10017);
    $r->setAuth('username','password');
    $r->setKeepAlive(60);
    $r->setLogger(new Logger());
    $r->setMaxReconnectTimesWhenError(360*12);
    //reconnect interval
    $r->setReconnectInterval(10);
    //subscribe topics,callback's params can be any data we mapped into the container(IOC)
    $r->setTopics(
    [
        new \mqttclient\src\subscribe\Topic('test/slim',function($msg){
            echo "I receive:".$msg."\r\n";}),
        new \mqttclient\src\subscribe\Topic('test/slim3',function(\mqttclient\src\swoole\MqttClient $client,$msg){
            echo "I receive:".$msg." for slim3 \r\n";
            echo $client->getClientId();
        })
    ]
    );
    //set trigger
    $r->on(\mqttclient\src\consts\ClientTriggers::SOCKET_CONNECT,function(){
        //do something
    });
    $r->start();
    
Sender:

    $host = '127.0.0.1';
    $port = 1883;
    $r = new \mqttclient\src\mosquitto\MqttSender($host,$port,10017);
    $r->setAuth('username','password');
    $r->setKeepAlive(60);
    $r->setLogger(new Logger());
    $r->setMaxReconnectTimesWhenError(360*12);
    //reconnect interval
    $r->setReconnectInterval(10);
    $r->setQueue(new Queue());
    $r->start();

It need a queue implements mqttclient\src\mosquitto\MqttSendingQueue,to get msg that need to be sent in loop.

#### Example base on swoole(deprecated)

define your logger:

    class Logger implements \mqttclient\src\swoole\MqttLogInterface {

		public function log($type,$content,$params = []){
		        echo "$type : $content \r\n";
		 }
	}

define your tmp store (use Redis/Memory/...)

	class Store implements \mqttclient\src\swoole\TmpStorageInterface{

    	private $data = [];

	    public function set($message_type, $key, $sub_key, $data, $expire = 3600)
	    {
	        $this->data[$message_type][$key][$sub_key] = $data;
	    }
	
	    public function get($message_type, $key, $sub_key)
	    {
	        return $this->data[$message_type][$key][$sub_key];
	    }
	
	    public function delete($message_type, $key, $sub_key)
	    {
	        if (!isset($this->data[$message_type][$key][$sub_key])){
	            echo "storage not found:$message_type $key $sub_key";
	        }
	        unset($this->data[$message_type][$key][$sub_key]);
	    }

	}

use MqttClient

	$host = '127.0.0.1';
	$port = 1883;

	$r = new \mqttclient\src\swoole\MqttClient($host,$port,10017);
	$r->setAuth('username','password');
	$r->setKeepAlive(60);
	$r->setLogger(new Logger());
	$r->setStore(new Store());
    //dns lookup
    $r->setDnsLookup(true);
    //buffer size
    $r->setSocketBufferSize(1024*1024*5);
    //reconnect times when error
    $r->setMaxReconnectTimesWhenError(360*12);
    //reconnect interval
    $r->setReconnectInterval(10000);
    //subscribe topics,callback's params can be any data we mapped into the container(IOC)
	$r->setTopics(
    [
        new \mqttclient\src\subscribe\Topic('test/slim',function($msg){
            echo "I receive:".$msg."\r\n";}),
        new \mqttclient\src\subscribe\Topic('test/slim3',function(\mqttclient\src\swoole\MqttClient $client,$msg){
            echo "I receive:".$msg." for slim3 \r\n";
            echo $client->getClientId();
        })
    ]
	);
	
	//set trigger
	$r->on(\mqttclient\src\consts\ClientTriggers::RECEIVE_SUBACK,function(\mqttclient\src\swoole\MqttClient $client){
    	$client->publish('slim/echo','GGXX',\mqttclient\src\consts\Qos::ONE_TIME);
    });
	
	$r->connect();
	$r->publish('test/slim','test qos',2);
	
### Extends

You can also use own client extends MqttClient.

Example:

	class Client extends MqttClient
	{
	    private $mysql_handler;
	    private $mongo_handler;
	
	    public function __construct($host,$port,$client_id,$mysql_conf,$mongo_conf)
	    {
	        $this->mysql_handler = new Mysqli($mysql_conf);
	        $this->mongo_handler = new \MongoClient('mongodb://'.$mongo_conf['username'].':'.$mongo_conf['password'].'@'.$mongo_conf['host'].':'.$mongo_conf['port'].'/'.$mongo_conf['db']);
	        parent::__construct($host,$port,$client_id);
	    }
	
		 /**
	     * override the produceContainer function and map your own class/data/closure to the injector,and they can be used in every publish receive handler
	     * for exp: $client->setTopics([new Topic('test/own',function($mongo,$msg){ $result = $mongo->selectCollection('log_platform','test')->find(['sid' => ['$gte' => intval($msg)]]); })]);
	     * @return Injector
	     */
	    protected function produceContainer()
	    {
	        $container = new Injector();
	        $container->mapData(MqttClient::class,$this);
	        $container->mapData(Client::class,$this);
	        $container->mapData('mysqli',$this->mysql_handler);
	        $container->mapData('mongo',$this->mongo_handler);
	        return $container;
	    }
	
	}

