<x-guest-layout>
    <!-- Session Status -->


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
                    <form method="POST" action="{{ route('password.email') }}">
                        @csrf
                        <x-auth-session-status class="mb-4" :status="session('status')" />

                        <div class="form-group">
                            <input type="email" class="form-control form-control-lg" type="email" name="email"
                                :value="old('email')" required autofocus placeholder="email">
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        <div class="mt-3 d-grid gap-2">
                            <button class="btn btn-block btn-primary btn-lg fw-medium auth-form-btn"
                                type="submit">Email Password Reset Link</button>
                        </div>
                        <div class="my-2 d-flex justify-content-between align-items-center">
                            <span>Already have account ? <a href="{{ route('login') }}" class="auth-link text-black">Log
                                    In</a></span>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
