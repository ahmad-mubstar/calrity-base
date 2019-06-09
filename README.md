# SHOPGO RELEASE NOTES (V1.3)

All notable changes to this project will be documented in this file.
The format is based on [Hackmd.io]

Staging URL: http://clarity2.devshopgo.me
Reference ticket #: 

## [1.3.0] - Tue May 28 09:41:48 CEST 2019

> **Released by**
> This release aim is to add shopping cart responsive page 
> [name=Ahmad Rasmi][color=red]

### Functionality Bugs:
* Quick View minimum product quantity

### SEO
* Disallow Quickview urls by modifying robots.txt (#60434)


## [1.2.0] - Tue May 28 09:41:48 CEST 2019

> **Released by**
> This release aim is to add shopping cart responsive page 
> [name=Ahmad Rasmi][color=red]

### Functionality Bugs:
* Quick View Issue After Adding a Product With Disabled Redirect to Shopping Cart
* Favicon issue (#62307)

### Design Bugs
* Responsive design for the shopping cart table (#62587)
* Error messages images and icons
    * errors/default/images/
    * errors/default/page.phtml

### Configuration:
* Increase session time [Advanced > Admin > Security > Session Lifetime (seconds)]
* Fix email arabic templates 

## [1.1.0] - Mon Apr  8 13:22:38 CEST 2019


> **Released by**
> This release aim is to solves common bugs and install common requested extentions. 
> [name=Ahmad Rasmi][color=red]

### Functionality Bugs:

* Enable custom category list (#57997)
* Black Background Fav icon Issue for PNG images (#56823)
* Increase execution time & memory limit (#57362)
* Flipper is not working in case store has SSL (#44186)
* Symlinks notification message (#60851)
* Admin dashboard error message (#59644)
* Product > Quick View > Continue Shopping Button (#59289)
* ShopGo GeoIP Database Synchronize Error (#61248)


### Design Bugs
* Logo not clickable and disappears at some point (Reference: 56825)
* Logo overlapping mini cart when on right (EN view) and left (AR view)
* Style Editor > Image Flipper > 2 & 3 Columns > Quick View not aligned
* Adding verified by ShopGo as hidden
* Update footer copyright

### Additions:
* Shell exporter (#58128)
* Open Graph Protocol (#57258)

### Disabled:
* Admin Base URL (#58974)

### Extensions:
* Aschroder SMTP Pro 
* Plumrocket Twitter & Facebook
* Grid customization
* SMS Notifier
* EaDesign PDF
* Ebizmarts_Mailchimp4Magento-1.1.14
* Facebook Ads Extension v2.6.2
* PayFort latest version
* HyperPay 2.0.0 (#60733)
* Blue Jalepino order export (#61090)
* Aheadworks Product Color Swatches (#61126)

### Configuration:
* Enable static URLs for Media Content in WYSIWYG for Catalog
* Disabling both users and errors logs , under (System -> Configuration -> Advanced -> System -> Log & System -> Configuration -> Advanced -> Developer -> Log Settings )
* System > Advanced > Settings > General > Web > Url Options > Add Store Code to Urls = No
* System ->Advanced -> Settings -> Catalog Section -> Catalog -> Search Engine Optimizations -> Use Canonical Link Meta Tag For Categories = Yes
* System ->Advanced -> Settings -> Catalog Section -> Catalog -> Search Engine Optimizations -> Use Canonical Link Meta Tag For Products to = Yes
* Easy Checkout > General Configuration > Default Country: Saudi Arabia / Default ZIP/Postal Code: null
* Easy Checkout > Arabic configuration scope > General Configuration and UX (Look and Feel) Configuration translation
* Role: Support/Admin: All. Reason: With custom access, admin cannot upload images. Browse icon disappears due to inability to save role with OAuth Authorized Tokens permission


### Default information:

* General Contact:  (Sender Name: Info | Sender Email: info@store.com)
* Sales Representative: (Sender Name: Sales | Sender Email: sales@store.com)
* Customer Support: (Sender Name: Customer Support | Sender Email: support@store.com)

### Performance:
* Changing directory attributes for frequently accessed files

### Security:
* Change default Admin dashboard URL from (/admin TO /dashboard)


### Post-testing changes
* Remove orders, customers and searches
* Clean system.log, exception.log, errors_log and reports
* Clean old logs from the following tables (log_customer, log_visitor, log_visitor_info,log_visitor_online,log_summary, log_url, log_url_info, log_quote, index_event, report_viewed_product_index, report_event, Log Settings,catalog_compare_item, report_event)
