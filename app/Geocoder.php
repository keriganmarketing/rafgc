<?php
namespace App;

use App\Listing;
use GuzzleHttp\Client;

class Geocoder
{
    private $listing;
    private $remoteConnection;
    private $url;

    public function __construct(Listing $listing)
    {
        $this->listing = $listing;
        $this->remoteConnection = new Client([
            'base_uri' => 'http://dev.virtualearth.net/REST/v1/Locations/US/FL/'
            ]);
        $this->url = $this->encodedUrl();
    }

    public function getCoordinates()
    {
        $response = $this->remoteConnection->get($this->url);

        return new MapPosition(json_decode($response->getBody()));
    }

    private function encodedUrl()
    {
        $city = urlencode($this->listing->city);
        $address = urlencode($this->listing->street_num . ' ' . $this->listing->street_name);

        return ($city . '/' . $address . '?key=' . env('BING_MAPS_KEY'));
    }
}
