@props([
    'id' => 'dropdownMenu',
    'items' => [],
    'position' => 'right', // 'right' or 'left'
    'minWidth' => '220px'
])

<div class="dropdown-menu" 
     id="{{ $id }}" 
     style="--dropdown-min-width: {{ $minWidth }};"
     data-position="{{ $position }}">
    
    @if(!empty($items))
        @foreach($items as $item)
            @if($item['type'] === 'link')
                <a href="{{ $item['url'] }}" 
                   class="dropdown-item {{ $item['class'] ?? '' }}"
                   @if(isset($item['target'])) target="{{ $item['target'] }}" @endif>
                    @if(isset($item['icon']))
                        {!! $item['icon'] !!}
                    @endif
                    <span>{{ $item['label'] }}</span>
                </a>
                
            @elseif($item['type'] === 'button')
                <button type="button" 
                        class="dropdown-item {{ $item['class'] ?? '' }}"
                        @if(isset($item['onclick'])) onclick="{{ $item['onclick'] }}" @endif>
                    @if(isset($item['icon']))
                        {!! $item['icon'] !!}
                    @endif
                    <span>{{ $item['label'] }}</span>
                </button>
                
            @elseif($item['type'] === 'form')
                <form method="POST" 
                      action="{{ $item['action'] }}" 
                      class="dropdown-item-form">
                    @csrf
                    @if(isset($item['method']) && strtoupper($item['method']) !== 'POST')
                        @method($item['method'])
                    @endif
                    <button type="submit" 
                            class="dropdown-item {{ $item['class'] ?? '' }}">
                        @if(isset($item['icon']))
                            {!! $item['icon'] !!}
                        @endif
                        <span>{{ $item['label'] }}</span>
                    </button>
                </form>
                
            @elseif($item['type'] === 'divider')
                <div class="dropdown-divider"></div>
                
            @elseif($item['type'] === 'header')
                <div class="dropdown-header">{{ $item['label'] }}</div>
            @endif
        @endforeach
    @else
        {{ $slot }}
    @endif
</div>