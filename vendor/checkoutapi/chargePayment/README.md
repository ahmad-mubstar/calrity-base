Partner teams, please ask Shopgo partners to communicate checkout.com to set them up the Webhooks for their live stores. Partners need to provide store URLs to checkout.com so they can set up the Webhooks.

*Here you can find some useful tips on how to fill this extension admin panel fields to get it fully functional:*

* **Private shared key:**
This key adds an additional layer of security that enables you to check if the request comes from Checkout.com, and is provided from chekcout.com side when they configure the Webhook.
Mandatory: No.

* **Secret key:**
Your account secret API key, and is provided from chekcout.com side.
Mandatory: Yes.

* **Public key:**
Your account public API key, and is provided from chekcout.com side.
Mandatory: Yes.

* **Payment Action:**

*-Authorize Only:*
This will check if the amount of the order is available in the customers credit card balance without deducting it. Partner will then need to capture the order.
Recommended settings for "Order Status when capture": Processing, and for "New Order Status": Pending.

*-Authorize and Capture:*
This will deduct the order amount directly from the customer's credit card.
Recommended settings for "Order Status when capture": Processing, and for "New Order Status": Processing.

* **Endpoint URL mode:**
This will enable payment gateway testing by choosing: Sandbox.

* **Is 3D?**
This option is not available on this release, **we need to keep it disabled (No) as enabling it will break the payment flow.**

* **Auto capture time:**
This option will capture the order amount after known number of hours. It's recommended to keep it's value as (0) to enable auto capturing.

* **Time-out value for a request to the gateway:**
This enables order decline after known number of seconds, it's highly recommended to leave it as (60).

* **Check the following screen-shot for more details about:** the Lightbox logo url, Theme color, Button color, and Icon color.
![imgpsh_fullsize.png](https://bitbucket.org/repo/e46ngR/images/911502266-imgpsh_fullsize.png)

* **Use currency Code:**
Enabling this will show currency ISO code (USD) on the pop up, otherwise it will show the currency symbol ($).

* **Debug:**
This will allow more detailed logs once you encounter an issue with any store integration.

Go to System > Configuration > Under Sales > Checkout.com > General > Is PCI compliant:
This option can be only enabled if the store has all PCI related certificates.

* **Test credit cards details:**
http://developers.checkout.com/docs/server/api-reference/charges/simulator


#######################

Here is my checkout.com sandbox account credentials, please use on the admin panel after installation:

```
#!php

Secret Key: sk_test_cc9d99f9-4ddd-48bb-b623-cd42a26706d6
Publishable Key: pk_test_abbd3f8b-8001-4c46-b8a2-b82c4a5b70d1
```


* PCI version refers to the API inserted form fields., while the Non PCI version refers to the JS pop up.


* Please note the (PCI version) requires a validation on the "Phone number" field length to be >= 6, I remember that I opened an issue (#72) for phone number field length validation at the last Easy Checkout QA day but I guess it was marked as an enhancement.