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
use Excel;

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
        echo 'Version MySql:   '.$this->getMySQLVersion();
        echo '<br>';
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
            echo $cod.'<br>';
            //$arts= $artcs->where('codice', $cod)->first();
            //$arts= DB::table('articolo')->where('codice',$cod)->first();
            //$arts= Articolo::where('codice',$cod)->first();
            $arts=  Articolo::firstOrNew(['codice'=>$cod]);
            //$id=$artcs->where('codice',$cod)->first()->id;
            //if(!empty($arts))
            //$single=$artcs->where('codice',$cod)->first();
            if($arts)
            {
               // $id=$artcs->where('codice',$cod)->first();
                //echo 'id:    '.$id=$single['id'];
                $id=$arts['id'];    
                
                //echo $id.'<br>';$id=$arts['id'];
                //CONVERTE IL CODICE IVA IN ID DELLA TABELLA IVA
                
                $data_row['iva']=$codici_iva->where('codice',$data_row['iva'])->first()->id;

                $data_row['aspetto_bene']= $aspettobene->where('codice',$data_row['aspetto_bene'])->first()->id;

                $data_row['unita_misura']=$unitamisura->where('codice',$data_row['unita_misura'])->first()->id;

                $data_row['sconto']= $scont->where('codice',$data_row['sconto'])->first()->id;

                $data_row['provv']= $provv->where('codice',$data_row['provv'])->first()->id;
                /*
                //$arts->codice= $data_row['codice'];
                $arts-> id_padre = $data_row['codice'];
                $arts-> codice_alt = $data_row['codice_alt'];
                $arts->codice_barre = $data_row['codice_barre'];
                $arts-> descrizione = $data_row['descrizione'];
                $arts->    provv = $data_row['provv'];
                $arts->     unita_misura= $data_row['unita_misura'];
                $arts->      qta_min= $data_row['qta_min'];
                $arts->       iva= $data_row['iva'];
                $arts->       nota= $data_row['nota'];
                $arts->        aspetto_bene= $data_row['aspetto_bene'];
                $arts->         is_kit= $data_row['is_kit'];
                $arts->         is_novita= $data_row['is_novita'];
                $arts->         is_vincolante= $data_row['is_vincolante'];
                $arts->         is_online= $data_row['is_online'];
                $arts->         url_img= $data_row['url_img'];
                $arts->         color= $data_row['color'];
                $arts->         pezzi_confezione= $data_row['pezzi_confezione'];
                $arts->         descrizione_agg= $data_row['descrizione_agg'];
                $arts->         sconto= $data_row['sconto'];
                $arts->        data_scadenza= $data_row['data_scadenza'];

                */
                //array_unshift($data_row, $id);
             //   echo 'codice   '.$data_row['id'].'<br>';
            //    $insert[]=$data_row;
                //$arts->save();
             //   $this->articolob->where('id',$id)->fill($data_row)->save();
          
             $arts->fill($data_row)->save();
             /*([
                            //'id' => $id,
                            'codice' => $data_row['codice'],
                            'codice_alt' =>$data_row['codice_alt'],
                            'codice_barre' => $data_row['codice_barre'],
                            'id_padre' => $data_row['id_padre'],
                            'descrizione' =>  $data_row['descrizione'],
                            'provv' => $data_row['provv'],
                            'unita_misura' => $data_row['unita_misura'],
                            'qta_min' =>  $data_row['qta_min'],
                            'iva' => $data_row['iva'],
                            'nota' =>$data_row['nota'],
                            'aspetto_bene' => $data_row['aspetto_bene'],
                            'is_kit' => $data_row['is_kit'],
                            'is_novita' => $data_row['is_novita'],
                            'is_vincolante' => $data_row['is_vincolante'],
                            'is_online' =>  $data_row['is_online'],
                            'url_img' => $data_row['url_img'],
                            'color' => $data_row['color'],
                            'pezzi_confezione' => $data_row['pezzi_confezione'],
                            'descrizione_agg' => $data_row['descrizione_agg'],
                            'sconto' => $data_row['sconto'],
                            'created_at' => $data_row['created_at'],
                            'data_scadenza' => $data_row['data_scadenza'],
                            ]);*/
                $counter++;
               
            }
            else {
                echo 'NO<br>';
                $id='';
                $data_row['iva']=$codici_iva->where('codice',$data_row['iva'])->first()->id;

                $data_row['aspetto_bene']= $aspettobene->where('codice',$data_row['aspetto_bene'])->first()->id;

                $data_row['unita_misura']=$unitamisura->where('codice',$data_row['unita_misura'])->first()->id;

                $data_row['sconto']= $scont->where('codice',$data_row['sconto'])->first()->id;

                $data_row['provv']= $provv->where('codice',$data_row['provv'])->first()->id;
                array_unshift($data_row, $id);
                //fputs($fp, implode($data_row,';')."\n");
                $this->articolob->create($data_row);

            }

            } // else Validate fail

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
//        $path=__DIR__.'/../storage/logs/'. $file_name ;
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
       $path=__DIR__.'/../storage/imports/'. $file_name ;
//     $path=__DIR__.'/../storage/logs/'. $file_name ;
        $name_tab='articolo';
        $this->resetDB($name_tab);
        $this->writeDb($name_tab, $path);
        echo 'Query Ok<br>';
       // echo 'Counter: '.$counter.'<br>';
        echo 'Fine Scrittura DB ';
        echo date("H:i:s").'<br>';
        echo 'Fine';
        
    }
    
    private function writeDb2($name_tab,$path) {
        //$path='/var/lib/mysql/bianchi16.csv';
        echo $path.'<br>';
        echo ini_set('mysql.allow_local_infile', 1).'<br>';
        $query = sprintf("LOAD DATA  INFILE '%s' REPLACE INTO TABLE  articolo FIELDS TERMINATED BY ';' LINES TERMINATED BY '\\n' ", addslashes($path),$name_tab);
        echo $query;
        DB::connection()->getPdo()->exec($query);
        return 1;
    }
    
    private function writeDb($name_tab,$path) {
        echo $path.'<br>';
        $con = mysqli_init();
        $con->options(MYSQLI_OPT_LOCAL_INFILE, true);
        $host= env('DB_HOST', 'forge');
        echo $host.'<br>';
        echo '<br>'.$user= env('DB_USERNAME','forge');
     echo '<br>'.   $pw= env('DB_PASSWORD','forge');
      echo '<br>'.  $db= env('DB_DATABASE','forge');
        mysqli_real_connect($con, $host, $user, $pw, $db);
        
mysqli_query($con, "LOAD DATA LOCAL INFILE '/var/www/testcsv/app/../storage/imports/bianchi16.csv' REPLACE INTO TABLE  articolo FIELDS TERMINATED BY ';' LINES TERMINATED BY '\\n' ");

//        $path='/var/lib/mysql/bianchi16.csv';
        //ini_set('mysql.allow_local_infile', 1);
        $query = sprintf("LOAD DATA LOCAL INFILE '%s' REPLACE INTO TABLE  articolo FIELDS TERMINATED BY ';' LINES TERMINATED BY '\\n' ", addslashes($path));
        $query2= "LOAD DATA LOCAL INFILE ".$path."  INTO TABLE  articolo FIELDS TERMINATED BY ';' LINES TERMINATED BY '\\n'";
        echo $query2.'<br>';

           //     $f=  fopen(__DIR__.'/../storage/imports/'.$path, 'r');
        //DB::connection()->getPdo()->exec($query);
//        mysqli_query($con, $query2);
        return 1;
    }
    
    
    
    public function insertDb($path) {
        //ini_set('mysql.allow_local_infile', 1);
        //$query = sprintf("LOAD DATA LOCAL INFILE '%s' REPLACE INTO TABLE  articolo FIELDS TERMINATED BY ';' LINES TERMINATED BY '\\n' ", addslashes($path),$name_tab);
      //  echo $query;
       // $path='C:\laragon\www\testcsv\storage\imports\bianchi18.csv';
        echo $path.'<br>';
        $f=  fopen(__DIR__.'/../storage/imports/'.$path, 'r');
        $header = fgetcsv($f, 0, ';');
        echo 'campi header :'. $h=count($header);
         while(!feof($f))
        {
        $values = fgetcsv($f, 0, ';');
        echo 'campi Values :'.$c=count($values).'   <br>';
        if($c==$h){
        $value = array_combine($header, $values);
       // echo $value;
        //Excel::('chunk')->load($path)->chunk(5000,function($data){
                     echo '<br> load data Excel<br>';

                    
                    //foreach ($data as $key => $value){
                 //     foreach ($data as $value){  

                        $insert[] = [
                                'id'=> $value['id'],
                  'codice'=> $value['codice'],
                  'id_padre' => $value['id_padre'],
                'codice_alt' => $value['codice_alt'],
               'codice_barre' => $value['codice_barre'],
                 'descrizione' => $value['descrizione'],
                  'provv' => $value['provv'],
                   'unita_misura'=> $value['unita_misura'],
                   'qta_min'=> $value['qta_min'],
                    'iva'=> $value['iva'],
                   'nota'=> $value['nota'],
                    'aspetto_bene'=> $value['aspetto_bene'],
                   'is_kit'=> $value['is_kit'],
                       'is_novita'=> $value['is_novita'],
                      'is_vincolante'=> $value['is_vincolante'],
                       'is_online'=> $value['is_online'],
                       'url_img'=> $value['url_img'],
                      'color'=> $value['color'],
                     'pezzi_confezione'=> $value['pezzi_confezione'],
                    'descrizione_agg'=> $value['descrizione_agg'],
                  'sconto'=> $value['sconto'],
                   'created_at'=>$value['created_at'],
                        'updated_at'=>$value['updated_at'],
                'data_scadenza'=> $value['data_scadenza']
                           
        ];}
        }
                   //   }
                        if(!empty($insert)){   
                        foreach (array_chunk($insert,1013,true) as $row){
                        echo "Ok !<br>";
                //        \App\Artc::insert($row);}
                            $conn=DB::connection('mysql2')->table('articolo')->insert($row); 
                            
                        }
                    }
                    //dd('Inserimento Record Completato!');
                  //  echo $data->count();
                // dd    ( $value->codice); 
                            echo date("H:i:s").'<br>';
  fclose($f);
                    return 1;
                     }               
                   // });
        //DB::connection()->getPdo()->exec($query);
  
    //}
      
    public function resetDB($name_tab) {
          $pdo =  DB::connection()->getPdo();
        $pdo->exec('truncate articolo');
//        DB::table($name_tab)->delete();
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
        $fp = fopen(__DIR__.'/../storage/imports/'.$file_name, 'w');
//      $fp = fopen(__DIR__.'/../storage/logs/'.$file_name, 'w');
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
        $hostdb='localhost';
        $userdb='root';
        $passdb='';
        
       // $conn = new PDO("mysql:host=$hostdb; dbname=$namedb", $userdb, $passdb);
        
        
        //$art=  Articolo::all('id','codice');
        $codici_iva=  Codice_iva::all(['id','codice']);
        $aspettobene= Aspetto_bene::all(['id','codice']);
        $provv= Articolo_categoria_provv::all(['id','codice']);
        $scont= Articolo_categoria_sconto::all(['id','codice']);
        $unitamisura = Unita_misura::all(['id','codice']);
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
        $fp = fopen(__DIR__.'/../storage/imports/'.$csv_file_name, 'w');
//      $fp = fopen(__DIR__.'/../storage/logs/'.$csv_file_name, 'w');
        $lines = count(file($csv_file_path)) - 1; 
        $fp_error = fopen(__DIR__.'/../storage/logs/articoli_non_validati.log', 'w');
        $this->csv_import->create(['original_filename'=>$csv_file_name,'status'=>'importato','row_count'=> $lines]);
        
        fputs($fp, 'id;'.implode($header,';')."\n");
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
                   // $data_row['id']=$id;
                    //echo $codici_iva->first();
                    //CONVERTE IL CODICE IVA IN ID DELLA TABELLA IVA
                    $data_row['iva']= $this->articolob->get_iva_id($data_row['iva']);  
                    $data_row['aspetto_bene']=  $this->articolob->get_aspetto_id($data_row['aspetto_bene']);
                    $data_row['unita_misura']=  $this->articolob->get_misura_id($data_row['unita_misura']);
                    $data_row['sconto']= $this->articolob->get_cat_sconto_id($data_row['sconto']);  
                    $data_row['provv']= $this->articolob->get_cat_provv_id($data_row['provv']);  
                   
                    array_unshift($data_row, $id);
                    fputs($fp, implode($data_row,';')."\n");
                     //$arts->codice= $data_row['codice'];
                                        $insert[]=$data_row;

/*              $insert[]=[ 'id'=> $data_row['id'],
                  'codice'=> $data_row['codice'],
                  'id_padre' => $data_row['id_padre'],
                'codice_alt' => $data_row['codice_alt'],
               'codice_barre' => $data_row['codice_barre'],
                 'descrizione' => $data_row['descrizione'],
                  'provv' => $data_row['provv'],
                   'unita_misura'=> $data_row['unita_misura'],
                   'qta_min'=> $data_row['qta_min'],
                    'iva'=> $data_row['iva'],
                   'nota'=> $data_row['nota'],
                    'aspetto_bene'=> $data_row['aspetto_bene'],
                   'is_kit'=> $data_row['is_kit'],
                       'is_novita'=> $data_row['is_novita'],
                      'is_vincolante'=> $data_row['is_vincolante'],
                       'is_online'=> $data_row['is_online'],
                       'url_img'=> $data_row['url_img'],
                      'color'=> $data_row['color'],
                     'pezzi_confezione'=> $data_row['pezzi_confezione'],
                    'descrizione_agg'=> $data_row['descrizione_agg'],
                  'sconto'=> $data_row['sconto'],
                   'created_at'=>$data_row['created_at'],
                        'updated_at'=>$data_row['updated_at'],
                'data_scadenza'=> $data_row['data_scadenza']
                       ];
  */                  
                    
                    
                    
                    
                    
                    
                    
                    
                  //  $insert[]=$data_row;
                    
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
                   // $data_row['id']=$id;
                    array_unshift($data_row, $id);

                    fputs($fp, implode($data_row,';')."\n");
                    /// $insert[]=$data_row;
                    $insert[]=$data_row;
                            /*[ 'id'=> $data_row['id'],
                      'codice'=> $data_row['codice'],
                  'id_padre' => $data_row['id_padre'],
                'codice_alt' => $data_row['codice_alt'],
               'codice_barre' => $data_row['codice_barre'],
                 'descrizione' => $data_row['descrizione'],
                  'provv' => $data_row['provv'],
                   'unita_misura'=> $data_row['unita_misura'],
                   'qta_min'=> $data_row['qta_min'],
                    'iva'=> $data_row['iva'],
                   'nota'=> $data_row['nota'],
                    'aspetto_bene'=> $data_row['aspetto_bene'],
                   'is_kit'=> $data_row['is_kit'],
                       'is_novita'=> $data_row['is_novita'],
                      'is_vincolante'=> $data_row['is_vincolante'],
                       'is_online'=> $data_row['is_online'],
                       'url_img'=> $data_row['url_img'],
                      'color'=> $data_row['color'],
                     'pezzi_confezione'=> $data_row['pezzi_confezione'],
                    'descrizione_agg'=> $data_row['descrizione_agg'],
                  'sconto'=> $data_row['sconto'],
                        'created_at'=>$data_row['created_at'],
                        'updated_at'=>$data_row['updated_at'],
                'data_scadenza'=> $data_row['data_scadenza']
                       ];*/


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
        $path=__DIR__.'/../storage/imports/'. $csv_file_name ;
//      $path=__DIR__.'/../storage/logs/'. $csv_file_name ;        
        echo $csv_file_name.'<br>';
        $name_tab='articolo';
        //$query = sprintf("LOAD DATA LOCAL INFILE '%s' REPLACE INTO TABLE %s FIELDS TERMINATED BY ';' LINES TERMINATED BY '\\n' ", addslashes($path),$name_tab);
        //DB::getPdo()->exec($query);
        //OPPURE -> 
        //DB::connection()->getPdo()->exec($query);
//$query= $this->writeDb($name_tab,$path);
        //DB::table('articolo')->delete();
  //      foreach (array_chunk($insert,1000) as $row2){
            /*
                        echo "Ok ";
                //        \App\Artc::insert($row);}
                foreach ($row2 as $row)
                     //DB::table('articolo')->updateOrCreate($row); 
                     $this->articolob->($row); */
           // foreach ($row2 as $row)
           // DB::table('articolo')->where('id',$row['id'])->update($row); 
            
           // DB::connection()->getPdo()->exec('truncate articolo');
        //   $this->resetDB('articolo');
           //$artdelete->forceDelete();
  //          $this->articolob->insert($row2);
      //  $pdo2 =  DB::connection()->getPdo();
        //$pdo2->exec($query)
        //$this->articolob=NULL;
       // new DB::table('articolo')->insert($row2);
         
    
            //DB::table('articolo')->insert($row2); 
       //$query = sprintf("INSERT INTO TABLE 'articolo' VALUES '%s'", implode($row,','));
        //echo $query;
    //    $row=  implode($row, ',');
       // DB::connection()->getPdo()->exec($query);
                            
  //      }
                   
                 
  $this->writeDb($name_tab,$path);
        echo 'Query Ok<br>';
        echo 'Counter: '.$counter.'<br>';
        echo 'Fine Scrittura DB ';
        echo date("H:i:s").'<br>';
        echo memory_get_usage().'<br>';
        echo 'Fine';
        
        //return $this->validator;
        //return $vld_error;
    }
    
    public function getMySQLVersion() { 
            $output = shell_exec('mysql -V'); 
            echo $output.'<br>';
            preg_match('@[0-9]+\.[0-9]+\.[0-9]+@', $output, $version); 
             return $version[0]; 
    }
}
