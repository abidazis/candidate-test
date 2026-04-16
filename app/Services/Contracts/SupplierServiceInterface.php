<?php

namespace App\Services\Contracts;

interface SupplierServiceInterface
{
    public function getAllSuppliers();
    public function createSupplier(array $data);
    public function getSupplierById($id);
    public function updateSupplier($id, array $data);
    public function deleteSupplier($id);
    public function exportJson($id);
    public function importJson($id, array $data);
}