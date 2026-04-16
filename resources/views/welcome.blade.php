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
<body class="bg-gray-50 text-gray-800 font-sans antialiased">
    <div id="app" class="max-w-5xl mx-auto p-8">
        
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Supplier Management</h1>
                <p class="text-gray-500 mt-1">CLT Toolbox - Feature Test Assignment</p>
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

    </div>

    <script>
        const { createApp, ref, onMounted } = Vue;

        createApp({
            setup() {
                const suppliers = ref([]);

                // Fetch data dari Backend API
                const fetchSuppliers = async () => {
                    try {
                        const response = await axios.get('/api/suppliers');
                        suppliers.value = response.data.data;
                    } catch (error) {
                        console.error('Error fetching suppliers:', error);
                    }
                };

                // Bikin Supplier Baru (Pake prompt sederhana biar cepat)
                const createSupplier = async () => {
                    const name = prompt('Enter new supplier name:');
                    if (name) {
                        try {
                            await axios.post('/api/suppliers', { name });
                            fetchSuppliers(); // Refresh table
                        } catch (error) {
                            alert('Failed to create supplier.');
                        }
                    }
                };

                // Hapus Supplier
                const deleteSupplier = async (id) => {
                    if (confirm('Are you sure you want to delete this supplier?')) {
                        try {
                            await axios.delete(`/api/suppliers/${id}`);
                            fetchSuppliers();
                        } catch (error) {
                            alert('Failed to delete supplier.');
                        }
                    }
                };

                // Fitur Export (Buka link API Export di tab baru)
                const exportData = (id) => {
                    window.open(`/api/suppliers/${id}/export`, '_blank');
                };

                // Fitur Import (Baca file JSON, kirim ke API)
                const importData = async (event, id) => {
                    const file = event.target.files[0];
                    if (!file) return;

                    const reader = new FileReader();
                    reader.onload = async (e) => {
                        try {
                            // Parse text menjadi object JSON
                            const payload = JSON.parse(e.target.result);
                            
                            // Tembak API Import kita
                            await axios.post(`/api/suppliers/${id}/import`, payload);
                            
                            alert('Import Success! Conflict resolution (Overwrite Existing) applied.');
                            fetchSuppliers(); // Refresh data
                        } catch (error) {
                            console.error(error);
                            alert('Error: Invalid JSON format or Server Error.');
                        }
                    };
                    reader.readAsText(file);
                    
                    // Reset input file biar bisa import file yang sama lagi kalau mau tes
                    event.target.value = '';
                };

                // Jalan otomatis pas halaman dimuat
                onMounted(() => {
                    fetchSuppliers();
                });

                return {
                    suppliers,
                    createSupplier,
                    deleteSupplier,
                    exportData,
                    importData
                }
            }
        }).mount('#app');
    </script>
</body>
</html>