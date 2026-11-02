<?php

namespace VentureDrake\LaravelCrmFilament\Widgets;

use Filament\Widgets\ChartWidget;
use VentureDrake\LaravelCrm\Models\Lead;
use VentureDrake\LaravelCrm\Models\PipelineStage;

class LeadsByStageChart extends ChartWidget
{
    protected int | string | array $columnSpan = 1;

    public function getHeading(): ?string
    {
        return __('laravel-crm-filament::labels.dashboard.leads_by_stage');
    }

    protected function getData(): array
    {
        $stages = PipelineStage::query()->orderBy('order')->get();

        $counts = Lead::query()
            ->whereNull('converted_at')
            ->whereIn('pipeline_stage_id', $stages->pluck('id'))
            ->selectRaw('pipeline_stage_id, COUNT(*) as total')
            ->groupBy('pipeline_stage_id')
            ->pluck('total', 'pipeline_stage_id');

        return [
            'datasets' => [
                [
                    'label' => __('laravel-crm-filament::labels.dashboard.open_leads'),
                    'data' => $stages->map(fn ($s) => (int) ($counts[$s->id] ?? 0))->all(),
                    'backgroundColor' => '#05b3a9',
                ],
            ],
            'labels' => $stages->pluck('name')->all(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
