<x-layouts.mobile title="Hasil Analisis">
    <!-- Header -->
    <header class="bg-[#247B46] text-white py-4 px-4 flex items-center shadow-md relative">
        <a href="{{ route('detection.index') }}" class="p-1 hover:bg-white/20 rounded-full transition">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18">
                </path>
            </svg>
        </a>
        <h1 class="text-lg font-bold flex-1 text-center pr-7">Hasil Analisis</h1>
    </header>

    <!-- Content -->
    <main class="flex-1 px-6 py-6 flex flex-col items-center justify-between overflow-y-auto">
        <div class="w-full flex flex-col items-center">
            <!-- Icon -->
            <div class="mt-4 mb-4">
                @if($isHoax)
                    <div class="w-28 h-28 rounded-full border-[7px] border-[#E04B4B] flex items-center justify-center">
                        <svg class="w-16 h-16 text-[#E04B4B]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3.5"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </div>
                @else
                    <div class="w-28 h-28 rounded-full border-[7px] border-[#72B74B] flex items-center justify-center">
                        <svg class="w-16 h-16 text-[#72B74B]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3.5" d="M5 13l4 4L19 7">
                            </path>
                        </svg>
                    </div>
                @endif
            </div>

            <!-- Title -->
            <h2 class="text-lg font-black text-center text-gray-900 uppercase leading-snug tracking-tight mb-4">
                @if($isHoax)
                    WASPADA!<br>TERIDENTIFIKASI HOAKS
                @else
                    INFORMASI<br>VALID/TERPERCAYA
                @endif
            </h2>

            <!-- Confidence Badge -->
            <div
                class="bg-[#B9D7C8] text-[#1B4B32] font-bold text-sm px-6 py-2 rounded-xl mb-4 text-center w-full max-w-[280px]">
                Tingkat Akurasi AI: {{ $confidence }}%
            </div>

            <!-- Description -->
            <div
                class="bg-[#EAECEF] rounded-2xl p-4 w-full text-left text-xs text-gray-800 leading-relaxed min-h-[90px]">
                <span class="font-bold">Analisis:</span> {{ $analysis }}
            </div>
        </div>

        <!-- Buttons -->
        <div class="w-full space-y-2.5 mt-6 mb-2">
            @if($isHoax)
                <button type="button"
                    class="w-full bg-[#245D81] hover:bg-[#1d4c6a] text-white font-bold py-3 px-4 rounded-xl text-sm transition">
                    Laporkan
                </button>
            @endif
            <a href="{{ route('detection.index') }}"
                class="block text-center w-full {{ $isHoax ? 'bg-white border-2 border-[#245D81] text-[#245D81]' : 'bg-[#245D81] text-white' }} font-bold py-2.5 px-4 rounded-xl text-sm transition">
                Kembali
            </a>
        </div>
    </main>
</x-layouts.mobile>