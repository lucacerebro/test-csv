<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Csv_importati extends Model
{
    protected $table='csv_importati';
    protected $fillable = ['original_filename','status','row_count'];
}
