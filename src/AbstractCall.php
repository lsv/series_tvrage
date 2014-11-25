<?php
namespace Series\TVRage;

use GuzzleHttp\Client;
use Series\Models\Episode as EpisodeModel;
use Series\Models\Genre as GenreModel;
use Series\Models\Link as LinkModel;
use Series\Models\Show as ShowModel;

abstract class AbstractCall
{

    const URL = 'http://services.tvrage.com/feeds/%s.php';

    const LINK_NAME = 'tvrage.com';

    /**
     * @var Client
     */
    private $client;

    /**
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @param string $url
     * @param array $query
     * @return \SimpleXMLElement
     */
    protected function call($url, array $query = array())
    {
        $url = sprintf(self::URL, $url);
        $response = $this->client->get($url, [
            'query' => $query
        ]);
        return $response->xml();
    }

    /**
     * @param string $datestring
     * @param string $format
     * @param string $splitter
     * @return \DateTime|null
     */
    protected function parseDate($datestring, $format = 'M/d/Y', $splitter = '/')
    {
        if ($datestring === 0) {
            return null;
        }

        if (strpos($datestring, $splitter) === false) {
            // Only year
            $date = date_create_from_format('Y', $datestring);
        } else {
            $date = date_create_from_format($format, $datestring);
        }

        if (! $date) {
            return null;
        }

        return $date;

    }

    /**
     * @param \SimpleXMLElement $xml
     * @param bool $parseEpisodes
     * @throws \Exception
     * @return ShowModel
     */
    protected function parseShow(\SimpleXMLElement $xml, $parseEpisodes = false)
    {
        $show = new ShowModel();
        $show
            ->setId((int) $xml->showid)
            ->setName((string) $xml->name)
            ->setCountry((string) $xml->country)
            ->setNumberOfSeasons((int) $xml->totalseasons)
            ->setStartedDate($this->parseDate((string) $xml->started))
            ->setEndedDate($this->parseDate((string) $xml->ended))
            ->setRunning($this->parseDate((string) $xml->ended) === null)
        ;

        $link = new LinkModel();
        $show->addLink($link
                ->setName(AbstractCall::LINK_NAME)
                ->setUrl((string) $xml->showlink)
        );

        $genres = [];
        foreach($xml->genres->genre as $genre) {
            $g = new GenreModel();
            $g->setGenre((string) $genre);
            $genres[] = $g;
        }

        $show->setGenres($genres);

        if ($parseEpisodes) {
            $show->setEpisodes($this->parseEpisodes($xml));
        }

        return $show;
    }

    /**
     * @param \SimpleXMLElement $xml
     * @return ShowModel[]
     * @throws \Exception
     */
    protected function parseShows(\SimpleXMLElement $xml)
    {
        $shows = [];
        foreach($xml->show as $q) {
            /** @var \SimpleXMLElement $q */
            $shows[] = $this->parseShow($q);
        }
        return $shows;
    }

    /**
     * @param \SimpleXMLElement $xml
     * @return EpisodeModel[]
     */
    protected function parseEpisodes(\SimpleXMLElement $xml)
    {
        $episodes = [];
        foreach($xml->Episodelist->Season as $season) {
            /** @var \SimpleXMLElement $season */
            $attr = $season->attributes();
            $seasonNumber = $attr['no'];
            foreach($season->episode as $episode) {
                /** @var \SimpleXMLElement $episode */
                $ep = new EpisodeModel();
                $ep
                    ->setSeason((int)$seasonNumber)
                    ->setEpisodenumber((int)$episode->seasonnum)
                    ->setName((string)$episode->title)
                    ->setAirdate($this->parseDate((string)$episode->airdate, 'Y-m-d', '-'))
                ;

                $link = new LinkModel();
                $link
                    ->setName(AbstractCall::LINK_NAME)
                    ->setUrl((string)$episode->link)
                ;

                $ep->addLink($link);
                $episodes[] = $ep;
            }
        }

        return $episodes;
    }

}
