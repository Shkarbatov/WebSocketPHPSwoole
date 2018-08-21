<?php
go(function(){
    $cli = new Swoole\Coroutine\Http\Client("127.0.0.1", 9502);
    $cli->upgrade('/');
    $cli->push('{"command": "update_data", "user": "tester01"}');
    $cli->close();
});