<?php
namespace App;

use GuzzleHttp\Psr7\Stream;


class MapPosition
{
    public $lat;
    public $long;
    public $coords;
    public $resources;
    public $confidence;

    public function __construct($mapData)
    {
        $this->resources = $mapData->resourceSets[0]->resources[0];
        $this->coords = $this->resources->point->coordinates;
        $this->lat = $this->coords[0];
        $this->long = $this->coords[1];
        $this->confidence = $this->resources->confidence;
    }
}
