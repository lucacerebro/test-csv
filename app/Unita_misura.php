<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Unita_misura extends Model
{
    protected $table='unita_misura';
    public function articolo(){
        return $this->hasMany('App\Articolo');
    }
}
