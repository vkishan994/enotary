@extends('front.layouts.common')
@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/dropzone.min.css">
    <style>
        /* Uploaded files container */
        #uploaded-files-list {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-top: 20px;
        }

        /* Each uploaded file item */
        .uploaded-file-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 12px;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            background-color: #f8f9fa;
        }

        /* File name and status */
        .uploaded-file-item h5 {
            margin-bottom: 2px;
            font-size: 15px;
        }

        .uploaded-file-item p {
            font-size: 13px;
            color: #6c757d;
        }

        /* Buttons container */
        .pending-actions {
            display: flex;
            gap: 8px;
        }

        /* Download button */
        .btn-download,
        .action-btn {
            background-color: #b7862b;
            color: #fff;
            border-radius: 50%;
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: none;
            text-decoration: none;
        }

        .btn-download i {
            font-size: 16px;
        }

        /* Delete button */
        .btn-delete {
            background-color: #dc3545;
            color: #fff;
            border-radius: 50%;
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: none;
            cursor: pointer;
        }

        .btn-delete i {
            font-size: 16px;
        }

        /* Hover effect */
        .btn-download:hover,
        .action-btn:hover {
            background-color: #a3731f;
            color: #fff;
        }

        .btn-delete:hover {
            background-color: #c82333;
            color: #fff;
        }

        /* .document-upload {
                            height: unset !important;
                        } */
    </style>
@endsection
@section('content')
    @include('front.layouts.dashboard.sidebar')

    <!-- Main content start -->
    <main class="main-content">

        <div class="document-upload document-pending"
            style="overflow: hidden;height: 500px !important;overflow-y: auto; @if (isset($userUploadedDocuments) && $userUploadedDocuments->status == 'submitted') height: 500px; @else height: unset; @endif">
            <div class="section-title">
                <div class="row">
                    <div class="col-6">
                        <h4>{{ $uploadDocument ? $uploadDocument->name : '' }}</h4>
                    </div>
                    <div class="col-6 text-end">
                        <a href="{{ route('user.documentList', ['id' => $order_id]) }}" class="btn back-btn">Back <a>
                    </div>
                </div>

            </div>

            <!-- Dropzone -->

            @if (
                (isset($userUploadedDocuments) &&
                    $userUploadedDocuments->status !== 'submitted' &&
                    $userUploadedDocuments->status !== 'verified') ||
                    !isset($userUploadedDocuments))
                <div class="upload-document-section mt-4">
                    <form
                        action="{{ route('user.storeUploadDocument', ['order_id' => $order_id, 'document_id' => $document_id, 'upload_document_id' => encrypt($uploadDocument->id)]) }}"
                        method="POST" enctype="multipart/form-data" class="dropzone" id="documentDropzone">
                        @csrf

                        <div class="dz-message">
                            <i class="fas fa-cloud-upload-alt fa-3x mb-3"></i>
                            <h5>Drag & drop files here</h5>
                            <p>or click to upload multiple documents</p>
                        </div>
                    </form>
                </div>
                <div id="dropzone-error" style="margin-top:5px; color:red; display:none;">dropzone error mesasge</div>
            @endif

            @if (isset($userUploadedDocuments) && !empty($userUploadedDocuments))
                <div id="uploaded-files-list">
                    @if (isset($userUploadedDocuments->verify_document_items) && !empty($userUploadedDocuments->verify_document_items))
                        @foreach ($userUploadedDocuments->verify_document_items as $item)
                            @php
                                $fileUrl = asset('storage/' . $item->file_path);
                                $isImage = preg_match('/\.(jpg|jpeg|png)$/i', $item->file_name);
                            @endphp

                            <div class="pending-item uploaded-file-item d-flex justify-content-between align-items-center"
                                id="uploaded-file-{{ $item->id }}">
                                <div class="pending-item-content d-flex align-items-center gap-3">
                                    @if ($isImage)
                                        <img src="{{ $fileUrl }}" alt="{{ $item->file_name }}"
                                            style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;">
                                    @else
                                        <i class="fas fa-file-alt fa-2x"></i>
                                    @endif

                                    <div class="pending-text">
                                        <h5>{{ $item->file_name }}</h5>
                                        <p>Uploaded</p>
                                    </div>
                                </div>

                                <div class="pending-actions d-flex gap-2">
                                    <a href="{{ $fileUrl }}" target="_blank" class="action-btn"
                                        title="Open in new tab">
                                        <i class="fas fa-external-link-alt"></i>
                                    </a>

                                    <a href="{{ $fileUrl }}" download class="action-btn" title="Download">
                                        <i class="fas fa-download"></i>
                                    </a>

                                    @if ($item->status !== 'submitted' && $item->status !== 'verified')
                                        <button type="button" class="btn-delete delete-uploaded-file action-btn"
                                            data-file-id="{{ encrypt($item->id) }}" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            @endif

            <div id="uploaded-files-list" class="pending-list mt-4"></div>

        </div>
    </main>

    <!-- Main content end -->
@endsection

@section('js')
    {{-- Dropzone JS --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/dropzone.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        Dropzone.autoDiscover = false;

        let existingFileCount =
            {{ isset($userUploadedDocuments) && isset($userUploadedDocuments->verify_document_items) ? count($userUploadedDocuments->verify_document_items) : 0 }};

        document.addEventListener("DOMContentLoaded", function() {

            const maxAllowedFiles = 2;

            const myDropzone = new Dropzone("#documentDropzone", {
                url: document.getElementById("documentDropzone").action,
                paramName: "file",
                maxFilesize: 5,
                uploadMultiple: false,
                acceptedFiles: ".pdf,.jpg,.jpeg,.png,.doc,.docx",
                addRemoveLinks: true, // ✅ SHOW REMOVE LINK

                maxFiles: maxAllowedFiles - existingFileCount,

                headers: {
                    'X-CSRF-TOKEN': document
                        .querySelector('meta[name="csrf-token"]')
                        .getAttribute('content')
                },

                init: function() {

                    const dz = this;

                    // If already 2 files uploaded
                    if (existingFileCount >= maxAllowedFiles) {
                        dz.options.maxFiles = 0;

                        document.getElementById('dropzone-error').style.display = 'block';
                        document.getElementById('dropzone-error').innerText =
                            "Only 2 files are allowed. Delete one to upload a new file.";
                    }

                    // Prevent exceeding limit
                    dz.on("maxfilesexceeded", function(file) {
                        dz.removeFile(file);

                        document.getElementById('dropzone-error').style.display = 'block';
                        document.getElementById('dropzone-error').innerText =
                            "Only 2 files are allowed. Delete one to upload a new file.";
                    });

                    // On upload success
                    dz.on("success", function(file, response) {

                        existingFileCount++;
                        dz.options.maxFiles = maxAllowedFiles - existingFileCount;

                        if (existingFileCount >= maxAllowedFiles) {
                            document.getElementById('dropzone-error').style.display = 'block';
                            document.getElementById('dropzone-error').innerText =
                                "Only 2 files are allowed. Delete one to upload a new file.";
                        }

                        file.serverId = response.file_id; // store for delete

                        const isImage = /\.(jpg|jpeg|png)$/i.test(response.file_name);

                        const fileHTML = `
                <div class="pending-item uploaded-file-item d-flex justify-content-between align-items-center"
                    id="uploaded-file-${response.file_id}">
                    <div class="pending-item-content d-flex align-items-center gap-3">
                        ${isImage
                            ? `<img src="${response.download_url}" style="width:50px;height:50px;object-fit:cover;border-radius:4px;">`
                            : `<i class="fas fa-file-alt fa-2x"></i>`}
                        <div class="pending-text">
                            <h5>${response.file_name}</h5>
                            <p>Uploaded successfully</p>
                        </div>
                    </div>
                </div>
            `;

                        document.getElementById("uploaded-files-list")
                            .insertAdjacentHTML("beforeend", fileHTML);
                    });

                    // ✅ HANDLE REMOVE FROM DROPZONE
                    dz.on("removedfile", function(file) {

                        // If file was not uploaded yet, just reduce counter
                        if (!file.serverId) {
                            return;
                        }

                        fetch("{{ route('user.deleteUploadDocument') }}", {
                                method: "POST",
                                headers: {
                                    "Content-Type": "application/json",
                                    "X-CSRF-TOKEN": "{{ csrf_token() }}",
                                    "Accept": "application/json"
                                },
                                body: JSON.stringify({
                                    file_id: file.serverId
                                })
                            })
                            .then(res => res.json())
                            .then(response => {
                                if (response.success) {

                                    existingFileCount--;
                                    dz.options.maxFiles = maxAllowedFiles -
                                        existingFileCount;

                                    document.getElementById(
                                        `uploaded-file-${file.serverId}`)?.remove();

                                    document.getElementById('dropzone-error').style
                                        .display = 'none';
                                }
                            })
                            .catch(error => {
                                console.error("File delete failed:", error);
                            });
                    });

                }
            });

            // Use event delegation for dynamically added elements
            document.body.addEventListener("click", function(e) {
                if (e.target.closest(".delete-uploaded-file")) {

                    const button = e.target.closest(".delete-uploaded-file");
                    const fileId = button.dataset.fileId;
                    const fileCard = button.closest(".uploaded-file-item");

                    // SweetAlert confirmation
                    Swal.fire({
                        title: 'Are you sure?',
                        text: "This file will be permanently deleted!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Yes, delete it!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // AJAX request to delete file
                            fetch("{{ route('user.deleteUploadDocument') }}", {
                                    method: "POST",
                                    headers: {
                                        "Content-Type": "application/json",
                                        "X-CSRF-TOKEN": "{{ csrf_token() }}",
                                        "Accept": "application/json"
                                    },
                                    body: JSON.stringify({
                                        file_id: fileId
                                    })
                                })
                                .then(async res => {
                                    if (!res.ok) {
                                        const text = await res.text();
                                        throw new Error(text);
                                    }
                                    return res.json();
                                })
                                .then(response => {
                                    if (response.success) {
                                        Swal.fire({
                                            title: 'Deleted!',
                                            text: 'Your file has been deleted.',
                                            icon: 'success',
                                            confirmButtonText: 'OK'
                                        }).then(() => {
                                            location
                                                .reload(); //  reload page after clicking OK
                                        });
                                    } else {
                                        Swal.fire('Error!', response.message ||
                                            'File could not be deleted.', 'error');
                                    }
                                })
                                .catch(error => {
                                    console.error("File delete failed:", error);
                                    Swal.fire(
                                        'Error!',
                                        'Server error occurred. Check console for details.',
                                        'error'
                                    );
                                });
                        }
                    });
                }
            });

        });
    </script>
@endsection
