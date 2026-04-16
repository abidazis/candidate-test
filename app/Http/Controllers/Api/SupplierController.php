<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSupplierRequest;
use App\Services\Contracts\SupplierServiceInterface;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    protected $supplierService;

    public function __construct(SupplierServiceInterface $supplierService)
    {
        $this->supplierService = $supplierService;
    }

    public function index()
    {
        $suppliers = $this->supplierService->getAllSuppliers();
        return response()->json(['data' => $suppliers]);
    }

    public function store(StoreSupplierRequest $request)
    {
        $supplier = $this->supplierService->createSupplier($request->validated());
        return response()->json(['message' => 'Supplier created successfully', 'data' => $supplier], 201);
    }

    public function show($id)
    {
        $supplier = $this->supplierService->getSupplierById($id);
        return response()->json(['data' => $supplier]);
    }

    public function update(StoreSupplierRequest $request, $id)
    {
        $supplier = $this->supplierService->updateSupplier($id, $request->validated());
        return response()->json(['message' => 'Supplier updated successfully', 'data' => $supplier]);
    }

    public function destroy($id)
    {
        $this->supplierService->deleteSupplier($id);
        return response()->json(['message' => 'Supplier deleted successfully']);
    }

    public function export($id)
    {
        $data = $this->supplierService->exportJson($id);
        return response()->json($data)
            ->header('Content-Disposition', 'attachment; filename="supplier_'.$id.'_export.json"');
    }

    public function import(Request $request, $id)
    {
        $request->validate([
            'layups' => 'required|array',
        ]);

        $updatedSupplier = $this->supplierService->importJson($id, $request->all());

        return response()->json([
            'message' => 'Import successful with conflict resolution applied.',
            'data' => $updatedSupplier
        ]);
    }
}