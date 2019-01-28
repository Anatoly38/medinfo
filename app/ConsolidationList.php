<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ConsolidationList extends Model
{
    //
    protected $fillable = ['script', 'scripthash', 'prophash', 'comment'];
    protected $hidden = ['properties'];

    public function scopePropHash($query, $hash)
    {
        return $query
            ->where('prophash', $hash);
    }
}
