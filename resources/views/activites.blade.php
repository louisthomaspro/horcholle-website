@extends('layouts.app')
@section('pageTitle', $pageTitle)

@section('content')
    <div class="container">
        <div class="row justify-content-center mt-5">
            <div class="col-md-10">

                <div class="row">

                    <div class="col-md-5 text-style">
                        {{-- <h3 class="category mt10">
                          Notre activité
                        </h3> --}}
                        <h2 class="title">
                            @include('templates.texteditable', ["text" => ["activites", "part_1", "title"]])
                        </h2>
                        <hr class="separation">
                        <div class="text">
                            <p>
                                @include('templates.texteditable', ["text" => ["activites", "part_1", "paragraph"]])
                            </p>
                        </div>
                        <a href="{{ route('contact') }}">
                            <button class="button">Faire une demande</button>
                        </a>
                    </div>

                    <div class="col-md-7">

                        <div class="activites">
                            <a href="#activite0" data-toggle="modal" class="activite"
                               title="{{ $pictures["activite1"][0]['name'] }}"
                               style="background-image: url({{ bucket_url($pictures["activite1"][0]['img_path']) }})">
                                <div class="inner">@include('templates.texteditable', ["text" => ["activites", "skill_1", "title"]])</div>
                            </a>
                            <a href="#activite1" data-toggle="modal" class="activite"
                               title="{{ $pictures["activite2"][0]['name']}}"
                               style="background-image: url({{ bucket_url($pictures["activite2"][0]['img_path']) }})">
                                <div class="inner">@include('templates.texteditable', ["text" => ["activites", "skill_2", "title"]])</div>
                            </a>
                        </div>

                    </div>

                </div>{{-- row --}}


                <div class="text-style mt-5">
                    <h2 class="title text-center">@include('templates.texteditable', ["text" => ["activites", "part_2", "title"]])</h2>
                    <div class="text text-center mt-3">
                        <p class="ng-binding">
                            @include('templates.texteditable', ["text" => ["activites", "part_2", "paragraph"]])
                        </p>
                    </div>

                    <div class="list-img" style="position: relative; margin-bottom: 100px;">
                        @foreach ($pictures["fournisseurs"] as $pic)
                            <img alt="{{ $pic['name'] }}" src="{{ bucket_url($pic['img_path']) }}">
                        @endforeach
                    </div>
                </div>

                <div class="f-container" id="lightgallery">
                    {{--https://github.com/solodev--}}
                    @foreach ($pictures["fournisseurs-photos"] as $key => $pic)

                        <section class="section mt-5">
                            <div class="container">
                                <div class="row {{ $key%2 != 0 ? 'flex-row-reverse' : '' }}">
                                    <div class="col-md-6">
                                        <div>
                                            <img class="img-fluid img_lg" alt="{{ $pictures["fournisseurs-photos"][0]['name'] }}" src="{{ bucket_url($pictures["fournisseurs-photos"][0]['img_path']) }}" data-src="{{ bucket_url($pictures["fournisseurs-photos"][0]['img_path']) }}" style="width: 100%;" />
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-lg-5 d-flex align-items-center mt-4 mt-md-0 {{ $key%2 != 0 ? 'mr-auto' : 'ml-auto' }}">
                                        <div style="width: 100%;">
                                            <h3 class="mb-4">@include('templates.texteditable', ["text" => ["activites", "fournisseurs-photos-".$key, "title"]])</h3>
                                            <p class="margin-top-s">@include('templates.texteditable', ["text" => ["activites", "fournisseurs-photos-".$key, "paragraph"]])</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>

                    @endforeach
                </div>

            </div>
        </div>
    </div>


    <!-- activites modal -->
    <div id="activite0" class="activites-modal modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="close-modal" data-dismiss="modal">
                    <div class="lr">
                        <div class="rl">
                        </div>
                    </div>
                </div>
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-md-10">
                            <div class="modal-body text-style">
                                <!-- Project Details Go Here -->
                                <h2 class="title">@include('templates.texteditable', ["text" => ["activites", "skill_1", "title"]])</h2>
                                <p class="text mt-4 mb-4">
                                    @include('templates.texteditable', ["text" => ["activites", "skill_1", "paragraph"]])
                                </p>
                                <div class="row mb-3">
                                    @foreach ($pictures["activite1"] as $pic)
                                        <div class="col-md-6">
                                            <img width="100%" alt="{{ $pic['name'] }}"
                                                 src="{{ bucket_url($pic['img_path']) }}">
                                        </div>
                                    @endforeach
                                </div>

                                <button type="button" class="button" data-dismiss="modal">Revenir</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="activite1" class="activites-modal modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="close-modal" data-dismiss="modal">
                    <div class="lr">
                        <div class="rl">
                        </div>
                    </div>
                </div>
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-md-10">
                            <div class="modal-body text-style">
                                <!-- Project Details Go Here -->
                                <h2 class="title">@include('templates.texteditable', ["text" => ["activites", "skill_2", "title"]])</h2>
                                <p class="text mt-4">
                                    @include('templates.texteditable', ["text" => ["activites", "skill_2", "paragraph"]])
                                </p>
                                <div class="row mt-4 mb-4">
                                    @foreach ($pictures["activite2"] as $pic)
                                        <div class="col-md-6">
                                            <img width="100%" alt="{{ $pic['name'] }}"
                                                 src="{{ bucket_url($pic['img_path']) }}">
                                        </div>
                                    @endforeach
                                </div>

                                <button type="button" class="button" data-dismiss="modal">Revenir</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')

    <script src="{{ asset('js/util.js') }}"></script>
    <script src="{{ asset('js/modal.js') }}"></script>


    <script>
        $('#lightgallery').lightGallery({
            getCaptionFromTitleOrAlt: false,
            selector: '.img_lg',
            download: false
        });
    </script>

@endsection
