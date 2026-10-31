<?php

namespace VentureDrake\LaravelCrmFilament\Contracts;

use Illuminate\Database\Eloquent\Model;

/**
 * Sends a document (a quote today) that a user asked to send from the panel.
 *
 * Bound by class name in `laravel-crm.document_sender` (a class name, not a closure: `config:cache` writes the config
 * with var_export). When the key is null the package mails as before ({@see \VentureDrake\LaravelCrmFilament\Support\MailDocumentSender}).
 */
interface SendsDocuments
{
    /**
     * @param  string  $type  the document type slug of CrmPdf ('quote')
     * @param  array<string, mixed>  $data  the send form: to, subject, message, cc
     */
    public function send(Model $document, string $type, array $data): void;
}
