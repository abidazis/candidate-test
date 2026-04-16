<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Supplier;

class SupplierImportTest extends TestCase
{
    // RefreshDatabase sangat penting! Ini memastikan database selalu bersih 
    // setiap kali test dijalankan, sehingga tidak ada data bentrok.
    use RefreshDatabase; 

    public function test_it_can_import_layups_and_layers_successfully()
    {
        // 1. Arrange (Persiapan Data)
        // Kita buat 1 data supplier bohongan khusus untuk test ini
        $supplier = Supplier::create(['name' => 'PT Konstruksi Uji Coba']);

        $payload = [
            "layups" => [
                [
                    "name" => "Premium Wall Layup",
                    "layers" => [
                        [
                            "layer_order" => 1,
                            "thickness" => 40.5,
                            "width" => 150.0,
                            "angle" => 0
                        ]
                    ]
                ]
            ]
        ];

        // 2. Act (Eksekusi API)
        // Menembak endpoint import dengan method POST dan payload JSON di atas
        $response = $this->postJson("/api/suppliers/{$supplier->id}/import", $payload);

        // 3. Assert (Verifikasi Hasil)
        // Memastikan respons dari server adalah 200 OK
        $response->assertStatus(200)
                 ->assertJsonStructure(['message', 'data']);

        // Memastikan data Layup benar-benar tersimpan ke database
        $this->assertDatabaseHas('layups', [
            'supplier_id' => $supplier->id,
            'name' => 'Premium Wall Layup',
        ]);

        // Memastikan data Layer benar-benar tersimpan ke database
        $this->assertDatabaseHas('layers', [
            'layer_order' => 1,
            'thickness' => 40.5,
        ]);
    }
}