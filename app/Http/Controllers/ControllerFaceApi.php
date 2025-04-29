<?php

namespace App\Http\Controllers;

use App\Models\Asistencia;
use App\Models\Empleado;
use App\Models\Horario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Psy\Command\WhereamiCommand;

class ControllerFaceApi extends Controller
{
    //

    public function index()
    {
        return view('faceapi.index');
    }


    public function show(Request $request)
    {
        $nombre = $request->query('name'); // nombre del empleado

        $empleado = DB::table('empleados')
            ->where('cedula', $nombre)
            ->first();
        
        if (!$empleado || !$empleado->image) {
            return response()->json(['error' => 'Imagen no encontrada'], 404);
        }
        
        // Ruta del archivo físico
        $path = storage_path('app/public/' . $empleado->image); // o public_path('images/' . $empleado->image)
        
        if (!file_exists($path)) {
            return response()->json(['error' => 'Archivo no encontrado en el servidor'], 404);
        }
        
        $mime = mime_content_type($path);
        $contenido = file_get_contents($path);
        
        return response($contenido, 200)
                ->header('Content-Type', $mime);
    }


    /**
     * Obtiene todas las etiquetas (nombres de los usuarios) y el total de usuarios.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getLabels(Request $request)
    {
        $users = Empleado::select('nombre', 'cedula')->get();
        
        // Devolvemos array de objetos con ambos campos
        $labels = $users->map(function($user) {
            return [
                'displayLabel' => $user->nombre,  // Solo el nombre para mostrar
                'fullData' => $user->nombre . ' (' . $user->cedula . ')', // Opcional: si quieres tener ambos concatenados
                'nombre' => $user->nombre,
                'cedula' => $user->cedula
            ];
        })->toArray();
    
        return response()->json([
            'labels' => $labels,
            'totalUsers' => count($labels),
        ]);
    }
public function getUser(Request $request)
    {
        // Obtener el nombre del usuario de la solicitud
        $nombre = $request->query('name');
    
        $empleado = DB::table('empleados')->where('cedula', $nombre)->first();
    
        if (!$empleado) {
            return response()->json(['error' => 'Empleado no encontrado'], 404);
        }
    
        return response()->json([
            'id' => $empleado->id
        ]);
    }


    public function checkhoraentrada(Request $request)
    {
        $idempledo = $request->query('usuarioId'); // id del empleado

        $empleado = Asistencia::where('empleado_id', $idempledo)
            ->where('fecha', date('Y-m-d'))
            ->where('hora_entrada', '!=', null);

        
            if ($empleado->exists()) {
                return response()->json(['entryExists' => true], 200);
            }
            
            // <-- Esto es lo que faltaba
            return response()->json(['entryExists' => false], 200);
    }
    public function checkhorasalida(Request $request)
    {
        $idempledo = $request->query('usuarioId'); // id del empleado

        $empleado = Asistencia::where('empleado_id', $idempledo)
            ->where('fecha', date('Y-m-d'))
            ->where('hora_salida', '!=', null);

        
            if ($empleado->exists()) {
                return response()->json(['entryExists' => true], 200);
            }
            
            // <-- Esto es lo que faltaba
            return response()->json(['entryExists' => false], 200);
    }


    public function registrarEntrada(Request $request)
    {
        $data = $request->json()->all();
        $idempledo = $data['usuarioId'] ?? null; // Uso de null coalescing por si no viene

        if (!$idempledo) {
            return response()->json(['error' => 'ID de usuario requerido'], 400);
        }


        $asistencia = new Asistencia();
        $asistencia->empleado_id = $idempledo;
        $asistencia->fecha = date('Y-m-d');
        $asistencia->hora_entrada = date('H:i:s');
        $horaEntradaHorario = Horario::where('accion', 'entrada')->first()->desde;
        $horaLimite = date('H:i:s', strtotime($horaEntradaHorario . ' +30 minutes'));

        if ($asistencia->hora_entrada > $horaLimite) {
            $asistencia->estado = 'atrasado';
        } else {
            $asistencia->estado = 'presente';
        }
     
        $asistencia->save();

        return response()->json(['success' => 'Entrada registrada'], 200);
    }
    public function registrarSalida(Request $request)
    {
        $data = $request->json()->all();
        $idempledo = $data['usuarioId'] ?? null; // Uso de null coalescing por si no viene

        if (!$idempledo) {
            return response()->json(['error' => 'ID de usuario requerido'], 400);
        }


        $asistencia = Asistencia::where('empleado_id', $idempledo)
            ->where('fecha', date('Y-m-d'))
            ->first();

        if (!$asistencia) {
            return response()->json(['error' => 'No se encontró la asistencia'], 404);
        }

        $asistencia->hora_salida = date('H:i:s');
        $asistencia->save();

        return response()->json(['ok' => 'Salida registrada'], 200);
    }

    public function getHorarios()
    {
        $horarios = Horario::select('accion', 'desde', 'hasta')->get();
        if ($horarios->isEmpty()) {
            return response()->json(['error' => 'No se encontraron horarios'], 404);
        }
        return response()->json($horarios);
    }
}


