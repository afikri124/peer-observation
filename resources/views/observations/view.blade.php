@extends('layouts.master')
@section('title', 'Observation : '.$lecturer->name)

@section('css')
<link rel="stylesheet" type="text/css" href="{{asset('assets/css/vendors/select2.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('assets/css/vendors/select2-bootstrap.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('assets/css/vendors/rating.css')}}">
@endsection

@section('style')
<style>
    .input-error ~ .select2-container .select2-selection--single {
        border: 1px solid red;
    }
</style>
@endsection

@section('breadcrumb-title')
<!-- <h3></h3> -->
@endsection

@section('breadcrumb-items')
<li class="breadcrumb-item">Observations</li>
<li class="breadcrumb-item active">{{ $lecturer->name }}</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <!-- <div class="card-header">
                    <h5>Observation</h5>
                </div> -->
                <div class="card-body">
                    <form class="f1" method="POST" action="" enctype="multipart/form-data">
                        @csrf
                        <div class="f1-steps">
                            <div class="f1-progress">
                                <div class="f1-progress-line" data-now-value="16.66" data-number-of-steps="3"></div>
                            </div>
                            <div class="f1-step active">
                                <div class="f1-step-icon"><i class="fa fa-file-text"></i></div>
                                <p>Observation Data</p>
                            </div>
                            <div class="f1-step">
                                <div class="f1-step-icon"><i class="fa fa-clipboard"></i></div>
                                <p>Observation Form</p>
                            </div>
                            <div class="f1-step">
                                <div class="f1-step-icon"><i class="fa fa-file-archive-o"></i></div>
                                <p>Observation Report</p>
                            </div>
                        </div>
                        @if ($errors->any())
                        <div class="alert alert-danger outline alert-dismissible fade show" role="alert">
                            <ul>
                                @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"
                                data-bs-original-title="" title=""></button>
                        </div>
                        @endif
                        <fieldset>
                            <div class="row">
                                <div class="mb-3 mb-2 col-lg-6 col-md-12">
                                    <label>Lecturer</label>
                                    <input class="form-control" type="text" value="{{ $lecturer->name }}" disabled>
                                </div>
                                <div class="mb-3 mb-2 col-lg-6 col-md-12">
                                    <label>Auditor</label>
                                    <input class="form-control" type="text" value="{{ $data->auditor->name }}" disabled>
                                </div>
                                <div class="mb-3 mb-2 col-lg-6 col-md-12">
                                    <label>Study Program {{old('study_program')}}</label>
                                    <select class="form-control input-sm select2" name="study_program"
                                        title="Study Program" data-placeholder="Select Study Program" required>
                                        @foreach($study_program as $d)
                                        <option value="{{ $d->study_program }}"
                                            {{ ($d->study_program==$data->study_program ? "selected": "") }}
                                            {{ ($d->study_program==old('study_program') ? "selected": "") }}>
                                            {{ $d->study_program }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3 mb-2 col-lg-6 col-md-12">
                                    <label>Total Students</label>
                                    <input class="form-control" type="number" name="total_students"
                                        title="Total Students"
                                        value="{{ (old('total_students')==null ? $data->total_students : old('total_students')) }}"
                                        required>
                                </div>
                                <div class="mb-3 mb-2 col-lg-6 col-md-12">
                                    <label>Class Type</label>
                                    <input class="form-control" type="text" name="class_type" title="Class Type"
                                        value="{{ (old('class_type')==null ? $data->class_type : old('class_type')) }}"
                                        required>
                                </div>
                                <div class="mb-3 mb-2 col-lg-6 col-md-12">
                                    <label>Location</label>
                                    <input class="form-control" type="text" name="location" title="Location"
                                        value="{{ (old('location')==null ? $data->location : old('location')) }}"
                                        required>
                                </div>
                                <div class="mb-3 mb-2 col-lg-6 col-md-12">
                                    <label>Subject Course</label>
                                    <input class="form-control" type="text" name="subject_course" title="Subject Course"
                                        value="{{ (old('subject_course')==null ? $data->subject_course : old('subject_course')) }}"
                                        required>
                                </div>
                                <div class="mb-3 mb-2 col-lg-6 col-md-12">
                                    <label>Topic</label>
                                    <input class="form-control" type="text" name="topic" title="Topic"
                                        value="{{ (old('topic')==null ? $data->topic : old('topic')) }}">
                                </div>
                            </div>
                            <div class="f1-buttons">
                                <button class="btn btn-primary btn-next" type="button">Next</button>
                            </div>
                        </fieldset>
                        <fieldset>
                            <div class="mb-3 mb-2 col-lg-12 col-md-12">
                                <table class="table table-hover" width="100%">
                                    @foreach($survey as $key => $q)
                                    <tr valign="top">
                                        <th><strong>{{ $q->id }}</strong></th>
                                        <th colspan="2">{{ $q->title }} <u>{{ $q->description }}</u></th>
                                    </tr>
                                    @foreach($q->criterias as $no => $c)
                                    <tr valign="top">
                                        <td>{{ $q->id }}.{{ $no + 1 }}</td>
                                        <td>{{ $c->title }}
                                            <input type="hidden" name='questions[{{ $c->id }}][w]' value='{{ $c->weight }}'>
                                            <input type="hidden" name='questions[{{ $c->id }}][c]' value='{{ $q->id }}'>
                                        </td>
                                        <td>
                                            <span class="star-ratings">
                                                <select class="u-rating-fontawesome-o" name="questions[{{ $c->id }}][s]"
                                                    title="Question {{ $q->id }}.{{ $no + 1 }}" autocomplete="off" required>
                                                    <option value="" selected>0</option>
                                                    <option value="1"
                                                        {{ (old('questions.'.$c->id.'.s')=='1'? "selected": "")}}>1</option>
                                                    <option value="2"
                                                        {{ (old('questions.'.$c->id.'.s')=='2'? "selected": "")}}>2</option>
                                                    <option value="3"
                                                        {{ (old('questions.'.$c->id.'.s')=='3'? "selected": "")}}>3</option>
                                                    <option value="4"
                                                        {{ (old('questions.'.$c->id.'.s')=='4'? "selected": "")}}>4</option>
                                                    <option value="5"
                                                        {{ (old('questions.'.$c->id.'.s')=='5'? "selected": "")}}>5</option>
                                                </select>
                                            </span>
                                        </td>
                                    </tr>
                                    @endforeach
                                    <tr valign="top">
                                        <td></td>
                                        <td colspan="2">
                                            <label><i>Remark (Additional Comments) for {{ $q->id }}</i></label>
                                            <textarea class="form-control mb-4" rows="2"
                                                name="categories[{{ $q->id }}]">{{ old('categories.'.$q->id)}}</textarea>
                                        </td>
                                    </tr>
                                    @endforeach
                                </table>
                            </div>
                            <div class="f1-buttons">
                                <button class="btn btn-secondary btn-previous" type="button">Previous</button>
                                <button class="btn btn-primary btn-next" type="button">Next</button>
                            </div>
                        </fieldset>
                        <fieldset>
                            <div class="row">
                                <!-- <div class="mb-3 mb-2 col-lg-6 col-md-12">
                                    <label>Date/time</label>
                                    <input class="form-control digits" autocomplete="off" type="datetime-local"
                                     name="updated_at" required>
                                </div> -->
                                <div class="mb-3 mb-2 col-lg-12 col-md-12">
                                    <label>Photo documentation</label>
                                    <input class="form-control" name="image_path" type="file" accept="image/*"
                                        title="Photo documentation" data-bs-original-title="" title="only accept image"
                                        required>
                                </div>
                                <div class="mb-3 mb-2 col-lg-12 col-md-12">
                                    <label>Remark (Additional Comments)</label>
                                    <textarea class="form-control" name="remark" title="Additional Comments"
                                        rows="3">{{ (old('remark')==null ? $data->remark : old('remark')) }}</textarea>
                                </div>
                            </div>
                            <div class="f1-buttons">
                                <button class="btn btn-secondary btn-previous" type="button">Previous</button>
                                <input class="btn btn-primary btn-submit" type="submit" name="submit" value="Submit">
                            </div>
                        </fieldset>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="{{asset('assets/js/select2/select2.full.min.js')}}"></script>
<script>
    "use strict";
    setTimeout(function () {
        (function ($) {
            "use strict";
            $(".select2").select2({
                allowClear: true,
                minimumResultsForSearch: 7,
            });
        })(jQuery);
    }, 350);

</script>
<script src="{{asset('assets/js/form-wizard/jquery.backstretch.min.js')}}"></script>
<script src="{{asset('assets/js/rating/jquery.barrating.js')}}"></script>
<script src="{{asset('assets/js/notify/bootstrap-notify.min.js')}}"></script>
<script src="{{asset('assets/js/notify/notify-script.js')}}"></script>
<script>
    'use strict';
    $(function () {
        function ratingEnable() {
            $('.u-rating-fontawesome-o').barrating({
                theme: 'fontawesome-stars-o',
                showSelectedRating: true,
                initialRating: $(this).val(),
            });
        }
        ratingEnable();
    });

</script>
<script>
    "use strict";

    function scroll_to_class(element_class, removed_height) {
        var scroll_to = $(element_class).offset().top - removed_height;
        if ($(window).scrollTop() != scroll_to) {
            $('html, body').stop().animate({
                scrollTop: scroll_to
            }, 0);
        }
    }

    function bar_progress(progress_line_object, direction) {
        var number_of_steps = progress_line_object.data('number-of-steps');
        var now_value = progress_line_object.data('now-value');
        var new_value = 0;
        if (direction == 'right') {
            new_value = now_value + (100 / number_of_steps);
        } else if (direction == 'left') {
            new_value = now_value - (100 / number_of_steps);
        }
        progress_line_object.attr('style', 'width: ' + new_value + '%;').data('now-value', new_value);
    }
    (function ($) {
        "use strict";
        $.backstretch;
        $('#top-navbar-1').on('shown.bs.collapse', function () {
            $.backstretch("resize");
        });
        $('#top-navbar-1').on('hidden.bs.collapse', function () {
            $.backstretch("resize");
        });
        $('.f1 fieldset:first').fadeIn('slow');

        $('.f1 input[required], select[required]').on('focus', function () {
            $(this).removeClass('input-error');
        });
        $('select.select2').on('select2:closing', function (e) {
            $(this).removeClass('input-error');  
        });
        $('.f1 .btn-next').on('click', function () {
            var parent_fieldset = $(this).parents('fieldset');
            var next_step = true;
            var current_active_step = $(this).parents('.f1').find('.f1-step.active');
            var progress_line = $(this).parents('.f1').find('.f1-progress-line');
            parent_fieldset.find('input[required], select[required]').each(function () {
                if ($(this).val() == "") {
                    $(this).addClass('input-error');
                    next_step = false;
                    var tes = $(this).attr('title');
                    if (tes == null || tes == '') {
                        tes = $(this).attr('name');
                    }
                    $.notify({
                        message: 'The \"' + tes + '\" field is required.'
                    }, {
                        type: 'danger',
                        allow_dismiss: true,
                        newest_on_top: false,
                        mouse_over: false,
                        showProgressbar: false,
                        spacing: 10,
                        timer: 2000,
                        placement: {
                            from: 'top',
                            align: 'right'
                        },
                        offset: {
                            x: 30,
                            y: 30
                        },
                        delay: 1000,
                        z_index: 10000,
                    });
                } else {
                    $(this).removeClass('input-error');
                }
            });
            if (next_step) {
                parent_fieldset.fadeOut(400, function () {
                    current_active_step.removeClass('active').addClass('activated').next().addClass(
                        'active');
                    bar_progress(progress_line, 'right');
                    $(this).next().fadeIn();
                    scroll_to_class($('.f1'), 20);
                });
            }
        });
        $('.f1 .btn-previous').on('click', function () {
            var current_active_step = $(this).parents('.f1').find('.f1-step.active');
            var progress_line = $(this).parents('.f1').find('.f1-progress-line');
            $(this).parents('fieldset').fadeOut(400, function () {
                current_active_step.removeClass('active').prev().removeClass('activated').addClass(
                    'active');
                bar_progress(progress_line, 'left');
                $(this).prev().fadeIn();
                scroll_to_class($('.f1'), 20);
            });
        });
        $('.f1').on('submit', function (e) {
            $(this).find('input[required], select[required]').each(function () {
                if ($(this).val() == "") {
                    e.preventDefault();
                    $(this).addClass('input-error');
                } else {
                    $(this).removeClass('input-error');
                }
            });
        });
    })(jQuery);

</script>
@endsection
