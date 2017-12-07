<?php

namespace JrpcMock\Json\Server;

use JRpc\Json\Server\Server as BaseJRpcServer;

class Server extends BaseJRpcServer
{
    public function readInput()
    {
        return file_get_contents(__DIR__ . '/../../../../tests/_files/input.data');
    }
}
