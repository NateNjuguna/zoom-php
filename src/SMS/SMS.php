<?php
namespace Zoom\SMS;

class SMS {

    /**
     * The driver to use when sending messages
     * 
     * @var \Zoom\SMS\SMSDriverInterface
     */
    protected $driver;

    /**
     * Create a new SMS object
     * 
     * @param   string  $driver_name
     * @return  void
     */
    public function __construct($driver_name) {
        $config = config("sms.drivers.{$driver_name}");
        $driver_class = $config['class'];
        $this->driver = new $driver_class($config['settings']);
    }

    /**
     * Get the SMS driver
     * 
     * @return  \Zoom\SMS\SMSDriverInterface
     */
    public function driver() {
        return $this->driver;
    }

    /**
     * Send a message to a recipient(s)
     *
     * @param   mixed   $recipients
     * @param   string  $message
     * @return  \Zoom\SMS\SMSResult
     * 
     * @throws  \Zoom\SMS\SMSException
     */
    static public function send($recipients, $message) {
        $driver = app()->get(static::class)->driver();
        return $driver->send($recipients, $message);
    }

    /**
     * Send a message to a recipient(s)
     *
     * @param   mixed   $recipients
     * @param   string  $message
     * @return  \Zoom\SMS\SMSStatus
     * 
     * @throws  \Zoom\SMS\SMSException
     */
    static public function status() {
        $driver = app()->get(static::class)->driver();
        return $driver->status();
    }

}
