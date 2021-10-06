<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ProductsImages extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($csv_path,$emails,$shop)
    {
        $this->csv = $csv_path;
        $this->emails = $emails;
        $this->shop = $shop;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->to($this->shop->shopowner_email)->cc($this->emails)->from(env('MAIL_FROM_ADDRESS'),env('APP_NAME'))->markdown('emails.products.images', [
                    'url' => $this->csv,
                    'domain' => 'https://'.$this->shop->shopify_domain,
                ])->attach($this->csv);
    }
}
