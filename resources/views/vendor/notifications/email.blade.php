<x-mail::message>
# {{ $greeting ?? 'Halo!' }}

@foreach ($introLines as $line)
{{ $line }}

@endforeach

@isset($actionText)
<x-mail::button :url="$actionUrl" color="primary">
{{ $actionText }}
</x-mail::button>
@endisset

@foreach ($outroLines as $line)
{{ $line }}

@endforeach

@isset($salutation)
{{ $salutation }}
@else
Salam,<br>
**{{ config('app.name') }}**
@endisset

@isset($actionText)
<x-mail::subcopy>
Jika tombol "{{ $actionText }}" tidak berfungsi, salin dan tempel URL berikut ke browser Anda:
[{{ $displayableActionUrl }}]({{ $actionUrl }})
</x-mail::subcopy>
@endisset
</x-mail::message>