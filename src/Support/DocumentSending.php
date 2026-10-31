<?php

namespace VentureDrake\LaravelCrmFilament\Support;

use VentureDrake\LaravelCrmFilament\Contracts\SendsDocuments;

/**
 * One place that answers the document sender for every send action, so the resource row action and the view page
 * cannot drift apart.
 */
class DocumentSending
{
    public static function sender(): SendsDocuments
    {
        $sender = config('laravel-crm.document_sender') ?: MailDocumentSender::class;
        $instance = app($sender);

        if (! $instance instanceof SendsDocuments) {
            throw new \UnexpectedValueException(
                'laravel-crm.document_sender must name a class implementing '.SendsDocuments::class.', got '.$sender
            );
        }

        return $instance;
    }
}
