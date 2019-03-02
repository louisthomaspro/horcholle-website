<?php
//    page = $text[0]
//    context = $text[1]
//    id = $text[2]
?>
@if (Auth::check())
<vue-editable data-value="{!! $texts[$text[1]][$text[2]] !!}" data-page="{{ $text[0] }}" data-context="{{ $text[1] }}" data-id="{{ $text[2] }}">{!! nl2br($texts[$text[1]][$text[2]]) !!}</vue-editable>@else
{!! nl2br($texts[$text[1]][$text[2]]) !!}@endif
