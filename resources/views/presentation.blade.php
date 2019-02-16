
@extends('layouts.app')
@section('pageTitle', $pageTitle)

@section('content')
  <div class="container">
    <div class="row justify-content-center mt-5">
      <div class="col-md-10">

        <div class="text-style">


          {{-- <h3 class="category text-center mt10">
            Présentation
          </h3> --}}
          <h2 class="title text-center">
            @include('templates.texteditable', ["page" => "presentation", "context" => "part_1", "id" => "title"])
          </h2>


          <ul class="timeline mt-5">
            <li>
              <div class="timestamp">
                <span class="date">@include('templates.texteditable', ["page" => "presentation", "context" => "timeline", "id" => "date_1"])</span>
              </div>
              <div class="status">
                <div class="mask"></div>
                <p class="mb-2">@include('templates.texteditable', ["page" => "presentation", "context" => "timeline", "id" => "paragraph_1"])</p>
              </div>
            </li>
            <li>
              <div class="timestamp">
                <span class="date">@include('templates.texteditable', ["page" => "presentation", "context" => "timeline", "id" => "date_2"])</span>
              </div>
              <div class="status">
                <div class="mask"></div>
                <p class="mb-2">@include('templates.texteditable', ["page" => "presentation", "context" => "timeline", "id" => "paragraph_2"])</p>
                <p></p>
              </div>
            </li>
            <li>
              <div class="timestamp">
                <span class="date">@include('templates.texteditable', ["page" => "presentation", "context" => "timeline", "id" => "date_3"])</span>
              </div>
              <div class="status">
                <div class="mask"></div>
                <p class="mb-2">@include('templates.texteditable', ["page" => "presentation", "context" => "timeline", "id" => "paragraph_3"])</p>
              </div>
            </li>
          </ul>



          <div class="text text-center mt30">
            <p>
              @include('templates.texteditable', ["page" => "presentation", "context" => "part_1", "id" => "paragraph"])
            </p>
          </div>

          <h2 class="title text-center mt-5">
            @include('templates.texteditable', ["page" => "presentation", "context" => "part_2", "id" => "title"])
          </h2>

          <div class="row row-padding mt-4" id="lightgallery">
            <div class="col-sm-5">
              @foreach ($pictures["equipe"] as $pic)
              <img class="img_lg" width="100%" alt="{{ $pic['name'] }}" src="{{ bucket_url($pic['img_path']) }}" data-src="{{ bucket_url($pic['img_path']) }}">
              @endforeach
            </div>
            <div class="col-sm-5 col-xs-12">
              <div class="text">
                <p>
                  @include('templates.texteditable', ["page" => "presentation", "context" => "part_2", "id" => "paragraph"])
                </p>
              </div>
            </div>
          </div>


          <h2 class="title text-center mt-5">
            @include('templates.texteditable', ["page" => "presentation", "context" => "part_3", "id" => "title"])
          </h2>

          <div class="text text-center mt-4">
            <p>
              @include('templates.texteditable', ["page" => "presentation", "context" => "part_3", "id" => "paragraph"])
            </p>
          </div>


          <div class="row mt-3 text-center">

          </div>
          {{-- <script type="text/javascript">
            setTimeout(function(){
              equalheight('.case-public');
            }, 1000);
          </script> --}}





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