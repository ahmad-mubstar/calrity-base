ShopGo EasyCheckout version 2.3.3  
================================== 

- Cities API Integration fixes (http/https request, send hostname)
- Disable "Place Order" button until all info is filled
- Replace checkout.com to checkoutcom.js (PCI only)
- Add validation on phone number digits (6 digits min.)

ShopGo EasyCheckout version 2.3.2  
================================== 
- Cities API integration

ShopGo EasyCheckout version 2.3.0  && 2.3.1
==================================
**********Fixes**********

- Fix text direction .
- Remove next button, and add 
- Fix error messages styling
- Fix Default Payment Method
- Translate Function
- City field in billing information box.
- Reduce redirect to success page.
- Fix modgit installation failed to create certain directory .
- Fix grand total still not showing while in Ipad portrait .
- Fix Checkout loading
- Fix Design issues
- Fix Console error .
- Fix the issue from Qa on V.2.2 .
- Fix failed email reminder on the place order button .
- Fix spacing when adding a note like the COD note.

**********enhancement**********

- Ability to change sub titles for payment and shipping information.
- Adding compatibility with cities for the next release .
- Enable delivery date option at Easy Checkout .
- Specify the date for the shipping in 24 hours .
- Adding Special Price .
- Remove state/ province for MENA countries .
- Adding new tab for ship to a different address .
- Add Default message for payment and shipping .
- Add date format optional  DD/MM/YYYY instead of MM/DD/YYYY



ShopGo EasyCheckout version 2.2.4
==================================

- Fixing jQuery, Prototype conflict.
- Updating API fetchCities error handling.

ShopGo EasyCheckout version 2.2.3
==================================

- Fixing apply coupon not updating payment.
- Fixing the main layout.
- Adding after body start and before body end support to allow 3rd party scripts.

ShopGo EasyCheckout version 2.2.2
==================================

- Performance and UI/UX improvements.
- Code enhancements and cleaning up.
- Enhanced backend configuration.
- Adding multiple city field input type (Autocomplete and Dropdown).


ShopGo EasyCheckout version 2.2.1
==================================

- Fixing canShowForUnregisteredUsers() function.
- Adding client side payment/shipping method selection validation.

ShopGo EasyCheckout version 2.2.0
==================================

- Fixing login popup not showing issue.
- Turn off canShowForUnregisteredUsers().

ShopGo EasyCheckout version 2.1.9
==================================

- Fixing major guest checkout block issue.
- Fixing reloadReview, reloadPayment 404 errors.

ShopGo EasyCheckout version 2.1.8
==================================

- Adding missing getTranslationData function.
- Replacing GE dinar one font with Bein sport font.
- Fixing city input field Arabic alignment.

ShopGo EasyCheckout version 2.1.7
==================================

- Updating checkUpdate helper function.
- Fixing update version info.
- Removing query string from baseURL.

ShopGo EasyCheckout version 2.1.6
==================================

- Updating translation.
- Fixing getPrimary Billing/Shipping Address bug.
- Fixing the translation issue.

ShopGo EasyCheckout version 2.1.5
==================================

- Adding skip cart feature.
- Adding clear cart feature.
- Adding Growl notification jQuery library.
- Adding citiesDataSource instead of useAramexAPI.
- Adding Citiesdatasources dropdown options.
- Adding the update feature.
- Removing lp2location old file.
- Adding extension version info in the backend.
- Replacing failure messages with console messages.
- Adding enable/disable consoleLog config option.
- Minor bug fixes (error log file).

ShopGo EasyCheckout version 2.1.4
==================================

- Fixing Paypal invalid token error (in case it was selected by default).
- Fixing review table responsive design.
- Updating review block custom text option.

ShopGo EasyCheckout version 2.1.3
==================================

- Fixing missing shipping method information bug.
- Pushing some code enhancement to handle saved addresses shipping rates.
- Fixing saveShipping when there is shipping_address_id.
- Updating review input validation rules.
- Fixing Paypal invalid token.
- Updating agreement check validation.


ShopGo EasyCheckout version 2.1.2
==================================

- Firefox dropdown Kendo UI issue fixed (updated to 2014.1.416).
- Review block custom text option added.



ShopGo EasyCheckout version 2.1.1
==================================

- Save order bug fixed.
- Fix missing shipping description bug.
- Fix SSL HTTPS cdn links bug.
- Fix SSL loading insecure content issue.
- Fix kendo CDN SSL HTTPS.
- Enabling secure url feature.



ShopGo EasyCheckout version 2.1.0
==================================

- Updated layout to handle design fallback.
- JS, CSS libraries were updated to use faster CDN instead of having them on the same server.
- Performance improvements CDN option, API cache.
- Minor/Major bug fixes (missing next button in shipping address, order placing functionality, regions and cities API, etc).
- Update Qty button was removed due to major shipping bug related to 3rd party APIs
- Some UI enhancements were added (city field loading icon, progress bar youtube like).
- Easy Checkout JS plugin was updated, enhanced and cleaned up.
- Alertify JS was replaced with the stock notifications.
- The review block was updated to render at page first time load without Ajax.