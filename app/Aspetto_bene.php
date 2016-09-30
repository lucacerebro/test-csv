<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Aspetto_bene extends Model
{
        protected $table='aspetto_bene';
    
    public function artb(){
        return $this->hasMany('App\Articolo');
    }
}
