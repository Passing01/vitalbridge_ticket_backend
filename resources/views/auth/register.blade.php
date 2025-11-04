@extends('layouts.guest')

@section('content')
<main class="mt-0 transition-all duration-200 ease-soft-in-out">
    <section class="min-h-screen mb-32">
        <div class="relative flex items-start pt-12 pb-56 m-4 overflow-hidden bg-center bg-cover min-h-50-screen rounded-xl" style="background-image: url('{{ asset('assets/img/curved-images/curved14.jpg') }}')">
            <span class="absolute top-0 left-0 w-full h-full bg-center bg-cover bg-gradient-to-tl from-gray-900 to-slate-800 opacity-60"></span>
            <div class="container z-10">
                <div class="flex flex-wrap justify-center -mx-3">
                    <div class="w-full max-w-full px-3 mx-auto mt-0 text-center lg:flex-0 shrink-0 lg:w-5/12">
                        <h1 class="mt-12 mb-2 text-white">{{ __('Welcome!') }}</h1>
                        <p class="text-white">
                            {{ __('Create a new account to get started.') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="container">
            <div class="flex flex-wrap -mx-3 -mt-48 md:-mt-56 lg:-mt-48">
                <div class="w-full max-w-full px-3 mx-auto mt-0 md:flex-0 shrink-0 md:w-7/12 lg:w-5/12 xl:w-4/12">
                    <div class="relative z-0 flex flex-col min-w-0 break-words bg-white border-0 shadow-soft-xl rounded-2xl bg-clip-border">
                        <div class="p-6 mb-0 text-center bg-white border-b-0 rounded-t-2xl">
                            <h5>{{ __('Register with') }}</h5>
                        </div>
                        
                        <div class="flex flex-wrap px-3 -mx-3 sm:px-6 xl:px-12">
                            <!-- Social login buttons can be added here -->
                        </div>
                        
                        <div class="flex items-center my-4">
                            <div class="flex-1 h-px bg-gray-200"></div>
                            <p class="px-4 mb-0 text-sm text-gray-500">{{ __('or sign up with credentials') }}</p>
                            <div class="flex-1 h-px bg-gray-200"></div>
                        </div>
                        
                        <div class="flex-auto p-6">
                            <form method="POST" action="{{ route('register') }}" role="form">
                                @csrf
                                
                                <!-- First Name -->
                                <div class="mb-4">
                                    <label class="mb-2 ml-1 font-bold text-xs text-slate-700">{{ __('First Name') }}</label>
                                    <input
                                        id="first_name"
                                        name="first_name"
                                        type="text"
                                        class="focus:shadow-soft-primary-outline text-sm leading-5.6 ease-soft block w-full appearance-none rounded-lg border border-solid border-gray-300 bg-white bg-clip-padding px-3 py-2 font-normal text-gray-700 transition-all focus:border-fuchsia-300 focus:outline-none focus:transition-shadow @error('first_name') border-red-500 @enderror"
                                        placeholder="{{ __('Your first name') }}"
                                        value="{{ old('first_name') }}"
                                        required
                                        autofocus
                                        autocomplete="given-name" />
                                    <x-input-error :messages="$errors->get('first_name')" class="mt-2 text-red-500 text-xs" />
                                </div>
                                
                                <!-- Last Name -->
                                <div class="mb-4">
                                    <label class="mb-2 ml-1 font-bold text-xs text-slate-700">{{ __('Last Name') }}</label>
                                    <input
                                        id="last_name"
                                        name="last_name"
                                        type="text"
                                        class="focus:shadow-soft-primary-outline text-sm leading-5.6 ease-soft block w-full appearance-none rounded-lg border border-solid border-gray-300 bg-white bg-clip-padding px-3 py-2 font-normal text-gray-700 transition-all focus:border-fuchsia-300 focus:outline-none focus:transition-shadow @error('last_name') border-red-500 @enderror"
                                        placeholder="{{ __('Your last name') }}"
                                        value="{{ old('last_name') }}"
                                        required
                                        autocomplete="family-name" />
                                    <x-input-error :messages="$errors->get('last_name')" class="mt-2 text-red-500 text-xs" />
                                </div>
                                
                                <!-- Phone -->
                                <div class="mb-4">
                                    <label class="mb-2 ml-1 font-bold text-xs text-slate-700">{{ __('Phone Number') }}</label>
                                    <input
                                        id="phone"
                                        name="phone"
                                        type="tel"
                                        class="focus:shadow-soft-primary-outline text-sm leading-5.6 ease-soft block w-full appearance-none rounded-lg border border-solid border-gray-300 bg-white bg-clip-padding px-3 py-2 font-normal text-gray-700 transition-all focus:border-fuchsia-300 focus:outline-none focus:transition-shadow @error('phone') border-red-500 @enderror"
                                        placeholder="{{ __('Your phone number') }}"
                                        value="{{ old('phone') }}"
                                        required
                                        autocomplete="tel" />
                                    <x-input-error :messages="$errors->get('phone')" class="mt-2 text-red-500 text-xs" />
                                </div>
                                
                                <!-- Email Address -->
                                <div class="mb-4">
                                    <label class="mb-2 ml-1 font-bold text-xs text-slate-700">{{ __('Email') }}</label>
                                    <input
                                        id="email"
                                        name="email"
                                        type="email"
                                        class="focus:shadow-soft-primary-outline text-sm leading-5.6 ease-soft block w-full appearance-none rounded-lg border border-solid border-gray-300 bg-white bg-clip-padding px-3 py-2 font-normal text-gray-700 transition-all focus:border-fuchsia-300 focus:outline-none focus:transition-shadow @error('email') border-red-500 @enderror"
                                        placeholder="{{ __('Email') }}"
                                        :value="old('email')"
                                        required
                                        autocomplete="username" />
                                    <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-500 text-xs" />
                                </div>
                                
                                <!-- Password -->
                                <div class="mb-4">
                                    <label class="mb-2 ml-1 font-bold text-xs text-slate-700">{{ __('Password') }}</label>
                                    <input
                                        id="password"
                                        name="password"
                                        type="password"
                                        class="focus:shadow-soft-primary-outline text-sm leading-5.6 ease-soft block w-full appearance-none rounded-lg border border-solid border-gray-300 bg-white bg-clip-padding px-3 py-2 font-normal text-gray-700 transition-all focus:border-fuchsia-300 focus:outline-none focus:transition-shadow @error('password') border-red-500 @enderror"
                                        placeholder="{{ __('Password') }}"
                                        required
                                        autocomplete="new-password" />
                                    <x-input-error :messages="$errors->get('password')" class="mt-2 text-red-500 text-xs" />
                                </div>
                                
                                <!-- Confirm Password -->
                                <div class="mb-6">
                                    <label class="mb-2 ml-1 font-bold text-xs text-slate-700">{{ __('Confirm Password') }}</label>
                                    <input
                                        id="password_confirmation"
                                        name="password_confirmation"
                                        type="password"
                                        class="focus:shadow-soft-primary-outline text-sm leading-5.6 ease-soft block w-full appearance-none rounded-lg border border-solid border-gray-300 bg-white bg-clip-padding px-3 py-2 font-normal text-gray-700 transition-all focus:border-fuchsia-300 focus:outline-none focus:transition-shadow"
                                        placeholder="{{ __('Confirm Password') }}"
                                        required
                                        autocomplete="new-password" />
                                </div>
                                
                                <!-- Terms and conditions -->
                                <div class="min-h-6 mb-0.5 block pl-12">
                                    <input
                                        id="terms"
                                        type="checkbox"
                                        class="mt-0.54 rounded-10 duration-250 ease-soft-in-out after:rounded-circle after:shadow-soft-2xl after:duration-250 checked:after:translate-x-5.25 h-5 relative float-left -ml-12 w-10 cursor-pointer appearance-none border border-solid border-gray-200 bg-slate-800/10 bg-none bg-contain bg-left bg-no-repeat align-top transition-all after:absolute after:top-px after:h-4 after:w-4 after:translate-x-px after:bg-white after:content-[''] checked:border-slate-800/95 checked:bg-slate-800/95 checked:bg-none checked:bg-right"
                                        required />
                                    <label class="mb-2 ml-1 font-normal cursor-pointer select-none text-sm text-slate-700" for="terms">
                                        {{ __('I agree with the') }}
                                        <a href="#" class="font-bold text-slate-700">{{ __('Terms and Conditions') }}</a>
                                    </label>
                                </div>
                                
                                <!-- Submit Button -->
                                <div class="text-center">
                                    <button
                                        type="submit"
                                        class="inline-block w-full px-6 py-3 mt-6 mb-2 font-bold text-center text-white uppercase align-middle transition-all bg-transparent border-0 rounded-lg cursor-pointer shadow-soft-md bg-x-25 bg-150 leading-pro text-xs ease-soft-in tracking-tight-soft bg-gradient-to-tl from-blue-600 to-cyan-400 hover:scale-102 hover:shadow-soft-xs active:opacity-85">
                                        {{ __('Sign up') }}
                                    </button>
                                </div>
                                
                                <!-- Login Link -->
                                <p class="mt-4 mb-0 leading-normal text-sm">
                                    {{ __('Already have an account?') }}
                                    <a href="{{ route('login') }}" class="font-bold text-slate-700">
                                        {{ __('Sign in') }}
                                    </a>
                                </p>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>
@endsection
