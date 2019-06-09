<?php
class Shopgo_PackgeListener_Helper_Data extends Mage_Core_Helper_Data
{
    /*
     * Send Email
     *
     * @param $messagetype type of allert, $param parameter of message
     * @return
     */
    public function sendMail($messagetype,$param)
    {
        $message = '<html><body>';

        switch ($messagetype) {
            case "info":
            
                 $message .= '<h4 style="display:inline";>This is an automatic informational message to notify you that the domain </h4>'
                          .'<h3 style="display:inline"; >' . Mage::getBaseUrl() . '</h3>'
                          .'<h4 style="display:inline"; > has exceeded the maximum number of products (SKUs) allowed for the current package.</br></h4>'
                          .'<h4>Please upgrade your package or keep the number of products under the limit.</br>
                                For help, contact your On-Boarding or Growth Specialist.</h4>'
                          .'<h4 style="display:inline";> Current package: ' . $param["packageName"] . ', </br>'
                          .'<h4 style="display:inline";> Allowed number of products: ' . $param["availableLimit"] . ', </br>'
                          .'<h4 style="display:inline";> Number of products above the limit: ' . ($param["productsCount"] - $param["availableLimit"]) . '</h4>';
                
                $sender     = $param['sender'];
                $receiver   = $param['receiver'];
                $subject    = $param['subject'];
                $senderName = $param['senderName'];
                $bcc        = $param['bcc'];
                break;
                
            case "UrlAlert":

                $message   .= '<h4>Alert:Shopgo Packages File Has Been Removed From The Domain '.Mage::getBaseUrl().'</h4>';
                $sender     = "info@shopgo.me";
                $receiver   = "emad@shopgo.me";
                $subject    = "urlAlert";
                $senderName = "urlAlert";
                $bcc        = array("emad@shopgo.me","emad@shopgo.me");
                break;

            case "FileAlert":

                $message   .= '<h4>Alert: Package.xml File Has Been Deleted From '.Mage::getBaseUrl().'</h4>';
                $sender     = "info@shopgo.me";
                $receiver   = "emad@shopgo.me";
                $subject    = "FileAlert";
                $senderName = "FileAlert";
                $bcc        = array("emad@shopgo.me","emad@shopgo.me");
                break;
        }

        $message .= '</body></html>';

        $mail1 = Mage::getModel('core/email')
            ->setToName('Shopgo Support')
            ->setToEmail($receiver)
            ->setBody($message)
            ->setSubject($subject)
            ->setFromEmail($sender)
            ->setFromName($senderName)
            ->setType('html');

        $mail2 = Mage::getModel('core/email')
            ->setToName('Shopgo Support')
            ->setToEmail($bcc)
            ->setBody($message)
            ->setSubject($subject)
            ->setFromEmail($sender)
            ->setFromName($senderName)
            ->setType('html');
        try {
            $mail1->send();
            //$mail2->send();
        }
        catch (Exception $e) {
            Mage::log($e);
        }
    }
}