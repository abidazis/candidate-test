<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CLT Toolbox - Supplier Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
</head>
<body class="bg-gray-50 text-gray-800 font-sans antialiased relative">
    <div id="app" class="max-w-5xl mx-auto p-8">
        
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Supplier Management</h1>
                <p class="text-gray-500 mt-1">CLT Toolbox - Feature Test Assignment (With UI Conflict Resolution)</p>
            </div>
            <button @click="createSupplier" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg shadow font-medium transition-colors">
                + Add New Supplier
            </button>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100 text-gray-600 text-sm">
                        <th class="p-4 font-semibold">ID</th>
                        <th class="p-4 font-semibold">Supplier Name</th>
                        <th class="p-4 font-semibold">Total Layups</th>
                        <th class="p-4 font-semibold text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-if="suppliers.length === 0">
                        <td colspan="4" class="p-8 text-center text-gray-500">No suppliers found. Click add to create one.</td>
                    </tr>
                    <tr v-for="supplier in suppliers" :key="supplier.id" class="border-b border-gray-50 hover:bg-gray-50 transition-colors">
                        <td class="p-4 text-gray-500">#@{{ supplier.id }}</td>
                        <td class="p-4 font-medium text-gray-900">@{{ supplier.name }}</td>
                        <td class="p-4 text-gray-600">@{{ supplier.layups ? supplier.layups.length : 0 }} layups</td>
                        <td class="p-4 text-center space-x-2">
                            <button @click="exportData(supplier.id)" class="text-sm bg-green-100 hover:bg-green-200 text-green-700 px-3 py-1.5 rounded-md font-medium transition-colors">
                                Export JSON
                            </button>
                            
                            <label :for="'file-upload-' + supplier.id" class="text-sm bg-purple-100 hover:bg-purple-200 text-purple-700 px-3 py-1.5 rounded-md font-medium transition-colors cursor-pointer inline-block">
                                Import JSON
                            </label>
                            <input :id="'file-upload-' + supplier.id" type="file" accept=".json" class="hidden" @change="importData($event, supplier.id)">
                            
                            <button @click="deleteSupplier(supplier.id)" class="text-sm bg-red-100 hover:bg-red-200 text-red-700 px-3 py-1.5 rounded-md font-medium transition-colors">
                                Delete
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div v-if="showConflictModal" class="fixed inset-0 bg-black bg-opacity-60 flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-xl shadow-2xl max-w-3xl w-full p-6">
                <div class="flex justify-between items-center border-b pb-4 mb-4">
                    <h2 class="text-2xl font-bold text-red-600 flex items-center">
                        ⚠️ Data Conflict Detected!
                    </h2>
                    <span class="bg-gray-200 text-gray-700 px-3 py-1 rounded-full text-sm font-semibold">
                        Conflict @{{ currentConflictIndex + 1 }} of @{{ conflicts.length }}
                    </span>
                </div>
                
                <p class="text-gray-600 mb-6">
                    Layup: <strong>@{{ currentConflict().layupName }}</strong> | Layer Order: <strong>@{{ currentConflict().layerOrder }}</strong><br>
                    Please choose which data version you want to keep:
                </p>

                <div class="grid grid-cols-2 gap-6 mb-6">
                    <div class="border-2 border-gray-200 rounded-lg p-5 bg-gray-50 relative">
                        <span class="absolute -top-3 left-4 bg-gray-200 text-gray-700 px-2 text-xs font-bold rounded">EXISTING (CURRENT)</span>
                        <pre class="text-sm text-gray-800 mt-2 overflow-auto whitespace-pre-wrap">@{{ formatJson(currentConflict().existing) }}</pre>
                        <button @click="resolveConflict('existing')" class="mt-5 w-full bg-gray-700 hover:bg-gray-800 text-white px-4 py-2 rounded shadow transition">
                            ✅ Keep Existing Data
                        </button>
                    </div>

                    <div class="border-2 border-blue-300 rounded-lg p-5 bg-blue-50 relative">
                        <span class="absolute -top-3 left-4 bg-blue-500 text-white px-2 text-xs font-bold rounded">INCOMING (IMPORTED)</span>
                        <pre class="text-sm text-blue-900 mt-2 overflow-auto whitespace-pre-wrap">@{{ formatJson(currentConflict().incoming) }}</pre>
                        <button @click="resolveConflict('incoming')" class="mt-5 w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded shadow transition">
                            ✅ Accept Incoming Data
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <script>
        const { createApp, ref, onMounted } = Vue;

        createApp({
            setup() {
                const suppliers = ref([]);
                
                // State for Conflict Resolution
                const showConflictModal = ref(false);
                const conflicts = ref([]);
                const currentConflictIndex = ref(0);
                const pendingPayload = ref(null);
                const activeSupplierId = ref(null);

                const fetchSuppliers = async () => {
                    try {
                        const response = await axios.get('/api/suppliers');
                        suppliers.value = response.data.data;
                    } catch (error) {
                        console.error(error);
                    }
                };

                const createSupplier = async () => {
                    const name = prompt('Enter new supplier name:');
                    if (name) {
                        await axios.post('/api/suppliers', { name });
                        fetchSuppliers();
                    }
                };

                const deleteSupplier = async (id) => {
                    if (confirm('Delete this supplier?')) {
                        await axios.delete(`/api/suppliers/${id}`);
                        fetchSuppliers();
                    }
                };

                const exportData = (id) => {
                    window.open(`/api/suppliers/${id}/export`, '_blank');
                };

                // Helper formatting JSON for modal
                const formatJson = (obj) => {
                    return JSON.stringify(obj, null, 2);
                };

                const currentConflict = () => {
                    return conflicts.value[currentConflictIndex.value];
                };

                // The Magic Import with Conflict Detection
                const importData = async (event, id) => {
                    const file = event.target.files[0];
                    if (!file) return;

                    const reader = new FileReader();
                    reader.onload = async (e) => {
                        try {
                            const incomingPayload = JSON.parse(e.target.result);
                            const supplierData = suppliers.value.find(s => s.id === id);
                            
                            conflicts.value = [];
                            
                            // Deteksi Konflik Logik
                            if (supplierData.layups) {
                                incomingPayload.layups.forEach((inLayup, layupIndex) => {
                                    const exLayup = supplierData.layups.find(l => l.name === inLayup.name);
                                    if (exLayup && exLayup.layers) {
                                        inLayup.layers.forEach((inLayer, layerIndex) => {
                                            const exLayer = exLayup.layers.find(l => l.layer_order === inLayer.layer_order);
                                            // Jika urutan sama tapi ada value yang beda = KONFLIK!
                                            if (exLayer && (
                                                parseFloat(exLayer.thickness) !== parseFloat(inLayer.thickness) ||
                                                parseFloat(exLayer.width) !== parseFloat(inLayer.width) ||
                                                parseFloat(exLayer.angle) !== parseFloat(inLayer.angle)
                                            )) {
                                                conflicts.value.push({
                                                    layupName: inLayup.name,
                                                    layerOrder: inLayer.layer_order,
                                                    layupIndex: layupIndex,
                                                    layerIndex: layerIndex,
                                                    existing: { thickness: exLayer.thickness, width: exLayer.width, angle: exLayer.angle },
                                                    incoming: { thickness: inLayer.thickness, width: inLayer.width, angle: inLayer.angle }
                                                });
                                            }
                                        });
                                    }
                                });
                            }

                            if (conflicts.value.length > 0) {
                                // Tahan pengiriman ke API, buka Modal UI
                                pendingPayload.value = incomingPayload;
                                activeSupplierId.value = id;
                                currentConflictIndex.value = 0;
                                showConflictModal.value = true;
                            } else {
                                // Aman, langsung gas tembak API
                                executeApiImport(id, incomingPayload);
                            }

                        } catch (error) {
                            alert('Invalid JSON format');
                        }
                    };
                    reader.readAsText(file);
                    event.target.value = '';
                };

                // Fungsi saat tombol di modal di-klik
                const resolveConflict = (choice) => {
                    const conflict = currentConflict();
                    
                    if (choice === 'existing') {
                        // Jika pilih data lama, kita ganti data di payload (yang mau dikirim) pakai data existing
                        const targetLayer = pendingPayload.value.layups[conflict.layupIndex].layers[conflict.layerIndex];
                        targetLayer.thickness = conflict.existing.thickness;
                        targetLayer.width = conflict.existing.width;
                        targetLayer.angle = conflict.existing.angle;
                    }

                    // Lanjut ke konflik berikutnya atau selesai
                    if (currentConflictIndex.value < conflicts.value.length - 1) {
                        currentConflictIndex.value++;
                    } else {
                        showConflictModal.value = false;
                        executeApiImport(activeSupplierId.value, pendingPayload.value);
                    }
                };

                const executeApiImport = async (id, payload) => {
                    try {
                        await axios.post(`/api/suppliers/${id}/import`, payload);
                        alert('✅ Import & Conflict Resolution Successfully Applied!');
                        fetchSuppliers();
                    } catch (error) {
                        alert('Server error during import.');
                    }
                };

                onMounted(() => {
                    fetchSuppliers();
                });

                return {
                    suppliers, createSupplier, deleteSupplier, exportData, importData,
                    showConflictModal, conflicts, currentConflictIndex, currentConflict, resolveConflict, formatJson
                }
            }
        }).mount('#app');
    </script>
</body>
</html>