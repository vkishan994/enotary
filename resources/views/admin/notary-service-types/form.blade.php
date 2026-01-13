@extends('admin.layouts.common')
@section('content')
    <h4 class="py-3 mb-4"><span class="text-muted fw-light">Notary Service Type/</span> {{ $title ?? (isset($record) ? 'Edit' : 'Create') }}</h4>

    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Notary Service Type</h5>
                </div>
                <div class="card-body">
                    <form
                        action="{{ isset($record) ? route('notary-service-types.update', $record->id) : route('notary-service-types.store') }}"
                        method="post">
                        @csrf
                        @if (isset($record))
                            @method('PUT')
                        @endif

                        <div class="row">
                        <div class="col-md-4">
                            <label class="form-label" for="name">Name</label>
                            <input type="text" name="name" class="form-control"
                                value="{{ old('name', isset($record) ? $record->name : '') }}" id="name"
                                placeholder="Enter Name" />
                            @error('name')
                                <div class="invalid-feedback">
                                    <strong>{{ $message }}</strong>
                                </div>
                            @enderror
                        </div>
                        </div>

                        <button type="submit" class="btn btn-primary mt-3">Submit</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
