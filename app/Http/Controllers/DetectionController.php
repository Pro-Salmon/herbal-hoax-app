<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

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
        $scriptPath = base_path('python_engine/predict.py');

        // 1. Tentukan path python (gunakan path python Laragon atau 'python')
        $pythonBinary = 'C:\\laragon\\bin\\python\\python-3.12.8\\python.EXE';
        if (!file_exists($pythonBinary)) {
            $pythonBinary = 'python';
        }

        // 2. Siapkan Environment Variable Windows agar WinError 10106 tidak muncul
        $env = array_merge($_SERVER, $_ENV, [
            'SYSTEMROOT' => getenv('SYSTEMROOT') ?: 'C:\Windows',
            'PATH' => getenv('PATH'),
            'PYTHONIOENCODING' => 'utf-8'
        ]);

        // 3. Jalankan skrip tanpa passing teks langsung di argumen CLI
        $process = new Process([$pythonBinary, $scriptPath], null, $env);
        
        // 4. Kirim teks panjang & karakter khusus secara aman via STDIN
        $process->setInput($inputText);
        $process->setTimeout(60);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        $output = json_decode($process->getOutput(), true);

        if (!$output || isset($output['error'])) {
            $errorMsg = $output['error'] ?? 'Gagal memproses prediksi teks.';
            return back()->with('error', $errorMsg);
        }

        return view('result', [
            'isHoax' => $output['label'] === 1,
            'confidence' => $output['confidence'],
            'analysis' => $output['analysis'],
            'originalText' => $inputText
        ]);
    }
}