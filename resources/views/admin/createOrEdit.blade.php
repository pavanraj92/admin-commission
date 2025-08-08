@extends('admin::admin.layouts.master')

@section('title', isset($commission) ? 'Edit Commission' : 'Create Commission')
@section('page-title', isset($commission) ? 'Edit Commission' : 'Create Commission')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.commissions.index') }}">Commission Manager</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ isset($commission) ? 'Edit Commission' : 'Create Commission' }}
    </li>
@endsection

@section('content')
    <div class="container-fluid">
        <!-- Start faq Content -->
        <div class="row">
            <div class="col-12">
                <div class="card card-body">
                    <form
                        action="{{ isset($commission) ? route('admin.commissions.update', $commission) : route('admin.commissions.store') }}"
                        method="POST">
                        @csrf
                        @if (isset($commission))
                            @method('PUT')
                        @endif
                        <!--laravel error display -->
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <div class="form-group">
                            <label for="type">Type <span class="text-danger">*</span></label>
                            <select name="type" id="type" class="form-control select2" required>
                                <option value="global"
                                    {{ old('type', $commission->type ?? 'global') == 'global' ? 'selected' : '' }}>Global
                                </option>
                                <option value="category"
                                    {{ old('type', $commission->type ?? '') == 'category' ? 'selected' : '' }}>Category
                                </option>                              
                            </select>
                            @error('type')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>                        
                        <div class="form-group {{ (old('type', $commission->type ?? '') == 'category') ? '' : 'd-none' }}" id="categorySection">
                            <label for="category_id">Category</label>
                            <select name="category_id" id="category_id" class="form-control select2" {{ (old('type', $commission->type ?? '') == 'category') ? '' : 'disabled' }}>
                                <option value="">Select Category</option>
                                @if (isset($categories) && $categories->count() > 0)
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}"
                                            {{ old('category_id', $commission->category_id ?? '') == $category->id ? 'selected' : '' }}>
                                            {{ $category->title }}</option>
                                    @endforeach
                                @else
                                    <option value="">No Categories Available</option>
                                @endif
                            </select>
                            @error('category_id')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="commission_type">Commission Type <span class="text-danger">*</span></label>
                            <select name="commission_type" id="commission_type" class="form-control select2" required>
                                <option value="percentage"
                                    {{ old('commission_type', $commission->commission_type ?? 'percentage') == 'percentage' ? 'selected' : '' }}>
                                    Percentage</option>
                                <option value="fixed"
                                    {{ old('commission_type', $commission->commission_type ?? '') == 'fixed' ? 'selected' : '' }}>
                                    Fixed</option>
                            </select>
                            @error('commission_type')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="commission_value">Commission Value <span class="text-danger">*</span></label>
                            <input type="number" name="commission_value" id="commission_value" class="form-control"
                                value="{{ old('commission_value', $commission->commission_value ?? '') }}" step="0.01"
                                required>
                            @error('commission_value')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="status">Status <span class="text-danger">*</span></label>
                            <select name="status" id="status" class="form-control select2" required>
                                <option value="1"
                                    {{ old('status', $commission->status ?? 1) == 1 ? 'selected' : '' }}>Active</option>
                                <option value="0"
                                    {{ old('status', $commission->status ?? 1) == 0 ? 'selected' : '' }}>Inactive</option>
                            </select>
                            @error('status')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <button type="submit"
                                class="btn btn-primary">{{ isset($commission) ? 'Update' : 'Create' }}</button>
                            <a href="{{ route('admin.commissions.index') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('styles')
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endpush
@push('scripts')
    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.select2').select2({
                //placeholder: "Select options",
                allowClear: true,
                width: '100%'
            });

            $('#type').on('change', function() {
                var type = $(this).val();
                $.ajax({
                    url: '{{ route('admin.commissions.fetchOptions') }}',
                    type: 'GET',
                    data: {
                        type: type
                    },
                    success: function(response) {                        
                        // Populate category, product, vendor fields
                        if (response.categories) {
                            $('#categorySection').removeClass('d-none');                            
                            var catSelect = $('#category_id');
                            catSelect.prop('disabled', false);
                            catSelect.empty();
                            catSelect.append('<option value="">Select Category</option>');
                            $.each(response.categories, function(i, cat) {
                                catSelect.append('<option value="' + cat.id + '">' + cat
                                    .title + '</option>');
                            });
                        }                        
                    }
                });
            });
        });
    </script>
@endpush
