@if (Auth::check())
<vue-editable data-value="{!! $texts[$context][$id] !!}" data-page="{{ $page }}" data-context="{{ $context }}" data-id="{{ $id }}">{!! nl2br($texts[$context][$id]) !!}</vue-editable>@else
{!! nl2br($texts[$context][$id]) !!}@endif