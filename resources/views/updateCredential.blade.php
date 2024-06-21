@extends('layouts.app')

@section('content')
    <section>
        <div class="sm:w-1/3 sm:px-0 mx-5 sm:mx-auto my-14 border border-black p-5 rounded-lg">
            <h1 class="sm:text-4xl text-3xl font-semibold text-center">Update Credential</h1>
            <form action="{{ route('updateCredentials') }}" method="post">
                @csrf
                <input type="hidden" name="credential_id" value="{{ $data->id }}">
                <div class="mt-10">
                    <label for="title" class="block text-sm font-medium leading-6 text-gray-900">Title</label>
                    <div class="mt-2">
                        <input type="text" name="title" id="title" value="{{ $data->title }}" autocomplete="given-name"
                            class="block px-3 w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                    </div>
                </div>
                <div class="mt-5">
                    <label for="username" class="block text-sm font-medium leading-6 text-gray-900">Username</label>
                    <div class="mt-2">
                        <input type="text" name="username" id="username" value="{{ $data->username }}" autocomplete="given-name"
                            class="block px-3 w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                    </div>
                </div>
                @php
                    $password = decrypt($data->password);
                @endphp
                <div class="mt-5">
                    <label for="password" class="block text-sm font-medium leading-6 text-gray-900">Password</label>
                    <div class="mt-2">
                        <input type="text" name="password" id="password" value="{{ $password }}" autocomplete="given-name"
                            class="block px-3 w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                    </div>
                </div>
                <div class="mt-5">
                    <label for="note" class="block text-sm font-medium leading-6 text-gray-900">Note</label>
                    <div class="mt-2">
                        <input type="text" name="note" id="note" value="{{ $data->note }}" autocomplete="given-name"
                            class="block px-3 w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                    </div>
                </div>
                
                <div class="mt-10">
                  <button type="submit" class="w-full text-white bg-blue-500 hover:bg-primary-700 focus:ring-4 focus:outline-none focus:ring-primary-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800">Update</button>

                </div>
            </form>
        </div>
    </section>
@endsection
