<?php
namespace App;

use App\Listing;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

class Geocoder
{
    private $url;
    private $listing;
    private $remoteConnection;

    public function __construct(Listing $listing)
    {
        $this->listing = $listing;
        $this->remoteConnection = new Client([
            'base_uri' => 'http://dev.virtualearth.net/REST/v1/Locations/US/FL/'
            ]);
        $this->url = $this->structuredUrl();
    }

    public function getCoordinates()
    {
        try {
            $response = $this->remoteConnection->get($this->url);
        } catch (ClientException $e) {
            // Fallback to try a different query to see if we can find the address
            $response = $this->performFallbackQuery();
        }

        return new MapPosition(json_decode($response->getBody()));
    }

    protected function performFallbackQuery()
    {
        $this->remoteConnection = new Client([
            'base_uri' => 'http://dev.virtualearth.net/REST/v1/Locations?'
            ]);
        $this->url = $this->unstructuredUrl();

        return $this->remoteConnection->get($this->url);
    }

    private function structuredUrl()
    {
        $city = urlencode($this->listing->city);
        $address = urlencode((int) $this->listing->street_num . ' ' . $this->listing->street_name);

        return ($city . '/' . $address . '?key=' . env('BING_MAPS_KEY'));
    }

    private function unstructuredUrl()
    {
        $postalCode = urlencode((int) $this->listing->zip);
        $address = urlencode((int) $this->listing->street_num . ' ' . $this->listing->street_name);

        return ('?postalCode='. $postalCode . '&addressLine=' . $address . '&key=' . env('BING_MAPS_KEY'));
    }
}
