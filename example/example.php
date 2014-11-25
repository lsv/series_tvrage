<?php
use GuzzleHttp\Subscriber\Retry\RetrySubscriber;

require __DIR__ . '/../vendor/autoload.php';

$retry = new RetrySubscriber([
    'filter' => RetrySubscriber::createStatusFilter(),
    'delay'  => function () { return 1; },
    'max' => 5
]);

$client = new GuzzleHttp\Client();
$client->getEmitter()->attach($retry);

$search = new \Series\TVRage\Search($client);
$shows = $search->query('gotham');
foreach($shows as $show) {
    $episodes = new \Series\TVRage\Episodes($client);
    $episodes->getEpisodes($show);
    var_dump($show->getEpisodes());
    exit;
}