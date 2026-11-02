{{-- Read-only card for a Note, used by the entity timeline embed.
     Accepts: $record (\VentureDrake\LaravelCrm\Models\Note) --}}
<div class="crm-card-card">
    <div class="crm-card-card-head">
        <div class="crm-card-card-meta">
            {{ $record->created_at?->diffForHumans() }} - {{ $record->createdByUser?->name }}
        </div>
    </div>
    <div class="crm-card-card-body">{{ $record->content }}</div>
    @if ($record->noted_at)
        <div class="crm-card-card-footer">
            <span class="crm-card-pill">{{ __('laravel-crm-filament::labels.fields.noted_at_on', ['time' => $record->noted_at->format('H:i'), 'date' => $record->noted_at->format('d.m.Y')]) }}</span>
        </div>
    @endif
</div>
