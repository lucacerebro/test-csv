<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;

use Illuminate\Support\Facades\Input;
use Redirect;
use App\articoloValidator;
use App\Articolo;

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
    
    public function show()
            { 
            $arts= Articolo::where('id','>',0)->paginate(10);
            return view('show', ['arts' => $arts]);
            }
            
    public function showOne($id) 
            {
            $art= Articolo::find($id);
            return view('showone',['art' => $art]);
            }
}
