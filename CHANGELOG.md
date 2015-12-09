### 2.1.1 2015-12-09

* Entries are filterable now
* Form::get refactored to work with the existingForms variable
* Added support for no notifications
* Added password field support
* Form and field classes are now properly filterable
* The defaulValue of a field is now filterable before and after Tags
* Custom validation support
* Minor bugfixes.



### 2.1.0 2015-11-25

* Datefields are working on the front-end now
* Big formbuilder fixes. Also fetching corresponding form-ids
* New deletable-boolean added to fields.
* The Entry class is now extendable and filterable.
* The Notification class is now extendable and filterable.
* Notification values can now contain post-meta tags.
* Some code-cleanup and styling fixes
* Various bugfixes to multifields
* Minor bugfixes.



### 2.0.9 2015-11-12

* Added support for form-redirecting
* Added persistent session-storage for redirecting
* Added support for max entries
* Added support for validity between dates
* Validation Errors are now overwritable as a Cuisine JS-var
* Added {{ entry_id }}-tag to notifications
* Minor bugfixes.



### 2.0.8 2015-10-25

* Added Tag support in notifications
* Added Address-field validation
* Added the option of overwriting the Notification HTML by template.
* Each field-class can now submit there own html to a notification
* Fixed a bug where confirmation messages weren't being saved.
* Minor bugfixes.


### 2.0.7 2015-09-30

* Fixes textarea validation
* Adds tag-support for hidden fields.
* Address-fields done neatly
* Address-field styling
* Minor bugfixes.



### 2.0.6 2015-09-16

* Added the ability to submit the form from outside Form.js
* Fixes address validation
* Fixes non-required validation
* Fixes the column just displaying the last 5 forms.
* Fixes checkboxes fields, which were not displaying properly
* Minor bugfixes.



### 2.0.5 2015-09-09

* Added an address field-type.
* Added getters for the form-object.
* Fixed a bug with field labels
* Fixed a bug where choicefields rendered the wrong options
* Minor bugfixes.



### 2.0.4 2015-08-17

* Added a formbuilder for adding forms through code
* Added button-filters
* Changed the namespaces of builders, to better reflect what they do
* Minor bugfixes.


### 2.0.3 2015-08-15

* Added a settingspage for Mandrill
* Added smart default-value tags
* Added flexible form-settings panels
* Further minor bugfixes


### 2.0.2 2015-08-05

* Sending via STMP with Mandrill
* Field validation bugfix
* Further minor bugfixes


### 2.0.1: 2015-07-10

* Notifications working
* Sending feedback
* Added a loader


### 2.0.0: 	2015-07-06

* First public release
