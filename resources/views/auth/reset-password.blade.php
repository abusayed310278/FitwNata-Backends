<x-guest-layout>

    <div class="content-wrapper d-flex align-items-center auth px-0">
        <div class="row w-100 mx-0">
            <div class="col-lg-4 mx-auto">
                <div class="auth-form-light text-start py-5 px-4 px-sm-5">
                    <div class="brand-logo text-center">
                        <img src="{{ asset('assets/logo.png') }}" alt="logo">
                    </div>
                    <h6 class="fw-light">
                        {{ __('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.') }}.
                    </h6>
                    <form method="POST" action="{{ route('password.store') }}">
                        @csrf
                        <!-- Password Reset Token -->
                        <input type="hidden" name="token" value="{{ $request->route('token') }}">

                        <div class="form-group">
                            <input type="email" class="form-control form-control-lg" name="email"
                                value="{{ old('email', $request->email) }}" required autofocus placeholder="Email"
                                autocomplete="username">
                            <x-input-error :messages="$errors->get('email')" class="mt-2 error" />
                        </div>

                        <div class="form-group mt-3">
                            <input type="password" class="form-control form-control-lg" name="password" required
                                placeholder="Password" autocomplete="new-password">
                            <x-input-error :messages="$errors->get('password')" class="mt-2 error" />
                        </div>

                        <div class="form-group mt-3">
                            <input type="password" class="form-control form-control-lg" name="password_confirmation"
                                required placeholder="Confirm Password" autocomplete="new-password">
                            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2 error" />
                        </div>

                        <div class="mt-3 d-grid gap-2">
                            <button class="btn btn-block btn-primary btn-lg fw-medium auth-form-btn" type="submit">
                                Reset Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
