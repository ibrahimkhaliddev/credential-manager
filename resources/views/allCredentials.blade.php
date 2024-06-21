@extends('layouts.app')

@section('content')
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    {{-- <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script> --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/3.1.9-1/crypto-js.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css" />
    <link rel="stylesheet" href="{{ asset('jquery.sweet-modal.min.css') }}" />
    <script src="{{ asset('jquery.sweet-modal.min.js') }}"></script>
    <div class="px-5"><button class="bg-blue-400 text-white rounded-sm py-2 px-4 w-full" id="openCreateModal">Create</button>
    </div>
    <section class="px-5 my-5">

        <div>
            <input type="search" placeholder="Search" id="search-input" onkeyup="searchCredentials()" name="note"
                id="note" autocomplete="off"
                class="block px-3 w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
        </div>
        <div id="success-message" class="hidden bg-green-500 text-white mt-4 rounded-md px-4 py-2">
            <i class="fas fa-check"></i> Credential created successfully!
        </div>
        <div id="delete-message" class="hidden bg-green-500 text-white mt-4 rounded-md px-4 py-2">
            <i class="fas fa-check"></i> Credential deleted successfully!
        </div>
        <div id="update-message" class="hidden bg-green-500 text-white mt-4 rounded-md px-4 py-2">
            <i class="fas fa-check"></i> Credential updated successfully!
        </div>

        <div id="toast-container" class="toast-container"></div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-x-5 gap-y-1 theDataSec pt-5" id="theDataSec">
            @foreach ($data as $item)
                <div class="bg-gray-100 rounded-lg shadow-sm sm:mt-0" style="margin-top: 2px">
                    <div class="px-5 py-3">
                        <div class="flex justify-between align-items-center">
                            <h4 class="font-semibold text-sm capitalize">{{ $item->title }}</h4>
                            <div class="flex gap-5">
                                <a class="delete-button" data-credential-id="{{ $item->id }}" href="#"><i
                                        style="font-size: 10px" class=" fas fa-trash-alt"></i></a>
                                <a class="edit-button" data-credential-id="{{ $item->id }}" href="#"><i
                                        style="font-size: 10px" class=" fas fa-edit"></i></a>
                            </div>
                        </div>

                        @php
                            $password = decrypt($item->password);
                            $visiblePart = substr($password, 0, 2);
                            $remainingSpace = 100 - strlen($visiblePart);
                            $hiddenPart = str_repeat('*', max(0, $remainingSpace));
                            $maskedPassword = $visiblePart . $hiddenPart;
                        @endphp
                        <div class="mb-1">
                            <h4 class="text-xs">{{ $item->username }}</h4>
                        </div>
                        @if ($password !== null)
                            <hr class="border my-1.5">
                            <div class="flex justify-between">
                                <p class="overflow-hidden text-xs" id="password-{{ $item->id }}">{{ $maskedPassword }}
                                </p>
                                <div class="flex justify-between gap-x-1 ml-2">
                                    <div id="copy-button-{{ $item->id }}">
                                        <button
                                            class="border border-black px-2 ml-4 rounded-sm text-xs well-designed-button"
                                            onclick="copyPassword({{ $item->id }}, '{{ $password }}')">Copy</button>

                                    </div>
                                    <button class="border border-black px-2 ml-4 rounded-sm text-xs well-designed-button"
                                        data-password-visible="false"
                                        onclick="togglePasswordVisibility({{ $item->id }}, '{{ $password }}')">Show</button>
                                </div>
                            </div>
                        @endif

                        <div class="mt-1">
                            <p style="font-size: 10px">{{ $item->note }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    <style>
        .well-designed-button {
            border: 1px solid #000;
            padding: 0px 5px;
            height: 23px;
            display: flex;
            justify-content: center;
            align-items: center;
            margin-left: 4px;
            border-radius: 3px;
            cursor: pointer;
        }
    </style>

    <script>
        $(document).on('click', '.edit-button', function(event) {
            event.preventDefault();

            var credentialId = $(this).data('credential-id');

            $.ajax({
                type: 'GET',
                url: '/edit/' + credentialId,
                success: function(data) {
                    console.log(data)
                    var modalContent = `
                <form id="update-form">
                    <input type="hidden" name="credential_id" value="${data[0].id}">
                    <div class="form-group">
                        <label for="title">Title</label>
                        <input type="text" class="block px-3 w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" id="title" name="title" value="${data[0].title}">
                    </div>
                    <div class="form-group mt-5">
                        <label for="username">Username</label>
                        <input type="text" class="block px-3 w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" id="username" name="username" value="${data[1]}">
                    </div>
                    <div class="form-group mt-5">
                        <label for="password">Password</label>
                        <input type="text" class="block px-3 w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" id="password" name="password" value="${data[2]}">
                    </div>
                    <div class="form-group mt-5">
                        <label for="note">Note</label>
                        <input type="text" class="block px-3 w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" id="note" name="note" value="${data[3]}">
                    </div>
                </form>
            `;
                    $.sweetModal({
                        title: 'Update Credential Details',
                        content: modalContent,
                        buttons: [{
                                label: 'Update',
                                classes: 'btn btn-primary',
                                action: function() {
                                    var title = $('#title').val();
                                    var username = $('#username').val();
                                    var password = $('#password').val();
                                    var note = $('#note').val();
                                    var credentialId = $('input[name="credential_id"]')
                                        .val();

                                    $.ajax({
                                        type: 'POST',
                                        url: '{{ route('updateCredentials') }}',
                                        data: {
                                            _token: '{{ csrf_token() }}',
                                            title: title,
                                            username: username,
                                            password: password,
                                            note: note,
                                            credential_id: credentialId
                                        },
                                        success: function(response) {
                                            console.log('Success:',
                                                response);
                                            const dataSection = document
                                                .getElementById(
                                                    'theDataSec');
                                            dataSection.innerHTML =
                                                response;

                                            const successMessage = document
                                                .getElementById(
                                                    'update-message');
                                            successMessage.style.display =
                                                'block';

                                            setTimeout(function() {
                                                successMessage.style
                                                    .display =
                                                    'none';
                                            }, 2000);
                                        },
                                        error: function(xhr, status, error) {
                                            console.error('Error:', error);
                                            $.sweetModal({
                                                content: 'Error updating credential: ' +
                                                    error
                                            });
                                        }
                                    });
                                }
                            },
                            {
                                label: 'Cancel',
                                classes: 'btn btn-default',
                                action: function() {}
                            }
                        ]
                    });
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                }
            });
        });





        $(document).on('click', '.delete-button', function(event) {
            event.preventDefault();

            var credentialId = $(this).data('credential-id');

            $.sweetModal.confirm('Are you sure?', 'You are about to delete this credential!', function() {
                $.ajax({
                    type: 'POST',
                    url: '/delete/' + credentialId,
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        const dataSection = document.getElementById('theDataSec');
                        dataSection.innerHTML = response;

                        const successMessage = document.getElementById('delete-message');
                        successMessage.style.display = 'block';

                        setTimeout(function() {
                            successMessage.style.display = 'none';
                        }, 3000);
                    },
                    error: function(xhr, status, error) {
                        console.error('Error:', error);
                    }
                });
            });
        });



        $('#openCreateModal').click(function() {
            var modal = $.sweetModal({
                title: 'Enter Credential Details',
                content: '<form id="create-form">' +
                    '<input type="text" name="title" placeholder="Title" id="title" required autocomplete="off" ' +
                    'class="block px-3 w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"><br>' +
                    '<input type="text" name="username" placeholder="Username" id="username" autocomplete="off" ' +
                    'class="block px-3 w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"><br>' +
                    '<input type="password" name="password" placeholder="Password" id="password" autocomplete="off" ' +
                    'class="block px-3 w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"><br>' +
                    '<input type="text" name="note" id="note" placeholder="Note" autocomplete="off" ' +
                    'class="block px-3 w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"><br>' +
                    '<div id="error-message" class="text-red-500 mt-2" style="display: none;">Title is required.</div>' +
                    '</form>',
                buttons: [{
                        label: 'Create',
                        classes: 'btn btn-primary',
                        action: function() {
                            var title = $('#title').val();
                            var username = $('#username').val();
                            var password = $('#password').val();
                            var note = $('#note').val();

                            if (!title) {
                                $('#error-message').css('display', 'block');
                                return false; // Prevent closing the modal
                            }

                            $('#error-message').css('display', 'none');

                            $.ajax({
                                type: 'POST',
                                url: '{{ route('storeCredential') }}',
                                data: {
                                    _token: '{{ csrf_token() }}',
                                    title: title,
                                    username: username,
                                    password: password,
                                    note: note
                                },
                                success: function(response) {
                                    const dataSection = document.getElementById(
                                        'theDataSec');
                                    dataSection.innerHTML = response;

                                    const successMessage = document.getElementById(
                                        'success-message');
                                    successMessage.style.display = 'block';

                                    setTimeout(function() {
                                        successMessage.style.display = 'none';
                                    }, 3000);
                                },
                                error: function(xhr, status, error) {
                                    $.sweetModal({
                                        content: 'Error: ' + error
                                    });
                                }
                            });
                        }
                    },
                    {
                        label: 'Cancel',
                        classes: 'btn btn-default',
                        action: function() {}
                    }
                ]
            });
        });






        function searchCredentials() {
            const searchInput = document.getElementById('search-input').value;
            if (searchInput) {
                fetch('/search', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            query: searchInput
                        }),
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.text();
                    })
                    .then(data => {
                        const dataSection = document.getElementById('theDataSec');
                        dataSection.innerHTML = data;
                    })
                    .catch(error => {
                        console.error('Error:', error);
                    });
            }
        }


        function togglePasswordVisibility(itemId, fullPassword) {
            const passwordElement = document.getElementById(`password-${itemId}`);
            const copyButtonElement = document.getElementById(`copy-button-${itemId}`);
            const buttonElement = document.querySelector(
                `[data-password-visible][onclick="togglePasswordVisibility(${itemId}, '${fullPassword}')"]`);
            const currentVisibility = buttonElement.getAttribute('data-password-visible');

            if (currentVisibility === 'false') {
                passwordElement.innerText = fullPassword;
                buttonElement.innerText = 'Hide';
                buttonElement.setAttribute('data-password-visible', 'true');
                copyButtonElement.style.display = 'inline-block';
            } else {
                const visiblePart = fullPassword.substring(0, 2);
                const remainingSpace = 100 - visiblePart.length;
                const hiddenPart = '*'.repeat(Math.max(0, remainingSpace));
                passwordElement.innerText = visiblePart + hiddenPart;
                buttonElement.innerText = 'Show';
                buttonElement.setAttribute('data-password-visible', 'false');
            }
        }

        function copyPassword(itemId, fullPassword) {
            const copyButtonElement = document.getElementById(`copy-button-${itemId}`);
            const tempInput = document.createElement('textarea');
            document.body.appendChild(tempInput);
            tempInput.value = fullPassword;
            tempInput.select();
            document.execCommand('copy');
            document.body.removeChild(tempInput);
            copyButtonElement.querySelector('button').innerText = 'Copied';
            copyButtonElement.disabled = true;
        }
    </script>
    <style>
        /* .toast-container{
                        margin: 30px 0px;
                    } */
    </style>
@endsection
