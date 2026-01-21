@extends('front.layouts.common')
@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/dropzone.min.css">
@endsection
@section('content')
    @include('front.layouts.dashboard.sidebar')

    <!-- Main content start -->
    <main class="main-content">

        <div class="document-upload document-pending" style="overflow: hidden;height: 500px;overflow-y: auto;">
            <div class="section-title">
                <div class="row">
                    <div class="col-6">
                        <h4>{{ $uploadDocument ? $uploadDocument->name : '' }}</h4>
                    </div>
                    <div class="col-6 text-end">
                        <a href="{{ route('user.documentList',['id' => $order_id]) }}" class="btn back-btn">Back <a>
                    </div>
                </div>


            </div>

            <!-- Dropzone -->
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


        </div>
    </main>

    <!-- Main content end -->
@endsection

@section('js')
    {{-- Dropzone JS --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/dropzone.min.js"></script>

    <script>
        Dropzone.autoDiscover = false;

        document.addEventListener("DOMContentLoaded", function() {

            const myDropzone = new Dropzone("#documentDropzone", {
                url: document.getElementById("documentDropzone").action,
                paramName: "file", // MUST be 'file'
                maxFilesize: 10, // MB
                uploadMultiple: false, // ❗ REQUIRED
                parallelUploads: 5,
                addRemoveLinks: true,
                acceptedFiles: ".pdf,.jpg,.jpeg,.png,.doc,.docx",

                headers: {
                    'X-CSRF-TOKEN': document
                        .querySelector('meta[name="csrf-token"]')
                        .getAttribute('content')
                },

                init: function() {

                    // When upload succeeds
                    this.on("success", function(file, response) {
                        file.serverId = response.file_id; // store DB id
                    });

                    // When remove is clicked
                    this.on("removedfile", function(file) {

                        // If file was not uploaded yet
                        if (!file.serverId) return;

                        fetch("{{ route('user.deleteUploadDocument') }}", {
                                method: "POST",
                                headers: {
                                    "Content-Type": "application/json",
                                    "X-CSRF-TOKEN": document
                                        .querySelector('meta[name="csrf-token"]')
                                        .getAttribute('content')
                                },
                                body: JSON.stringify({
                                    file_id: file.serverId
                                })
                            })
                            .then(res => res.json())
                            .catch(() => {
                                console.error("Failed to delete file");
                            });
                    });
                }
            });

        });
    </script>
@endsection
