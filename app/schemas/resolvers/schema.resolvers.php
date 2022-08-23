<?php

use RedBeanPHP\R;

R::setup('sqlite:'.__DIR__.'/../../databases/db.sqlite');

$roomByName = function ($name) {
    return R::findOne('room', 'name = ?', [$name]);
};

$roomType = [
    'messages' => function ($room) {
        return R::findAll('message', 'room_id = ?', [$room['id']]);
    },
];

$queryType = [
    'rooms' => function () {
        return  R::findAll('room')
        ;
    },
    'messages' => function ($root, $args) use ($roomByName) {
        $roomName = $args['roomName'];
        $room = $roomByName($roomName);
        $messages = R::find('message', 'room_id = ?', [$room['id']]);

        return $messages;
    },
    'peoples' => function(){
        return R::findAll('people');
    }
];

$mutationType = [
    'start' => function ($root, $args) {
        $roomName = array_key_exists("roomName", $args)?$args['roomName']:'no room name';
        $room = R::dispense('room');
        $room['name'] = $roomName;

        R::store($room);

        return $room;
    },
    'users' => function ($root, $args){
        $peopleName = array_key_exists("name", $args)? $args['name']: 'no name';
        $peoplePhone = array_key_exists("phone", $args)? $args['phone']: 'no name';
        $people = R::dispense('people');
        $people['name'] = $peopleName;
        $people['phone'] = $peoplePhone;
        R::store($people);
        return $people;
    },
    'chat' => function ($root, $args) use ($roomByName) {
        $roomName = $args['roomName'];
        $body = $args['body'];

        $room = $roomByName($roomName);

        $message = R::dispense('message');
        $message['roomId'] = $room['id'];
        $message['body'] = $body;
        $message['timestamp'] = new \DateTime();

        R::store($message);

        return $message;
    },
];

return [
    'Room'     => $roomType,
    'Query'    => $queryType,
    'Mutation' => $mutationType,
];