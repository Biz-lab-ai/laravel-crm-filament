<?php

namespace VentureDrake\LaravelCrmFilament\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
use VentureDrake\LaravelCrm\Models\Activity;

class RecentActivityList extends TableWidget
{
    protected static ?string $heading = null;

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->heading(__('laravel-crm-filament::labels.dashboard.recent_activity'))
            ->query(fn (): Builder => Activity::query()
                ->with(['causeable', 'timelineable', 'recordable'])
                ->orderByDesc('created_at'))
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('laravel-crm-filament::labels.fields.when'))
                    ->since(),
                Tables\Columns\TextColumn::make('causeable.name')
                    ->label(__('laravel-crm-filament::labels.fields.user'))
                    ->placeholder(__('laravel-crm-filament::labels.fields.system')),
                Tables\Columns\TextColumn::make('recordable_type')
                    ->label(__('laravel-crm-filament::labels.fields.action'))
                    ->formatStateUsing(fn (?string $state, $record) => static::describeAction($state, $record)),
                Tables\Columns\TextColumn::make('timelineable_type')
                    ->label(__('laravel-crm-filament::labels.fields.on'))
                    ->formatStateUsing(fn (?string $state, $record) => static::describeTimelineable($state, $record)),
            ])
            ->paginated([10, 25])
            ->defaultPaginationPageOption(10)
            ->emptyStateHeading(__('laravel-crm-filament::labels.dashboard.no_activity_yet'));
    }

    protected static function describeAction(?string $recordableType, $row): string
    {
        $verb = match (class_basename($recordableType ?? '')) {
            'Note' => __('laravel-crm-filament::labels.timeline.logged_note'),
            'Task' => __('laravel-crm-filament::labels.timeline.created_task'),
            'Call' => __('laravel-crm-filament::labels.timeline.logged_call'),
            'Meeting' => __('laravel-crm-filament::labels.timeline.scheduled_meeting'),
            'Lunch' => __('laravel-crm-filament::labels.timeline.scheduled_lunch'),
            default => __('laravel-crm-filament::labels.timeline.logged_activity'),
        };

        return $verb;
    }

    protected static function describeTimelineable(?string $type, $row): string
    {
        if (! $type || ! $row->timelineable) {
            return '—';
        }
        $parent = $row->timelineable;
        $entity = class_basename($type);
        $label = in_array($entity, ['Person', 'Organization', 'Lead', 'Deal', 'Quote'])
            ? __('laravel-crm-filament::labels.entities.'.strtolower($entity))
            : $entity;
        $title = $parent->title
            ?? $parent->name
            ?? trim(($parent->first_name ?? '') . ' ' . ($parent->last_name ?? ''))
            ?? '#' . $parent->getKey();

        return $label . ' · ' . trim((string) $title);
    }
}
