<?php

namespace App\Models\Base;

use Illuminate\Database\Eloquent\Model;

class Categories extends Model
{
    protected $table        = 'development.categories';
    protected $primaryKey   = 'category_id';

    const UPDATED_AT        = null;
    const CREATED_AT        = null;
}
