<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/free.png') }}">

    <title>Agenda-máster</title>
    @include('layouts.theme.webstyles')
    <livewire:styles />

</head>
<body>
    <h1>Correo: {{ $correo }}</h1>
    <h1>{{ $nombre }} dice:</h1>
    <h2>{{ $msj }}</h2>
    
    
    @include('layouts.theme.webscripts')
    <livewire:scripts />

</body>

</html>
