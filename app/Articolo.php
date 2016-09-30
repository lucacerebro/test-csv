<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Articolo extends Model
{
    protected $table='articolo';
    protected $fillable = ['codice','iva','descrizione','codice_alt','codice_barre','id_padre','descrizione','provv','unita_misura','qta_min','nota','aspetto_bene','is_kit','is_novita','is_vincolante','is_online','url_img','color','pezzi_confezione','descrizione_agg','sconto','data_scadenza'];
  //  protected $guarded =[ 'id'];
    
    public function ivas() {
        return $this->belongsTo('App\Codice_iva','iva');
    }
    
    public function aspetto(){
        return $this->belongsTo('App\Aspetto_bene','aspetto_bene');
    }
    
    public function unita_misura(){
        return $this->belongsTo('App\Aspetto_bene','unita_misura');
    }
    
    public function sconto(){
        return $this->belongsTo('App\Articolo_categoria_sconto','sconto');
    }
    
    public function provv(){
        return $this->belongsTo('App\Articolo_categoria_provv','provv');
    }
     public function get_iva_id($cod_iva) {    
        return $iva_id = Codice_iva::where('codice',$cod_iva)->first()->id;        
    }
    
    public function get_aspetto_id($cod_aspetto) {    
        return $aspetto_id = Aspetto_bene::where('codice',$cod_aspetto)->first()->id;        
    }
    
    public function get_misura_id($cod_misura) {    
        return $unita_id = Unita_misura::where('codice',$cod_misura)->first()->id;        
    }
    
    public function get_cat_sconto_id($cod_cat_sconto) {    
        return $cat_sconto_id = Articolo_categoria_sconto::where('codice',$cod_cat_sconto)->first()->id;        
    }
    
    public function get_cat_provv_id($cod_cat_provv) {    
        return $cat_provv_id = Articolo_categoria_provv::where('codice',$cod_cat_provv)->first()->id;        
    }
}
