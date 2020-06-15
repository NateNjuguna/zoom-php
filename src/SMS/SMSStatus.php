<?php
namespace Zoom\SMS;

use ArrayObject;

class SMSStatus {

    /**
     * The store of the failed, sent and total sms sent
     * 
     * @var \ArrayObject
     */
    protected $counts;

    /**
     * A list of all failed SMS
     * 
     * @var array
     */
    protected $failed = [];

    /**
     * A list of all sent SMS
     * 
     * @var array
     */
    protected $sent = [];

    /**
     * Create a new SMSResult instance
     * 
     * @param   array   $failed A list of recipients' and reasons whose SMS failed to send
     * @param   array   $sent   A list of reciients' and message reference IDs whose SMS was sent successfully
     * @return  
     */
    public function __construct(array $failed, array $sent) {
        foreach ($failed as $failed_) {
            array_push($this->failed, new ArrayObject($failed_));
        }
        foreach ($sent as $sent_) {
            array_push($this->sent, new ArrayObject($sent_));
        }
    }

    /**
     * Return data for accessible private, protected or non-existent properties
     * 
     * @param   string  $property   The name of a property
     * @return  mixed
     */
    public function __get($property) {
        if ($property === 'counts') {
            $failed_count = count($this->failed);
            $sent_count = count($this->sent);
            $this->counts = new ArrayObject([
                'failed'    => $failed_count,
                'sent'      => $sent_count,
                'total'     => $failed_count + $sent_count,
            ]);
        }
        return isset($this->{$property}) ? $this->{$property} : null;
    }
}
