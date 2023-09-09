<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head id="xm-inject">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="dns-prefetch" href="//fonts.googleapis.com">
    <link rel="dns-preconnect" href="//fonts.googleapis.com">
    <title>{{ $title ?? '' }} - {{ config('app.name', 'XM App') }}</title>
    @include ('layouts/includes/styles')
    @include ('layouts/includes/scripts')
    @include ('layouts/includes/analytics') 
</head>
<body onload="eventsLoaded();" id="xm-app"@isset($body_class) class="{{ $body_class }}" @endisset>
@include ('layouts/includes/header')
@include ('layouts/includes/sidebar')
<main role="main">
   <div class="xs-pd-20-10 pd-ltr-20">
	@yield ('content')
   </div>
   @include ('layouts/includes/footer')
</main>
@include ('layouts/includes/scripts_bottom')
</body>
</html>
