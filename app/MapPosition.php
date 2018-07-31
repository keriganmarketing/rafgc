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
        $this->resources  = $mapData->resourceSets[0]->resources[0] ?? null;
        $this->coords     = $this->resources->point->coordinates ?? null;
        $this->lat        = $this->coords[0] ?? null;
        $this->long       = $this->coords[1] ?? null;
        $this->confidence = $this->resources->confidence ?? null;
    }
}
