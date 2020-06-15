<?php
namespace Zoom;

use function DI\factory;
use function DI\object as di_object;
use Dotenv\Dotenv;
use PHPMailer\PHPMailer\PHPMailer;
use PDO;
use Zoom\Routing\Router;

class Application {

    /**
     * The application's debug status
     * 
     * @var string
     */
    public  $debug;

    /**
     * The application's namespace
     * 
     * @var string
     */
    public  $namespace = '\App';

    /**
     * Initialise the application
     * 
     * @param   boolean $debug
     * @return  void
     */
    public function __construct($debug = false) {
        if ($debug) {
            ini_set('display_errors', 'On');
            error_reporting(E_ALL);
        }
        $this->debug = $debug;
        $this->_init();
    }
    
    /**
     * Destroy the application
     * 
     * @return void
     */
    public function __destruct() {
        container(PDO::class, 0);
    }
    
    /**
     * Register important services
     * 
     * @return void
     */
    protected function _init() {
        // Configuration service
        $this->_loadEnv();
        $config = Config::cache();
        $this->register('config', $config);

        // Database service
        $db_config = $config['database'];
        $this->register(PDO::class, di_object()->constructor(
            "mysql:host={$db_config['host']};dbname={$db_config['database']}",
            $db_config['username'],
            $db_config['password'],
            [PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ]
        ));

        // Mailing service
        $mail_config = $config['mail'];
        $this->register(PHPMailer::class, di_object()->constructor(true));
        $this->register(
            'Zoom\Mailer',
            factory(function(PHPMailer $mail, FS $fs) use ($mail_config) {
                $smtp_driver_config = $mail_config['drivers']['smtp'];
                $mail->SMTPDebug = $this->debug ? 3 : 0;
                $mail->Debugoutput = function($str, $level) use ($fs) {
                    $fs->save('logs/mail.log', "{$level}: {$str}\n", true);
                };
                $mail->isSMTP();
                $mail->Host = $smtp_driver_config['host'];
                $mail->SMTPAuth = $smtp_driver_config['auth'];
                $mail->Username = $mail_config['username'];
                $mail->Password = $smtp_driver_config['password'];
                $mail->SMTPSecure = $mail_config['encryption'];
                $mail->Port = $smtp_driver_config['port'];
                return $mail;
            })->parameter('fs', FS::disk('storage'))
        );
        
        // Session service
        $this->register(Session::class, di_object());

        // SMS Service
        $sms_config = $config['mail'];
        $this->register(SMS::class, factory(function() {
            $default_driver = config('sms.driver');
            return new SMS($default_driver);
        }));
    }

    /**
     * Load environment variables
     * 
     * @return  void
     */
    protected function _loadEnv() {
        // Load the environment variables
        $dotenv = new Dotenv(str_replace(FS::OSCorrectPath('/src'), '', __DIR__));
        $dotenv->load();
    }

    /**
     * Obtain values from the app's container
     * 
     * @param   string  $key
     * @return  mixed
     */
    public function get($key) {
        return container($key);
    }

    /**
     * Add values to the app's container
     * 
     * @param   string  $key
     * @param   mixed   $value
     * @return  void
     */
    public function register($key, $value) {
        container($key, $value);
    }

    /**
     * Handle incoming requests
     * 
     * @return  void
     */
    public function route() {
        // Start the application's routing
        Router::start();
    }
}
