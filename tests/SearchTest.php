<?php
namespace Series\TVRageTests;

use Series\TVRage\Search;

class SearchTest extends AbstractTest
{

    public function test_can_do_search()
    {
        $searcher = new Search($this->getClient());
        $shows = $searcher->query('buffy');
        $this->assertGreaterThanOrEqual(3, count($shows));
    }

    public function test_can_search_for_no_shows()
    {
        $searcher = new Search($this->getClient());
        $shows = $searcher->query('asdffdsfsdfsf');
        $this->assertEquals(0, count($shows));
    }

}
 