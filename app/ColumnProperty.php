<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ColumnProperty extends Model
{
    //
    protected $fillable = [ 'column_id', 'properties', 'comment' ];

    public function column()
    {
        return $this->belongsTo('App\Column');
    }

    public function scopeColumn($query, $row)
    {
        return $query->where('column_id', $row);
    }
}
