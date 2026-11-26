<!DOCTYPE html>
<html lang="id">
<body style="margin:0;padding:24px;background:#F7F3EE;font-family:Arial,Helvetica,sans-serif;color:#1a1a1a;">
    <div style="max-width:560px;margin:0 auto;background:#ffffff;border-top:4px solid #0C3414;padding:28px;">
        <p style="margin:0 0 4px;font-size:12px;letter-spacing:.14em;text-transform:uppercase;color:#4a7c59;">Pesan baru dari website</p>
        <h1 style="margin:0 0 20px;font-size:20px;">{{ $topicLabel }}</h1>

        <table cellpadding="0" cellspacing="0" style="width:100%;font-size:14px;line-height:1.6;">
            <tr><td style="width:90px;color:#666;">Nama</td><td>{{ $message->name }}</td></tr>
            <tr><td style="color:#666;">Email</td><td><a href="mailto:{{ $message->email }}">{{ $message->email }}</a></td></tr>
            @if ($message->phone)
                <tr><td style="color:#666;">Telepon</td><td><a href="https://wa.me/{{ preg_replace('/^0/', '62', ltrim($message->phone, '+')) }}">{{ $message->phone }}</a></td></tr>
            @endif
            <tr><td style="color:#666;">Waktu</td><td>{{ $message->created_at?->timezone('Asia/Jakarta')->format('d M Y, H:i') }} WIB</td></tr>
        </table>

        <p style="margin:24px 0 6px;font-size:12px;letter-spacing:.14em;text-transform:uppercase;color:#666;">Pesan</p>
        <div style="white-space:pre-wrap;font-size:15px;line-height:1.6;border-left:3px solid #0C3414;padding-left:14px;">{{ $message->message }}</div>

        <p style="margin:28px 0 0;font-size:12px;color:#888;">Balas email ini untuk menjawab langsung ke pengirim. Semua pesan juga tersimpan di admin, menu Pesan Masuk.</p>
    </div>
</body>
</html>
