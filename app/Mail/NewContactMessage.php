<?php

namespace App\Mail;

use App\Models\ContactMessage;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

/** Pemberitahuan ke admin saat ada pesan baru dari formulir Kontak. Balas langsung ke email pengirim. */
class NewContactMessage extends Mailable
{
    public function __construct(public ContactMessage $message) {}

    public function envelope(): Envelope
    {
        $topic = config('muphus.contact_topics')[$this->message->topic] ?? $this->message->topic;

        return new Envelope(
            replyTo: [new Address($this->message->email, $this->message->name)],
            subject: 'Pesan baru dari website: ' . $topic . ' (' . $this->message->name . ')',
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.new-contact', with: [
            'topicLabel' => config('muphus.contact_topics')[$this->message->topic] ?? $this->message->topic,
        ]);
    }
}
