<?php

namespace App\Models\Base;

use Illuminate\Database\Eloquent\Model;

class Samples extends Model
{
    protected $table        = 'development.samples';
    protected $primaryKey   = 'sample_id';

    const UPDATED_AT        = null;
    const CREATED_AT        = null;
}
