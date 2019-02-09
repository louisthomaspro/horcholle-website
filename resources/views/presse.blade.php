
@extends('layouts.app')
@section('pageTitle', $pageTitle)

@section('content')
  <div class="container">
    <div class="row justify-content-center mt-5">
      <div class="col-md-10">

        <div class="text-style">
          <h2 class="title">
            On parle de nous !
          </h2>
          <hr class="separation">
        </div>

          <ul id="waterfall">
            <li data-src="{{ bucket_url('img/presse/1.jpg') }}">
                <img src="{{ bucket_url('img/presse/1.jpg') }}" width="100%" alt="Prix 1"/>
            </li>
            <li data-src="{{ bucket_url('img/presse/2.jpg') }}">
                <img src="{{ bucket_url('img/presse/2.jpg') }}" width="100%" alt="Prix 2"/>
            </li>
            <li data-src="{{ bucket_url('img/presse/3.jpg') }}">
                <img src="{{ bucket_url('img/presse/3.jpg') }}" width="100%" alt="Prix 3"/>
            </li>
            <li data-src="{{ bucket_url('img/presse/4.jpg') }}">
                <img src="{{ bucket_url('img/presse/4.jpg') }}" width="100%" alt="Prix 4"/>
            </li>
            <li data-src="{{ bucket_url('img/presse/5.jpg') }}">
                <img src="{{ bucket_url('img/presse/5.jpg') }}" width="100%" alt="Prix 5"/>
            </li>

          </ul>

      </div>
    </div>
  </div>
@endsection

@section('script')
  <script src="{{ asset('js/newWaterfall.js') }}" type="text/javascript"></script>
  <script>
    $('#waterfall').NewWaterfall();

    $('#waterfall').lightGallery();
  </script>
@endsection
