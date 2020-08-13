<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes; 
class ClientSite extends Model
{
 

    use SoftDeletes;
    
    protected $fillable=['sitename','siteurl','accesstoken'];
}
