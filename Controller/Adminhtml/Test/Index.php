<?php
namespace Emizentech\CustomSmtp\Controller\Adminhtml\Test;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Index extends \Magento\Backend\App\Action
{

    /**
     * @var PageFactory
     */
    protected $_resultPageFactory;
    
    /**
     * @var Data
     */
    protected $_dataHelper;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        \Emizentech\CustomSmtp\Helper\Data $dataHelper
    ) {
        $this->_resultPageFactory = $resultPageFactory;
        $this->_dataHelper = $dataHelper;
        parent::__construct($context, $dataHelper);
        
    }

    /**
     * Index action
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute() {

        $request = $this->getRequest();
        $store_id = $request->getParam('store', null);
        
        
        $name = 'Emizen Tech Custom SMTP Test Email';
        $username = $request->getPost('username');
        $port = $request->getPost('port');
        $password = $request->getPost('password');
        
        //if default view 
        //see https://github.com/magento/magento2/issues/3019
        if(!$request->getParam('store', false)){
            if(empty($username) || empty($password)){
                $this->getResponse()->setBody(__('Please enter a valid username/password'));
                return;
            }
        }

        //if password mask (6 stars)
        $password = ($password == '******') ? $this->_dataHelper->getConfigPassword($store_id) : $password;
        
        $to = $request->getPost('email') ? $request->getPost('email') : $username;

        //SMTP server configuration
        $smtpHost = $request->getPost('smtphost');
        
        $smtpConf = array(
            'auth' => strtolower($request->getPost('auth')),
            'ssl' => $request->getPost('ssl'),
            'username' => $username,
            'password' => $password,
            'port' => $port
        );
        
        $transport = new \Zend_Mail_Transport_Smtp($smtpHost, $smtpConf);
        
        //Create email
        $mail = new \Zend_Mail();
        $mail->setFrom($username, $name);
        $mail->addTo($to, $to);
        $mail->setSubject('Test SMTP Email from Emizen Tech Private Limited');
        $mail->setBodyText('Thank you for choosing Emizen Tech\'s extension.');
        
        
        $result = __('Sent... Please check your email') . ' ' . $to;
        
        try {
            //only way to prevent zend from giving a error
            if (!$mail->send($transport) instanceof \Zend_Mail){}
        } catch (\Exception $e) {
            $result = __($e->getMessage());
        }
        
        $this->getResponse()->setBody($this->makeClickableLinks($result));
    }
    
    /**
     * Make link clickable
     * @param string $s
     * @return string
     */
    public function makeClickableLinks($s) {
        return preg_replace('@(https?://([-\w\.]+[-\w])+(:\d+)?(/([\w/_\.#-]*(\?\S+)?[^\.\s])?)?)@', '<a href="$1" target="_blank">$1</a>', $s);
    }

    /**
     * Is the user allowed to view the blog post grid.
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Emizentech_CustomSmtp');
    }


}