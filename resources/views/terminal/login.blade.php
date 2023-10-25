@extends('terminal.layout')

@section('body')
    <div class="flex flex-row justify-center items-center h-full">


        <div class="w-full sm:w-80 md:w-80 lg:w-80 px-7">
            <form action="{{ route('terminal.auth.login.store') }}" method="post">
                @csrf
                @method('post') 

                <div class="mb-6">
                    <label for="username" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Your
                        username</label>
                    <input type="text" value="{{ old('username') }}" id="username" name="username"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                        placeholder="example" required>
                    @error('username')
                        <p class="block mb-2 text-sm font-medium text-red-500 dark:text-white">{{ $message }}</p>
                    @enderror
                </div>
                <div class="mb-6">
                    <label for="password" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Your
                        password</label>
                    <input type="text" id="password" value="{{ old('password') }}" name="password"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 "
                        placeholder="********" required>
                    @error('password')
                        <p class="block mb-2 text-sm font-medium text-red-500 dark:text-white">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit"
                    class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center">Login</button>
            </form>

        </div>

    </div>
@endSection
