
@extends('layouts.app')
@section('pageTitle', $pageTitle)

@section('content')

  {{-- <div class="cover-center" style="height: 100%; background-image: url({{ bucket_url('img/accueil/accueil.jpg') }});"> --}}
  <div style="height: calc(100% - 75px);">
    <ul class="bck-parent cb-slideshow ">
        <li><span style="background-image: url({{ bucket_url($pictures["background"]["1"]["img_path"]) }});" title="{{ $pictures["background"]["1"]["alt"] }}">Image 01</span></li>
        <li><span style="background-image: url({{ bucket_url($pictures["background"]["2"]["img_path"]) }});" title="{{ $pictures["background"]["2"]["alt"] }}">Image 02</span></li>
        <li><span style="background-image: url({{ bucket_url($pictures["background"]["3"]["img_path"]) }});" title="{{ $pictures["background"]["3"]["alt"] }}">Image 03</span></li>
        <li><span style="background-image: url({{ bucket_url($pictures["background"]["4"]["img_path"]) }});" title="{{ $pictures["background"]["4"]["alt"] }}">Image 04</span></li>
    </ul>
    <div class="container">

      <div class="hometitle">
        <h1>@include('templates.texteditable', ["page" => "accueil", "context" => "title", "id" => "main_title"])</h1>
        <hr>
        <h2>@include('templates.texteditable', ["page" => "accueil", "context" => "title", "id" => "sub_title"])</h2>

        {{-- <a href="{{ route('realisations') }}"><button type="button" class="btn-discover mt-3" data-dismiss="modal">Découvrir nos réalisations</button></a> --}}
      </div>

    </div>
  </div>



@endsection