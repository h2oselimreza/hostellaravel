<?php

namespace App\Mail;

use App\Models\Admin\InvoiceSummary;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InvoiceMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public $invoiceSummary,
        public $invoiceDetails
    ) {
    }

    public function build()
    {
        return $this->subject('Invoice Slip')
            ->view('admin.invoice.invoice-payment.email-template');
    }
}
