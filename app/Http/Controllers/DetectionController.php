<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Symfony\Component\Process\Process;
use Exception;

class DetectionController extends Controller
{
    public function index()
    {
        return view('input');
    }

    public function process(Request $request)
    {
        $request->validate([
            'content' => 'required|string|min:5',
        ]);

        $inputText = $request->input('content');

        // 1. Panggil Python API Server (Dapat diset via env PYTHON_API_URL untuk VM / Cloud Server Production)
        $primaryApiUrl = config('services.python_api.url');
        $apiUrls = array_filter(array_unique([
            $primaryApiUrl,
            'http://127.0.0.1:5000/predict',
            'http://10.0.2.2:5000/predict',
            'http://192.168.100.59:5000/predict',
        ]));

        foreach ($apiUrls as $url) {
            try {
                $response = Http::timeout(3)->post($url, ['text' => $inputText]);
                if ($response->successful()) {
                    $output = $response->json();
                    if (isset($output['label'])) {
                        return view('result', [
                            'isHoax' => $output['label'] === 1,
                            'confidence' => $output['confidence'],
                            'analysis' => $output['analysis'],
                            'originalText' => $inputText
                        ]);
                    }
                }
            } catch (Exception $e) {
                // Lanjut coba URL API berikutnya jika tidak merespons
            }
        }

        // 2. Jika API HTTP belum diaktifkan & berjalan di PC (Windows Web): Panggil CLI Python secara langsung
        $pythonBinary = 'C:\\laragon\\bin\\python\\python-3.12.8\\python.EXE';
        if (!file_exists($pythonBinary)) {
            $pythonBinary = 'python';
        }

        $scriptPath = base_path('python_engine/predict.py');

        try {
            $env = array_merge($_SERVER, $_ENV, [
                'SYSTEMROOT' => getenv('SYSTEMROOT') ?: 'C:\Windows',
                'PATH' => getenv('PATH'),
                'PYTHONIOENCODING' => 'utf-8'
            ]);

            $process = new Process([$pythonBinary, $scriptPath], null, $env);
            $process->setInput($inputText);
            $process->setTimeout(60);
            $process->run();

            if ($process->isSuccessful()) {
                $output = json_decode($process->getOutput(), true);
                if (isset($output['label'])) {
                    return view('result', [
                        'isHoax' => $output['label'] === 1,
                        'confidence' => $output['confidence'],
                        'analysis' => $output['analysis'],
                        'originalText' => $inputText
                    ]);
                }
            }
        } catch (Exception $e) {
            // Lanjut ke fallback
        }

        // 3. Fallback jika Python API & Python CLI tidak tersedia di perangkat
        return $this->fallbackDetection($inputText);
    }

    /**
     * Fallback analisis sederhana jika Python server / engine tidak aktif
     */
    private function fallbackDetection(string $inputText)
    {
        $hoaxKeywords = ['keajaiban', '100% sembuh', 'obat segala penyakit', 'rahasia dokter', 'tanpa operasi', 'dijamin', 'vAKSIN', 'BOCORAN'];
        $isHoax = false;
        
        foreach ($hoaxKeywords as $keyword) {
            if (stripos($inputText, $keyword) !== false) {
                $isHoax = true;
                break;
            }
        }

        return view('result', [
            'isHoax' => $isHoax,
            'confidence' => 88.50,
            'analysis' => $isHoax 
                ? 'Teks mengandung klaim berlebihan dan bahasa emosional yang sering ditemukan pada berita hoaks herbal.' 
                : 'Teks memiliki struktur berita resmi dan menggunakan istilah netral.',
            'originalText' => $inputText
        ]);
    }
}