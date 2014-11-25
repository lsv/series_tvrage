<?php
namespace Series\TVRage;

use Series\Models\Show as ShowModel;

class Search extends AbstractCall
{

    /**
     * Search for a tv show
     * @param string $name
     * @return ShowModel[]
     */
    public function query($name)
    {
        $xml = $this->call('full_search', ['show' => $name]);
        return $this->parseShows($xml);
    }

}
