@extends('admin.layouts.common')
@section('title')
    @if(isset($testimonial))
        Testimonial - Edit
    @else
        Testimonial - Create
    @endif
@endsection
@section('css')
    <style>
        .error-alert {
            color: red
        }

        .invalid-feedback {
            display: block !important;
        }
    </style>
@endsection
@section('content')
    <h4 class="py-3 mb-4"><span class="text-muted fw-light">Testimonial/</span>
        @if (isset($testimonial))
            Edit
        @else
            Create
        @endif
    </h4>

    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Testimonial </h5>
                </div>
                <div class="card-body">
                    <form id="testimonialForm" novalidate
                        action="{{ isset($testimonial) ? route('testimonials.update', $testimonial->id) : route('testimonials.store') }}"
                        method="post">
                        @csrf
                        @if (isset($testimonial))
                            @method('PUT')
                        @endif

                        <div class="row">
                            <div class="col-md-4">
                                <label class="form-label" for="name">Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control"
                                    value="{{ old('name', isset($testimonial) ? $testimonial->name : '') }}" id="name"
                                    placeholder="Enter Name" />
                                @error('name')
                                    <div class="invalid-feedback">
                                        <strong>{{ $message }}</strong>
                                    </div>
                                @enderror
                            </div>

                            <div class="col-md-4"> <label for="rating" class="form-label">Rating</label>
                                <select class="form-select" name="rating" id="rating"
                                    aria-label="Default select example">
                                    <option value="1" @if (old('rating', isset($testimonial) ? $testimonial->rating : '') == 1) selected @endif> 1
                                    </option>
                                    <option value="2" @if (old('rating', isset($testimonial) ? $testimonial->rating : '') == 2) selected @endif> 2
                                    </option>
                                    <option value="3" @if (old('rating', isset($testimonial) ? $testimonial->rating : '') == 3) selected @endif> 3
                                    </option>
                                    <option value="4" @if (old('rating', isset($testimonial) ? $testimonial->rating : '') == 4) selected @endif> 4
                                    </option>
                                    <option value="5" @if (old('rating', isset($testimonial) ? $testimonial->rating : '') == 5) selected @endif> 5
                                    </option>
                                </select>
                                @error('rating')
                                    <div class="error-alert">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                <select class="form-select" name="status" id="status"
                                    aria-label="Default select example">
                                    <option value="" @if (old('status', isset($testimonial) ? $testimonial->status : '') == '') selected @endif>Select Status
                                    </option>
                                    <option value="1" @if (old('status', isset($testimonial) ? $testimonial->status : '') == 1) selected @endif> Active
                                    </option>
                                    <option value="0" @if (old('status', isset($testimonial) ? $testimonial->status : '') == 0) selected @endif> InActive
                                    </option>
                                </select>
                                @error('status')
                                    <div class="error-alert">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>


                        <div class="mb-3 mt-3">
                            <label class="form-label" for="content">Content <span class="text-danger">*</span></label>
                            <textarea name="content" id="content" class="form-control">{{ old('testimonial', isset($testimonial) ? $testimonial->content : '') }} </textarea>
                            @error('content')
                                <div class="invalid-feedback">
                                    <strong>{{ $message }}</strong>
                                </div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary">Submit</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js"></script>
        <script>
            $(function() {
                $('#testimonialForm').validate({
                    rules: {
                        name: { required: true },
                        status: { required: true },
                        content: { required: true }
                    },
                    messages: {
                        name: 'Please enter name',
                        status: 'Please select status',
                        content: 'Please enter content'
                    },
                    errorElement: 'div',
                    errorClass: 'invalid-feedback',
                    errorPlacement: function(error, element) {
                        if (element.hasClass('form-select')) {
                            error.addClass('error-alert');
                            element.closest('.col-md-4, .mb-3').append(error);
                        } else {
                            element.after(error);
                        }
                    },
                    highlight: function(element) {
                        $(element).addClass('is-invalid');
                    },
                    unhighlight: function(element) {
                        $(element).removeClass('is-invalid');
                        $(element).next('.invalid-feedback, .error-alert').remove();
                    }
                });
            });
        </script>
    @endpush
@endsection
