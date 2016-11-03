<html lang="it">

<head>

	<title>Import CSV - Laravel 5</title>

	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" >

</head>

<body>
    <nav class="navbar navbar-default">
		<div class="container-fluid">
			<div class="navbar-header">
				<a class="navbar-brand" href="#">Import - Export in Excel di CSV Laravel 5</a>
			</div>
		</div>
    </nav>
     <div class="container">
	 
                <h3>Visualizza tutti gli Articoli presenti nel DB</h3>
                <a href="/show"><button class="btn btn-success">Visualizza</button></a>
                @if(Session::has('message'))
                <hr/>
                <h4 style="color: red;">{{Session::get('message')}}</h4>
                @endif
                
                @if($errors->any())
                <hr/>
                <h5 style="color: red;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
                </h5>
                @endif
    </div>
    
    <div class="container">
                <h3>Caricamento File CSV Articoli (in tabella articolo)</h3>
                <div style="border: 4px solid #a1a1a1;margin-top: 15px;padding: 20px; width: auto; height: auto">
                        
                <form style="float: left " action="{{ URL::to('import_csv') }}" class="form-horizontal" method="post" enctype="multipart/form-data">
                <h4> Aggiorna DB Vers.1 </h4>
               
                    
                <input type="file" name="import_csv" value=" {{ csrf_token() }}" >
		<button class="btn btn-primary">Validazione File</button>
                <h5>Utilizzo Model</h5>
                </form>
                    
                <form style="float: left" action="{{ URL::to('import_csv2') }}" class="form-horizontal" method="post" enctype="multipart/form-data">
		<h4> Aggiorna DB Vers.2 </h4>
                <input type="file" name="import_csv" value=" {{ csrf_token() }}" >
		<button class="btn btn-primary">Validazione File</button>
                <h5>Load Data Local Infile</h5>
		</form>             
                
                
                <form style="float: none" action="{{ URL::to('import_csv3') }}" class="form-horizontal" method="post" enctype="multipart/form-data">
		<h4> Aggiorna DB Vers.3 </h4>
                <input type="file" name="import_csv" value=" {{ csrf_token() }}" >
		<button class="btn btn-primary">Validazione File</button>
                <h5>Load Data Infile</h5>
		</form>    
                
                </div>

                <p style="margin-top: 15px;">
                            <!-- Styles -->

                <!--  <a href="{{ URL::to('#') }}"><button class="btn btn-success">Download Excel xls</button></a>-->
		<!--  <a href="{{ URL::to('#') }}"><button class="btn btn-success">Download Excel xlsx</button></a>-->
		<a href="{{ URL::to('downloadExcel/csv') }}"><button class="btn btn-success">Download CSV</button></a>
                <span style="margin-left: 10px;">
                <a href="{{ URL::to('import') }}"><button class="btn btn-success" >Home</button></a>
                </span>
                <span style="margin-left: 10px;">
                <a href="{{ URL::to('dropDb') }}"><button class="btn btn-success" >Drop DB</button></a>
                </span>
                </p>
                
    </div>
    <div>
        <?php phpinfo(); ?>

        
    </div>
</body>