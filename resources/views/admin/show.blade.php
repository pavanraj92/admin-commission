@extends('admin::admin.layouts.master')

@section('title', 'Commission Details')
@section('page-title', 'Commission Details')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.commissions.index') }}">Commission Manager</a></li>
    <li class="breadcrumb-item active" aria-current="page">Commission Details</li>
@endsection

@section('content')
    <!-- Container fluid  -->
    <div class="container-fluid">
        <!-- Start Faq Content -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="table-responsive">
                        <div class="card-body">
                            <table class="table table-striped">
                                <tbody>
                                    <tr>
                                        <th>Type</th>
                                        <td>{{ ucfirst($commission->type) }}</td>
                                    </tr>
                                    @if ($commission->type == 'category')
                                        <tr>
                                            <th>Category</th>
                                            <td>{{ $commission?->category?->title ?? '-' }}</td>
                                        </tr>
                                    @endif
                                    <tr>
                                        <th>Commission Type</th>
                                        <td>{{ ucfirst($commission->commission_type) }}</td>
                                    </tr>
                                    <tr>
                                        <th>Commission Value</th>
                                        <td>
                                            @if ($commission->commission_type == 'percentage')
                                                {{ $commission->commission_value }}%
                                            @else
                                                ${{ number_format($commission->commission_value, 2) }}
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Status</th>
                                        <td>
                                            <span class="badge badge-{{ $commission->status ? 'success' : 'danger' }}">
                                                {{ $commission->status ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Created At</th>
                                        <td>{{ $commission->created_at->format('Y-m-d H:i') }}</td>
                                    </tr>
                                    <tr>
                                        <th>Updated At</th>
                                        <td>{{ $commission->updated_at->format('Y-m-d H:i') }}</td>
                                    </tr>
                                </tbody>
                            </table>

                            <a href="{{ route('admin.commissions.index') }}" class="btn btn-secondary">Back</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- End faq Content -->
    </div>
    <!-- End Container fluid  -->
@endsection
