<?php

namespace VentureDrake\LaravelCrmFilament\Support;

use Barryvdh\DomPDF\Facade\Pdf;
use VentureDrake\LaravelCrmFilament\Contracts\RendersPdf;

/**
 * The upstream path through the Pdf facade: the default of laravel-crm-filament.pdf_renderer.
 */
final class DompdfViewRenderer implements RendersPdf
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function render(string $view, array $data, string $paper = 'a4', string $orientation = 'portrait'): string
    {
        return Pdf::setOption(['fontDir' => public_path('vendor/laravel-crm/fonts')])->setPaper($paper, $orientation)
            ->loadView($view, $data)->output();
    }
}
