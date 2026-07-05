<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Mentor;
use App\Models\Telegram;

class TelegramController extends Controller
{
    public function index()
    {
        // Menampilkan view telegram.index jika bukan POST
        $title = "Telegram";

        $telegram = Telegram::all()->first();

        // dd($telegram);

        // Kirimkan data telegram jika ada
        return view('telegram.index', compact('title', 'telegram'));
    }

    public function store(Request $request)
    {
        // Cek apakah data sudah ada jika menggunakan POST (untuk menampilkan form dengan data)
        if ($request->isMethod('post')) {
            // Ambil data dari request
            $botToken = $request->input('botToken');
            $chatId = "6469893006";  // ID Telegram (gunakan ID default atau sesuaikan dengan input)
            $message = $request->input('message');
            $username = $request->input('username', 'PrakerinTracerBot'); // Gunakan nilai default jika tidak ada input

            // Validasi input
            $validatedData = $request->validate([
                'botToken' => 'required|string|max:255',
                'message' => 'required|string',
                'username' => 'nullable|string',  // Mengubah validasi agar username boleh kosong
            ]);

            // Cek apakah data dengan botToken yang sama sudah ada di database
            $telegram = Telegram::where('bot_token', $validatedData['botToken'])->first();

            if ($telegram) {
                // Jika data sudah ada, lakukan update
                $telegram->message = $validatedData['message'];
                $telegram->save();

                // Kirim pesan ke Telegram
                $this->TrySendMessage($botToken, $chatId, $message);

                // Kirim respons sukses ke AJAX
                return response()->json([
                    'status' => 'success',
                    'message' => 'Pesan berhasil diperbarui dan berhasil dikirim!',
                    'data' => $telegram,
                ]);
            } else {
                // Jika data belum ada, lakukan simpan
                $telegram = Telegram::create([
                    'bot_token' => $validatedData['botToken'],
                    'message' => $validatedData['message'],
                    'username' => $username, // Menggunakan username yang sudah diatur
                ]);

                // Kirim pesan ke Telegram
                $this->TrySendMessage($botToken, $chatId, $message);

                // Kirim respons sukses ke AJAX
                return response()->json([
                    'status' => 'success',
                    'message' => 'Data berhasil disimpan dan pesan berhasil dikirim!',
                    'data' => $telegram,
                ]);
            }
        }
    }


    private function TrySendMessage($botToken, $chatId, $message)
    {
        $url = "https://api.telegram.org/bot{$botToken}/sendMessage";

        // Menggunakan POST request untuk mengirim pesan
        $response = Http::post($url, [
            'chat_id' => $chatId,
            'text' => $message,
            'parse_mode' => 'Markdown',  // Format pesan menggunakan Markdown
        ]);

        // Cek apakah pengiriman pesan berhasil
        if ($response->failed()) {
            Log::error('Failed to send message: ' . $response->body());
        } else {
            Log::info('Message sent successfully to chat_id: ' . $chatId);
        }
    }

    public function handleWebhook(Request $request)
    {
        // Ambil data dari request Telegram
        $data = $request->all();
        $web = "https://prakerin.skahida.my.id/";

        // Log data yang diterima untuk debugging
        Log::info('Received data from Telegram: ', $data);

        // Cek apakah pesan yang diterima adalah /start
        if (isset($data['message']['text'])) {
            Log::info('Message received: ' . $data['message']['text']); // Menambahkan log untuk teks pesan

            // Periksa apakah pesan adalah /start
            if ($data['message']['text'] === '/start') {
                // Ambil chat_id dari pesan yang diterima
                $chatId = $data['message']['chat']['id'];

                // Kirimkan pesan selamat datang
                $welcomeMessage = "*Selamat datang di notifikasi bot Telegram Prakerin Tracer SMK NU Al Hidayah Kudus.*";

                // Cek apakah chat_id sudah ada di database (misalnya di tabel mentor)
                $mentor = Mentor::where('telegram_number', $chatId)->first();

                if ($mentor) {
                    // Jika mentor ditemukan, ambil nama
                    $name = $mentor->name;
                    // Kirimkan pesan bahwa chat ID sudah aktif
                    $message = "$welcomeMessage\n\nChat ID *$name* sudah aktif dan sudah bisa mendapatkan notifikasi presensi dari siswa.";
                } else {
                    // Jika mentor tidak ditemukan, beri pesan untuk mengisi form
                    $message = "$welcomeMessage\n\nChat ID Telegram *$chatId* \n\nTolong masukkan Chat ID kamu diatas di form web $web karena chat ID tersebut belum terdaftar.";
                }

                // Kirim pesan balasan ke pengguna
                $this->sendMessage($chatId, $message);

                // Menambahkan respons JSON jika kamu ingin memberikan status
                return response()->json([
                    'status' => 'success',
                    'message' => $message,
                ]);
            }

            // Jika perintah bukan /start, kirim pesan error
            $message = "Perintah tidak ada. Silakan klik /start untuk memulai.";

            // Kirim pesan error ke pengguna
            $chatId = $data['message']['chat']['id'];
            $this->sendMessage($chatId, $message);

            // Menambahkan respons JSON untuk perintah yang tidak ditemukan
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid command received',
            ]);
        }

        // Jika tidak ada teks yang diterima
        return response()->json([
            'status' => 'error',
            'message' => 'No valid message received',
        ]);
    }


    private function sendMessage($chatId, $message)
    {
        $botToken = env('TELEGRAM_BOT_TOKEN');  // Pastikan token bot ada di .env
        $url = "https://api.telegram.org/bot{$botToken}/sendMessage";

        // Menggunakan POST request untuk mengirim pesan
        $response = Http::post($url, [
            'chat_id' => $chatId,
            'text' => $message,
            'parse_mode' => 'Markdown',  // Format pesan menggunakan Markdown
        ]);

        // Cek apakah pengiriman pesan berhasil
        if ($response->failed()) {
            Log::error('Failed to send message: ' . $response->body());
        } else {
            Log::info('Message sent successfully to chat_id: ' . $chatId);
        }
    }
}
