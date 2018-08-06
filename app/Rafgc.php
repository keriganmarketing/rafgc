<?php
namespace App;

use PHRETS\Configuration;
use PHRETS\Session;
use Illuminate\Support\Facades\DB;
use Laravel\Tinker\Console\TinkerCommand;


class Rafgc
{
    const URL = 'http://rets.rafgc.net/Login.aspx';
    const CLASSES = ['CIB', 'DOC', 'LND', 'RES'];
    const QUERY_OPTIONS =  [
        'QueryType'     => 'DMQL2',
        'Count'         => 1, // count and records
        'Format'        => 'COMPACT-DECODED',
        'Limit'         => 99999999,
        'StandardNames' => 0, // give system names
    ];

    public $rets;
    public $class;
    public $username;
    public $password;

    public function __construct()
    {
        $this->username = config('rafgc.username');
        $this->password = config('rafgc.password');
    }

    public function connect()
    {
        $config = new Configuration();
        $config->setLoginUrl(self::URL)
            ->setUsername($this->username)
            ->setPassword($this->password)
            ->setRetsVersion('1.7.2')
            ->setOption("compression_enabled", true)
            ->setOption("offset_support", true);



        $this->rets = new Session($config);

        $this->rets->Login();

        return $this;
    }

    public function buildListings()
    {
       foreach (self::CLASSES as $class) {
           $this->class = $class;
           $this->fetchListings();
       }

       $this->getGeocodes();

       return $this;
    }

    public static function getGeocodes()
    {
        DB::table('listings')->orderBy('id', 'asc')->chunk(100, function ($listings) {
            foreach ($listings as $listing) {
                echo $listing->id . PHP_EOL;
                $listingObject = Listing::find($listing->id);
                Location::forListing($listingObject);
            }
        });
    }

    public function buildMediaObjects()
    {
        $counter = 0;
        $totalListings = Listing::pluck('id')->count();
        DB::table('listings')->orderBy('id', 'ASC')->chunk(100, function ($listings) use (&$totalListings, &$counter) {
            foreach ($listings as $listing) {
                $objects = $this->rets->Search('Media', 'GFX', 'MLS_ACCT='. $listing->mls_acct, self::QUERY_OPTIONS);
                echo 'Adding ' . count($objects) . ' media objects to listing ' . ($counter + 1) . ' of '. $totalListings . PHP_EOL;
                $this->attachMediaObjects($listing, $objects);
                $counter = $counter + 1;
            }
        });
    }

    protected function attachMediaObjects($listing, $objects)
    {
        foreach ($objects as $object) {
            $returnedObject = new ReturnedObject($object);
            $returnedObject->attachTo($listing);
        }
    }

    protected function fetchListings($query = 'DATE_MODIFIED=1970-01-01+')
    {
        $offset = 0;
        $maxRowsReached = false;

        while (!$maxRowsReached) {
            $results = $this->rets->Search('Property', $this->class, $query, self::QUERY_OPTIONS);

            echo '---------------------------------------------------------' . PHP_EOL;
            echo 'Class: ' . $this->class . PHP_EOL;
            echo 'Returned Results: ' . $results->getReturnedResultsCount() . PHP_EOL;
            echo 'Total Results: ' . $results->getTotalResultsCount() . PHP_EOL;
            echo 'Offset before this batch: ' . $offset . PHP_EOL;

            foreach ($results as $result) {
                $returnedProperty = new ReturnedProperty($result);
                $returnedProperty->save();
            }

            $offset += $results->getReturnedResultsCount();
            echo 'Offset after this batch: ' . $offset . PHP_EOL;

            if ($offset >= $results->getTotalResultsCount()) {

                echo 'Final Offset: ' . $offset . PHP_EOL;
                $maxRowsReached = true;
            }
        }
    }

    public function __destruct()
    {
        $this->rets->Disconnect();
    }
}
