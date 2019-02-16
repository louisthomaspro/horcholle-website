
@extends('layouts.app')
@section('pageTitle', $pageTitle)

@section('content')
  
  <div class="container">
    <div class="row justify-content-center mt-5">
      <div class="col-md-10">

        <div class="text-style">
          <h2 class="title">
            @include('templates.texteditable', ["page" => "presentation", "context" => "part_1", "id" => "title"])
          </h2>
          <hr class="separation">
        </div>

        <p>@include('templates.texteditable', ["page" => "presentation", "context" => "part_1", "id" => "paragraph"])</p>

        <div class="row albumeffect f-gallery mt-4">

          @foreach ($categories as $category)
            <div class="col-lg-6 mb-4 f-element">
              <figure class="effect-julia">
                <div class="bg" style="background-image: url({{ bucket_url($category['thumbnail']['img_path']) }});" role="img" aria-label="{{ $category['thumbnail']['name'] }}"></div>
    						<figcaption>
    							<h2>{{ $category['name'] }}</h2>
    							<a href="realisations/{{ $category['url_friendly'] }}">View more</a>
    						</figcaption>
    					</figure>
            </div>
          @endforeach

        </div>

      </div>
    </div>
  </div>
@endsection

@section('script')
  <!-- <script src="{{ asset('js/gallery_filter.js') }}"></script> -->
@endsection