@props([
    'title',
    'description',
    'time',
    'iconBg' => 'rgba(255, 140, 66, 0.2)',
    'iconColor' => 'var(--primary-orange)'
])

<div class="activity-item">
    <div class="activity-icon" style="background: {{ $iconBg }}; color: {{ $iconColor }};">
        {{ $icon }}
    </div>
    
    <div class="activity-content">
        <div class="activity-title">{{ $title }}</div>
        <div class="activity-description">{{ $description }}</div>
    </div>
    
    <div class="activity-time">{{ $time }}</div>
</div>

<style>
.activity-item {
    display: flex;
    align-items: center;
    gap: var(--space-4);
    padding: var(--space-4);
    border-radius: var(--radius-lg);
    transition: all var(--transition-base);
}

.activity-item:hover {
    background: rgba(255, 255, 255, 0.02);
}

.activity-icon {
    width: 40px;
    height: 40px;
    border-radius: var(--radius-lg);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.activity-icon svg {
    width: 20px;
    height: 20px;
}

.activity-content {
    flex: 1;
}

.activity-title {
    font-weight: 600;
    margin-bottom: var(--space-1);
}

.activity-description {
    font-size: var(--text-sm);
    color: var(--text-secondary);
}

.activity-time {
    font-size: var(--text-xs);
    color: var(--text-muted);
    flex-shrink: 0;
}
</style>