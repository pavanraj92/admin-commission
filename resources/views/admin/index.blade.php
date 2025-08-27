@extends('admin::admin.layouts.master')

@section('title', 'Commissions Management')
@section('page-title', 'Commission Manager')

@section('breadcrumb')
<li class="breadcrumb-item active" aria-current="page">Commission Manager</li>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Filter Section -->
    <div class="row">
        <div class="col-12">
            <div class="card card-body">
                <h4 class="card-title">Filter</h4>
                <form action="{{ route('admin.commissions.index') }}" method="GET" id="filterForm">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="keyword">Name</label>
                                <input type="text" name="keyword" id="keyword" class="form-control"
                                    value="{{ app('request')->query('keyword') }}" placeholder="Enter commission name">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="type">Type</label>
                                <select name="type" id="type" class="form-control select2">
                                    <option value="">All</option>
                                    <option value="percentage"
                                        {{ app('request')->query('type') == 'percentage' ? 'selected' : '' }}>Percentage</option>
                                    <option value="fixed"
                                        {{ app('request')->query('type') == 'fixed' ? 'selected' : '' }}>Fixed</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-auto mt-1 text-right">
                            <div class="form-group">
                                <button type="submit" form="filterForm" class="btn btn-primary mt-4">Filter</button>
                                <a href="{{ route('admin.commissions.index') }}" class="btn btn-secondary mt-4">Reset</a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Commissions Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    @admincan('commissions_manager_create')
                    <div class="text-right">
                        <a href="{{ route('admin.commissions.create') }}" class="btn btn-primary mb-3">
                            Create New Commission
                        </a>
                    </div>
                    @endadmincan

                    <div class="table-responsive">
                        <table class="table">
                            <thead class="thead-light">
                                <tr>
                                    <th scope="col">S. No.</th>
                                    <th scope="col">@sortablelink('type', 'Type', '', ['class' => 'text-dark'])</th>
                                    <th scope="col">Category</th>
                                    <th scope="col">@sortablelink('commission_type', 'Commission Type', '', ['class' => 'text-dark'])</th>
                                    <th scope="col">@sortablelink('commission_value', 'Commission Value', '', ['class' => 'text-dark'])</th>
                                    <th scope="col">@sortablelink('status', 'Status', '', ['class' => 'text-dark'])</th>
                                    <th scope="col">@sortablelink('created_at', 'Created At', '', ['class' => 'text-dark'])</th>
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if (isset($commissions) && $commissions->count() > 0)
                                @php
                                $i = ($commissions->currentPage() - 1) * $commissions->perPage() + 1;
                                @endphp
                                @foreach ($commissions as $commission)
                                <tr>
                                    <th scope="row">{{ $i }}</th>
                                    <td>{{ ucfirst($commission->type) }}</td>
                                    <td>
                                        @if($commission->categories && $commission->categories->count() > 0)
                                        {{ $commission->categories->pluck('title')->join(', ') }}
                                        @else
                                        —
                                        @endif
                                    </td>
                                    <td>{{ ucfirst($commission->commission_type) }}</td>
                                    <td>
                                        @if ($commission->commission_type == 'percentage')
                                        {{ $commission->commission_value }}%
                                        @else
                                        ${{ number_format($commission->commission_value, 2) }}
                                        @endif
                                    </td>
                                    <td>
                                        @if ($commission->status == 1)
                                        <a href="javascript:void(0)" data-toggle="tooltip" data-placement="top"
                                            title="Click to change status to inactive"
                                            data-url="{{ route('admin.commissions.updateStatus') }}"
                                            data-method="POST" data-status="0"
                                            data-id="{{ $commission->id }}"
                                            class="btn btn-success btn-sm update-status">Active</a>
                                        @else
                                        <a href="javascript:void(0)" data-toggle="tooltip" data-placement="top"
                                            title="Click to change status to active"
                                            data-url="{{ route('admin.commissions.updateStatus') }}"
                                            data-method="POST" data-status="1"
                                            data-id="{{ $commission->id }}"
                                            class="btn btn-warning btn-sm update-status">Inactive</a>
                                        @endif
                                    </td>
                                    <td>{{ $commission->created_at ? $commission->created_at->format(config('GET.admin_date_time_format') ?? 'M d, Y') : '—' }}</td>
                                    <td>
                                        @admincan('commissions_manager_view')
                                        <a href="{{ route('admin.commissions.show', $commission) }}"
                                            data-toggle="tooltip" data-placement="top"
                                            title="View this record" class="btn btn-warning btn-sm"><i
                                                class="mdi mdi-eye"></i></a>
                                        @endadmincan
                                        @admincan('commissions_manager_edit')
                                        <a href="{{ route('admin.commissions.edit', $commission) }}"
                                            data-toggle="tooltip" data-placement="top"
                                            title="Edit this record" class="btn btn-success btn-sm"><i
                                                class="mdi mdi-pencil"></i></a>
                                        @endadmincan
                                        @admincan('commissions_manager_delete')
                                        <a href="javascript:void(0)"
                                            data-toggle="tooltip" data-placement="top"
                                            title="Delete this record"
                                            data-url="{{ route('admin.commissions.destroy', $commission) }}"
                                            data-text="Are you sure you want to delete this record?"
                                            data-method="DELETE"
                                            class="btn btn-danger btn-sm delete-record"><i
                                                class="mdi mdi-delete"></i></a>
                                        @endadmincan
                                    </td>
                                </tr>
                                @php $i++; @endphp
                                @endforeach
                                @else
                                <tr>
                                    <td colspan="8" class="text-center">No records found.</td>
                                </tr>
                                @endif
                            </tbody>
                        </table>

                        <!-- Pagination -->
                        @if ($commissions->count() > 0)
                        {{ $commissions->links('admin::pagination.custom-admin-pagination') }}
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
@endpush