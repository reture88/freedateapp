<?php

namespace App\Filament\Resources\MetricEvents\Pages;

use App\Filament\Resources\MetricEvents\MetricEventResource;
use Filament\Resources\Pages\ListRecords;

class ListMetricEvents extends ListRecords
{
    protected static string $resource = MetricEventResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
