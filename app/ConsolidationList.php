<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ConsolidationList extends Model
{
    //
    protected $fillable = ['script', 'hash', 'comment'];
    protected $hidden = ['properties'];

    public function scopeHash($query, $hash)
    {
        return $query
            ->where('hash', $hash);
    }
}
