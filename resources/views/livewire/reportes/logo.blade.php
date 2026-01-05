
@if(Auth::user()->salon->picture!=='storage/no-image.jpg')
<img src="{{ asset( Auth::user()->salon->picture) }}" class="rounded" style="width:100px;margin-bottom:4rem"
    alt="{{ Auth::user()->salon->name }}">
@endif