<?php
class Redis{
    function config(){
        $client = new Predis\Client([
            'scheme' => 'tcp',
            'host'   => 'localhost',
            'port'   => 6379,
            'database' => 1 // default is 0 you can put 0-15
        ]);

        return $client;
    }

}