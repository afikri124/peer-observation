@extends('layouts.master')
@section('title', 'Result Recap')

@section('css')
<link rel="stylesheet" type="text/css" href="{{asset('assets/css/vendors/select2.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('assets/css/vendors/date-picker.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('assets/css/vendors/datatables.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('assets/css/vendors/datatable-extension.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('assets/css/vendors/daterange-picker.css')}}">
@endsection

@section('style')
<style>
    table.dataTable tbody td {
        vertical-align: middle;
    }

    table.dataTable td:nth-child(2) {
        max-width: 120px;
    }

    table.dataTable td:nth-child(6) {
        max-width: 60px;
    }

    table.dataTable td:nth-child(5) {
        max-width: 75px;
    }

    table.dataTable td {
        white-space: nowrap;
        text-overflow: ellipsis;
        overflow: hidden;
    }

</style>
@endsection

@section('breadcrumb-title')
<h3>@yield('title')</h3>
@endsection

@section('breadcrumb-items')
<!-- <li class="breadcrumb-item">Settings</li> -->
<li class="breadcrumb-item active">@yield('title')</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12 project-list">
            <div class="card">
                <form method="GET" class="row" action="{{ route('pdf.recap') }}">
                    @csrf
                    <div class="col-md-3">
                        <input class="form-control" name="range" id="select_range" type="text" placeholder="Select Date">
                    </div>
                    <div class="col-md-2">
                        <select id="Select_program" name="study_program" class="form-control input-sm select2"
                            data-placeholder="Program">
                            <option value="">Program</option>
                            @foreach($study_program as $d)
                            <option value="{{ $d->study_program }}">{{ $d->study_program }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select id="Select_1" name="lecturer_id" class="form-control input-sm select2" data-placeholder="Lecturer">
                            <option value="">Lecturer</option>
                            @foreach($lecturer as $d)
                            <option value="{{ $d->id }}">{{ $d->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select id="Select_2" name="status_id" class="form-control input-sm select2" data-placeholder="Status">
                            <option value="">Status</option>
                            @foreach($status as $d)
                            <option value="{{ $d->id }}">{{ $d->title }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 justify-content-center justify-content-md-end">
                        <button class="btn btn-primary btn-block btn-mail" title="Pdf" type="submit">
                            <i data-feather="printer"></i>Print
                        </button>
                    </div>
                </form>
            </div>
        </div>
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body">
                    <div class="">
                        <table class="table table-hover table-sm" id="datatable" width="100%">
                            <thead>
                                <tr>
                                    <th scope="col" data-priority="1" width="20px">No</th>
                                    <th scope="col" data-priority="2">Auditee</th>
                                    <th scope="col">Schedule</th>
                                    <th scope="col">Auditor</th>
                                    <th scope="col" data-priority="3">Score</th>
                                    <th scope="col" data-priority="4">Status</th>
                                    <th scope="col" width="65px" class="text-end">Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="{{asset('assets/js/datepicker/date-picker/datepicker.js')}}"></script>
<script src="{{asset('assets/js/datepicker/date-picker/datepicker.en.js')}}"></script>
<script src="{{asset('assets/js/datatable/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('assets/js/datatable/datatable-extension/dataTables.responsive.min.js')}}"></script>
<script src="{{asset('assets/js/select2/select2.full.min.js')}}"></script>
<script src="{{asset('assets/js/datepicker/daterange-picker/moment.min.js')}}"></script>
<script src="{{asset('assets/js/datepicker/daterange-picker/daterangepicker.js')}}"></script>
<script>
    "use strict";
    setTimeout(function () {
        (function ($) {
            "use strict";
            $(".select2").select2({
                allowClear: true,
                minimumResultsForSearch: 7
            });
        })(jQuery);
    }, 350);

</script>
<script type="text/javascript">
    $(document).ready(function () {
        var table = $('#datatable').DataTable({
            responsive: true,
            processing: true,
            serverSide: true,
            ordering: false,
            bFilter: false,
            language: {
                // searchPlaceholder: 'Search by lecturer..',
                sSearch: '_INPUT_ &nbsp;',
                lengthMenu: '<span>Show:</span> _MENU_',
            },
            ajax: {
                url: "{{ route('api.recap') }}",
                data: function (d) {
                    d.lecturer_id = $('#Select_1').val(),
                        d.status_id = $('#Select_2').val(),
                        d.study_program = $('#Select_program').val(),
                        d.range = $('#select_range').val(),
                        d.search = $('input[type="search"]').val()
                },
            },
            columns: [{
                    render: function (data, type, row, meta) {
                        var no = (meta.row + meta.settings._iDisplayStart + 1);
                        return no;
                    },
                    orderable: false,
                    className: "text-center"
                },
                {
                    render: function (data, type, row, meta) {
                        var x = "<span title='" + row.lecturer['name'] + "'>" + row.lecturer[
                            'name'] + "</span>";
                        return x;
                    },
                },
                {
                    render: function (data, type, row, meta) {
                        var x = moment(row.date_start).format("DD MMM YY HH:mm") +
                            " - ";
                        if (moment(row.date_start).format("DD/MM/YY") == moment(row
                                .date_end).format("DD/MM/YY")) {
                            x += moment(row.date_end).format("HH:mm");
                        } else {
                            x += moment(row.date_end).format("DD MMM YY HH:mm");
                        }
                        return x;
                    },
                },
                {
                    render: function (data, type, row, meta) {
                        var x = "";
                        row.observations.forEach((e) => {
                            x += '<i class="badge rounded-pill badge-' + e.color +
                                '">' + e.auditor['name'] + '</i><br>';
                        });
                        return x;
                    },
                },

                {
                    render: function (data, type, row, meta) {
                        var x = 0;
                        var score = 0;
                        var weight = 0;
                        row.observations.forEach((e) => {
                            e.observation_criterias.forEach((q) => {
                                score += (q.score * parseFloat(q.weight));
                                weight += parseFloat(q.weight);
                            });
                        });
                        x = (score / (weight * 5) * 100);
                        if (x < "{{ $MINSCORE->content }}") {
                            return "<b class='text-danger'>" + x.toFixed(1) + "%</b>";
                        } else if (x >= "{{ $MINSCORE->content }}") {
                            return "<b>" + x.toFixed(1) + "%</b>";
                        } else {
                            return "<i class='text-light'>" + x.toFixed(1) + "</i>";
                        }
                    },
                    className: "text-center"
                },
                {
                    render: function (data, type, row, meta) {
                        var x = '<span title="' + row.remark + '" class="text-' + row.status[
                            'color'] + '">' + row.status['title'] + '</span>';
                        return x;
                    },
                },
                {
                    render: function (data, type, row, meta) {
                        var x = row.id;
                        var html = "";
                        if (row.status_id == "S04" || row.status_id == "S05" || row.status_id ==
                            "S06") {
                            html =
                                `<a class="btn btn-info btn-sm px-2" title="View Report" href="{{ url('pdf/report/` +
                                row.link +
                                `') }}" target="_blank"><i class="fa fa-eye"></i></a>`;
                        }
                        return html;
                    },
                    orderable: false,
                    className: "text-end"
                }
            ]
        });
        $('#Select_1').change(function () {
            table.draw();
        });
        $('#Select_2').change(function () {
            table.draw();
        });
        $('#Select_program').change(function () {
            table.draw();
        });
        $('#select_range').change(function () {
            table.draw();
        });
    });

</script>
<script>
    //DateRange Picker
    (function ($) {
        $(function () {
            var start = moment("2021-01-01", 'YYYY-MM-DD');
            var end = moment();

            function cb(start, end) {
                document.getElementById("select_range").value = null;
            }
            $('#select_range').daterangepicker({
                startDate: start,
                endDate: end,
                ranges: {
                    'All': [moment("2021-01-01", 'YYYY-MM-DD'), moment()],
                    'Today': [moment(), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment()
                        .subtract(1, 'month').endOf('month')
                    ]
                }
            }, cb);
            cb(start, end);
        });
        // alert($('#select_range').val());
    })(jQuery);

</script>
@endsection
