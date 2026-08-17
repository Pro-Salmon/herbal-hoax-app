<x-layouts.mobile title="Cek Herbal Hoaks">
    <!-- Header -->
    <header class="bg-[#247B46] text-white pt-9 pb-4 px-6 sm:py-4 text-center shadow-md">
        <h1 class="text-lg font-bold">Cek Herbal Hoaks</h1>
    </header>

    <!-- Main Form -->
    <main class="flex-1 px-6 py-8 flex flex-col items-center">
        <h2 class="text-2xl font-black text-gray-900 text-center leading-tight mb-8">
            Periksa Keaslian<br>Informasi
        </h2>

        <form action="{{ route('detection.process') }}" method="POST"
            class="w-full flex-1 flex flex-col justify-between">
            @csrf
            <div class="w-full">
                <textarea name="content" rows="9" placeholder="Tempelkan atau ketik narasi berita herbal di sini..."
                    class="w-full p-4 border border-gray-400 rounded-2xl text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#247B46] focus:border-transparent resize-none"
                    required></textarea>
            </div>

            <button type="submit"
                class="w-full bg-[#247B46] hover:bg-[#1e663a] text-white font-bold py-3.5 px-4 rounded-xl transition duration-200 uppercase tracking-wider text-sm shadow-md mt-6">
                PERIKSA SEKARANG
            </button>
        </form>
    </main>
</x-layouts.mobile>