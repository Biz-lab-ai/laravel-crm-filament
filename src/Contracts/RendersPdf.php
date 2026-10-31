<?php

namespace VentureDrake\LaravelCrmFilament\Contracts;

/**
 * The one door of every document PDF: the bytes of a blade view rendered with its data. The host may bind its own
 * renderer by config laravel-crm-filament.pdf_renderer.
 */
interface RendersPdf
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function render(string $view, array $data, string $paper = 'a4', string $orientation = 'portrait'): string;
}
