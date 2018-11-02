# SIVI AFD Connectors
Currently implemented:
- TIMEConnector

## TIMEConnector
The TIMEConnector implements the [TIME API](https://solera.nl/time). A prerequisite 
for using this API is that you have a valid [Solera Digital Passport](https://solera.nl/bedrijfscertificaat/).

### How to 
Instantiation requires a `TIMEConfig` interface which you will need to implement 
to provide the connector with configuration details.

Example implementation:

```php
class TIMEConfig implements \SIVI\AFDConnectors\Config\Contracts\TIMEConfig
{
    
    protected $host;
    protected $wsdlStoragePath;
    protected $certificatePath;
    protected $certificatePassphrase;

    public function __construct($host, $wsdlStoragePath, $certificatePath, $certificatePassphrase)
    {
        $this->host = $host;
        $this->wsdlStoragePath = $wsdlStoragePath;
        $this->certificatePath = $certificatePath;
        $this->certificatePassphrase = $certificatePassphrase;
    }

    /**
     * Get the host of the service
     *
     * @return string
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * The path where the WSDL for the service will be stored.
     *
     * This is because the WSDL is not public and needs to be requested with
     * a client certificate. The PHP SOAP extension does not support
     * this and that is why a local one must be created.
     *
     * @return string
     */
    public function getWSDLStoragePath()
    {
        return $this->wsdlStoragePath;
    }

    /**
     * The path where the Solera certificate is stored.
     * This needs to be a PEM file.
     *
     * @return string
     */
    public function getCertificatePath()
    {
        return $this->certificatePath;
    }

    /**
     * The passphrase of the Solera certificate.
     *
     * @return string
     */
    public function getCertificatePassphrase()
    {
        return $this->certificatePassphrase;
    }
}
```

After you've got your config sorted it's time to get some messages. This piece of 
code will get all unread messages from TIME and print them.


```php
use SIVI\AFDConnectors\Connectors\TIMEConnector;

// Initialize config
$config = new TIMEConfig(
    'https://www.web.service/sts/portal',
    '/tmp/',
    '/path/to/solera/certificate.pem',
    'certificate password'
);

// Initialize connector
$connector = new TIMEConnector($config);

// This will return an array of SIVI\AFD\Interfaces\TIME\Message items 
// which implement the SIVI\AFD\Interfaces\BatchMessage interface. 
foreach ($connector->getMessage() as $batchMessage) {
    // This will return an array of SIVI\AFD\Interfaces\TIME\Message items 
    // which implement the SIVI\AFD\Interfaces\Message interface.
    foreach ($batchMessage->getMessages() as $message) {
        echo sprintf('TIME Message extension: %s%s', $message->getType(), PHP_EOL);
        echo sprintf('TIME Message content: %s%s%s', PHP_EOL, $message->getData(), PHP_EOL);
    }
}
```