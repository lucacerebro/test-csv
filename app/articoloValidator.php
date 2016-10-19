<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App;

/**
 * Description of artValidator
 *
 * @author sviluppo2
 */
use App\Articolo;
use App\Csv_importati;
use Illuminate\Validation\Factory as ValidationFactory;
use DB;

class articoloValidator {
    private $validator;
    private $articolob;
    private $rules = [
        'import_csv' => 'mimes:csv',
        'codice' => 'required',
        // 'codice_barre'=> 'required',
        'descrizione'=> 'required',
        //verifica che 'iva' sia presente nella tabella 'codice_iva' campo 'codice'
        'iva' => 'required|exists:codice_iva,codice' ,
        //'id_padre' => 'exists',
        'unita_misura' => 'required|exists:unita_misura,codice',
        'qta_min'=> 'required',
        'is_kit'=> 'required',
        'is_novita'=> 'required',
        'is_vincolante'=> 'required',
        'is_online'=> 'required',
        'pezzi_confezione'=> 'required',
        'sconto'=> 'required|exists:articolo_categoria_sconto,codice',
        'url_img'=>'required',
        'aspetto_bene'=>'required|exists:aspetto_bene,codice',
        'provv'=>'required|exists:articolo_categoria_provv,codice'
        ];
        
    private $messages= [

        'codice.required' => 'Codice obbligatorio',
        'iva.required'=> 'Iva obbligatoria',
        //'id_padre.exists'=> 'codice id_padre non esistente'
        'iva.exists'=> 'Codice iva non presente nella tabella',
        'aspetto_bene.exists'=>'Codice aspetto bene non presente nella tabella',
        'unita_misura.exists'=>'Codice unita_misura non presente nella tabella',
        'provv.exists'=>'Codice provv non presente nella tabella'
        ];
    
    public function __construct(ValidationFactory $validator)
    {
        $this->validator = $validator;
        $this->articolob = new Articolo;
        $this->csv_import= new Csv_importati;
    }
    
    public function validate($csv_file_path){
        
        echo memory_get_usage().'<br>';
        $codici_iva=  Codice_iva::all(['id','codice']);
        $aspettobene= Aspetto_bene::all(['id','codice']);
        $provv= Articolo_categoria_provv::all(['id','codice']);
        $scont= Articolo_categoria_sconto::all(['id','codice']);
        $unitamisura = Unita_misura::all(['id','codice']);
        $artcs=  Articolo::all(['id','codice']);
        echo memory_get_usage().'<br>';

        ini_set("auto_detect_line_endings", true);
        //$articoli_validati= array();
        $csv_file_name=$csv_file_path->getClientOriginalName();
        if (($opened_file = fopen($csv_file_path, 'r')) === false) {
            throw new Exception('File cannot be opened for reading');
        }
        $header = fgetcsv($opened_file, 0, ';');
//        $h= count($header);
        echo 'Lettura Header ';        
        echo date("H:i:s").'<br>';
        $counter=0;
        $fp_error = fopen(__DIR__.'/../storage/logs/articoli_non_validati.log', 'w');
        $this->csv_import->create(['original_filename'=>$csv_file_name,'status'=>'importato','row_count'=> 0]);
        $file_name=$csv_file_name;
//        $fp = fopen(__DIR__.'/../storage/imports/'.$file_name, 'w');
        while(!feof($opened_file))
        {
        $data_rows = fgetcsv($opened_file, 0, ';');
//        $n= count($data_rows);
//        if($h==$n){

        $data_row = array_combine($header, $data_rows);
        $vld= $this->validator->make($data_row, $this->rules, $this->messages);
        if ($vld->fails()){
            echo $vld->errors().'<br>';
            fputs($fp_error, 'Codice: '.$data_row['codice'].' '.$vld->errors()."\n");           
        }
        else {
             
            $cod=$data_row['codice'];
            //echo $cod.'<br>';

            //$arts= $artcs->where('codice', $cod)->first();
            //$arts= DB::table('articolo')->where('codice',$cod)->first();
            //$arts= Articolo::where('codice',$cod)->first();
            $arts=  Articolo::firstOrNew(['codice'=>$cod]);

            if(!empty($arts))
            {

                echo $cod.'<br>';
                //$id=$arts['id'];    
                //echo $id.'<br>';$id=$arts['id'];
                //CONVERTE IL CODICE IVA IN ID DELLA TABELLA IVA
 
                $data_row['iva']=$codici_iva->where('codice',$data_row['iva'])->first()->id;

                $data_row['aspetto_bene']= $aspettobene->where('codice',$data_row['aspetto_bene'])->first()->id;

                $data_row['unita_misura']=$unitamisura->where('codice',$data_row['unita_misura'])->first()->id;

                $data_row['sconto']= $scont->where('codice',$data_row['sconto'])->first()->id;

                $data_row['provv']= $provv->where('codice',$data_row['provv'])->first()->id;

                $arts->update($data_row);

                $counter++;
               
            }
            


            } // fine if Validate fail

        } //fine while lettura file import
        
        echo 'Inizio Scrittura file ';
        echo date("H:i:s").'<br>';
        
        //$file_name=$csv_file_name;
        //$fp = fopen(__DIR__.'/../storage/imports/'.$file_name, 'w');

        /* foreach ($articoli_validati as $fields) {
            //scrive sul file i ogni campo di ogni riga delimitandoli con ';'
            fputs($fp, implode($fields,';')."\n");
            $counter++;
        }*/
 
        //       fclose($fp);
        $this->csv_import->create(['original_filename'=>$csv_file_name,'status'=>'processato','row_count'=> $counter]);
        //echo 'Inizio Scrittura DB ';
        echo date("H:i:s").'<br>';
//        $path=__DIR__.'/../storage/imports/'. $file_name ;
        $path=__DIR__.'/../storage/logs/'. $file_name ;
        echo $file_name.'<br>';
        echo memory_get_usage().'<br>';
       
        // $query = sprintf("LOAD DATA LOCAL INFILE '%s' REPLACE INTO TABLE articolo FIELDS TERMINATED BY ';' LINES TERMINATED BY '\\n' ", addslashes($path));
        //DB::getPdo()->exec($query);
        //OPPURE -> 
        // DB::connection()->getPdo()->exec($query);
        
        echo 'Query Ok<br>';
        echo 'Counter: '.$counter.'<br>';
        //echo 'Fine Scrittura DB ';
        echo 'Scrittura con model completata<br>';
        echo date("H:i:s").'<br>';

        echo 'Fine';
        return $this->validator;
        //return $vld_error;
      }      
      
    public function validate2($csv_file_path){
        echo 'Metodo validate2<br>';
        $articoli_validati=$this->check_row($csv_file_path);    
        echo 'Inizio Scrittura file ';
        echo date("H:i:s").'<br>';
        $file_name=$csv_file_path->getClientOriginalName();
        $this->write_file($articoli_validati,$file_name);

        echo 'Inizio Scrittura DB ';
        echo date("H:i:s").'<br>';
//        $path=__DIR__.'/../storage/imports/'. $file_name ;
        $path=__DIR__.'/../storage/logs/'. $file_name ;
        $name_tab='articolo';
        $this->resetDB($name_tab);
        $this->writeDb($name_tab, $path);
        echo 'Query Ok<br>';
       // echo 'Counter: '.$counter.'<br>';
        echo 'Fine Scrittura DB ';
        echo date("H:i:s").'<br>';
        echo 'Fine';
        
    }
    
    private function writeDb($name_tab,$path) {
        $query = sprintf("LOAD DATA LOCAL INFILE '%s' REPLACE INTO TABLE %s FIELDS TERMINATED BY ';' LINES TERMINATED BY '\\n' ", addslashes($path),$name_tab);
        DB::connection()->getPdo()->exec($query);
        return 1;
    }
      
    public function resetDB($name_tab) {
        DB::table($name_tab)->delete();
        echo 'tabella '.$name_tab.' svuotata<br>';
        return 1;
    }
    
    public function check_row($csv_file_path) {
        $articoli_validati= array();
        ini_set("auto_detect_line_endings", true);
        $csv_file_name=$csv_file_path->getClientOriginalName();
        if (($opened_file = fopen($csv_file_path, 'r')) === false) {
            throw new Exception('File cannot be opened for reading');
        }
        $header = fgetcsv($opened_file, 0, ';');
        $h= count($header);
        echo 'Lettura Header ';        
        echo date("H:i:s").'<br>';
        $counter=0;
        $fp_error = fopen(__DIR__.'/../storage/logs/articoli_non_validati.log', 'w');
        $lines = count(file($csv_file_path)) - 1;
        $this->csv_import->create(['original_filename'=>$csv_file_name,'status'=>'importato','row_count'=> $lines]);
        while(!feof($opened_file))
        {
        $data_rows = fgetcsv($opened_file, 0, ';');
        $n= count($data_rows);
        if($h==$n){
        $data_row = array_combine($header, $data_rows);
        $vld= $this->validator->make($data_row, $this->rules, $this->messages);
        if ($vld->fails()){
            echo $vld->errors().'<br>';
            fputs($fp_error, $data_row['codice'].' '.$vld->errors()."\n");           
        }
        else {
               $data_row['iva']= $this->articolob->get_iva_id($data_row['iva']);
               $data_row['aspetto_bene']=  $this->articolob->get_aspetto_id($data_row['aspetto_bene']);
               $data_row['unita_misura']=  $this->articolob->get_misura_id($data_row['unita_misura']);
               $data_row['sconto']= $this->articolob->get_cat_sconto_id($data_row['sconto']);
               $data_row['provv']= $this->articolob->get_cat_provv_id($data_row['provv']);
               $id='';
               array_unshift($data_row, $id);
               $articoli_validati[]=$data_row;
            }
        }
        else{
            fputs($fp_error, implode($data_rows,';')." {Riga vuota o Numero di campi non corrispondente all'header} \n");
        }    
        }
        return $articoli_validati;
    }
    
    public function write_file($articoli_validati,$file_name){
        $counter=0;
//      $fp = fopen(__DIR__.'/../storage/imports/'.$file_name, 'w');
        $fp = fopen(__DIR__.'/../storage/logs/'.$file_name, 'w');
        foreach ($articoli_validati as $fields) {
            //scrive sul file i ogni campo di ogni riga delimitandoli con ';'
            fputs($fp, implode($fields,';')."\n");
            $counter++;
        }
        fclose($fp);
        $this->csv_import->create(['original_filename'=>$file_name,'status'=>'processato','row_count'=> $counter]);
        echo 'Counter: '.$counter.'<br>';
        echo 'Fine metodo write_file<br>';
    }

         public function validate3($csv_file_path){
        echo memory_get_usage().'<br>';
        //$art=  Articolo::all('id','codice');
        $codici_iva=  Codice_iva::all('id','codice');
        $aspettobene= Aspetto_bene::all('id','codice');
        $provv= Articolo_categoria_provv::all('id','codice');
        $scont= Articolo_categoria_sconto::all('id','codice');
        $unitamisura = Unita_misura::all('id','codice');
        echo memory_get_usage().'<br>';
        ini_set("auto_detect_line_endings", true);
        $articoli_validati= array();
        $csv_file_name=$csv_file_path->getClientOriginalName();
        if (($opened_file = fopen($csv_file_path, 'r')) === false) {
            throw new Exception('File cannot be opened for reading');
        }
        $header = fgetcsv($opened_file, 0, ';');
//        $h= count($header);
        echo 'Lettura Header ';        
        echo date("H:i:s").'<br>';
        $counter=0;
//        $fp = fopen(__DIR__.'/../storage/imports/'.$csv_file_name, 'w');
        $fp = fopen(__DIR__.'/../storage/logs/'.$csv_file_name, 'w');
        $lines = count(file($csv_file_path)) - 1; 
        $fp_error = fopen(__DIR__.'/../storage/logs/articoli_non_validati.log', 'w');
        $this->csv_import->create(['original_filename'=>$csv_file_name,'status'=>'importato','row_count'=> $lines]);
        while(!feof($opened_file))
        {
        $data_rows = fgetcsv($opened_file, 0, ';');
//        $n= count($data_rows);
//        if($h==$n){
            $data_row = array_combine($header, $data_rows);
            $vld= $this->validator->make($data_row, $this->rules, $this->messages);      
            if ($vld->fails()){
                echo $vld->errors().'<br>';
                fputs($fp_error, $data_row['codice'].' '.$vld->errors()."\n");
            }
            else { 
             
                $cod=$data_row['codice'];
                echo $cod.'<br>';

                $arts= Articolo::where('codice',$cod)->first();
                //$arts= $art->where('codice',$cod)->first();
                if(!empty($arts))
                {
                    echo 'YES<br>';

                    $id=$arts['id'];
                    //echo $codici_iva->first();
                    //CONVERTE IL CODICE IVA IN ID DELLA TABELLA IVA
                    $data_row['iva']= $this->articolob->get_iva_id($data_row['iva']);  
                    $data_row['aspetto_bene']=  $this->articolob->get_aspetto_id($data_row['aspetto_bene']);
                    $data_row['unita_misura']=  $this->articolob->get_misura_id($data_row['unita_misura']);
                    $data_row['sconto']= $this->articolob->get_cat_sconto_id($data_row['sconto']);  
                    $data_row['provv']= $this->articolob->get_cat_provv_id($data_row['provv']);  
                   
                    array_unshift($data_row, $id);
                    fputs($fp, implode($data_row,';')."\n");
                    $counter++;

               
                }
                else{
                    echo 'NO<br>';
                    $data_row['iva']= $this->articolob->get_iva_id($data_row['iva']);  
                    $data_row['aspetto_bene']=  $this->articolob->get_aspetto_id($data_row['aspetto_bene']);
                    $data_row['unita_misura']=  $this->articolob->get_misura_id($data_row['unita_misura']);
                    $data_row['sconto']= $this->articolob->get_cat_sconto_id($data_row['sconto']);  
                    $data_row['provv']= $this->articolob->get_cat_provv_id($data_row['provv']);  
                    $id='';
                    array_unshift($data_row, $id);
                    fputs($fp, implode($data_row,';')."\n");


                }
            }

//        }
/*        else{
            if(empty($data_rows))
                echo 'End of file<br>';
            else {
            //    fputs($fp_error, implode($data_rows,';')." {Numero di campi non corrispondente all'header} \n");
                  fputs($fp_error, 'Codice: '.$data_rows[0].' { Numero di campi errato }'."\n");           
            }
                
                }*/
            
        }     
        
        echo 'Inizio Scrittura file ';
        echo date("H:i:s").'<br>';
//        $file_name=$csv_file_name;
//        $fp = fopen(__DIR__.'/../storage/imports/'.$file_name, 'w');

/*        foreach ($articoli_validati as $fields) {
            //scrive sul file i ogni campo di ogni riga delimitandoli con ';'
            fputs($fp, implode($fields,';')."\n");
            $counter++;
        }
*/ 
        fclose($fp);
        $this->csv_import->create(['original_filename'=>$csv_file_name,'status'=>'processato','row_count'=> $counter]);
        echo 'Inizio Scrittura DB ';
        echo date("H:i:s").'<br>';
//        $path=__DIR__.'/../storage/imports/'. $csv_file_name ;
        $path=__DIR__.'/../storage/logs/'. $csv_file_name ;        
        echo $csv_file_name.'<br>';
        $name_tab='articolo';
        //$query = sprintf("LOAD DATA LOCAL INFILE '%s' REPLACE INTO TABLE %s FIELDS TERMINATED BY ';' LINES TERMINATED BY '\\n' ", addslashes($path),$name_tab);
        //DB::getPdo()->exec($query);
        //OPPURE -> 
        //DB::connection()->getPdo()->exec($query);
        $query= $this->writeDb($name_tab,$path);
        echo 'Query Ok<br>';
        echo 'Counter: '.$counter.'<br>';
        echo 'Fine Scrittura DB ';
        echo date("H:i:s").'<br>';
        echo memory_get_usage().'<br>';
        echo 'Fine';
        //return $this->validator;
        //return $vld_error;
    }
}
