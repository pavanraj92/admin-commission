<?php

namespace admin\commissions\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use admin\commissions\Models\Commission;
use admin\commissions\Requests\CommissionCreateRequest;
use admin\commissions\Requests\CommissionUpdateRequest;
use admin\categories\Models\Category;
use Illuminate\Support\Facades\DB;

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
        $commissions = Commission::with('categories')
            ->sortable()
            ->latest()
            ->paginate(Commission::getPerPageLimit());

        return view('commission::admin.index', compact('commissions'));
    }

    public function create()
    {
        $globalPercentage = Commission::where('type', 'global')
            ->where('commission_type', 'percentage')
            ->exists();

        $globalFixed = Commission::where('type', 'global')
            ->where('commission_type', 'fixed')
            ->exists();

        $disableGlobal = $globalPercentage && $globalFixed;

        // Get all categories
        $categories = Category::select('id', 'title')->get();

        // Get category IDs already used in other commissions of type 'category'
        $usedCategoryIds = Commission::where('type', 'category')
            ->with('categories')
            ->get()
            ->pluck('categories.*.id') // pluck all related category IDs
            ->flatten()
            ->map(fn($id) => (int) $id)
            ->toArray();

        // Filter out categories already used
        $availableCategories = $categories->filter(function ($category) use ($usedCategoryIds) {
            return !in_array($category->id, $usedCategoryIds);
        });

        return view('commission::admin.createOrEdit', [
            'categories' => $availableCategories->values(),
            'disableGlobal' => $disableGlobal
        ]);
    }

    public function store(CommissionCreateRequest $request)
    {
        $data = $request->validated();
        // Create commission without category_ids (many-to-many handled separately)
        $commission = Commission::create([
            'type' => $data['type'],
            'commission_type' => $data['commission_type'],
            'commission_value' => $data['commission_value'],
            'status' => $data['status'],
        ]);
        // Attach selected categories if type is 'category'
        if ($request->type === 'category' && $request->has('category_ids')) {
            $commission->categories()->sync($request->category_ids);
        }
        return redirect()->route('admin.commissions.index')->with('success', 'Commission created successfully.');
    }

    public function show(Commission $commission)
    {
        $commission->load('categories');
        return view('commission::admin.show', compact('commission'));
    }

    public function edit(Commission $commission)
    {
        $globalPercentage = Commission::where('type', 'global')
            ->where('commission_type', 'percentage')
            ->exists();

        $globalFixed = Commission::where('type', 'global')
            ->where('commission_type', 'fixed')
            ->exists();

        $disableGlobal = $globalPercentage && $globalFixed;

        $commission->load('categories');

        // Fetch categories for select dropdown
        $categories = Category::select('id', 'title')->get();

        // Get category IDs already used in other commissions of type 'category'
        $usedCategoryIds = DB::table('commission_category')
            ->whereIn('commission_id', function ($query) use ($commission) {
                $query->select('id')
                    ->from('commissions')
                    ->where('type', 'category')
                    ->where('id', '!=', $commission->id);
            })
            ->pluck('category_id')
            ->map(fn($id) => (int) $id)
            ->toArray();

        // Filter available categories: allow ones not used or already assigned to this commission
        $availableCategories = $categories->filter(function ($category) use ($usedCategoryIds, $commission) {
            return !in_array($category->id, $usedCategoryIds) || $commission->categories->contains('id', $category->id);
        });

        return view('commission::admin.createOrEdit', [
            'commission' => $commission,
            'categories' => $availableCategories->values(),
            'disableGlobal' => $disableGlobal
        ]);
    }

    public function update(CommissionUpdateRequest $request, Commission $commission)
    {
        // Update commission basic data
        $commission->update([
            'type' => $request->type,
            'commission_type' => $request->commission_type,
            'commission_value' => $request->commission_value,
            'status' => $request->status,
        ]);

        // Sync categories if type is 'category', otherwise detach all
        if ($request->type === 'category' && $request->has('category_ids')) {
            $commission->categories()->sync($request->category_ids);
        } else {
            $commission->categories()->detach();
        }
        return redirect()->route('admin.commissions.index')->with('success', 'Commission updated successfully.');
    }

    public function destroy(Commission $commission)
    {
        // Detach all related categories
        $commission->categories()->detach();

        // Delete the commission
        $commission->delete();

        return redirect()->route('admin.commissions.index')
            ->with('success', 'Commission deleted successfully.');
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
        $commissionId = $request->get('commission_id'); // optional: current commission ID when editing
        $data = [];

        if ($type === 'category') {
            // Get all categories
            $categories = Category::select('id', 'title')->get();

            // Get category IDs already linked via pivot, excluding current commission if editing
            $usedCategoryIdsQuery = DB::table('commission_category')
                ->join('commissions', 'commission_category.commission_id', '=', 'commissions.id')
                ->where('commissions.type', 'category');

            if ($commissionId) {
                $usedCategoryIdsQuery->where('commission_id', '!=', $commissionId);
            }

            $usedCategoryIds = $usedCategoryIdsQuery->pluck('commission_category.category_id')
                ->map(fn($id) => (int) $id)
                ->toArray();

            // Filter available categories
            $availableCategories = $categories->filter(function ($category) use ($usedCategoryIds) {
                return !in_array($category->id, $usedCategoryIds);
            });

            $data['availableCategories'] = $availableCategories->values();
        }

        return response()->json($data);
    }
}
