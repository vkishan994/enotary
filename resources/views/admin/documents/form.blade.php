@extends('admin.layouts.common')
@section('content')
<h4 class="py-3 mb-4"><span class="text-muted fw-light">Document /</span> {{ isset($record) ? 'Edit' : 'Create' }}</h4>

<div class="row">
    <div class="col-md-12">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Document</h5>
            </div>
            <div class="card-body">
                <form
                    action="{{ isset($record) ? route('documents.update', $record->id) : route('documents.store') }}"
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

                        <div class="col-md-4">
                            <label class="form-label" for="price">Price</label>
                            <input type="number" step="0.01" name="price" class="form-control"
                                value="{{ old('price', isset($record) ? $record->price : '') }}" id="price"
                                placeholder="Enter Price" />
                            @error('price')
                            <div class="invalid-feedback">
                                <strong>{{ $message }}</strong>
                            </div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label" for="notary_service_types">Notary Service Types</label>
                            <select name="notary_service_types[]" id="notary_service_types" class="form-select" multiple>
                                @foreach ($notaryServiceTypes as $type)
                                <option value="{{ $type->id }}"
                                    @if (isset($record) && $record->notaryServiceTypes->pluck('id')->contains($type->id)) selected @endif>
                                    {{ $type->name }}
                                </option>
                                @endforeach
                            </select>
                            @error('notary_service_types')
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