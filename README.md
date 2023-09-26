# Healthwatch Feedback

Adds "rate and review" support for Health and Social Care services in WordPress.

## Description

Local Healthwatch (LHW) have a duty to "[obtain] the views of local people about their needs for, and their experiences of, local care services" (https://www.legislation.gov.uk/ukpga/2007/28/part/14/crossheading/local-involvement-networks)

This plugin was created to facilitate that process. It adds support for managing and displaying Health and Social Care services in WordPress and allows anonymous/public users to "rate and review" these services.

NOTE: this plugin was originally created so that the display of services could be customised. However, with the integration of CQC data, this customisation is now limited. Local service categories and descriptors must align with the CQC taxonomies.

Not Gutenberg compatible.

## Features

Adds:

### Manging Services
* ```local_services``` post type, which adds multiple additional fields for storing service information, including fields for the Healthwatch Bucks Dignity in Care project, which has now ended
* ```service_types``` taxonomy - name, slug, category, description, icon (through the WordPress media selector)
* ```cqc_reg_status``` "hidden" taxonomy - contains 5 default terms that partially align with CQC terminology
* ```cqc_inspection_category``` "hidden" taxonomy - contains 23 default terms that exactly align with CQC terminology/taxonomies
* Support to check existing service information, and preview/import new services, via the CQC API (based on CQC location IDs)

### Rate and Review
* A customised comment form on ```local_services``` posts that provides "rate and review" fields, including provider and LHW responses
* Star rating system including overall and recent averages
* Includes schema.org markup support via JSON-LD (Search engines can crawl and display rating information)
* Simple moderation tools for new "rate and review" comments
* Notification email to service providers when new comments are approved and published

### Shortcodes
* WordPress shortcodes for:
  * [signposts_menu] - outputs a graphical menu based on the taxonomy
 

## Installation

1. Upload the 'hw-feedback' folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Do not delete it.

## Frequently Asked Questions

- Will deactivating or deleting the plugin mean that feedback isn't displayed on the website?

Yes.

- Will deactivating or deleting the plugin delete the feedback from the website?

No. As soon as you re-upload and/or activate the plugin, the feedback will reappear.

## Contributors

jasoncharlesstuartking
