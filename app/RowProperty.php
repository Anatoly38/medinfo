<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RowProperty extends Model
{
    //
    protected $fillable = [ 'row_id', 'properties', 'comment' ];
}
