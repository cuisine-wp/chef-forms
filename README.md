Chef Forms - Form Builder
===========================

Chef Forms offers a way to build your own forms. Highly customizable and extendable with different plugins. Chef Forms tries to offer the best UI possible for your end-users.


---

## Requirements

| Prerequisite    | How to check | How to install
| --------------- | ------------ | ------------- |
| PHP >= 5.4.x    | `php -v`     | [php.net](http://php.net/manual/en/install.php) |



## Features

* Easily create forms
* Add as many e-mail notifications
* All mails send with SMTP (Mandrill)
* Inline form validation
* Instant error-feedback for the end-user.
* Completely OOP


## Installing

Clone the git repo - `git clone https://bitbucket.org/chefduweb/chef-forms.git` or install with composer:

`composer require chefduweb/chef-forms`

Composer will also install Chef Forms' dependency (which is [Cuisine](https://github.com/chefduweb/cuisine) ).

After you have all the files you need to install Chef Forms like a regular WordPress plugin:

1. move the files to wp-content/plugins
2. get into the WordPress admin and go to plugins
3. Check if Cuisine is running ( Chef Forms needs Cuisine )
4. Activate Chef Forms.
