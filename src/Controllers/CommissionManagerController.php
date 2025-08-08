<?php

namespace admin\commissions\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use admin\commissions\Models\Commission;
use admin\commissions\Requests\CommissionCreateRequest;
use admin\commissions\Requests\CommissionUpdateRequest;
use admin\categories\Models\Category;

class CommissionManagerController extends Controller
{
    public function __construct()
    {       
        $this->middleware('admincan_permission:commissions_manager_list')->only(['index']);
        $this->middleware('admincan_permission:commissions_manager_create')->only(['create', 'store']);
        $this->middleware('admincan_permission:commissions_manager_edit')->only(['edit', 'update']);
        $this->middleware('admincan_permission:commissions_manager_view')->only(['show']);
        $this->middleware('admincan_permission:commissions_manager_delete')->only(['destroy']);
    }

    public function index(Request $request)
    {
        $commissions = Commission::query()->with(['category'])
            ->sortable()->latest()->paginate(Commission::getPerPageLimit());
    
        return view('commission::admin.index', compact('commissions'));
    }

    public function create()
    {
        //fetch categories for select dropdown
        $categories = Category::select('id', 'title')->get();
        return view('commission::admin.createOrEdit', compact('categories'));
    }

    public function store(CommissionCreateRequest $request)
    {
        $data = $request->validated();
        $commission = Commission::create($data);
        return redirect()->route('admin.commissions.index')->with('success', 'Commission created successfully.');
    }

    public function show(Commission $commission)
    {
        $commission->load(['category']);
        return view('commission::admin.show', compact('commission'));
    }

    public function edit(Commission $commission)
    {
        $commission->load(['category']);
        //fetch categories for select dropdown
        $categories = Category::select('id', 'title')->get();
        return view('commission::admin.createOrEdit', compact('commission', 'categories'));
    }

    public function update(CommissionUpdateRequest $request, Commission $commission)
    {
        $commission->update($request->validated());
        return redirect()->route('admin.commissions.index')->with('success', 'Commission updated successfully.');
    }

    public function destroy(Commission $commission)
    {
        $commission->delete();
        return redirect()->route('admin.commissions.index')->with('success', 'Commission deleted successfully.');
    }

    public function updateStatus(Request $request)
    {
        try {
            $commission = Commission::findOrFail($request->id);
            $commission->status = $request->status;
            $commission->save();

            // create status html dynamically
            $dataStatus = $commission->status == '1' ? '0' : '1';
            $label = $commission->status == '1' ? 'Active' : 'InActive';
            $btnClass = $commission->status == '1' ? 'btn-success' : 'btn-warning';
            $tooltip = $commission->status == '1' ? 'Click to change status to inactive' : 'Click to change status to active';

            $strHtml = '<a href="javascript:void(0)"'
                . ' data-toggle="tooltip"'
                . ' data-placement="top"'
                . ' title="' . $tooltip . '"'
                . ' data-url="' . route('admin.commissions.updateStatus') . '"'
                . ' data-method="POST"'
                . ' data-status="' . $dataStatus . '"'
                . ' data-id="' . $commission->id . '"'
                . ' class="btn ' . $btnClass . ' btn-sm update-status">' . $label . '</a>';

            return response()->json(['success' => true, 'message' => 'Status updated to ' . $label, 'strHtml' => $strHtml]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to delete record.', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Fetch options for select fields based on the type.
     */
    public function fetchOptions(Request $request)
    {
        $type = $request->get('type');
        $data = [];
        if ($type === 'category') {
            $data['categories'] = Category::select('id', 'title')->get();
        }       
        return response()->json($data);        
    }
}
