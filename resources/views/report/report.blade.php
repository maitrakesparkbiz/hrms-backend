<!DOCTYPE html>
<html>

<head>
    <title></title>
    <style>
        /*start-common-css*/
        * {
            -webkit-box-sizing: border-box;
            -moz-box-sizing: border-box;
            -ms-box-sizing: border-box;
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-size: 15px;
            color: #000;
            margin: 0;
            padding: 0;
            font-family: 'Montserrat', sans-serif;
        }

        a {
            color: #17293e;
            text-decoration: none;
            transition: all 300ms linear;
            -moz-transition: all 300ms linear;
            -o-transition: all 300ms linear;
            -ms-transition: all 300ms linear;
            -webkit-transition: all 300ms linear;
        }

        a:focus {
            outline: none;
            text-decoration: none;
            color: #3c97ac;
        }

        a:active {
            outline: 0;
            text-decoration: none;
            color: #3c97ac;
        }

        h1,
        h2,
        h3,
        h4,
        h5,
        h6 {
            color: #000;
            margin: 0;
            font-weight: bold;
            font-family: 'Montserrat', sans-serif;
        }

        h1 a,
        h2 a,
        h3 a,
        h4 a,
        h5 a,
        h6 a {
            color: inherit;
        }

        h1 {
            font-size: 150px;
            line-height: 155px;
        }

        h2 {
            font-size: 30px;
            line-height: 35px;
        }

        h3 {
            font-size: 14px;
            line-height: 24px;
        }

        h4 {}

        h5 {}

        h6 {}

        img {
            border: 0;
            max-width: 100%;
        }

        input:not([type="radio"]):not([type="checkbox"]) {
            -webkit-appearance: none;
            -webkit-border-radius: 0px;
        }

        input,
        button,
        textarea,
        select {
            border: 1px solid #ccc;
            outline: none;
            font-size: 13px;
            color: #000;
        }

        input[type=submit],
        button {
            cursor: pointer;
            -webkit-transition: all 0.4s ease-in-out;
            -moz-transition: all 0.4s ease-in-out;
            -o-transition: all 0.4s ease-in-out;
            transition: all 0.4s ease-in-out;
        }

        p {
            padding: 0 0 15px;
        }

        ol,
        ul {
            margin: 0;
            padding: 0;
            list-style: none;
        }

        table {
            border-collapse: collapse;
            border-spacing: 0
        }

        hr {
            background-color: rgba(0, 0, 0, 0.1);
            border: 0;
            height: 1px;
            margin-bottom: 23px;
        }


        ::-moz-selection {
            background-color: #47d5ff;
            color: #fff;
            text-shadow: none;
        }

        ::selection {
            background-color: #47d5ff;
            color: #fff;
            text-shadow: none;
        }

        ::-webkit-input-placeholder {
            color: #858585;
            opacity: 1;
            -ms-filter: "progid:DXImageTransform.Microsoft.Alpha(Opacity=100)";
        }

        ::-moz-placeholder {
            color: #858585;
            opacity: 1;
            -ms-filter: "progid:DXImageTransform.Microsoft.Alpha(Opacity=100)";
        }

        :-ms-input-placeholder {
            color: #858585;
            opacity: 1;
            -ms-filter: "progid:DXImageTransform.Microsoft.Alpha(Opacity=100)";
        }

        .container:after {
            content: "";
            clear: both;
            display: table;
        }

        .clearfix:after {
            content: "";
            clear: both;
            display: table;
        }

        .thumb {
            background-repeat: no-repeat;
            background-size: cover;
            background-position: center;
        }

        .row {
            width: auto;
            float: none;
            margin: 0 -15px;
        }

        .col1,
        .col2,
        .col3 {
            float: left;
            padding: 0 15px;
            box-sizing: border-box;
        }

        .col1 {
            width: 8.33%;
            float: left;
        }

        .col2 {
            width: 16.33%;
            float: left;
        }

        .col3 {
            width: 25%;
            float: left;
        }

        .col4 {
            width: 33.33%;
            float: left;
        }

        .col5 {
            width: 41.66%;
            float: left;
        }

        .col6 {
            width: 50%;
            float: left;
        }

        .col7 {
            width: 58.31%;
            float: left;
        }

        .col8 {
            width: 66.66%;
            float: left;
        }

        .col9 {
            width: 74.98%;
            float: left;
        }

        .col10 {
            width: 83.31%;
            float: left;
        }

        .col11 {
            width: 91.66%;
            float: left;
        }

        .col12 {
            width: 100%;
            float: left;
        }

        .bg-1 {
            background-color: transparent !important;
            border-color: #fff !important;
        }

        a.btn,
        input[type="submit"] {
            font-size: 14px;
            line-height: 24px;
            text-transform: uppercase;
            border: 3px solid;
            color: #fff;
            padding: 8px 28px;
            font-weight: 700;
        }

        a.btn:hover {}

        a:hover {
            color: #e08984;
        }

        .container {
            max-width: 1170px;
            margin: 0 auto;
            padding: 0 15px;
            width: 100%;
        }

        /*end-common-css*/

        /**/
        .table-section {
            padding-top: 100px;
        }

        .table-section .content-wrapper {
            max-width: 650px;
            margin: 0 auto;
        }

        .table-section h3 {
            font-size: 20px;
            line-height: 25px;
            margin: 0 0 10px 0 !important;
            padding: 0 !important;
            float: left;
            width: 100%;
        }

        .table {
            margin-bottom: 30px;
        }

        .table-section .logo {
            text-align: center;
            float: left;
            width: 100%;
            margin-bottom: 25px;
        }

        .table-section .logo img {
            display: inherit;
            margin: 0 auto;
            width: 60%;
        }


        .table-section .date {
            padding-bottom: 0;
        }


        .table-section span {
            display: block;
            float: left;
            width: 100%;
            clear: both;
            margin-bottom: 30px;
        }

        table {
            width: 100%;
        }

        table thead {
            border-bottom: 1px solid #ddd;
        }

        table thead tr th {
            padding: 10px;
            text-align: left;
        }

        table tbody tr td {
            padding: 8px 10px;
        }

        /**/
        table tbody tr {
            border-bottom: 1px solid #ddd;
        }

        table tbody tr th {
            padding: 8px 10px;
        }

        .left-heading {
            float: left
        }

        tr.incomplete {
            background: #ffe6e6;
        }

        /**/
    </style>
</head>

<body>
    <div class="table-section">
        <div class="container">
            <div class="content-wrapper">
                <div class="logo">
                    <img src="{{ App\Helper::GetAssetPath('assets/images/logo.jpg') }}">
                    <!-- <img src="{{ url('/assets/images/logo.jpg') }}"> -->
                </div>

                <span>Date :- {{$newArr['date']}}</span>
                <span>Present :- {{$newArr['present']}}</span>
                <span>Absent :- {{$newArr['absent']}}</span>
                @foreach($newArr['all'] as $dep)
                <h3>{{$dep['dep_name']}}</h3>
                @if(!empty($dep['users']))
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col">Employee Name</th>
                            <th scope="col">Check In Time</th>
                            <th scope="col">Check Out Time</th>
                            <th scope="col">Break Time</th>
                            <th scope="col">Working Hours</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($dep['users'] as $user)
                        @if(isset($user['entry_time']))
                        <tr class="{{isset($user['incomplete']) && $user['incomplete'] ? 'incomplete' : 'completed'}}">
                            <td>{{$user['name']}}</td>
                            <td>{{$user['entry_time']}}</td>
                            <td>{{isset($user['exit_time']) ? $user['exit_time'] : '-'}}</td>
                            <td>{{isset($user['break_time']) ? $user['break_time']: '-'}}</td>
                            <td>{{isset($user['working_time']) ? $user['working_time'] : '-'}}</td>
                        </tr>
                        @endif
                        @endforeach
                    </tbody>
                </table>
                @else
                <p class="left-heading">No one was present from {{$dep['dep_name']}}</p>
                @endif
                @endforeach

                @if(isset($newArr['other']))
                <h3>Other</h3>
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col">Employee Name</th>
                            <th scope="col">Check In Time</th>
                            <th scope="col">Check Out Time</th>
                            <th scope="col">Break Time</th>
                            <th scope="col">Working Hours</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($newArr['other'] as $user)
                        <tr class="{{isset($user['incomplete']) && $user['incomplete'] ? 'incomplete' : 'completed'}}">
                            <td>{{$user['name']}}</td>
                            <td>{{$user['entry_time']}}</td>
                            <td>{{$user['exit_time']}}</td>
                            <td>{{$user['break_time']}}</td>
                            <td>{{$user['working_time']}}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @endif
            </div>
        </div>
    </div>
</body>

</html> 