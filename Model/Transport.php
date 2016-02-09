<?php
namespace Emizentech\CustomSmtp\Model;



class Transport extends \Zend_Mail_Transport_Smtp implements \Magento\Framework\Mail\TransportInterface
{
    /**
     * @var \Magento\Framework\Mail\MessageInterface
     */
    protected $_message;


    /**
     * @param MessageInterface $message
     * @param null $parameters
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @throws \InvalidArgumentException
     */
    public function __construct(\Magento\Framework\Mail\MessageInterface $message, \Emizentech\CustomSmtp\Helper\Data $dataHelper)
    {
        if (!$message instanceof \Zend_Mail) {
            throw new \InvalidArgumentException('The message should be an instance of \Zend_Mail');
        }

         $smtpHost = $dataHelper->getConfigSmtpHost();
       
         $smtpConf = [
            'auth' => strtolower($dataHelper->getConfigAuth()),
            'ssl' => $dataHelper->getConfigSsl(),
            'username' => $dataHelper->getConfigUsername(),
            'password' => $dataHelper->getConfigPassword(),
            'port' => $dataHelper->getConfigPort()
         ];
        parent::__construct($smtpHost, $smtpConf);
        $this->_message = $message;
    }

    /**
     * Send a mail using this transport
     *
     * @return void
     * @throws \Magento\Framework\Exception\MailException
     */
    public function sendMessage()
    {

        try {
            parent::send($this->_message);
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\MailException(new \Magento\Framework\Phrase($e->getMessage()), $e);
        }
    }
}