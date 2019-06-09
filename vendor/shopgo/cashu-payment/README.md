This extension enables online Credit Cards payments via CashU, it requires the following to be filled on the CashU Magento admin panel settings:

**Merchant ID:** It will be provided by CashU.

**Encryption Keyword:** This is the same keyword you set on your CashU account. Go to Merchant Services > Service Set-up > Encryption Keyword.

From your CashU account, Go to Merchant Services > Service Set-up to fill the following:
**Return URL** > http://example.com/StoreCode/cashu/payment/response

**Notification URL** > http://example.com/StoreCode/cashu/payment/notification

**Sorry URL** > http://example.com/StoreCode/cashu/payment/sorry

Also, you need to set the **security settings** from you CashU account: Go to Merchant Services > Security Settings > Choose "Enhanced Encryption"