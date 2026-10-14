<!-- MODAL DETAIL HISTORI ITEM (OPSI A AGREGASI) -->
        <div x-show="detailModalOpen" x-transition class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm" style="display: none;">
            <div class="bg-white rounded-3xl w-full max-w-2xl p-6 shadow-2xl relative border border-white/40 overflow-y-auto max-h-[90vh]" @click.away="detailModalOpen = false">
                <div class="flex items-center justify-between mb-4 pb-3 border-b border-gray-100">
                    <div>
                        <h3 class="font-serif font-bold text-xl text-gray-800" x-text="detailData.nama_barang"></h3>
                        <p class="text-xs text-gray-400">Agregasi Pembelian & History Transaksi</p>
                    </div>
                    <button @click="detailModalOpen = false" class="text-gray-400 hover:text-gray-600"><i class="fa-solid fa-xmark text-xl"></i></button>
                </div>

                <template x-if="detailLoading">
                    <div class="py-12 text-center text-gray-400">
                        <i class="fa-solid fa-spinner fa-spin text-2xl text-[#359FA0] mb-2"></i>
                        <p class="text-xs">Memuat histori item...</p>
                    </div>
                </template>

                <template x-if="!detailLoading">
                    <div class="space-y-4">
                        <!-- Summary Cards Item -->
                        <div class="grid grid-cols-3 gap-3 bg-gray-50 p-4 rounded-2xl text-center">
                            <div>
                                <p class="text-[10px] text-gray-400 font-semibold">Total Pembelian</p>
                                <p class="text-base font-bold text-gray-800" x-text="detailData.total_dibeli + ' Kali'"></p>
                            </div>
                            <div>
                                <p class="text-[10px] text-gray-400 font-semibold">Total Pengeluaran</p>
                                <p class="text-base font-bold text-[#359FA0]" x-text="'Rp' + Number(detailData.total_pengeluaran).toLocaleString('id-ID')"></p>
                            </div>
                            <div>
                                <p class="text-[10px] text-gray-400 font-semibold">Rata-Rata Bulanan</p>
                                <p class="text-base font-bold text-[#FF8C52]" x-text="'Rp' + Number(Math.round(detailData.rata_rata_bulanan)).toLocaleString('id-ID')"></p>
                            </div>
                        </div>

                        <!-- Table History Item -->
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-xs text-gray-600">
                                <thead class="bg-gray-100 text-gray-700 font-semibold uppercase">
                                    <tr>
                                        <th class="py-2.5 px-3">Tgl Beli</th>
                                        <th class="py-2.5 px-3">Harga</th>
                                        <th class="py-2.5 px-3">Tempat Beli</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    <template x-for="item in detailData.history_list" :key="item.id">
                                        <tr>
                                            <td class="py-2.5 px-3 font-medium text-gray-800" x-text="item.tanggal_beli"></td>
                                            <td class="py-2.5 px-3 font-bold text-gray-900" x-text="'Rp' + Number(item.harga).toLocaleString('id-ID')"></td>
                                            <td class="py-2.5 px-3 text-gray-500" x-text="item.tempat_beli"></td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <!-- MODAL CREATE BARANG MANUAL -->
        <div x-show="createModalOpen" x-transition class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm" style="display: none;">
            <div class="bg-white rounded-3xl w-full max-w-md p-6 shadow-2xl relative border border-white/40" @click.away="createModalOpen = false">
                <div class="flex items-center justify-between mb-5">
                    <h3 class="font-serif font-bold text-xl text-gray-800">Tambah Barang Manual</h3>
                    <button @click="createModalOpen = false" class="text-gray-400 hover:text-gray-600"><i class="fa-solid fa-xmark text-xl"></i></button>
                </div>

                <form action="{{ route('barang.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Nama Barang</label>
                        <input type="text" name="nama_barang" placeholder="Contoh: Sabun Mandi, Laptop Asus" required class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#359FA0] outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Jenis Barang</label>
                        <select name="jenis_barang" required class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#359FA0] outline-none">
                            <option value="barang_mati">Barang Operasional</option>
                            <option value="barang_hidup">Barang Produktif</option>
                            <option value="kebutuhan_rutin">Kebutuhan Rutin</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Harga / Estimasi Nilai (Rp)</label>
                        <input type="number" name="harga" placeholder="0" min="0" required class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-lg font-bold text-gray-800 focus:ring-2 focus:ring-[#359FA0] outline-none">
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Tanggal Beli</label>
                            <input type="date" name="tanggal_beli" value="{{ date('Y-m-d') }}" required class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#359FA0] outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Tempat Beli (Opsional)</label>
                            <input type="text" name="tempat_beli" placeholder="Tokopedia, Minimarket..." class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#359FA0] outline-none">
                        </div>
                    </div>

                    <button type="submit" class="w-full py-3 bg-[#FF8C52] hover:bg-[#e07740] text-white font-semibold rounded-xl shadow-md transition-all flex items-center justify-center space-x-2">
                        <i class="fa-solid fa-floppy-disk"></i>
                        <span>Simpan Barang</span>
                    </button>
                </form>
            </div>
        </div>

        <!-- MODAL EDIT BARANG -->
        <div x-show="editModalOpen" x-transition class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm" style="display: none;">
            <div class="bg-white rounded-3xl w-full max-w-md p-6 shadow-2xl relative border border-white/40" @click.away="editModalOpen = false">
                <div class="flex items-center justify-between mb-5">
                    <h3 class="font-serif font-bold text-xl text-gray-800">Edit Data Barang</h3>
                    <button @click="editModalOpen = false" class="text-gray-400 hover:text-gray-600"><i class="fa-solid fa-xmark text-xl"></i></button>
                </div>

                <form :action="'/barang/' + editData.id" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Nama Barang</label>
                        <input type="text" name="nama_barang" x-model="editData.nama_barang" required class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#359FA0] outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Jenis Barang</label>
                        <select name="jenis_barang" x-model="editData.jenis_barang" required class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#359FA0] outline-none">
                            <option value="barang_mati">Barang Operasional</option>
                            <option value="barang_hidup">Barang Produktif</option>
                            <option value="kebutuhan_rutin">Kebutuhan Rutin</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Harga / Nilai (Rp)</label>
                        <input type="number" name="harga" x-model="editData.harga" min="0" required class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-lg font-bold text-gray-800 focus:ring-2 focus:ring-[#359FA0] outline-none">
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Tanggal Beli</label>
                            <input type="date" name="tanggal_beli" x-model="editData.tanggal_beli" required class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#359FA0] outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Tempat Beli</label>
                            <input type="text" name="tempat_beli" x-model="editData.tempat_beli" class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#359FA0] outline-none">
                        </div>
                    </div>

                    <button type="submit" class="w-full py-3 bg-[#359FA0] hover:bg-[#2c8384] text-white font-semibold rounded-xl shadow-md transition-all flex items-center justify-center space-x-2">
                        <i class="fa-solid fa-floppy-disk"></i>
                        <span>Perbarui Barang</span>
                    </button>
                </form>
            </div>
        </div>