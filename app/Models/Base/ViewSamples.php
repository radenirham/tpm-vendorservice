<?php

namespace App\Models\Base;

use Illuminate\Database\Eloquent\Model;

class ViewSamples extends Model
{
    protected $table        = 'development.view_samples';
    protected $primaryKey   = 'sample_id';

    const UPDATED_AT        = null;
    const CREATED_AT        = null;
}
