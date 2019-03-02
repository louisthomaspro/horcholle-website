
@extends('layouts.app')
@section('pageTitle', $pageTitle)

@section('content')
  {{-- <div class="cover-center" style="height: 100%; background-image: url({{ bucket_url('img/accueil/accueil.jpg') }});"> --}}
  <div style="height: calc(100% - 75px);">
    <ul class="bck-parent cb-slideshow ">

        @foreach ($pictures['background'] as $picture)
            <li><span style="background-image: url({{ bucket_url($picture['img_path']) }});" title="{{ $picture["name"] }}">{{ $picture['name'] }}</span></li>
        @endforeach
    </ul>
    <div class="container">

      <div class="hometitle">
        <h1>@include('templates.texteditable', ["text" => ["accueil", "title", "main_title"]])</h1>
        <hr>
        <h2>@include('templates.texteditable', ["text" => ["accueil", "title", "sub_title"]])</h2>

        {{-- <a href="{{ route('realisations') }}"><button type="button" class="btn-discover mt-3" data-dismiss="modal">Découvrir nos réalisations</button></a> --}}
      </div>

    </div>
  </div>



@endsection
