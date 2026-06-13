<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Address;

class GateEntryMail extends Mailable
{
    use Queueable, SerializesModels;
	public $mailData;

    /**
     * Create a new message instance.
     */
    public function __construct($mailData)
    {
        $this->mailData = $mailData;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address('otpauth-alerts@growel.com', 'GROWEL GATE ENTRY'),
			subject: $this->mailData['title']
			//from: ['address' => 'otpauth-alerts@growel.com', 'name' => 'GATE ENTRY'],
            //subject: 'New Gate Entry ('.$this->mailData['gate_in_no'].') For '.$this->mailData['doc_type_name'].' ('.$this->mailData['doc_no'].') From '.$this->mailData['sec_id_gt_in_name'],
        );
    }
	
	 public function build()
    {
        $mailobj = $this->view('mail.gateentry')
                    ->subject($this->mailData['title']);
                    
                    //attachment
        if(isset($this->mailData['attachment']))
        {
            $mailobj->attach($this->mailData['attachment']);
        }
        
        
        return $mailobj;
        // return $this->view('mail.vendormaster')
        //             ->subject($this->mailData['title'])
        //             ->attach($this->mailData['attachment']);
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'mail.gateentry',
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
