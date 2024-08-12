<?php

namespace App\Service;

use Exception;
use Http\Adapter\Curl\Client;
use Geocoder\StatefulGeocoder;
use Geocoder\Query\GeocodeQuery;
use Geocoder\Provider\Nominatim\Nominatim;
use Http\Client\Curl\Client as CurlClient;

class GeocoderService
{
    private $geocoder;
    public function __construct(
        private CurlClient $httpClient,
        private Nominatim $provider

        )
    {
        $this->provider = Nominatim::withOpenStreetMapServer($httpClient,'fr');
        $this->geocoder = new StatefulGeocoder($this->provider, 'fr');
    }

    public function geocodeAddress(string $address){
        $result = $this->geocoder->geocodeQuery(GeocodeQuery::create($address));
        if ($result->isEmpty()){
            throw new \Exception('adresse non trouvée');
        }
        return $result->first()->getCoordinates();
    }
}