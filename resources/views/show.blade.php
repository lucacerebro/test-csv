<html lang="it">

    <head>
	<title>VisualizzaArticoli</title>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" >
        <style>
table, th, td {
    border: 1px solid black;
    border-collapse: collapse;
}
th, td {
    padding: 5px;
    text-align: left;    
}
</style>

    </head>    
    <body>
            
  
        <nav class="navbar navbar-default">
	<div class="container-fluid">
		<div class="navbar-header">
			<a class="navbar-brand" href="#">Elenco degli articoli presenti</a>
		</div>
	</div>
	</nav> 
        

        <div class="container">
            <table>
                
             <tr>
                    <th>
                        Id Articolo
                    </th>
                    <th>
                        Codice
                    </th>
              </tr>
           @foreach ($arts as $art)
           <tr> 
               <td>
                   <a href="showone/{{$art->id}}">{{ $art->id }}</a>
               </td>
               <td>
                   {{ $art->codice }}
               </td>
           </tr>
           @endforeach
             
           
         
           </table>
     
        </div>
                <div>
                    <div class="container" style="margin-top:10px;">
        <a href="{{ URL::previous()}}"><button class="btn btn-success">Back</button></a>
        <a href="{{ URL::to('import') }}"><button class="btn btn-success" >HOme</button></a>
        </div>
                    
    </div>
        
        <div  align="center">
                {!! $arts->render() !!}
        </div>
    </body>
</html>