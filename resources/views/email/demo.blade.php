<!DOCTYPE html>
<!DOCTYPE html>
<html>

<head>
    <title>Call Letter</title>
    <meta charset="UTF-8">
    <meta http-equiv="X-YA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        * {
            -webkit-box-sizing: border-box;
            -moz-box-sizing: border-box;
            box-sizing: border-box;
            margin: 0;
            padding: 0
        }

        body {
            font-size: 16px;
            color: #2d3035;
            margin: 0;
            padding: 0;
            font-weight: 400;
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

        a:hover,
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
            clear: both;
            font-weight: 400;
            color: #464646;
            line-height: 20px;
            font-size: 16px;
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
            line-height: 30px;
            font-weight: 700
        }

        h2 {
            color: #146198;
            font-weight: 700
        }

        h3 {
            color: #146198
        }

        p {
            color: #146198;
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

        .container {
            max-width: 1170px;
            padding: 0 15px;
            margin: 0 auto;
        }

        .page-wrap {
            max-width: 550px;
            margin: 0 auto;
            padding: 0 15px;
        }

        .inner-page {
            float: left;
            width: 100%;
            border-left: 15px solid #F77F38;
            border-right: 12px solid #F77F38;
            border-radius: 5px;
        }

        .page-header {
            float: left;
            width: 100%;
            background: #F77F38;
        }

        .page-header h1 {
            color: #fff;
            text-align: center;
            padding: 10px 0;
        }

        .logo-wrap {
            float: left;
            width: 100%;
            padding: 15px;
        }

        .logo-wrap img {
            width: 300px;
        }

        .page-text {
            float: left;
            width: 100%;
            padding: 0 15px 15px;
        }

        .page-text h2 {

            text-transform: uppercase;
            padding-bottom: 15px;
        }

        .page-text h2 span {

            text-transform: none;
        }

        .page-text h3 {
            padding-bottom: 5px;
        }

        .page-text p {
            padding-bottom: 5px;
            max-width: 317px;
        }

        .page-time {
            float: left;
            width: 100%;
            padding: 0 15px
        }

        .page-time h3 {
            font-weight: 700;
            padding-bottom: 20px;
        }

        .page-time h3 span {
            font-weight: 400
        }

        .page-address {
            float: left;
            width: 100%;
            padding: 0 15px 15px;
        }

        .page-address h3 {
            font-weight: 700;
            padding-bottom: 5px;
        }

        .page-address p {
            padding-bottom: 20px;
            font-size: 16px;
        }

        .page-address h6 {}

        .page-address h6 a {
            color: #E36013 !important;
            text-decoration: underline;
        }

        .page-address h5 {

            font-weight: 700;
            color: #E36013
        }

        .page-address span {

            font-weight: 700;
            color: #E36013
        }

        .page-last {
            float: left;
            width: 100%;
            background: #F77F38;
            padding: 15px 15px 10px;
        }

        .page-last p {
            color: #fff;
            padding-bottom: 5px;
        }

        .page-last p a {
            text-decoration: underline;
            color: darkslateblue;
        }
    </style>
</head>

<body>
    <div class="page-wrap">
        <div class="inner-page">
            <div class="page-header">
                <h1>Interview call letter, please confirm for the same...</h1>
            </div>

            <div class="logo-wrap">
                <img src="{{ App\Helper::GetAssetPath('assets/images/logo.jpg') }}" alt="esparkbiz logo" title="Logo" style="display:block">
            </div>

            <div class="page-text">
                <h2><span>Dear </span> {{$view_array['name']}} </h2>
                <h3>Greetings for the day !!!</h3>
                <p>Congratulations...</p>
                <p>As per telephonic words we had, please find following interview details.</p>
            </div>

            <div class="page-time">
                <h3>Interview Date And Time</h3>
                @if(isset($view_array['opening_name']))
                <h3>Designation :- <span> {{$view_array['opening_name']}} </span></h3>
                @endif
                <h3>Date :- <span> {{$view_array['interview_date']}} </span></h3>
                <h3>Time :- <span> {{$view_array['interview_time']}} </span></h3>
                <h3>Venue :-</h3>
            </div>

            <div class="page-address">
                <h3>eSparkBiz Technologies Pvt. Ltd.</h3>
                {!! $view_array['company_address'] !!}

                <h6>
                    For map direction, click on this link <a href="http://goo.gl/SRc3xS">http://goo.gl/SRc3xS</a>

                </h6>
                <p>Please keep your updated CV with you.</p>
                <h5>Please revert me back with your confirmation for the same.</h5>
            </div>

            <div class="page-last">
                <p>Thanks & Regrads,</p>
                <p>{{$view_array['hr_names']}}</p>
                <p>HR@eSparkBiz Technologies Pvt.Ltd</p>
                <p>{{$view_array['hr_numbers']}} | @foreach($view_array['hr_emails'] as $hr_email)
                    <a href="{{$hr_email}}">{{$hr_email}} </a>
                    @endforeach | <a href="{{$view_array['company_site']}}">{{$view_array['company_site']}}</a>
                </p>
            </div>
        </div>
    </div>
</body>

</html> 