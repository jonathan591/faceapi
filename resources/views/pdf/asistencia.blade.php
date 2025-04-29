<style>

table { vertical-align: top; }
tr    { vertical-align: top; }
td    { vertical-align: top; }
.midnight-blue{
	background:#335fe0;
	padding: 4px 4px 4px;
	color:white;
	font-weight:bold;
	font-size:14px;
}
.silver{
	background:white;
	padding: 3px 4px 3px;
}
.clouds{
	background:#ecf0f1;
	padding: 3px 4px 3px;
}
.border-top{
	border-top: solid 1px #bdc3c7;
    border-left: solid 1px #bdc3c7;
    border-right: solid 1px #bdc3c7;
    border-width: solid 1px #bdc3c7;
    padding: 3px 4px 3px;
	
}
.border-left{
	border-left: solid 1px #bdc3c7;
}
.border-right{
	border-right: solid 1px #bdc3c7;
}
.border-bottom{
	border-bottom: solid 1px #bdc3c7;
}
table.page_footer {width: 100%; border: none; background-color: white; padding: 2mm;border-collapse:collapse; border: none;}

</style>
<table cellspacing="0" style="width: 100%;">
        <tr>
       
            <td style="width: 25%; color: #444444;">
           
            <img style="width: 100%;" src="storage/{{ $empresa->image }}" alt="Logo">

            <br>
          
            </td>
          
                
           
			<td style="width: 50%; color: #34495e;font-size:12px;text-align:center">
                <span style="color: #34495e;font-size:14px;font-weight:bold"> DATOS EMMPRESA</span>
				<br>Nombre :{{$empresa->nombre }}<br> 
				Email: {{ $empresa->email}} <br>
                Ruc : {{$empresa->ruc}} <br>
                Direccion:{{$empresa->direccion}}
                
            </td>
         
			<td style="width: 25%;text-align:right">
            Fecha : <?php echo date('M. d, Y'); ?>  
			</td>
			
        </tr>
    </table>
    
<h2 style="text-align:center">Asistencia  {{ $asistencias->first()->empleado->nombre }}</h2>
<table cellspacing="0" style="width: 100%; text-align: left; font-size: 11pt;">
    <thead>
      
        <th style="width: 20% ; text-align: center;" class='midnight-blue'>fecha</th>
        <th style="width: 30% ; text-align: center;" class='midnight-blue'>Entrada</th>
        <th style="width: 30% ; text-align: center;" class='midnight-blue'>Salida</th>
        <th style="width:20%; " class='midnight-blue'>Estado</th>
    </thead>
    <tbody>
        @foreach ($asistencias as $item)
        <tr>
            
            <td class='border-top' style="width: 20%; text-align: center">{{ \Carbon\Carbon::parse($item->fecha)->format('M.d,Y') }}</td>
            <td class='border-top' style="width: 30%; text-align: center">{{ $item->hora_entrada }}</td>
            <td class='border-top' style="width: 30%; text-align: center">{{ $item->hora_salida }}</td>
            <td class='border-top' style="width: 20%; text-align: center">{{ $item->estado }}</td>
        </tr>

        @endforeach
    </tbody>
</table>