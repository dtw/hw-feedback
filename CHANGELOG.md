# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

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

## [Released]

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
