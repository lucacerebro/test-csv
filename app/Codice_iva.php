<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Codice_iva extends Model
{
    protected $table='codice_iva';
    
    public function articolo(){
        return $this->hasMany('App\Articolo');
    }
}
