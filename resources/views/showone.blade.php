<html lang="it">

    <head>
	<title>VisualizzaArticolo</title>
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
			<a class="navbar-brand" href="#">Pagina di visualizzazione articolo singolo</a>
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
                    <th>
                        Descrizione
                    </th>
                    <th>
                        Iva
                    </th>
              </tr>
         
           <tr> 
               <td>
                    {{ $art->id }}
               </td>
               <td>
                   {{ $art->codice }}
               </td>
               <td>
                   {{$art->descrizione}}
               </td>
               <td>
                   {{$art->iva}}
               </td>
           </tr>
         
             
           
         
           </table>
     
        </div>
        
        
        <div class="container" style="margin-top: 10px;">
        <a href="{{ URL::previous() }}"><button class="btn btn-success">Back</button></a>
        <a href="{{ URL::to('import') }}"><button class="btn btn-success">HOme</button></a>
        </div>
        
        <div  align="center">
            Copyright 2016
        </div>
    </body>
</html>