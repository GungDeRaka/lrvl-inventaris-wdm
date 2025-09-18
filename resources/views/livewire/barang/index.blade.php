
<div>
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold text-gray-800">Manajemen Data Barang</h1>
        <button class="bg-purple-700 hover:bg-purple-800 text-white font-bold py-2 px-4 rounded">
            Tambah Barang
        </button>
    </div>

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <table class="min-w-full leading-normal">
            <thead class="bg-gray-200">
                <tr>
                    <th class="px-5 py-3 border-b-2 border-gray-200 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Kode Barang</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Nama Barang</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Kategori</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Lokasi</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Stok</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($barangs as $barang)
                    <tr class="hover:bg-gray-50">
                        <td class="px-5 py-4 border-b border-gray-200 bg-white text-sm">{{ $barang->kode_barang }}</td>
                        <td class="px-5 py-4 border-b border-gray-200 bg-white text-sm">{{ $barang->nama_barang }}</td>
                        <td class="px-5 py-4 border-b border-gray-200 bg-white text-sm">{{ $barang->kategori->nama_kategori }}</td>
                        <td class="px-5 py-4 border-b border-gray-200 bg-white text-sm">{{ $barang->ruangan->nama_ruangan }}</td>
                        <td class="px-5 py-4 border-b border-gray-200 bg-white text-sm">{{ $barang->jumlah_saat_ini }} / {{ $barang->jumlah_total }}</td>
                        <td class="px-5 py-4 border-b border-gray-200 bg-white text-sm">
                            <button class="text-yellow-600 hover:text-yellow-900 mr-2">Edit</button>
                            <button class="text-red-600 hover:text-red-900">Hapus</button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-4">Tidak ada data barang.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-5 py-4">
            {{ $barangs->links() }}
        </div>
    </div>
</div>