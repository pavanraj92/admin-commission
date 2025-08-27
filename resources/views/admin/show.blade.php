@extends('admin::admin.layouts.master')

@section('title', 'Commission Management')
@section('page-title', 'Commission Details')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('admin.commissions.index') }}">Commission Manager</a></li>
<li class="breadcrumb-item active" aria-current="page">Commission Details</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">

                    <!-- Header -->
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h4 class="card-title mb-0">Commission #{{ $commission->id }}</h4>
                        <a href="{{ route('admin.commissions.index') }}" class="btn btn-secondary ml-2">Back</a>
                    </div>

                    <div class="row">
                        <!-- Commission Information -->
                        <div class="col-md-8">
                            <div class="card">
                                <div class="card-header bg-primary">
                                    <h5 class="mb-0 text-white">Commission Information</h5>
                                </div>
                                <div class="card-body">
                                    <ul class="list-unstyled">
                                        <li class="mt-2"><strong>Type:</strong> {{ ucfirst($commission->type) }}</li>
                                        @if ($commission->type == 'category')
                                        <li class="mt-2">
                                            <strong>Categories:</strong>
                                            @if($commission->categories && $commission->categories->count() > 0)
                                            @foreach($commission->categories as $category)
                                            <span class="badge badge-success">{{ $category->title }}</span>
                                            @endforeach
                                            @else
                                            —
                                            @endif
                                        </li>
                                        @endif
                                        <li class="mt-2"><strong>Commission Type:</strong> {{ ucfirst($commission->commission_type) }}</li>
                                        <li class="mt-2"><strong>Commission Value:</strong>
                                            @if ($commission->commission_type == 'percentage')
                                            {{ $commission->commission_value }}%
                                            @else
                                            ${{ number_format($commission->commission_value, 2) }}
                                            @endif
                                        </li>
                                        <li class="mt-2"><strong>Status:</strong> <span class="badge badge-{{ $commission->status ? 'success' : 'danger' }}">
                                                {{ $commission->status ? 'Active' : 'Inactive' }}
                                            </span></li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Commission Dates -->
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header bg-primary">
                                    <h5 class="mb-0 text-white">Commission Dates</h5>
                                </div>
                                <div class="card-body">
                                    <ul class="list-unstyled">
                                        <li class="mt-1"><strong>Created At:</strong> {{ $commission->created_at->format('Y-m-d H:i') }}</li>
                                        <li class="mt-1"><strong>Updated At:</strong> {{ $commission->updated_at->format('Y-m-d H:i') }}</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-8"></div>
                        <!-- Quick Actions -->
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header bg-primary">
                                    <h5 class="mb-0 text-white">Quick Actions</h5>
                                </div>
                                <div class="card-body">
                                    <div class="d-flex flex-column">
                                        <a href="{{ route('admin.commissions.edit', $commission) }}" class="btn btn-warning mb-2">
                                            <i class="fa fa-edit"></i> Edit Commission
                                        </a>
                                        <button type="button" class="btn btn-danger delete-btn" data-url="{{ route('admin.commissions.destroy', $commission) }}">
                                            <i class="fa fa-trash"></i> Delete Commission
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div><!-- row end -->

                </div>
            </div>
        </div>
    </div>
</div>
@endsection