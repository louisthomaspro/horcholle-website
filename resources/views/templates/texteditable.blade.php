<?php
//    page = $text[0]
//    context = $text[1]
//    id = $text[2]
?>

@php ($display_text = ( (array_key_exists($text[1], $texts) && array_key_exists($text[2], $texts[$text[1]])) ? $texts[$text[1]][$text[2]] : "À définir..."))

@if (Auth::check()) {{--admin--}}
        <vue-editable data-value="{!! ($display_text) !!}" data-page="{{ $text[0] }}" data-context="{{ $text[1] }}" data-id="{{ $text[2] }}">
            {!! nl2br($display_text) !!}
        </vue-editable>
@else {{--visitor--}}
    {!! nl2br($display_text) !!}
@endif
