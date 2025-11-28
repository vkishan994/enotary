<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Terminal</title>

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Ubuntu+Mono&display=swap" rel="stylesheet">

    @vite(['resources/sass/app.scss', 'resources/js/app.js'])

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        #execution {
            font-family: 'Ubuntu Mono', monospace;
            min-height: 120px;
            white-space: pre-wrap;
            overflow: auto;
        }
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-12 col-md-8">
                <div class="card shadow-sm">
                    <div class="card-header text-center">
                        <h5 class="mb-0"><i class="ti ti-terminal me-2"></i>Terminal</h5>
                    </div>

                    <div class="card-body">
                        <form id="terminalForm" method="POST" action="{{ route('terminal.execute') }}">
                            @csrf

                            <div class="mb-3">
                                <label for="command" class="form-label">Command <span class="text-danger">*</span></label>
                                <input id="command" name="command" class="form-control {{ $errors->get('command') ? 'is-invalid' : '' }}"
                                       type="text" value="{{ old('command') }}" placeholder="Enter command (e.g., ls -la)" required autofocus>
                                @if ($errors->get('command'))
                                    <div class="invalid-feedback">{{ $errors->first('command') }}</div>
                                @endif
                            </div>

                            <div class="d-flex gap-2">
                                <button id="executeButton" type="submit" class="btn btn-primary">Execute</button>
                                <button id="clearButton" type="button" class="btn btn-outline-secondary">Clear Output</button>

                                <div id="loader" class="ms-auto d-none align-self-center">
                                    <div class="spinner-border spinner-border-sm text-primary"></div>
                                </div>
                            </div>
                        </form>

                        <div class="mt-4">
                            <label class="form-label">Output</label>
                            <pre id="execution" class="border rounded p-3 bg-light d-none"></pre>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- jQuery + Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        $(function () {
            $('#terminalForm').on('submit', function (e) {
                e.preventDefault();

                let form = $(this);
                let formData = new FormData(this);
                let outputArea = $('#execution');
                let submitButton = $('#executeButton');

                $.ajax({
                    url: form.attr('action'),
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    beforeSend: function () {
                        submitButton.prop('disabled', true).text('Executing...');
                        $('#loader').removeClass('d-none');
                        outputArea.removeClass('d-none text-danger').text('');
                    },
                    success: function (response) {
                        let output = response.output?.trim() || 'Command executed successfully.';
                        outputArea.removeClass('text-danger').html(output);
                        resetButton();
                    },
                    error: function (xhr) {
                        let message =
                            xhr.responseJSON?.output ||
                            xhr.responseJSON?.message ||
                            xhr.responseText ||
                            'Error executing command.';
                        outputArea.addClass('text-danger').html(message);
                        resetButton();
                    }
                });

                function resetButton() {
                    submitButton.prop('disabled', false).text('Execute');
                    $('#loader').addClass('d-none');
                }
            });

            $('#clearButton').on('click', function () {
                $('#execution').addClass('d-none').text('');
            });
        });
    </script>
</body>
</html>
