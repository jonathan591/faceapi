<?php

namespace App\Filament\Widgets;

use App\Models\Asistencia;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Filament\Forms;
class BlogPostsChart extends ChartWidget
{
    protected static ?string $heading = 'Horas trabajadas por empleado';
    protected static ?int $sort = 2;

    public ?string $fecha_inicio = null;
    public ?string $fecha_final = null;

    protected function getData(): array
    {
        $horas = $this->calcularHorasTrabajadas();

        return [
            'datasets' => [
                [
                    'label' => 'Horas trabajadas',
                    'data' => array_values($horas),
                    'backgroundColor' => $this->generarColores(count($horas)),
                    'borderColor' => '#ffffff',
                ],
            ],
            'labels' => array_keys($horas),
        ];
    }

    protected function getType(): string
    {
        return 'polarArea';
    }

    protected function getFormSchema(): array
    {
        return [
            Forms\Components\DatePicker::make('fecha_inicio')
                ->label('Fecha de Inicio')
                ->default(now()->startOfWeek())
                ->required(),
            Forms\Components\DatePicker::make('fecha_final')
                ->label('Fecha Final')
                ->default(now()->endOfWeek())
                ->required(),
        ];
    }

    public function calcularHorasTrabajadas(): array
    {
        $inicio = $this->fecha_inicio ?? now()->startOfWeek()->toDateString();
        $final = $this->fecha_final ?? now()->endOfWeek()->toDateString();

        $asistencias = Asistencia::with('empleado')
            ->whereBetween('fecha', [$inicio, $final])
            ->get();

        $horasPorEmpleado = [];

        foreach ($asistencias as $asistencia) {
            if (!$asistencia->hora_entrada || !$asistencia->hora_salida || !$asistencia->empleado) {
                continue;
            }

            try {
                $entrada = Carbon::createFromFormat('H:i:s', $asistencia->hora_entrada);
                $salida = Carbon::createFromFormat('H:i:s', $asistencia->hora_salida);

                if ($salida->lessThanOrEqualTo($entrada)) {
                    continue;
                }

                $horas = $entrada->diffInMinutes($salida) / 60;
                $nombre = $asistencia->empleado->nombre;

                $horasPorEmpleado[$nombre] = ($horasPorEmpleado[$nombre] ?? 0) + $horas;
            } catch (\Exception $e) {
                continue;
            }
        }

        return $horasPorEmpleado;
    }

    private function generarColores(int $cantidad): array
    {
        $colores = [];
        for ($i = 0; $i < $cantidad; $i++) {
            $colores[] = sprintf('#%06X', mt_rand(0, 0xFFFFFF));
        }
        return $colores;
    }
}