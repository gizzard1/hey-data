<a style="{{ $citasCounter>0 || $ventasCounter>0 ? 'animation: pulse 1s infinite;
cursor: pointer;' : '' }}" data-toggle="dropdown" class="nav-link">
    <svg
    xmlns="http://www.w3.org/2000/svg"
    width="28"
    height="28"
    viewBox="0 0 24 24"
    fill="none"
    stroke="#1D3557"
    stroke-width="1"
    stroke-linecap="round"
    stroke-linejoin="round"
    >
    <path d="M10 5a2 2 0 1 1 4 0a7 7 0 0 1 4 6v3a4 4 0 0 0 2 3h-16a4 4 0 0 0 2 -3v-3a7 7 0 0 1 4 -6" />
    <path d="M9 17v1a3 3 0 0 0 6 0v-1" />
    </svg>
</a>

<div class="dropdown-menu dropdown-menu-right" style="top:85px" id="notifications-menu">
    <a href="{{ route('agenda',['action' => 2,'pestaña' => 5]) }}" class="dropdown-item">
        <span class="ml-2">Citas ({{ $citasCounter }})</span>
    </a>
    <a href="{{ route('productos',['search' => "null",'pestaña' => 5]) }}" class="dropdown-item">
        <span class="ml-2">Ventas ({{ $ventasCounter }})</span>
    </a>
</div>