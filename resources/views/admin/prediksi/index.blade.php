<x-layouts.app>
    <div class="p-6">
        <h1 class="text-2xl font-bold text-gray-800 mb-4">Prediksi Peminjaman (AI)</h1>

        <div class="bg-white p-6 rounded-lg shadow-md max-w-md">
            <p class="text-gray-600 mb-4">Fitur ini menggunakan Machine Learning (LSTM) untuk memprediksi jumlah total
                peminjaman barang untuk <strong>besok</strong> berdasarkan data historis.</p>

            <div id="result-area" class="hidden mb-4 p-4 bg-blue-50 text-blue-800 rounded-lg border border-blue-200">
                <h3 class="font-bold text-lg">Hasil Prediksi:</h3>
                <p id="prediction-text" class="text-2xl font-bold mt-2">...</p>
            </div>

            <button onclick="getPrediction()" id="btn-predict"
                class="w-full bg-purple-700 hover:bg-purple-800 text-white font-bold py-2 px-4 rounded transition flex justify-center items-center">
                <span>Jalankan Prediksi</span>
            </button>
        </div>
    </div>

     <div class="mt-8">
        <h2 class="text-xl font-bold text-gray-800 mb-4">Potensi Peminjaman Tertinggi (Besok)</h2>

        <div class="bg-white p-6 rounded-lg shadow-md">
            <div class="flex justify-between items-center mb-4">
                <p class="text-gray-600 text-sm">Sistem menganalisis tren 10 barang teraktif dan memprediksi
                    kebutuhannya.</p>
                <button onclick="getRanking()" id="btn-ranking"
                    class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded text-sm transition flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                    Analisa Potensi
                </button>
            </div>

            <div id="ranking-area" class="hidden">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="text-sm font-semibold text-gray-600 border-b">
                            <th class="py-2">Nama Barang</th>
                            <th class="py-2 text-right">Prediksi Kebutuhan</th>
                            <th class="py-2 text-right">Status</th>
                        </tr>
                    </thead>
                    <tbody id="ranking-list" class="text-sm text-gray-700">
                    </tbody>
                </table>
            </div>

            <div id="ranking-empty" class="hidden text-center py-4 text-gray-500 italic">
                Belum ada barang yang diprediksi memiliki lonjakan permintaan besok.
            </div>
        </div>
    </div>

    <script>
        async function getPrediction() {
            const btn = document.getElementById('btn-predict');
            const resultArea = document.getElementById('result-area');
            const resultText = document.getElementById('prediction-text');

            // Loading state
            btn.disabled = true;
            btn.innerHTML =
                '<svg class="animate-spin h-5 w-5 mr-3 text-white" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Memproses...';
            resultArea.classList.add('hidden');

            try {
                const response = await fetch("{{ route('prediksi.check') }}");
                const data = await response.json();

                if (data.status === 'success') {
                    resultArea.classList.remove('hidden');
                    resultText.innerText = data.prediction + " Peminjaman";
                } else {
                    alert('Error: ' + data.message);
                }
            } catch (error) {
                alert('Terjadi kesalahan sistem.');
                console.error(error);
            } finally {
                btn.disabled = false;
                btn.innerHTML = 'Jalankan Prediksi';
            }
        }

        async function getRanking() {
            const btn = document.getElementById('btn-ranking');
            const area = document.getElementById('ranking-area');
            const list = document.getElementById('ranking-list');
            const emptyMsg = document.getElementById('ranking-empty');

            btn.disabled = true;
            btn.innerHTML =
                '<svg class="animate-spin h-4 w-4 mr-2 text-white" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Menganalisa...';

            try {
                const response = await fetch("{{ route('prediksi.ranking') }}");
                const res = await response.json();

                if (res.status === 'success') {
                    list.innerHTML = '';

                    if (res.data.length > 0) {
                        area.classList.remove('hidden');
                        emptyMsg.classList.add('hidden');

                        res.data.forEach(item => {
                            const row = `
                            <tr class="border-b last:border-0 hover:bg-gray-50 transition">
                                <td class="py-3 font-medium">${item.nama_barang}</td>
                                <td class="py-3 text-right font-bold text-indigo-600">${item.prediksi} Unit</td>
                                <td class="py-3 text-right">
                                    <span class="bg-green-100 text-green-800 text-xs font-semibold px-2 py-1 rounded">Potensial</span>
                                </td>
                            </tr>
                        `;
                            list.innerHTML += row;
                        });
                    } else {
                        area.classList.add('hidden');
                        emptyMsg.classList.remove('hidden');
                    }
                } else {
                    alert('Gagal: ' + res.message);
                }
            } catch (error) {
                console.error(error);
                alert('Gagal menghubungi server AI.');
            } finally {
                btn.disabled = false;
                btn.innerHTML =
                    '<svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg> Analisa Potensi';
            }
        }
    
    </script>
</x-layouts.app>
