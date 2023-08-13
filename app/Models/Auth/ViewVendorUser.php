<?php

namespace App\Models\Auth;

use Illuminate\Database\Eloquent\Model;

class ViewVendorUser extends Model
{
    protected $table        = 'public.view_vendor_login';
    protected $primaryKey   = 'vendor_user_id';

    const UPDATED_AT        = null;
    const CREATED_AT        = null;
}
