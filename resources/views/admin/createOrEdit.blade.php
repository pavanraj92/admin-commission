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
                <form id="commissionForm"
                    action="{{ isset($commission) ? route('admin.commissions.update', $commission) : route('admin.commissions.store') }}"
                    method="POST">
                    @csrf
                    @if (isset($commission))
                    @method('PUT')
                    @endif
                    <!--laravel error display -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="type">Type <span class="text-danger">*</span></label>
                                <select name="type" id="type" class="form-control select2" required>
                                    <option value="">Select Type</option>

                                    <option value="global"
                                        {{ old('type', $commission->type ?? '') == 'global' ? 'selected' : '' }}
                                        {{ $disableGlobal && (!isset($commission) || $commission->type !== 'global') ? 'disabled' : '' }}>
                                        Global
                                    </option>

                                    <option value="category"
                                        {{ old('type', $commission->type ?? '') == 'category' ? 'selected' : '' }}>
                                        Category
                                    </option>
                                </select>

                                @error('type')
                                <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group {{ (old('type', $commission->type ?? '') == 'category') ? '' : 'd-none' }}" id="categorySection">
                                <label for="category_id">Category <span class="text-danger">*</span></label>
                                <select name="category_ids[]" id="category_id" class="form-control select2" multiple
                                    {{ (old('type', $commission->type ?? '') == 'category') ? '' : 'disabled' }}>
                                    @foreach($categories as $category)
                                    <option value="{{ $category->id }}"
                                        {{ isset($commission) && $commission->categories->contains($category->id) ? 'selected' : '' }}>
                                        {{ $category->title }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('category_ids')
                                <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-12">
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
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="commission_value">Commission Value <span class="text-danger">*</span></label>
                                <input type="number" name="commission_value" id="commission_value" class="form-control"
                                    value="{{ old('commission_value', $commission->commission_value ?? '') }}" step="0.01"
                                    required>
                                @error('commission_value')
                                <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-12">
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
                        </div>

                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <button type="submit"
                                class="btn btn-primary">{{ isset($commission) ? 'Update' : 'Create' }}</button>
                            <a href="{{ route('admin.commissions.index') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script>
    $(document).ready(function() {
        $('.select2').select2({
            // allowClear: true,
            width: '100%'
        });

        $('#commissionForm').validate({
            ignore: [],
            rules: {
                type: {
                    required: true,
                },
                "category_ids[]": {
                    required: function(element) {
                        return $('#type').val() === 'category';
                    }
                },
                commission_type: {
                    required: true,
                },
                commission_value: {
                    required: true,
                    number: true
                }
            },
            messages: {
                type: {
                    required: "Please enter a type"
                },
                "category_ids[]": {
                    required: "Please enter a category"
                },
                commission_type: {
                    required: "Please enter a commission type"
                },
                commission_value: {
                    required: "Please enter a commission value",
                    number: "Please enter a number only",
                }
            },
            submitHandler: function(form) {
                const $btn = $('#saveBtn');
                $btn.prop('disabled', true).text($btn.text().trim().toLowerCase() === 'update' ?
                    'Updating...' : 'Saving...');
                form.submit();
            },
            errorElement: 'div',
            errorClass: 'text-danger custom-error',
            errorPlacement: function(error, element) {
                $('.validation-error').hide();

                if (element.hasClass('select2-hidden-accessible')) {
                    // If element is a select2 dropdown
                    error.insertAfter(element.next('.select2')); // place after the Select2 container
                } else if (element.is('select')) {
                    // Regular select box
                    error.insertAfter(element);
                } else {
                    // Default placement for input, textarea, etc.
                    error.insertAfter(element);
                }
            }
        });

        $('#type').on('change', function() {
            var type = $(this).val();
            var catSelect = $('#category_id');

            if (type === 'category') {
                $.ajax({
                    url: "{{ route('admin.commissions.fetchOptions') }}",
                    type: 'GET',
                    data: {
                        type: type
                    },
                    success: function(response) {
                        console.log(response);
                        $('#categorySection').removeClass('d-none');
                        catSelect.prop('disabled', false);
                        catSelect.empty();

                        if (response.availableCategories && response.availableCategories.length) {
                            $.each(response.availableCategories, function(i, cat) {
                                catSelect.append('<option value="' + cat.id + '">' + cat.title + '</option>');
                            });
                        }

                        catSelect.trigger('change'); // refresh Select2
                    }
                });
            } else {
                $('#categorySection').addClass('d-none');
                catSelect.prop('disabled', true).empty();
            }
        });


    });
</script>
@endpush