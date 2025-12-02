<?php

namespace App\Mail;

use App\Models\KRS;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class KrsRejectedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $krs;
    public $user;
    public $reason;

    /**
     * Create a new message instance.
     */
    public function __construct(KRS $krs, User $user, ?string $reason = null)
    {
        $this->krs = $krs->load(['mahasiswa', 'jadwalKuliah.mataKuliah', 'semester']);
        $this->user = $user;
        $this->reason = $reason;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'KRS Ditolak - ' . $this->krs->jadwalKuliah->mataKuliah->nama_mk,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.krs-rejected',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
