# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [1.8.2] - 2020-11-26
### Changed
- Fix PHP debug notices for uninitiated variables

## [1.8.1] - 2020-06-18
### Changed
- Updates for PHP7.0 compatibility

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
