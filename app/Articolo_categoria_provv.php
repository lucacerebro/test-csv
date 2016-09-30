<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Articolo_categoria_provv extends Model
{
    protected $table='articolo_categoria_provv';

    public function articolo()
    {
        return $this->hasMany('App\Articolo');
    }
}
