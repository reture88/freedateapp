<?php

namespace App\Filament\Resources\MetricEvents;

use App\Filament\Resources\MetricEvents\Pages\ListMetricEvents;
use App\Filament\Resources\MetricEvents\Pages\ViewMetricEvent;
use App\Filament\Resources\MetricEvents\Schemas\MetricEventInfolist;
use App\Filament\Resources\MetricEvents\Tables\MetricEventsTable;
use App\Models\MetricEvent;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class MetricEventResource extends Resource
{
    protected static ?string $model = MetricEvent::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function infolist(Schema $schema): Schema
    {
        return MetricEventInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MetricEventsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMetricEvents::route('/'),
            'view' => ViewMetricEvent::route('/{record}'),
        ];
    }
}
