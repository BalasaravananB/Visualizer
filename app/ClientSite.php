<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ClientSite extends Model
{
    protected $fillable=['sitename','siteurl','accesstoken'];
}
