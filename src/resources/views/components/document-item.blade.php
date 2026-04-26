@props([
    'name',
    'reference',
    'type', // pdf, word, excel
    'size',
    'date',
])

@php
// Definimos los SVG según el tipo
$iconSvg = match($type) {
    'pdf' => '<svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
             </svg>',
    'word' => '<svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
              </svg>',
    'excel' => '<svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
               </svg>',
    default => '',
};

// Iconos de acciones (descargar, ver, eliminar)
$downloadIcon = '<svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                 </svg>';
$deleteIcon = '<svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>';
@endphp

<tr>
    <td>
        <div class="doc-cell">
            <div class="doc-icon doc-icon-{{ $type }}">{!! $iconSvg !!}</div>
            <span class="doc-name">{{ $name }}</span>
        </div>
    </td>
    <td><span class="badge badge-{{ $type }}">{{ strtoupper($type) }}</span></td>
    <td>{{ $reference }}</td>
    <td><span class="badge badge-{{ $type }}">{{ strtoupper($type) }}</span></td>
    <td>{{ $size }}</td>
    <td>{{ $date }}</td>
    <td>
        <div class="table-actions">
            <button class="action-icon" title="Descargar">{!! $downloadIcon !!}</button>
            <button class="action-icon" title="Eliminar">{!! $deleteIcon !!}</button>
        </div>
    </td>
</tr>
