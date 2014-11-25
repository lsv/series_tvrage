<?php
namespace Series\TVRageTests;

use Series\Models\Episode;
use Series\TVRage\Show;

class ShowTest extends AbstractTest
{

    /**
     * @var Show
     */
    static private $show;

    static public function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
        self::$show = new Show(self::$client);
    }

    public function showDataProvider()
    {
        return [
            // Buffy the Vampire Slayer
            [2930, 'getName', 'Buffy the Vampire Slayer', false, false],
            [2930, 'getNumberOfSeasons', 7, false, false],
            [2930, 'getId', 2930, false, false],
            [2930, 'getStartedDate', '1997-03-10', true, false],
            [2930, 'getEndedDate', '2003-05-20', true, false],
            [2930, 'getGenres', '7', false, true],
            [2930, 'isRunning', false, false, false],
            [2930, 'getEpisodes', 143, false, true],
            // Another?
            [38049, 'getEndedDate', null, false, false],
            [38049, 'isRunning', true, false, false]
        ];
    }

    public function episodeDataProvider()
    {
        return [
            // Buffy the Vampire Slayer
            [2930, 'getAirdate', '2003-05-20', true, false],
            [2930, 'getSeason', 7, false, false],
            [2930, 'getEpisodenumber', 22, false, false],
            [2930, 'getName', 'Chosen', false, false]
            // Another?
        ];
    }

    /**
     * @dataProvider showDataProvider
     *
     * @param int $id
     * @param string $method
     * @param mixed $expected
     * @param bool $isDate
     * @param bool $isCount
     */
    public function test_show($id, $method, $expected, $isDate, $isCount)
    {
        $show = self::$show->getShow($id);
        $this->assertInstanceOf('Series\Models\Show', $show);
        $this->doTest($show->{$method}(), $expected, $isDate, $isCount);
    }

    /**
     * @dataProvider episodeDataProvider
     *
     * @param int $id
     * @param string $method
     * @param mixed $expected
     * @param bool $isDate
     * @param bool $isCount
     */
    public function test_episode($id, $method, $expected, $isDate, $isCount)
    {
        $episodes = self::$show->getShow($id)->getEpisodes();
        /** @var Episode $episode */
        $episode = $episodes->last();
        $this->doTest($episode->{$method}(), $expected, $isDate, $isCount);
    }

    /**
     * @param mixed $data
     * @param mixed $expected
     * @param bool $isDate
     * @param bool $isCount
     */
    private function doTest($data, $expected, $isDate, $isCount)
    {
        if ($isDate) {
            $this->assertEquals($expected, $data->format('Y-m-d'));
        } elseif ($isCount) {
            $this->assertEquals($expected, count($data));
        } else {
            $this->assertEquals($expected, $data);
        }
    }

}
 