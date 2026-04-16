<?php

namespace App\Services;

use App\Models\Supplier;
use App\Services\Contracts\SupplierServiceInterface;

class SupplierService implements SupplierServiceInterface
{
    public function getAllSuppliers()
    {
        return Supplier::with('layups.layers')->get();
    }

    public function createSupplier(array $data)
    {
        return Supplier::create($data);
    }

    public function getSupplierById($id)
    {
        return Supplier::with('layups.layers')->findOrFail($id);
    }

    public function updateSupplier($id, array $data)
    {
        $supplier = Supplier::findOrFail($id);
        $supplier->update($data);
        return $supplier;
    }

    public function deleteSupplier($id)
    {
        $supplier = Supplier::findOrFail($id);
        $supplier->delete();
        return true;
    }

    public function exportJson($id)
    {
        return Supplier::with('layups.layers')->findOrFail($id);
    }

    public function importJson($id, array $data)
    {
        $supplier = Supplier::findOrFail($id);
        foreach ($data['layups'] as $layupData) {
            
            $layup = $supplier->layups()->updateOrCreate(
                ['name' => $layupData['name']] 
            );

            // Looping data layer dari JSON layup tersebut
            if (isset($layupData['layers'])) {
                foreach ($layupData['layers'] as $layerData) {
                    
                    $layup->layers()->updateOrCreate(
                        ['layer_order' => $layerData['layer_order']], 
                        [
                            'thickness' => $layerData['thickness'],
                            'width' => $layerData['width'],
                            'angle' => $layerData['angle'],
                        ]
                    );
                }
            }
        }
        return $supplier->load('layups.layers');
    }
}