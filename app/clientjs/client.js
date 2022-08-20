const mqtt = require('mqtt')
const options = {"username": "rendy", "password": "password"}
const client = mqtt.connect('mqtt://192.168.160.1', options)

client.on('connect', function () {
    console.log("connected")
    client.subscribe('presence', function (err) {
        if (!err) {
            client.publish('presence', 'Hello mqtt')
        }
    })
})

client.on('message', function (topic, message) {
    // message is Buffer
    console.log("Hallo message", message.toString());
    // console.log(message.toString())
    // client.end()
})