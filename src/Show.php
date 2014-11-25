<?php
namespace Series\TVRage;

use Series\Models\Show as ShowModel;

class Show extends AbstractCall
{

    /**
     * Get show and episodes
     * @param integer $id
     * @return ShowModel
     */
    public function getShow($id)
    {
        $xml = $this->call('full_show_info', ['sid' => $id]);
        $show = $this->parseShow($xml, true);
        return $show;
    }

} 
