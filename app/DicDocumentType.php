<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DicDocumentType extends Model
{
    //
    protected $primaryKey = 'code';

    public function scopePrimary($query)
    {
        return $query
            ->where('code', 1);
    }

    public function scopeNotPrimary($query)
    {
        return $query
            ->where('code', '<>', 1);
    }

    public function scopeAggregate($query)
    {
        return $query
            ->where('code', 2);
    }

    public function scopeConsolidate($query)
    {
        return $query
            ->where('code', 3);
    }

    public function scopeIndexes($query)
    {
        return $query
            ->where('code', 4);
    }


}
