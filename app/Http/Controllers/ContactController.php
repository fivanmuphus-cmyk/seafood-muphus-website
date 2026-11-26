<?php

namespace App\Http\Controllers;

use App\Mail\NewContactMessage;
use App\Models\ContactMessage;
use App\Models\Faq;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class ContactController extends Controller
{
    public function show(): View
    {
        return view('contact', [
            'faqs' => Faq::query()->where('page', 'kontak')->where('is_active', true)->orderBy('sort_order')->get(),
            'topics' => config('muphus.contact_topics'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $back = url('/kontak') . '#kirim-pesan';

        // Kolom jebakan: manusia tidak melihatnya, robot biasanya mengisinya.
        // Pura-pura berhasil supaya robot tidak tahu ketahuan.
        if (filled($request->input('website'))) {
            return redirect()->to($back)->with('sent', true);
        }

        // Rapikan nomor: buang spasi, tanda hubung, dan kurung.
        $request->merge([
            'phone' => preg_replace('/[\s\-().]/', '', (string) $request->input('phone')) ?: null,
        ]);

        $topics = array_keys(config('muphus.contact_topics'));

        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email:rfc', 'max:150'],
            'phone' => ['nullable', 'regex:/^(\+?62|0)8\d{7,12}$/'],
            'topic' => ['required', 'in:' . implode(',', $topics)],
            'message' => ['required', 'string', 'min:10', 'max:2000'],
        ], [
            'name.required' => 'Mohon isi nama Anda.',
            'name.max' => 'Nama terlalu panjang.',
            'email.required' => 'Mohon isi email Anda.',
            'email.email' => 'Format email belum sesuai. Contoh: nama@email.com',
            'phone.regex' => 'Format nomor belum sesuai. Contoh: 0811xxxxxxx',
            'topic.required' => 'Mohon pilih topik.',
            'topic.in' => 'Topik yang dipilih tidak tersedia.',
            'message.required' => 'Mohon tulis pesan Anda.',
            'message.min' => 'Pesan terlalu singkat. Tulis minimal 10 karakter.',
            'message.max' => 'Pesan terlalu panjang. Maksimal 2000 karakter.',
        ]);

        $message = ContactMessage::create($data);

        // Kabari admin lewat email setelah halaman dikirim ke pengunjung. Bila email gagal terkirim,
        // pesan tetap aman di admin dan pengunjung tidak melihat galat.
        $to = config('muphus.contact.email');
        // Hanya bila pengiriman email sudah diatur di .env (MAIL_MAILER bukan log). Tanpa itu, pesan cukup dibaca di admin.
        if (filled($to) && ! in_array(config('mail.default'), ['log', 'array'], true)) {
            dispatch(function () use ($to, $message) {
                try {
                    Mail::to($to)->send(new NewContactMessage($message));
                } catch (\Throwable $e) {
                    report($e);
                }
            })->afterResponse();
        }

        return redirect()->to($back)->with('sent', true);
    }
}