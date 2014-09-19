<!DOCTYPE html>
<html class="no-js" lang="en">
	<head>
	    <meta charset="utf-8">
	    <meta name="viewport" content="width=device-width">
	    @section('title')
	        <title>{{$title}}</title>
	    @show
	    {{ HTML::style('assets/css/foundation.css') }}
	    {{ HTML::style('assets/css/custom.css') }}
	    {{ HTML::script('./assets/js/vendor/custom.modernizr.js') }}
	</head>
    <body>
        @section('sidebar')
            This is the master sidebar.
        @show

        <div class="container">
            @yield('content')
        </div>
    </body>
</html>