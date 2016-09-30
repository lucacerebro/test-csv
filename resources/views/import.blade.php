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
	 
                <h3>
				On Server (local)
				Visualizza tutti gli Articoli</h3>
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
                <h3>Caricamento CSV Articoli con Query(LOAD DATA LOCAL INFILE) (tabella articolo)</h3>
                <div style="border: 4px solid #a1a1a1;margin-top: 15px;padding: 10px; width: auto">
               
                <form action="{{ URL::to('import_csv') }}" class="form-horizontal" method="post" enctype="multipart/form-data">
		<input type="file" name="import_csv" value=" {{ csrf_token() }}" >
		<button class="btn btn-primary">Validazione File</button>
                <div> Validate2 </div>
		</form>

                </div>

                <p style="margin-top: 15px;">
                <a href="{{ URL::to('#') }}"><button class="btn btn-success">Download Excel xls</button></a>
		<a href="{{ URL::to('#') }}"><button class="btn btn-success">Download Excel xlsx</button></a>
		<a href="{{ URL::to('#') }}"><button class="btn btn-success">Download CSV</button></a>
                <span style="margin-left: 10px;">
                <a href="{{ URL::to('import') }}"><button class="btn btn-success" >Home</button></a>
                </span>
                </p>
                
    </div>
</body>