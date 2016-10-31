<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;

use Illuminate\Support\Facades\Input;
use Redirect;
use App\articoloValidator;
use App\Articolo;
//use Excel;

class ImportazioneCsvController extends Controller
{
    private $art_validator;
    public function __construct(articoloValidator $art_validator)
    {
        $this->art_validator= $art_validator;
    }
    

    public function store(){
        echo 'Start ImportazioneCsvController<br>';
        echo 'init_set(max_execution_time, 600): '. ini_set('max_execution_time', 600).'<br>';
        echo 'init_get(upload_max_filesize): '. ini_get('upload_max_filesize').'<br>';
        echo 'time: '.ini_get('max_execution_time').'<br>';
        if(Input::hasFile('import_csv')){
            $csv_file = Input::file('import_csv');
            $csv_path = Input::file('import_csv')->getRealPath().'<br>';
            if(($csv_file->getClientOriginalExtension()) == 'csv'){
                if($csv_file->isValid()){
                    $validator = $this->art_validator->validate($csv_file);
                        if (!$validator)  {
                        echo '<br>Attenzione: sono presenti errori nel csv';
                    }           
                }
            }
            else{
               return Redirect::to('error')->with('message', 'File non valido: inserire un file CSV da importare!');
            }
        }
        else {

            $message = 'Seleziona un file da validare!';
            //return Redirect::to('error')->with('message', $message);
        }
       //return Redirect::to('error')->withErrors($validator);
       //return Redirect::back()->withErrors($validator);
       echo '<br>Fine ImportazioneCsvController';
    }
    
        
    public function store2(){
        echo 'Start ImportazioneCsvController<br>';
        echo 'time: '.ini_get('max_execution_time').'<br>';
        echo 'size: '.ini_get('upload_max_filesize').'<br>';
        echo 'init_set(max_execution_time, 500): '. ini_set('max_execution_time', 600).'<br>';
        echo 'init_set(upload_max_filesize, 5): '. ini_set('upload_max_filesize', '5M').'<br>';
        echo 'time: '.ini_get('max_execution_time').'<br>';
        echo 'size: '.ini_get('upload_max_filesize').'<br>';
        if(Input::hasFile('import_csv')){
            $csv_file = Input::file('import_csv');
            $csv_path = Input::file('import_csv')->getRealPath().'<br>';
            if(($csv_file->getClientOriginalExtension()) == 'csv'){
                if($csv_file->isValid()){
                    $validator = $this->art_validator->validate2($csv_file);
                        if (!$validator)  {
                        echo '<br>Attenzione: sono presenti errori nel csv';
                    }           
                }
            }
            else{
               return Redirect::back()->with('message', 'File non valido: inserire un file CSV da importare!');
            }
        }
        else {

            $message = 'Seleziona un file da validare!';
            return Redirect::back()->with('message', $message);
        }
       //return Redirect::to('error')->withErrors($validator);
       //return Redirect::back()->withErrors($validator);
       echo '<br>Fine ImportazioneCsvController';
    }

    public function store3(){
        echo 'Start ImportazioneCsvController<br>';
        echo 'time: '.ini_get('max_execution_time').'<br>';
        echo 'size: '.ini_get('upload_max_filesize').'<br>';
        echo 'init_set(max_execution_time, 1800): '. ini_set('max_execution_time', 1800).'<br>';
        echo 'init_set(upload_max_filesize, 5): '. ini_set('upload_max_filesize', '5M').'<br>';
        // ini_set('upload_max_filesize', '5M') non funziona, si imposta in .htaccess in /public la riga di comando: php_value upload_max_filesize 5M
        echo 'time: '.ini_get('max_execution_time').'<br>';
        echo 'size: '.ini_get('upload_max_filesize').'<br>';
        if(Input::hasFile('import_csv')){
            $csv_file = Input::file('import_csv');
            $csv_path = Input::file('import_csv')->getRealPath();
            $csv_name = Input::file('import_csv')->getClientOriginalName();
            if(($csv_file->getClientOriginalExtension()) == 'csv'){
                if($csv_file->isValid()){
                    $validator = $this->art_validator->validate3($csv_file);
                  //  $insert=  $this->art_validator->insertDb($csv_name);
                        if (!$validator)  {
                        echo '<br>Attenzione: sono presenti errori nel csv';
                    }           
                }
            }
            else{
               return Redirect::back()->with('message', 'File non valido: inserire un file CSV da importare!');
            }
        }
        else {

            $message = 'Seleziona un file da validare!';
            return Redirect::back()->with('message', $message);
        }
       //return Redirect::to('error')->withErrors($validator);
       //return Redirect::back()->withErrors($validator);
       echo '<br>Fine ImportazioneCsvController';
    }
    
    public function show()
            { 
            $output = shell_exec('mysql -V'); 
            echo $output.'<br>';
            preg_match('@[0-9]+\.[0-9]+\.[0-9]+@', $output, $version); 
            echo $version[0].'<br>';
            $arts= Articolo::where('id','>',0)->paginate(10);
            return view('show', ['arts' => $arts]);
            }
            
    public function showOne($id) 
            {
            $art= Articolo::find($id);
            return view('showone',['art' => $art]);
            }
/*    public function downloadExcel($type){
        ini_set('max_execution_time', 600);
        $data = Articolo::get()->toArray();
	return Excel::create('articoli_presenti', function($excel) use ($data) {
	$excel->sheet('mySheet', function($sheet) use ($data)
            {
		$sheet->fromArray($data);
            });
	})->download($type);
    } */
}
