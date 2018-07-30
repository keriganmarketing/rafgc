<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class MediaObject extends Model
{
    protected $guarded = [];

    public function listing()
    {
        return $this->belongsTo(Listing::class, 'listing_id');
    }

    public static function labelPreferred()
    {
        DB::table('media_objects')->where('media_order', '<=', 1)->orderBy('id', 'asc')->chunk(500, function ($photos) {
            foreach ($photos as $photo) {
                MediaObject::find($photo->id)->update([
                    'is_preferred' => true
                ]);
            }
        });
    }
}
