@extends('adminlte::page')
@section('title', 'Laravel logs viewer manage')
@section('content_header')
<h1>System logs</h1>
{{ Breadcrumbs::render('logs') }}
<hr class="custom_hr">
@stop
@section('content')
<div id="app">
    <div class="row">
        <div class="col-md-3">
            <h5><i class="fa fa-list" aria-hidden="true"></i> File logs</h1>
            <div class="list-group div-scroll">
                <ul class="logs-date">
                    @foreach($files as $index => $file)
                    <li>
                        <a href="?l={{ \Illuminate\Support\Facades\Crypt::encrypt(getAllFilesLogFullPath()[$index]) }}"
                            class="list-group-item @if ($current_file == $file) llv-active @endif">
                        {{$file}} 
                        <span class="f-right">
                            {{formatBytes(filesize(getAllFilesLogFullPath()[$index]))}}
                        </span>
                        </a>
                        @if($current_file)
                            <a class="bt-download" href="?dl={{ \Illuminate\Support\Facades\Crypt::encrypt(getAllFilesLogFullPath()[$index]) }}" title="download file {{$file}}">
                                <span class="fa fa-download"></span>
                            </a>
                        @endif
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>
        <div class="col-md-9 table-container table-responsive">
            @if ($logs === null)
            <div>
                Log file >50M, please download it.
            </div>
            @else
            <table id="table-log" class="table table-bordered" data-ordering-index="{{ $standardFormat ? 2 : 0 }}">
                <input type="checkbox" class="custom-control-input" id="darkSwitch">
                <thead>
                    <tr>
                        @if ($standardFormat)
                        <th>Level</th>
                        <th>Env</th>
                        <th>Date time</th>
                        @else
                        <th>Line number</th>
                        @endif
                        <th>Description</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($logs as $key => $log)
                    <tr data-display="stack{{{$key}}}">
                        @if ($standardFormat)
                        <td class="nowrap text-{{{$log['level_class']}}}">
                            <span class="fa fa-{{{$log['level_img']}}}" aria-hidden="true"></span>&nbsp;&nbsp;{{$log['level']}}
                        </td>
                        <td class="text">{{$log['context']}}</td>
                        @endif
                        <td class="date">{{{$log['date']}}}</td>
                        <td class="text">
                            @if ($log['stack'])
                            <button type="button"
                                class="float-right expand btn btn-outline-dark btn-sm mb-2 ml-2"
                                data-display="stack{{{$key}}}">
                            <span class="fa fa-search"></span>
                            </button>
                            @endif
                            {{{$log['text']}}}
                            @if (isset($log['in_file']))
                            <br/>{{{$log['in_file']}}}
                            @endif
                            @if ($log['stack'])
                            <div class="stack" id="stack{{{$key}}}"
                                style="display: none; white-space: pre-wrap;">{{{ trim($log['stack']) }}}
                            </div>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @endif
        </div>
    </div>
</div>
@stop
@section('css')
<style>
    #table-log {
        font-size: 0.85rem;
    }
    .stack {
        font-size: 0.85em;
    }
    .date {
        min-width: 75px;
    }
    .text {
        word-break: break-all;
    }
    ul.logs-date li a {
        color: #1a1a1a
    }
    a.llv-active {
        z-index: 2;
        background-color: #fff;
        border: 1px solid #1a1a1a;
        border-radius: 4px !important;
        font-weight: 600;
    }
    .list-group-item {
        word-break: break-word;
        padding: .75rem 1rem;
        border-radius: 4px !important;
    }
    
    .folder {
        padding-top: 15px;
    }
    .div-scroll {
        height: 450px;
        overflow: hidden auto;
    }
    .nowrap {
        white-space: nowrap;
    }
    .list-group {
        padding: 5px 0;
        margin-top: 15px;
    }
    /**
    * DARK MODE CSS
    */
    body[data-theme="dark"] {
        background-color: #151515;
        color: #cccccc;
    }
    [data-theme="dark"] a {
        color: #4da3ff;
    }
    [data-theme="dark"] a:hover {
        color: #a8d2ff;
    }
    [data-theme="dark"] .list-group-item {
        background-color: #1d1d1d;
        border-color: #444;
    }
    [data-theme="dark"] a.llv-active {
        background-color: #0468d2;
        border-color: #17a2b8;
        color: #ffffff;
    }
    [data-theme="dark"] a.list-group-item:focus, [data-theme="dark"] a.list-group-item:hover {
        background-color: #273a4e;
        border-color: rgba(255, 255, 255, 0.125);
        color: #ffffff;
    }
    [data-theme="dark"] .table td, [data-theme="dark"] .table th,[data-theme="dark"] .table thead th {
        border-color:#616161;
    }
    [data-theme="dark"] .page-item.disabled .page-link {
        color: #8a8a8a;
        background-color: #151515;
        border-color: #5a5a5a;
    }
    [data-theme="dark"] .page-link {
        background-color: #151515;
        border-color: #5a5a5a;
    }
    [data-theme="dark"] .page-item.active .page-link {
        color: #fff;
        background-color: #0568d2;
        border-color: #007bff;
    }
    [data-theme="dark"] .page-link:hover {
        color: #ffffff;
        background-color: #0051a9;
        border-color: #0568d2;
    }
    [data-theme="dark"] .form-control {
        border: 1px solid #464646;
        background-color: #151515;
        color: #bfbfbf;
    }
    [data-theme="dark"] .form-control:focus {
        color: #bfbfbf;
        background-color: #212121;
        border-color: #4a4a4a;
    }

    ul.logs-date {
        list-style: none;
        padding: 0;
    }

    ul.logs-date li {
        margin-bottom: 6px;
        position: relative;
    }

    span.f-right {
        float: right;
        padding-right: 20px;
    }

    a.bt-download {
        position: absolute;
        z-index: 5;
        right: 10px;
        top: 50%;
        margin-top: -11px;
    }
</style>
@stop
@section('js')
<script>
    function initTheme() {
            const darkThemeSelected =
            localStorage.getItem('darkSwitch') !== null &&
            localStorage.getItem('darkSwitch') === 'dark';
            darkSwitch.checked = darkThemeSelected;
            darkThemeSelected ? document.body.setAttribute('data-theme', 'dark') :
            document.body.removeAttribute('data-theme');
        }
        
    function resetTheme() {
        if (darkSwitch.checked) {
            document.body.setAttribute('data-theme', 'dark');
            localStorage.setItem('darkSwitch', 'dark');
        } else {
            document.body.removeAttribute('data-theme');
            localStorage.removeItem('darkSwitch');
        }
    }
</script>
<script>
    // dark mode by https://github.com/coliff/dark-mode-switch
    const darkSwitch = document.getElementById('darkSwitch');
    
    // this is here so we can get the body dark mode before the page displays
    // otherwise the page will be white for a second... 
    initTheme();
    
    window.addEventListener('load', () => {
        if (darkSwitch) {
                initTheme();
                darkSwitch.addEventListener('change', () => {
                resetTheme();
            });
        }
    });
    
    // end darkmode js
            
    $(document).ready(function () {
        $('.table-container tr').on('click', function () {
            $('#' + $(this).data('display')).toggle();
        });
        $('#table-log').DataTable({
            "order": [$('#table-log').data('orderingIndex'), 'desc'],
            "stateSave": true,
            "stateSaveCallback": function (settings, data) {
            window.localStorage.setItem("datatable", JSON.stringify(data));
            },
            "stateLoadCallback": function (settings) {
            var data = JSON.parse(window.localStorage.getItem("datatable"));
            if (data) data.start = 0;
            return data;
            }
        });
        $('#delete-log, #clean-log, #delete-all-log').click(function () {
            return confirm('Are you sure?');
        });
    });
</script>
@stop