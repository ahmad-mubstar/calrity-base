Cities: EasyCheckout Integration
=====

Cities is a new API, developed and maintained by ShopGo, that provides users with data for MENA countries and cities in both Arabic and English.

Website: [http://cities.shopgo.io](http://cities.shopgo.io)

Full documentation: [https://bitbucket.org/shopgo/cities](https://bitbucket.org/shopgo/cities)



#### Important Remarks:


###### General

- The API supports MENA countries **only**: AE, BH, DJ, DZ, EG, IQ, JO, KW, LB, LY, MA, MR, OM, PS, QA, SA, SD, SO, SY, TN, YE.

	Check the "Dropdown Option" section below to learn how this is handled with dropdowns. Additionally, check "Why Does Not Cities Support Non-MENA Countries?", also found below.

- The lists are more or less a customized copy of the Aramex Location API, with duplicate and obscure names dropped. One exception is Algeria, where the official list was condensed into the few dozen most popular cities instead of the over a thousand entries available from Aramex.

- All city names are translated and can be accessed in both languages.

- Results are cached on the server, which means that the API will respond faster the more it is used over time.

- The merchant has the option to display cities as a dropdown list or an autocomplete text field.


###### Autocomplete Option

- Users can type in their language of choice, even when they forget to change the keyboard language.

- Results will show the closest matches to users' input. This makes it flexible and spelling-tolerant.

- More popular cities have the extra advantage of being presented before other cities.

- The merchant can decide whether to limit users to the list of suggestions provided by the API or can alternatively allow them to enter anything.

- In the case that the server goes down or takes too long to respond, any value the user types _will_ be accepted , _even if the merchant chooses to limit users to the API suggestions_, so as not to stop the checkout process.


###### Dropdown Option

One problem with this option is that with large data sets:

1. The user has to scroll down long lists hunting for the desired name.

1. The UI can become unresponsive as the browser renders endless HTML elements.

This is not much of an issue for MENA countries because the number of entries is usually manageable. Many Non-MENA countries, on the other hand, have a much larger list of cities, so the points above become a considerable concern.

That said, if the merchant is willing to take the risk and still wishes to use a dropdown, they should note the following: For the sake of consistency, the API **will** provide lists for **non-MENA** countries if the dropdown option is picked. Keep in mind that these lists are:

1. Provided by a local copy (on our servers) of Aramex's Location API. This local copy is **not** planned to be updated on a regular basis.

1. **Not** translated.

1. **Not** checked for errors or missing and duplicate entries.


###### Which Option Is Recommended?

If the merchant's target market is **exclusively** MENA countries, then choosing the dropdown option is not a bad idea, mostly for performance considerations. Otherwise, the autocomplete option is the recommended one.


###### Why Does Not Cities Support Non-MENA Countries?

1. The number of cities for non-MENA countries is obviously huge. Checking the validity of the lists provided by Aramex, or any other source, is both time-consuming and labor-intensive. We decided that, at this point, this is not a path we are willing to invest in.

1. Shipping couriers usually depend on zipcodes or (their equivalent) in developed countries, which means that city names are not all that important. This is why wherever an address is needed for a developed country, websites typically allow users to enter anything in the text field so long as they get the zipcode right. Because many less developed non-MENA countries do require city names, this means each country needs to be manually checked and handled differently. Therefore, we decided to focus on the MENA region for the time being.

1. On a minor note, the large number of entries places the server under extra pressure when autocomplete results are required.


#### For developers:

Below is a list of the files modified to make the integration happen. Anything between the comments: `CITIES INTEGRATION: START` and `CITIES INTEGRATION: END` can be safely commented out or deleted without affecting the rest of the code.


1. **/app/code/community/Shopgo/EC/Model/Resources/Citiesdatasources.php**

	- Add `cities_api` as an option in the admin panel.


1. **/app/code/community/Shopgo/EC/etc/system.xml**
	- Add `cities_username` textfield in the admin panel.
	- Add `autocomplete_allow_any_value` dropdown in the admin panel.


1. **/app/code/community/Shopgo/EC/etc/config.xml**
	- Set dafault `cities_username` textfield value in the admin panel.
	- Set dafault `autocomplete_allow_any_value` textfield value in the admin panel.


1. **/js/Shopgo/EC/easycheckout.js` & `/js/Shopgo/EC/script.js**
	- Front-end integration with UI components. Most of the work is done here.


1. **/app/design/frontend/base/default/template/shopgo/ec/index.phtml**
	- Make `cities_username`, `store_code`, and `autocomplete_allow_any_value` available for JS scripts.


1. **/app/design/frontend/base/default/template/shopgo/ec/billing.phtml**
	- Add `<input type="hidden" name="billing[city_en]" id="billing:city_en"/>`.


1. **/app/design/frontend/base/default/template/shopgo/ec/shipping.phtml**
	- Add `<input type="hidden" name="shipping[city_en]" id="shipping:city_en"/>`.
