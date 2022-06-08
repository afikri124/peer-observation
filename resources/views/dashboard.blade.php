@extends('layouts.master')
@section('title', 'Dashboard')

@section('css')
<link rel="stylesheet" type="text/css" href="{{asset('assets/css/vendors/animate.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('assets/css/vendors/date-picker.css')}}">
@endsection

@section('style')
<style>
    .profile-greeting .media .badge-groups .badge {
    color: #d64d4d;
    padding: 10px 14px !important;
    background: -webkit-gradient(linear, right top, left top, color-stop(2%, rgba(255,255,255,0.2)), color-stop(3.07%, rgba(214,77,207,0.2)), color-stop(94.5%, rgba(255,255,255,0.2)));
    background: linear-gradient(270deg, rgba(255,255,255,0.2) 2%, rgba(214,77,207,0.2) 3.07%, rgba(255,255,255,0.2) 94.5%);
    border-radius: 5px;
    text-align: left;
}
</style>
@endsection

@section('breadcrumb-title')
<h3>Dashboard</h3>
@endsection

@section('breadcrumb-items')
<li class="breadcrumb-item active">@yield('title')</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-xl-4 col-lg-12 xl-50 morning-sec box-col-12">
            <div class="card profile-greeting">
                <div class="card-body pb-0">
                    <div class="media">
                        <div class="media-body">
                            <div class="greeting-user m-0">
                                <h4 class="f-w-600 font-primary m-0" id="greeting">Good Morning</h4>
                                <h3>{{ Auth::user()->name }}</h3>
                                @if(Auth::user()->roles->count() == 0)
                                    <p class="p-0 mb-0 text-danger">You don't have access rights, please contact the administrator!</p>
                                @else
                                    <p class="p-0 mb-0">You have access as:</p>
                                    @foreach(Auth::user()->roles as $x)
                                    <i class="badge badge-secondary m-0">{{ $x->title }}</i>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                        <div class="">
                            <div class="badge f-10 badge-secondary"><i class="me-1" data-feather="clock"></i><span id="txt"></span>
                            </div>
                        </div>
                    </div>
                    <div class="cartoon"><img class="img-fluid" src="../assets/images/cartoon.png"
                            style="max-width: 90%;" alt="">
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-lg-12 xl-50 calendar-sec box-col-6">
            <div class="card gradient-primary o-hidden">
                <div class="card-body">
                    <div class="default-datepicker">
                        <div class="datepicker-here" data-language="en"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')
<!-- <script src="{{asset('assets/js/notify/bootstrap-notify.min.js')}}"></script> -->
<script>
    // greeting
    var today = new Date()
    var curHr = today.getHours()

    if (curHr >= 0 && curHr < 4) {
        document.getElementById("greeting").innerHTML = 'Good Night';
    } else if (curHr >= 4 && curHr < 12) {
        document.getElementById("greeting").innerHTML = 'Good Morning';
    } else if (curHr >= 12 && curHr < 16) {
        document.getElementById("greeting").innerHTML = 'Good Afternoon';
    } else {
        document.getElementById("greeting").innerHTML = 'Good Evening';
    }
    // time 
    function startTime() {
        var today = new Date();
        var h = today.getHours();
        var m = today.getMinutes();
        // var s = today.getSeconds();
        var ampm = h >= 12 ? 'PM' : 'AM';
        h = h % 12;
        h = h ? h : 12;
        m = checkTime(m);
        // s = checkTime(s);
        document.getElementById('txt').innerHTML =
            h + ":" + m + ' ' + ampm;
        var t = setTimeout(startTime, 500);
    }

    function checkTime(i) {
        if (i < 10) {
            i = "0" + i
        }; // add zero in front of numbers < 10
        return i;
    }

    startTime(); 

</script>
<script src="{{asset('assets/js/notify/index.js')}}"></script>
<script src="{{asset('assets/js/datepicker/date-picker/datepicker.js')}}"></script>
<script src="{{asset('assets/js/datepicker/date-picker/datepicker.en.js')}}"></script>
<script src="{{asset('assets/js/datepicker/date-picker/datepicker.custom.js')}}"></script>
@endsection
