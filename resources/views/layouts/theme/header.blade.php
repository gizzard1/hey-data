<div class="header">
	<div class="header-content" >
		<nav class="navbar navbar-expand">
			<div class="collapse navbar-collapse justify-content-end">
				<div class="header-left">
				</div>

				<ul class="navbar-nav header-right">

					<li class="nav-item">
						<livewire:search />
					</li>
					<li class="nav-item">
						<livewire:simulador/>
					</li>

					<li class="nav-item dropdown" id="notifications-dropdown">
						<livewire:notificaciones/>
					</li>
				
					<li class="nav-item dropdown header-profile"  id="user-profile-dropdown">
						<a class="nav-link" href="#" role="button" data-toggle="dropdown">
							<div class="header-info" id="user-profile">
								<span class="fs-20 font-w500">
									@auth
									{{ Auth::user()->name }}
									@else
									Agenda Máster
									@endauth
								</span>
								<small>
									@auth
									{{ Auth::user()->role == 'estilista' || Auth::user()->role == 'recepcionista' ? 'Empleado' : '' }}
									@endauth
								</small>
							</div>
						</a>
						<div class="dropdown-menu dropdown-menu-right" style="top:85px" id="user-profile-menu">
							<a href="{{ route('profile.edit') }}" class="dropdown-item ai-icon" style="color:#3375B6 !important">
								<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
								<span class="ml-2">Mi Perfil</span>
							</a>
							@if(isset(Auth::user()->empleado))
							<a href="{{ route('mis-ganancias') }}" class="dropdown-item ai-icon" style="color:#2C942F">
								<i class="las la-piggy-bank w-18"></i>
								<span class="ml-2" >Mis Ganancias</span>
							</a>
							@endif
							@if(Auth::user()->role!='estilista')
							<a onclick="help()" class="dropdown-item ai-icon" style="color:black">
								<i class="las la-info-circle w-18"></i>
								<span class="ml-2">Ayuda</span>
							</a>
							@endif
							<!-- @if(Auth::user()->role=='admin')
							<a href="{{ route('suscripcion') }}" class="dropdown-item ai-icon" style="color:black">
								<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" width="20" height="20" stroke-width="2"> <path d="M21 15h-2.5c-.398 0 -.779 .158 -1.061 .439c-.281 .281 -.439 .663 -.439 1.061c0 .398 .158 .779 .439 1.061c.281 .281 .663 .439 1.061 .439h1c.398 0 .779 .158 1.061 .439c.281 .281 .439 .663 .439 1.061c0 .398 -.158 .779 -.439 1.061c-.281 .281 -.663 .439 -1.061 .439h-2.5"></path> <path d="M19 21v1m0 -8v1"></path> <path d="M13 21h-7c-.53 0 -1.039 -.211 -1.414 -.586c-.375 -.375 -.586 -.884 -.586 -1.414v-10c0 -.53 .211 -1.039 .586 -1.414c.375 -.375 .884 -.586 1.414 -.586h2m12 3.12v-1.12c0 -.53 -.211 -1.039 -.586 -1.414c-.375 -.375 -.884 -.586 -1.414 -.586h-2"></path> <path d="M16 10v-6c0 -.53 -.211 -1.039 -.586 -1.414c-.375 -.375 -.884 -.586 -1.414 -.586h-4c-.53 0 -1.039 .211 -1.414 .586c-.375 .375 -.586 .884 -.586 1.414v6m8 0h-8m8 0h1m-9 0h-1"></path> <path d="M8 14v.01"></path> <path d="M8 17v.01"></path> <path d="M12 13.99v.01"></path> <path d="M12 17v.01"></path> </svg> 
								<span class="ml-2">Suscripción</span>
							</a>
							@endif -->
							@if(Auth::user()->salon_id == 2)
								<a onclick="Javascript:onStart()"data-toggle="modal" data-target="#modalFingerprint" class="dropdown-item ai-icon" style="color:black">
									<i class="las la-fingerprint w-18"></i>
									<span class="ml-2">Checador</span>
								</a>
							@endif
							<a href="{{ route('logout') }}" 
							onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
							class="dropdown-item ai-icon">
								<svg id="icon-logout" xmlns="http://www.w3.org/2000/svg" class="text-danger" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
								<span class="ml-2">Cerrar Sesión </span>
							<form id="logout-form" action="{{ route('logout') }}" method="POST">
								@csrf
							</form>
							<a>
						</div>
					</li>
				</ul>
			</div>
		</nav>
	</div>
</div>