<?php

namespace App\Http\Controllers;

use App\Models\Asistencia;
use App\Models\Empresa;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Ramsey\Uuid\Uuid;
class PdfController extends Controller
{
    //
    public function pdfAsistencia($user, $fecha_inicio, $fecha_final)
    {
     
        $asistencias = Asistencia::with('empleado')
        ->where('empleado_id', $user)
        ->whereBetween('fecha', [$fecha_inicio, $fecha_final])
        ->get();
        $empresa= Empresa::first();
        $pdf = Pdf::loadView('pdf.asistencia',['asistencias'=>$asistencias, 'empresa'=>$empresa]);
        $name = Uuid::uuid4()->toString();
        return $pdf->download("$name.pdf");

        
    }
}
