<?php
namespace Series\TVRageTests;

use GuzzleHttp\Client;
use GuzzleHttp\Subscriber\Retry\RetrySubscriber;

abstract class AbstractTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var Client
     */
    static protected $client;

    public static function setUpBeforeClass()
    {
        $retry = new RetrySubscriber([
            'filter' => RetrySubscriber::createStatusFilter(),
            'delay'  => function () { return 1; },
            'max' => 5
        ]);

        $client = new Client();
        $client->getEmitter()->attach($retry);
        self::$client = $client;
    }

    protected function getClient()
    {
        return self::$client;
    }

}
 