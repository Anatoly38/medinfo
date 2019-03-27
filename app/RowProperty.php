<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RowProperty extends Model
{
    //
    protected $fillable = [ 'row_id', 'properties', 'comment' ];

    public function scopeRow($query, $row)
    {
        return $query->where('row_id', $row);
    }
}
