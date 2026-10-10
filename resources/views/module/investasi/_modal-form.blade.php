<!-- MODAL DETAIL & HISTORI TRANSAKSI EMITEN/ASET -->
        <div x-show="detailModalOpen" x-transition class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm" style="display: none;">
            <div class="bg-white rounded-3xl w-full max-w-2xl p-6 shadow-2xl relative border border-white/40 overflow-y-auto max-h-[90vh]" @click.away="detailModalOpen = false">
                <div class="flex items-center justify-between mb-4 pb-3 border-b border-gray-100">
                    <div class="flex items-center space-x-2.5">
                        <div class="w-9 h-9 bg-[#8AD6D1]/20 rounded-xl flex items-center justify-center text-[#359FA0]">
                            <i class="fa-solid fa-chart-pie text-lg"></i>
                        </div>
                        <div>
                            <h3 class="font-serif font-bold text-xl text-gray-800" x-text="'Histori Transaksi: ' + detailNamaAset"></h3>
                            <p class="text-xs text-gray-400" x-text="detailDetailAset"></p>
                        </div>
                    </div>
                    <button @click="detailModalOpen = false" class="text-gray-400 hover:text-gray-600"><i class="fa-solid fa-xmark text-xl"></i></button>
                </div>

                <!-- Loading State -->
                <template x-if="detailLoading">
                    <div class="py-12 text-center text-gray-400">
                        <i class="fa-solid fa-spinner fa-spin text-2xl text-[#359FA0] mb-2"></i>
                        <p class="text-xs">Memuat histori transaksi...</p>
                    </div>
                </template>

                <!-- Table Content -->
                <template x-if="!detailLoading">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs text-gray-600">
                            <thead class="bg-gray-50 text-gray-700 font-semibold uppercase tracking-wider border-b border-gray-100">
                                <tr>
                                    <th class="py-2.5 px-3">Tgl Beli</th>
                                    <th class="py-2.5 px-3">Tgl Jual</th>
                                    <th class="py-2.5 px-3">Harga Beli</th>
                                    <th class="py-2.5 px-3">Harga Jual</th>
                                    <th class="py-2.5 px-3">Durasi</th>
                                    <th class="py-2.5 px-3 text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <template x-for="item in detailHistoryList" :key="item.id">
                                    <tr class="hover:bg-gray-50/80">
                                        <td class="py-3 px-3 font-medium text-gray-800" x-text="item.formatted_tgl_beli"></td>
                                        <td class="py-3 px-3 font-medium text-gray-800" x-text="item.formatted_tgl_jual"></td>
                                        <td class="py-3 px-3 font-mono">
                                            <span x-text="item.harga_beli ? item.harga_beli : '-'"></span>
                                            <p class="text-[10px] text-gray-400" x-text="item.harga_beli_idr ? 'Rp' + Number(item.harga_beli_idr).toLocaleString('id-ID') : ''"></p>
                                        </td>
                                        <td class="py-3 px-3 font-mono">
                                            <span x-text="item.harga_jual ? item.harga_jual : '-'"></span>
                                            <p class="text-[10px] text-gray-400" x-text="item.harga_jual_idr ? 'Rp' + Number(item.harga_jual_idr).toLocaleString('id-ID') : ''"></p>
                                        </td>
                                        <td class="py-3 px-3 font-bold text-[#359FA0]" x-text="item.durasi_days + ' Hari'"></td>
                                        <td class="py-3 px-3 text-center">
                                            <span 
                                                :class="item.status === 'masih_dimiliki' ? 'bg-[#8AD6D1]/20 text-[#359FA0]' : 'bg-gray-100 text-gray-600'" 
                                                class="font-bold px-2 py-0.5 rounded-full text-[10px]"
                                                x-text="item.status === 'masih_dimiliki' ? 'Holding' : 'Sold'"
                                            ></span>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </template>
            </div>
        </div>

        <!-- MODAL LIQUIDATION / JUAL ASET -->
        <div x-show="jualModalOpen" x-transition class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm" style="display: none;">
            <div class="bg-white rounded-3xl w-full max-w-md p-6 shadow-2xl relative border border-white/40" @click.away="jualModalOpen = false">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="font-serif font-bold text-xl text-gray-800">Jual / Likuidasi Aset</h3>
                        <p class="text-xs text-[#359FA0] font-semibold mt-0.5" x-text="jualData.nama"></p>
                    </div>
                    <button @click="jualModalOpen = false" class="text-gray-400 hover:text-gray-600"><i class="fa-solid fa-xmark text-xl"></i></button>
                </div>

                <form :action="'/investasi/' + jualData.id + '/jual'" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Tanggal Jual</label>
                        <input type="date" name="tanggal_jual" x-model="jualData.tanggal_jual" required class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#359FA0] outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Harga Jual Unit (Opsional)</label>
                        <input type="number" step="0.0001" name="harga_jual" x-model="jualData.harga_jual" placeholder="Contoh: Harga per lembar saham" class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#359FA0] outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Total Realized IDR Diterima (Opsional)</label>
                        <input type="number" name="harga_jual_idr" x-model="jualData.harga_jual_idr" placeholder="Rupiah bersih yang diterima" class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-lg font-bold text-gray-800 focus:ring-2 focus:ring-[#359FA0] outline-none">
                    </div>

                    <button type="submit" class="w-full py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-xl shadow-md transition-all flex items-center justify-center space-x-2">
                        <i class="fa-solid fa-floppy-disk"></i>
                        <span>Konfirmasi Penjualan</span>
                    </button>
                </form>
            </div>
        </div>

        <!-- MODAL CREATE / INPUT INVESTASI LENGKAP -->
        <div x-show="createModalOpen" x-transition class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm" style="display: none;">
            <div class="bg-white rounded-3xl w-full max-w-lg p-6 shadow-2xl relative border border-white/40 overflow-y-auto max-h-[90vh]" @click.away="createModalOpen = false">
                <div class="flex items-center justify-between mb-5">
                    <h3 class="font-serif font-bold text-xl text-gray-800">Catat Investasi Baru</h3>
                    <button @click="createModalOpen = false" class="text-gray-400 hover:text-gray-600"><i class="fa-solid fa-xmark text-xl"></i></button>
                </div>

                <form action="{{ route('investasi.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Nama Investasi / Aset</label>
                        <input type="text" name="nama" placeholder="Contoh: Saham BBCA, USD/IDR, Emas Antam" required class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#359FA0] outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Detail Investasi / Aset (Opsional)</label>
                        <input type="text" name="detail" placeholder="Contoh: Bank Central Asia, USD/IDR, Emas Antam 1 gram" class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#359FA0] outline-none">
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Jenis Investasi</label>
                            <select name="jenis_investasi" required class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#359FA0] outline-none">
                                <option value="Saham">Saham</option>
                                <option value="USD">USD</option>
                                <option value="JPY">JPY</option>
                                <option value="Reksadana">Reksadana</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Platform (Opsional)</label>
                            <input type="text" name="platform" placeholder="Stockbit, Ajaib, Bibit..." class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#359FA0] outline-none">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Status Portofolio</label>
                        <select name="status" x-model="formStatus" required class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm font-semibold focus:ring-2 focus:ring-[#359FA0] outline-none">
                            <option value="masih_dimiliki">Masih Dimiliki (Holding)</option>
                            <option value="sudah_dijual">Sudah Dijual (Sold/Historis)</option>
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Tanggal Beli</label>
                            <input type="date" name="tanggal_beli" value="{{ date('Y-m-d') }}" required class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#359FA0] outline-none">
                        </div>
                        <div x-show="formStatus === 'sudah_dijual'">
                            <label class="block text-xs font-medium text-gray-600 mb-1">Tanggal Jual (Opsional)</label>
                            <input type="date" name="tanggal_jual" class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#359FA0] outline-none">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Harga Beli Unit (Opsional)</label>
                            <input type="number" step="0.0001" name="harga_beli" placeholder="Harga per lembar" class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#359FA0] outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Total Real Beli IDR (Opsional)</label>
                            <input type="number" name="harga_beli_idr" placeholder="Real Rupiah beli" class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#359FA0] outline-none">
                        </div>
                    </div>

                    <div x-show="formStatus === 'sudah_dijual'" class="grid grid-cols-2 gap-3 pt-2 border-t border-gray-100">
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Harga Jual Unit (Opsional)</label>
                            <input type="number" step="0.0001" name="harga_jual" placeholder="Harga per lembar jual" class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#359FA0] outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Total Real Jual IDR (Opsional)</label>
                            <input type="number" name="harga_jual_idr" placeholder="Real Rupiah jual" class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#359FA0] outline-none">
                        </div>
                    </div>

                    <button type="submit" class="w-full py-3 bg-[#FF8C52] hover:bg-[#e07740] text-white font-semibold rounded-xl shadow-md transition-all flex items-center justify-center space-x-2">
                        <i class="fa-solid fa-floppy-disk"></i>
                        <span>Simpan Catatan Investasi</span>
                    </button>
                </form>
            </div>
        </div>

        <!-- MODAL EDIT INVESTASI -->
        <div x-show="editModalOpen" x-transition class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm" style="display: none;">
            <div class="bg-white rounded-3xl w-full max-w-lg p-6 shadow-2xl relative border border-white/40 overflow-y-auto max-h-[90vh]" @click.away="editModalOpen = false">
                <div class="flex items-center justify-between mb-5">
                    <h3 class="font-serif font-bold text-xl text-gray-800">Edit Catatan Investasi</h3>
                    <button @click="editModalOpen = false" class="text-gray-400 hover:text-gray-600"><i class="fa-solid fa-xmark text-xl"></i></button>
                </div>

                <form :action="'/investasi/' + editData.id" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Nama Aset</label>
                        <input type="text" name="nama" x-model="editData.nama" required class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#359FA0] outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Detail Aset (Opsional)</label>
                        <input type="text" name="detail" x-model="editData.detail" placeholder="Contoh: Bank Central Asia, USD/IDR, Emas Antam 1 gram" class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#359FA0] outline-none">
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Jenis Investasi</label>
                            <select name="jenis_investasi" x-model="editData.jenis_investasi" required class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#359FA0] outline-none">
                                <option value="Saham">Saham</option>
                                <option value="USD">USD</option>
                                <option value="JPY">JPY</option>
                                <option value="Reksadana">Reksadana</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Platform</label>
                            <input type="text" name="platform" x-model="editData.platform" class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#359FA0] outline-none">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Tanggal Beli</label>
                            <input type="date" name="tanggal_beli" x-model="editData.tanggal_beli" required class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#359FA0] outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Status Portofolio</label>
                            <select name="status" x-model="editData.status" required class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#359FA0] outline-none">
                                <option value="masih_dimiliki">Masih Dimiliki</option>
                                <option value="sudah_dijual">Sudah Dijual</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Harga Beli Unit (Opsional)</label>
                            <input type="number" step="0.0001" name="harga_beli" x-model="editData.harga_beli" class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#359FA0] outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Total Real Beli IDR (Opsional)</label>
                            <input type="number" name="harga_beli_idr" x-model="editData.harga_beli_idr" class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#359FA0] outline-none">
                        </div>
                    </div>

                    <div x-show="editData.status === 'sudah_dijual'" class="space-y-4 pt-2 border-t border-gray-100">
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Tanggal Jual (Opsional)</label>
                            <input type="date" name="tanggal_jual" x-model="editData.tanggal_jual" class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#359FA0] outline-none">
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Harga Jual Unit (Opsional)</label>
                                <input type="number" step="0.0001" name="harga_jual" x-model="editData.harga_jual" class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#359FA0] outline-none">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Total Real Jual IDR (Opsional)</label>
                                <input type="number" name="harga_jual_idr" x-model="editData.harga_jual_idr" class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#359FA0] outline-none">
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="w-full py-3 bg-[#359FA0] hover:bg-[#2c8384] text-white font-semibold rounded-xl shadow-md transition-all flex items-center justify-center space-x-2">
                        <i class="fa-solid fa-floppy-disk"></i>
                        <span>Perbarui Catatan Investasi</span>
                    </button>
                </form>
            </div>
        </div>