<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>{{ config('app.name', 'SIMPEG Diklat ASN') }}</title>

	<!-- CSRF Token -->
	<meta name="csrf-token" content="{{ csrf_token() }}">

	<!-- FontAwesome -->
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />

	@vite(['resources/css/app.css', 'resources/js/app.js'])
	@routes
	@inertiaHead
</head>

<body class="font-sans antialiased">
	@inertia
</body>

</html>