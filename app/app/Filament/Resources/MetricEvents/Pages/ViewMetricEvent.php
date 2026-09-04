<?php

namespace App\Filament\Resources\MetricEvents\Pages;

use App\Filament\Resources\MetricEvents\MetricEventResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewMetricEvent extends ViewRecord
{
    protected static string $resource = MetricEventResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
