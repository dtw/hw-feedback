# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]
### Added
- feedbackstarrating() now takes an array of arguments including size and colour which correspond to CSS styles

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
