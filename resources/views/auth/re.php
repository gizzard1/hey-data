
<h3 class="text-center mb-4 text-black">Regístrate ahora</h3>


<x-input-label class="mb-1 text-black" for="name" :value="__('Usuario')" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
            <div>
                <x-text-input id="name" type="text" class="form-control" name="name":value="old('name')" required autocomplete="name"/>
            </div>

        <div class="form-group">
            <x-input-label class="mb-1 text-black" for="name" :value="__('Usuario')" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
            <div>
                <x-text-input id="name" type="text" class="form-control" name="name":value="old('name')" required placeholder="Usuario"/>
            </div>
        </div>
        
        <div class="form-group">
            <x-input-label class="mb-1 text-black" for="email" :value="__('Email')" />
            <div>
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                <x-text-input id="email" name="email" :value="old('email')" required type="email" class="form-control" placeholder="agenda@master.com"/>
            </div>
        </div>
        <div class="form-group">
            <x-input-label class="mb-1 text-black" for="password" :value="__('Contraseña')" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
            <div>
                <x-text-input type="password" class="form-control"  id="password" name="password" value="Contraseña" required autocomplete="new-password"/>
            </div>
        </div>

        <div class="form-group">
            <x-input-label class="mb-1 text-black" for="password_confirmation" :value="__('Contraseña')" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            <div>
                <x-text-input type="password_confirmation" class="form-control"  id="password_confirmation" name="password_confirmation" value="Contraseña" required autocomplete="new-password"/>
            </div>
        </div>

        <div class="form-group text-center mt-4">
            <x-primary-button class="btn btn-primary btn-block">{{ __('Registrar') }}</x-primary-button>
        </div>
    </form>

    <div class="new-account mt-3">
        <p>¿Ya tienes cuenta? <a class="text-primary" href="{{ route('login') }}">{{ __('Iniciar sesión') }}</a></p>
    </div>
</div>