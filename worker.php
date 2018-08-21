<?php

/** @var array - subscribers */
$subscribedTopics = array();

$server = new swoole_websocket_server("0.0.0.0", 9502, SWOOLE_BASE);

$server->on("start", function ($server) {
    echo "Swoole http server is started at http://127.0.0.1:9502\n";
});

$server->on('connect', function($server, $req) {
    echo "connect: {$req}\n";
});

$server->on('open', function($server, $req) {
    echo "connection open: {$req->fd}\n";
});

$server->on('message', function($server, $frame) use (&$subscribedTopics) {

    if (
        $message = json_decode($frame->data, true)
        and isset($message['command'])
        and $message['command'] == 'update_data'
        and isset($subscribedTopics[$message['user']])
    ) {
        $subscribedTopics[$message['user']]['server']
            ->push($subscribedTopics[$message['user']]['frame']->fd, $frame->data);

    } else {
        $subscribedTopics[$frame->data] = ['frame' => $frame, 'server' => $server];
    }
});

$server->on('close', function($server, $fd) {
    echo "connection close: {$fd}\n";
});

$server->start();