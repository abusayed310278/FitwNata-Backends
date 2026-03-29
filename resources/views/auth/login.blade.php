<x-guest-layout>
    <!-- Session Status -->

    <div class="content-wrapper d-flex align-items-center auth px-0">
        <div class="row w-100 mx-0">
            <div class="col-lg-4 mx-auto">
                <div class="auth-form-light text-start py-5 px-4 px-sm-5">
                    @include('auth.components.logo')
                    <h6 class="fw-light">Sign in to continue.</h6>
                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        <x-auth-session-status class="mb-4" :status="session('status')" />
                        <div class="form-group">
                            <input type="email" class="form-control form-control-lg" type="email" name="email"
                                :value="old('email')" required autofocus placeholder="email">
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>
                        <div class="form-group">
                            <input type="password" class="form-control form-control-lg" id="password" type="password"
                                name="password" required autocomplete="current-password" placeholder="Password">
                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                        </div>
                        <div class="mt-3 d-grid gap-2">
                            <button class="btn btn-block btn-primary btn-lg fw-medium auth-form-btn" type="submit">SIGN
                                IN</button>
                        </div>
                        <div class="my-2 d-flex justify-content-between align-items-center">
                            <a href="{{ route('password.request') }}" class="auth-link text-black">Forgot password?</a>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
