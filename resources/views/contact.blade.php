
@extends('layouts.app')
@section('pageTitle', $pageTitle)

@section('content')
  <div class="container">
    <div class="row justify-content-center mt-5 mb-5 flex-column flex-md-row">
      <div class="col-md-6">

        
        
        <h2 class="title text-center mt-4">@include('templates.texteditable', ["text" => ["contact", "title", "main_title"]])</h2>
        <h4 class="text-center text mt-4">@include('templates.texteditable', ["text" => ["contact", "title", "sub_title"]])</h4>
        <div class="mt-5 ml-3">
            <div class="contact_info mb-3">
            <i class="fa fa-map-marker" aria-hidden="true"></i>@include('templates.texteditable', ["text" => ["contact", "info", "adress"]])
            </div>
            <div class="contact_info mb-3">
            <i class="fa fa-clock-o" aria-hidden="true"></i>@include('templates.texteditable', ["text" => ["contact", "info", "schedule"]])
            </div>
            <div class="contact_info mb-3">
            <i class="fa fa-phone" aria-hidden="true"></i>@include('templates.texteditable', ["text" => ["contact", "info", "phone"]])
            </div>
            <div class="contact_info mb-3">
            <i class="fa fa-envelope" aria-hidden="true"></i>@include('templates.texteditable', ["text" => ["contact", "info", "mail"]])
            </div>
            <div class="contact_info mb-3">
            <i class="fa fa-facebook" aria-hidden="true"></i><a href="https://www.facebook.com/SARL-Horcholle-Fabien-150517972410986/" target="_blank">Suivez-nous !</a>
            </div>
            </div>


      </div>


      <div class="col-md-6">
        <iframe class="mt-4" src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2602.4603695295764!2d2.984862815948461!3d49.28662167933171!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x47e8871d53538297%3A0xa55dcc5bbf31b48c!2sSarl+Horcholle+Fabien!5e0!3m2!1sen!2sfr!4v1530565286456" width="100%" height="380" frameborder="0" style="border:0" allowfullscreen></iframe>
      </div>

    </div>
  </div>
@endsection
