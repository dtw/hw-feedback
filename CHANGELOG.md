# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Released]

## [3.2.1] - 2024-05-15
### Fixed
- comment UUID handling

### Changed
- date handling to use DateTime objects
- hw_feedback_get_rating function to take an ID not object

## [3.2] - 2024-04-28
### Added
- access to a raw copy of JSON results from the CQC API, since they can no longer be accessed with web browser
- a clean-up function to remove this raw copies
- cron job to run the clean-up function
- deactivate plugin action to run the clean-up function
- display ODS Code from CQC API (if available)
- minor code order/location fettling

## [3.1.3] - 2024-04-24
### Fixed
- obscure bug when a post has no thumbnail

## [3.1.2] - 2024-04-24
### Added
- error checks on CQC API calls

### Fixed
- API call error "Unspecified query parameter partnerCode is not allowed"

### Deleted
- uneeded hw_feedback_field_partner_code from plugin options

## [3.1.1] - 2024-04-22
### Added
- a UUID to the comment meta when feedback is submitted for a local_service (initial commit https://github.com/dtw/hw-feedback/commit/b4ba0befc03f41085a8e21bf676e75cf55da6c0b) - now sits alongside, rather than replacing, the previous implementation
- styles and scripts for new field

## [3.1] - 2024-04-22

This is a major release since the CQC API has been updated. Previously added ODS API features are now stable.

### Added
- authorisation support for new CQC API - add your new Syndication Product Subscription Key under HW Feedback -> Settings

### Changed
- minor fixes

## [3.0.1b] - 2024-03-22

This is marked as a beta release

### Changed
- minor fixes

## [3.0b] - 2024-03-22

This is marked as a beta release. Not something I would normally do but while all the functionality is here some of the housekeeping might need work.

### Added
- support for verifying local_services post metadata against the Organisation Data Service (ODS) API, including:
    - a new custom field (hw_services_ods_code) to store ODS Code - the unique identifier within the ODS API
    - new hidden taxonomy for ODS Role Codes (none to many of 195 codes provided in the API)
    - new hidden taxonomy for ODS Status (Active/Inactive and *Unmatched*)
    - bootstraping ODS Role Codes for *Unmatched* local_services posts based on CQC Inspection Categories
    - scheduled updates of local_services posts', including:
        - automatic 'best matching' of ODS Code, based on hw_services_postcode, ODS Role Codes and name
        - checking and reporting ODS Status changes via email
    - a list of possible matches for *Unmatched* local_services in the edit post screen, showing:
        - ODS Code, Name, name match percentage (using PHP similar_text), name Levenshtein distance, Last Updated, ODS API Link, plus Role Codes, Names and Start Dates
        - visual cues for name match % of 95% or more and a Levenshtein distance of less than 1

## [2.8.5] - 2024-03-19
### Changed
- very minor changes to syntax, comments and logging
- updated README

## [2.8.4] - 2023-09-28
### Fixed
- residual file reference

## [2.8.3] - 2023-09-28
### Added
- styles from scaffold-widget-tweaks

### Changed
- combined shortcode files into one

## [2.8.2] - 2023-09-27
### Changed
- check against a hardcode string to a Setting variable

### Fixed
- broken query in the widget-most-rated widget
- an incorrect reference in widget-list-service-types widget

## [2.8.1] - 2023-09-05
### Added
- throbber on CQC import tool

## [2.8] - 2023-08-18
### Added
- [multi_reg] shortcode - use when two or more services are co-located with DIFFERENT providers and disambiguation is needed
- [multi_serv] shortcode - use when two or more services are co-located with the SAME provider and disambiguation is needed
- CSS rules from scaffold-widget-tweaks

### Changed
- syntax changes to suppress PHP Notices

## [2.7.2] - 2023-06-20
### Added
- new menu icon

### Changed
- refactored hw_feedback_check_cqc_registration_status into a single function
- moved functions into separate file
- additional logging (debug.log)

## [2.7.1] - 2023-06-07
### Changed
- minor bug fixes
- code refactoring

## [2.7] - 2023-06-02
### Added
- support for NHS ODS (Organisation Data Service) codes - https://digital.nhs.uk/services/organisation-data-service
- column for ODS code in Local Services admin view (maybe temporary)
- key info from API shown for individual Local Services

### Changed
- comments now closed correctly when service Archived
- comments form and links hidden when comments are closed

### Deleted
- old references to commentor address and "who" was involved

## [2.6.2] - 2023-05-19
### Added
- checks to avoid Undefined Offset errors when creating new Local Services
- some logging

### Changed
- handling of checkbox changes in Local Services so they are stored correctly

## [2.6.1] - 2023-05-19
### Added
- styles for new tooltips

### Changed
- some hardcoded URL strings
- many text strings
- star rating explainer into tooltips
- spacing on the feedback form

## [2.6] - 2023-04-20
### Added
- support for "recent average" ratings for services (last 12 months)
- setting to enable/disable admin email reminders for missing contact emails (added in 2.5)
- per Local Service setting to "opt-out" of new comment emails - differentiates between missing email address and not wanting any emails

### Changed
- automatically disable comments (feedback) when a service's CQC Registration Status is set to "Archived"

### Removed
- contact, website and CQC location number columns from the Local Services admin screen

## [2.5] - 2023-02-22
### Added
- store an email address per provider
- moderation settings for comments - allows more consistent redaction
    - custom text for partially withheld comments - this text is appended
    - custom text for completely withheld comments - this text replaces the existing text
    - wipe data from comment fields containing personal information
- email alerts for providers when a new comment is published - can be enabled or disabled
    - custom footer for legal/privacy notices etc
    - custom from FROM name and address

### Changed
- fixed a "null on first run" checkbox in HW Feedback Settings
- minor layout changes in HW Feedback Settings

## [2.4] - 2023-02-17
### Added
- setting for Your Story target email address(es)
- new compact recent feedback widget - has no large panel, only sub-items

### Changed
- tabindex for accessibility purposes
- allow admin to set review score to zero
- feedback form field labels
- fixed edit link in new service emails
- fixed comments being completely disabled by meta_box removal

## [2.3] - 2022-08-19
### Added
- default service types for prisons and opticians and map P4 inspection code to prisons
- WP media selector can now be used for Local Services and Signpost icons
- general settings section in admin
- option to disable LHW ratings functions - does not affect public ratings (WIP)

### Changed
- default text on some categories
- renamed settings section in admin

## [2.2.4] - 2022-08-19
### Added
- cronjob now sets an excerpt if blank

### Changed
- minor text changes
- better handling of empty cache files (a good thing!)
- much improved layout for import screens
- fixed many PHP Notices
- import queries now check private posts

## [2.2.3] - 2022-08-14
### Added
- warnings for missing settings
- uninstall hook to delete options
- new setting to store API Cache path/directory

### Changed
- minor changes to fix PHP Notices
- Local Authority JSON file now stored in plugin root

## [2.2.2] - 2022-08-12
### Changed
- minor bug fixes and tweaks

## [2.2.1] - 2022-07-23
### Changed
- moved all menus settings to dash.php

## [2.2] - 2022-07-23
### Added
- settings page for the plugin
- CQC data import tool via CQC API
    - preview and add services based on their Primary Inspection Category code (uses taxonomy added in 2.1)
    - choose how many services to preview/add at a time (1, 5, 10, 20, 30, 40, 50)
    - ignores Services not marked as Registered
    - the Publication date of the local_service is set to the registration date with CQC
    - service_types are set automatically based on CQC Primary Inspection Category (e.g. "P1" is a **dentist**) or, in some case, the GAC category (e.g. "Care home service without nursing" is a **Care Home**)
    - (EXPERIMENTAL) adds a nice excerpt to the local_service

## [2.1.3] - 2022-08-10
### Changed
- fixed call to missing file
- fix slug rewrite in taxonomy

## [2.1.2] - 2022-07-22
### Changed
- fixed includes method

## [2.1.1] - 2022-07-09
### Changed
- fixed incorrect field name

## [2.1] - 2022-07-09
### Added
- automated email after registration update check has run
- admin guidance on how to process Deregistrations
- new taxonomy based on CQC Inspection Categories
- inspection categories are updated during the registration check introduced in 2.0 rc1
- new shortcode for dual registration

## [2.0.1] - 2021-07-07
### Changed
- fix critical error in cronjob function

## [2.0] - 2022-06-28
### Added
- activate and deactivate functions to better handle cronjob
- check cqc_reg_status changes on local_services save
- widgets and shortcodes from scaffold-widget-tweaks

### Changed
- cleaned up use of post_meta (deprecated)
- minor bug fixes to CQC API query feature
- updated content of cqc_location column in admin
- fixed outdated constructors
- layout of the "latest DIC visit" widget
- some CSS inline with rebrand (mostly in hw-scaffold themes)
- cqc_reg_status metabox to a dropdown

## [2.0 rc1] - 2022-06-09
### Added
- function to query CQC API to get location information
- button to copy data from API into local_service in edit screen
- function and cronjob to update cqc_reg_status from CQC API
- new taxonomy for cqc_reg_status, available terms are Registered, Deregistered, Not registered, Not applicable and Archived.
- new dashboard widget to show cqc_reg_status by local_services

### Changed
- show updated information in CQC Location column in admin
- mask phone numbers in Contact column in admin
- major changes to edit screen for local_services including: improved generation, reordered fields, show info from CQC API,
- enqueue copy_civicrm_subject_code to only run in admin
- new labels on local_services taxonomy
- The Great Function Rename 2022 (renamed functions in line with plugin name)

### Removed
- don't show County in Contact column in admin

## [1.9] - 2021-11-03
### Added
- column in comment admin to identify comments from NHS subnet

## [1.8.3] - 2021-09-08
### Added
- CiviCRM subject code prompt and copy to clipboard button
### Changed
- (Re-)Enable feed on Local Services

## [1.8.2] - 2020-11-26
### Changed
- fix PHP debug notices for uninitiated variables

## [1.8.1] - 2020-06-18
### Changed
- updates for PHP7.0 compatibility

## [1.8.0] - 2020-04-07
### Added
- new column in the admin area to show whether a hw or provider reply has been entered against a comment.

### Changed
- check the feedback_rating is numericbefore doing math on it

## [1.7.0] - 2020-02-10
### Changed
- strip URLs from comments
- fixed rounding for half stars
- changed wp_filter to allow URLs in responses
- updated bootstrap classes
- set small stars on DIC ratings

### Added
- a new field for hw_reply, distinct from provider replies

## [1.6.2] - 2020-01-28
### Changed
- fixed issue with fussy service URLs
- update multiple styles with altered colours to improve contrast based on WCAG2
- switched hex colour codes to colour names where possible (e.g. white)
- multiple changes to validate HTML

## [1.6.1] - 2020-01-21
### Added
- screen reader hints for ratings to all star ratings
- alt text to category images and aria hidden on some decorative links

## [1.6.0] - 2020-01-10
### Added
- feedbackstarrating() now takes an array of arguments including size and colour which correspond to CSS styles
- add code to disable rss feed on local_services posts

### Changed
- moved shortcode code to functions/
- rename post-types-taxonomies.php
- rename some clashing CSS classes
- merge styles from colours.css

### Removed
- remove unused form validation js

## [1.5.0] - 2020-01-06
### Changed
- feedbackstarrating() function now returns code rather than echos
- fixed privacy statement links
- admin interface now uses FontAwesome stars not gifs

## [1.4.0] - 2019-11-11
### Added
- getrating() function to generate count, total and average of rating for a service

## [1.3.0] - 2019-11-05
### Changed
- update to use feedbackstarrating() function throughout
- update how line breaks are handled in trimmed comments

## [1.2.0] - 2019-11-01
### Added
- feedbackstarrating() function to generate star ratings throughout the plugin and theme

## [1.1.0] - unknown
### Changed
- Removed unnecessary personal data fields from Feedback form

## [1.0.0] - unknown
### Added
- The first version.
