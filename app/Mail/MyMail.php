<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;


class MyMail extends Mailable{
    use Queueable, SerializesModels;
    public $to_name;
    public $to_email;
    public $subject;
    public $company_name;
    public $data_ar;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($to_name,$subject,$company_name,$data_ar){
        $this->to_name = $to_name;
        $this->subject = $subject;
        $this->company_name = $company_name;
        $this->data_ar = $data_ar;

    }
    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.sendOrderService')->with([
            'company_id'=> $this->data_ar['company_id'],
                'email'=>$this->data_ar['email'],
                'phone_number'=> $this->data_ar['phone_number'],
                'urgent'=> $this->data_ar['urgent'],
                'company_name'=>$this->data_ar['company_name'],
                'deviceNames'=>$this->data_ar['deviceNames'],
                'description'=>$this->data_ar['description'], 
         $this->subject,
         $this->company_name]) 
                    ->subject($this->subject)
                    ->from('alerts@recasoft.no','Recasoft Technologies')
                    ->replyTo('alerts@recasoft.no','Recasoft Technologies');
        // return $this->view('emails.sendOrderService')
        //             ->from('alerts@recasoft.no')
        //             ->subject('My Subject');
    }
}
