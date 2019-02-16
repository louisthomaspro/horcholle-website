
@extends('layouts.app')
@section('pageTitle', $category['name'])

@section('content')
  <div class="container">
    <div class="row justify-content-center mt-5">
      <div class="col-md-9">

        <div class="text-style">
          <h2 class="title mt-5 mb-5 text-center">
            {{ $category['name'] }}
          </h2>
        </div>

    		<div id="lightgallery">

          @foreach ($category['albums'] as $album)
          <h3>{{ $album['name'] }}</h3>
          <p>{!! $album['desc'] !!}</p>
          <div class="row mb-4">
            @foreach ($album['images'] as $image)
            <div class="col-md-6">
              <a href="{{ bucket_url($image['img_path']) }}" class="img_lg" role="img" aria-label="{{ $image['name'] }}" style="background-image: url({{ bucket_url($image['img_path']) }}); width: 100%; height: 250px; display: block;background-position: center;background-size: cover; margin-bottom: 30px;">
              </a>
            </div>
            @endforeach
          </div>    
          @endforeach

    		</div>


      </div>
    </div>
  </div>
@endsection

@section('script')
<script>
  $('#lightgallery').lightGallery({
    getCaptionFromTitleOrAlt: false,
    selector: '.img_lg',
    download: false
  });
</script>
@endsection
