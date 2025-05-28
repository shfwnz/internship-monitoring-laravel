@php
    $url = asset('storage/internship-files/' . $record->file);
@endphp

<a href="{{ $url }}" target="_blank" class="text-blue-600 hover:underline">
    📄 Download
</a>
