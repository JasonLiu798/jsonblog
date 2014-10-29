<!DOCTYPE html>
<html lang="zh-cn">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	@section('title')
	<title>{{ $title }}</title>
	@show
	{{ HTML::script('js/lib/jquery-1.11.1.js') }}
	{{ HTML::script('js/lib/jquery-plugin.js') }}
	{{ HTML::script('bootstrap/js/bootstrap.js') }}
	{{ HTML::script('js/lib/tool.js') }}
	{{ HTML::style('bootstrap/css/bootstrap.css') }}
    {{ HTML::style('css/style.css') }}
    
	@if (! empty ( $next_url ))
		<META HTTP-EQUIV="REFRESH" CONTENT="100;URL={{$next_url}}?>" />
	@endif

	<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
	<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
	<!--[if lt IE 9]>
		{{ HTML::script('bootstrap/js/html5shiv.min.js') }}
		{{ HTML::script('bootstrap/js/respond.min.js') }}
	<![endif]-->
</head>
<body>