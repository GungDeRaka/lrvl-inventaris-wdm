<x-layouts.app>
    <div class="p-6">
        <h1 class="text-2xl font-bold text-gray-800 mb-4">Prediksi Peminjaman (AI)</h1>
        
        <div class="bg-white p-6 rounded-lg shadow-md max-w-md">
            <p class="text-gray-600 mb-4">Fitur ini menggunakan Machine Learning (LSTM) untuk memprediksi jumlah total peminjaman barang untuk <strong>besok</strong> berdasarkan data historis.</p>
            
            <div id="result-area" class="hidden mb-4 p-4 bg-blue-50 text-blue-800 rounded-lg border border-blue-200">
                <h3 class="font-bold text-lg">Hasil Prediksi:</h3>
                <p id="prediction-text" class="text-2xl font-bold mt-2">...</p>
            </div>

            <button onclick="getPrediction()" id="btn-predict" class="w-full bg-purple-700 hover:bg-purple-800 text-white font-bold py-2 px-4 rounded transition flex justify-center items-center">
                <span>Jalankan Prediksi</span>
            </button>
        </div>
    </div>

    <script>
        async function getPrediction() {
            const btn = document.getElementById('btn-predict');
            const resultArea = document.getElementById('result-area');
            const resultText = document.getElementById('prediction-text');
            
            // Loading state
            btn.disabled = true;
            btn.innerHTML = '<svg class="animate-spin h-5 w-5 mr-3 text-white" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Memproses...';
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
    </script>
</x-layouts.app>