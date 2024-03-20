# Healthwatch Feedback

Adds "rate and review" support for Health and Social Care services in WordPress.

For support please contact Phil Thiselton (dtw) at phil@healthwatchbucks.co.uk

## Description

Local Healthwatch (LHW) have a duty to "[obtain] the views of local people about their needs for, and their experiences of, local care services". (https://www.legislation.gov.uk/ukpga/2007/28/part/14/crossheading/local-involvement-networks)

This plugin was created to facilitate that process. It adds support for managing and displaying Health and Social Care services in WordPress and allows anonymous/public users to "rate and review" these services.

NOTE: this plugin was originally created so that the display of services could be customised. However, with the integration of CQC data, this customisation is now limited. Local service categories and descriptors must align with the CQC taxonomies.

Not Gutenberg compatible.

## Features

### Manging Services
* `local_services` post type, which adds multiple additional fields for storing service information, including fields for the Healthwatch Bucks Dignity in Care project, which has now ended
* `service_types` taxonomy - name, slug, category, description, icon (through the WordPress media selector)
* `cqc_reg_status` "hidden" taxonomy - contains 5 default terms that partially align with CQC terminology
* `cqc_inspection_category` "hidden" taxonomy - contains 23 default terms that exactly align with CQC terminology/taxonomies
* Support to check existing service information, and preview/import new services, via the CQC API. See below for details.

### Rate and Review
* A customised comment form on `local_services` posts that provides "rate and review" fields, including provider and LHW responses
* Star rating system including overall and recent averages
* Includes schema.org markup support via JSON-LD (search engines can crawl and display rating information)
* Simple moderation tools for new "rate and review" comments
* Notification email to service providers when new comments are approved and published
* Seven widgets for displaying dynamic review content
* Uses Google ReCaptcha 2 for form submissions

### CQC API Integration
The plugin uses the CQC API to help populate and maintain `local_services` posts. This is administrated using the CQC Location ID, which is a (somewhat) unique identifier. For example, 'RXQ02' is the location code for Stoke Mandeville Hospital. With this you can find the location in the API (https://api.cqc.org.uk/public/v1/locations/RXQ02) and on the CQC website (https://www.cqc.org.uk/location/RXQ02).

With the CQC Location ID set for a `local_services` post, the plugin will look up and display information in the post edit screen, alongside existing post meta data, which can be used to update the local record. There are also checks and alerts for 'deregistration' (i.e. when a CQC registration fundamentally changes) both in the edit post screen and in bulk via a weekly cron job. The results of the bulk check are reported via email and via a WordPress dashboard widget.

New `local_services` posts can also be created in bulk via the 'CQC Data Import' tool in the admin interface. CQC registrations can be queried by Inspection category (P1 = Dentists) and imported in batches of between 5 and 50.

The tool automatically sets the following post data:
* post title to the organisation name
* excerpt based on `service_types` and `cqc_inspection_category`
* published date to the CQC registration date

The additional fields provided by the `local_services` post type are also populated, as are the values for the three associated taxonomies.

### Shortcodes

See this document for more information (https://healthwatchbucks.sharepoint.com/:w:/g/EUddNnm6oVdOu-LEm0P3zn0BfCApNSHQ_PAgWCoe7wmJbg)

* WordPress shortcodes for:
  * [new_service] - wrap an existing service's URL; adds a callout advising a new service at this location (useful for registration changes) - e.g. [new_service]https://mysite.net/services/new[/new_service]
  * [dual_reg] - wrap a CQC Location ID; adds a callout advising a dual registration and linking to the other provider on the CQC website - e.g. [dual_reg]1-11111111111[/dual_reg]
  * [multi_reg] - wrap a comma-separated list of local_service post IDs; adds a callout advising multiple providers running services at this location (useful for dentists that share premises) - e.g. [multi_reg]12345,67890[/multi_reg]
  * [multi_serv] - wrap a comma-separated list of local_service post IDs; adds a callout advising multiple services run by a single provider at this location (useful for large sites like hospitals) - e.g. [multi_serv]46079[/multi_serv]

## Installation

1. Upload the 'hw-feedback' folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Do not delete it.

## Frequently Asked Questions

*Will deactivating or deleting the plugin mean that feedback isn't displayed on the website?*

Yes.

*Will deactivating or deleting the plugin delete the feedback from the website?*

No. As soon as you re-upload and/or activate the plugin, the feedback will reappear.

## License
Unless otherwise specified, all the plugin files, scripts and images are licensed under GNU General Public License version 2, see http://www.gnu.org/licenses/gpl-2.0.html.

## Dependencies
None

## Contributors

Original code (circa 1.1.0) by jasoncharlesstuartking - former WordPress developer, now Google Ad Grant guru https://kingjason.co.uk/
