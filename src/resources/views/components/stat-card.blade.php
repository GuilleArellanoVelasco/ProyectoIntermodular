@props([
    'label',
    'value',
    'change',
    'changeType' => 'positive',
    'iconBg' => 'rgba(255, 140, 66, 0.2)',
    'period' => 'vs. mes anterior'
])

<div class="stat-card">
    <div class="stat-header">
        <span class="stat-label">{{ $label }}</span>
        <div class="stat-icon" style="background: {{ $iconBg }}">
            {{ $icon }}
        </div>
    </div>
    
    <div class="stat-value">{{ $value }}</div>
    
    <div class="stat-footer">
        <span class="stat-change {{ $changeType }}">
            @if($changeType === 'positive')
                <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
                </svg>
            @else
                <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                </svg>
            @endif
            {{ $change }}
        </span>
        <span>{{ $period }}</span>
    </div>
</div>