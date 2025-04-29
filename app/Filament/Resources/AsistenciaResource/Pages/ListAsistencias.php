<?php

namespace App\Filament\Resources\AsistenciaResource\Pages;

use App\Filament\Resources\AsistenciaResource;
use App\Models\Empleado;
use App\Models\User;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Forms\Form;
use Filament\Actions\Action;
use Filament\Forms;

class ListAsistencias extends ListRecords
{
    protected static string $resource = AsistenciaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Action::make("pdf de asistencia")
                ->color('success')
                ->form([
                    Forms\Components\Select::make('empleado_id')
                        ->label('Empleado')
                        ->options(Empleado::all()->pluck('nombre', 'id'))
                        ->searchable()
                        ->preload()
                        ->required(),
                    Forms\Components\DatePicker::make('fecha_inicio')
                        ->label('Fecha de Inicio')
                        ->required(),
                    Forms\Components\DatePicker::make('fecha_final')
                        ->label('Fecha Final')
                        ->required(),
                ])
                ->requiresConfirmation()
                ->action(function (array $data) {
                    $userId = $data['empleado_id'];
                    $fechaInicio = $data['fecha_inicio'];
                    $fechaFinal = $data['fecha_final'];
                    $url = route('pdf.asistencia', [
                        'user' => $userId,
                        'fecha_inicio' => $fechaInicio,
                        'fecha_final' => $fechaFinal,
                    ]);

                    redirect()->to($url);
                })
        ];
    }
}
