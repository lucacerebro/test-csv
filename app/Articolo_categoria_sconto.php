<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Articolo_categoria_sconto extends Model
{
    protected $table='articolo_categoria_sconto';

    public function articolo()
    {
        return $this->hasMany('App\Articolo');
    }
}
