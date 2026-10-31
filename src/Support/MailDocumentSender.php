<?php

namespace VentureDrake\LaravelCrmFilament\Support;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Mail;
use VentureDrake\LaravelCrm\Mail\SendQuote;
use VentureDrake\LaravelCrmFilament\Contracts\SendsDocuments;

/**
 * The upstream send: the core SendQuote mail with the PDF rendered to storage. Its build() reads the signed-in user, so
 * it only works inside the request that asked for it.
 */
class MailDocumentSender implements SendsDocuments
{
    public function send(Model $document, string $type, array $data): void
    {
        if ($type !== 'quote') {
            throw new \InvalidArgumentException('MailDocumentSender sends quotes only, got '.$type);
        }

        $signedUrl = PortalUrl::signedFor('laravel-crm.portal.quotes.show', $document);

        $pdfPath = CrmPdf::renderToDisk($document, 'quote');

        Mail::send(new SendQuote([
            'to' => $data['to'],
            'subject' => $data['subject'],
            // Null link, no placeholder: base's mailable expands it either way,
            // and an unexpanded null mails an empty anchor.
            'message' => PortalMessage::resolve(PortalMessage::QUOTE, (string) $data['message'], $signedUrl),
            'cc' => ! empty($data['cc']) ? 1 : 0,
            'onlineQuoteLink' => $signedUrl ?? '',
            'pdf' => $pdfPath,
        ]));
    }
}
