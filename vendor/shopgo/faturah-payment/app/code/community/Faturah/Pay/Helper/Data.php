<?php

class Faturah_Pay_Helper_Data extends Mage_Core_Helper_Abstract {

    public function deleteallCartItems() {
        $cartHelper = Mage::helper('checkout/cart');
        $items = $cartHelper->getCart()->getItems();
        foreach ($items as $item) {
            $itemId = $item->getItemId();
            $cartHelper->getCart()->removeItem($itemId)->save();
        }
    }

    /**
     * Translates the response code into a more meaningful description.
     * Response code descriptions are taken directly from the MIGS documentation.
     */
    function getResponseCodeDescription($responseCode) {
        switch ($responseCode) {
            case "?" : $result = "Response Unknown";
                break;
            case "0" : $result = "Transaction Successful";
                break;
            case "1" : $result = "Transaction Declined - Bank Error";
                break;
            case "2" : $result = "Bank Declined Transaction";
                break;
            case "3" : $result = "Transaction Declined - No Reply from Bank";
                break;
            case "4" : $result = "Transaction Declined - Expired Card";
                break;
            case "5" : $result = "Transaction Declined - Insufficient funds";
                break;
            case "6" : $result = "Transaction Declined - Error Communicating with Bank";
                break;
            case "7" : $result = "Payment Server Processing Error - Typically caused by invalid input data such as an in valid credit card number. Processing errors can also occur";
                break;
            case "8" : $result = "Transaction Declined - Transaction Type Not Supported";
                break;
            case "9" : $result = "Bank Declined Transaction (Do not contact Bank)";
                break;
            case "A" : $result = "Transaction Aborted";
                break;
            case "B" : $result = "Transaction Declined - Contact the Bank";
                break;
            case "C" : $result = "Transaction Cancelled";
                break;
            case "D" : $result = "Deferred Transaction";
                break;
            case "E" : $result = "Issuer Returned a Referral Response";
                break;
            case "F" : $result = "3D Secure Authentication failed";
                break;
            case "I" : $result = "Card Security Code Failed";
                break;
            case "L" : $result = "Shopping Transaction Locked (This indicates that there is another transaction taking place using the same shopping transaction number) ";
                break;
            case "N" : $result = "Cardholder is not enrolled in 3D Secure (Authentication Only)";
                break;
            case "P" : $result = "Transaction is Pending";
                break;
            case "R" : $result = "Retry Limits Exceeded, Transaction Not Processed";
                break;
            case "S" : $result = "Duplicate OrderInfo used. (This is only relevant for Payment Servers that enforce the uniqueness of this field)";
                break;
            case "U" : $result = "Card Security Code Failed";
                break;
            default : $result = "Response Unknown";
        }

        return $result;
    }

}
