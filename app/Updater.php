<?php
namespace App;

use Carbon\Carbon;

class Updater extends Rafgc
{
    protected $lastModified;

    public function __construct($lastModified = null)
    {
        parent::__construct();
        $this->lastModified = $lastModified !== null ? explode(' ', $lastModified): ['1970-01-01'];
    }

    public function full()
    {
        foreach (self::CLASSES as $class) {
            $this->class = $class;
            $this->update();
        }
    }
    private function update()
    {
        $date = $this->lastModified[0] !== '' ? $this->lastModified[0] : '1970-01-01';
        $time = isset($this->lastModified[1]) ? "T{$this->lastModified[1]}" :  null;
        $offset = 0;
        $maxRowsReached = false;

        while (!$maxRowsReached) {
            $results = $this->rets->Search('Property', $this->class, "DATE_MODIFIED={$date}{$time}+", self::QUERY_OPTIONS);

            echo '---------------------------------------------------------' . PHP_EOL;
            echo 'Class: ' . $this->class . PHP_EOL;
            echo 'Returned Results: ' . $results->getReturnedResultsCount() . PHP_EOL;
            echo 'Total Results: ' . $results->getTotalResultsCount() . PHP_EOL;
            echo 'Offset before this batch: ' . $offset . PHP_EOL;

            foreach ($results as $result) {
                $returnedProperty = new ReturnedProperty($result);
                $updatedListing = $returnedProperty->save();
                $this->fetchPhotosFor($updatedListing);
                $this->fetchGeocodeFor($updatedListing);
                echo 'Updated listing #' . $updatedListing->mls_acct . PHP_EOL;
            }

            $offset += $results->getReturnedResultsCount();
            echo 'Offset after this batch: ' . $offset . PHP_EOL;

            if ($offset >= $results->getTotalResultsCount()) {
                echo 'Final Offset: ' . $offset . PHP_EOL;
                $maxRowsReached = true;
            }
        }
    }
}
