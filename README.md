Magelight
=========

Magelight is a lightweight MVC framework inspired by [Magento](http://magentocommerce.com) written ground-up in PHP 5.4.

Framework features
---------
- MVC architecture with module-based customization mechanisms
- Query builder and Active-Record models 
- Extensible controllers
- Extensible views with redefinable templates
- Native PHP syntax templating
- Object forgery with class redefinition that allows lazy dependency loading
- PHPUnit-based essentials for unit testing regardless static calls
- Robust module-based customizations:
    - Class redefinition and preferences that allow to extend class behavior through all application 
    - Document layout redefinition
    - Blocks template redefinition
    - Runtime method hooks (before, after) that allow to modify method arguments or result
    - XML-based configuration that can be overriden in modules
    - XML-based routing that can be overriden in modules same as config
    - Customizable L10N and I18N support

Distributed Modules
----------

####Admin:
- Application backend framework with simple customizable scaffolding
- Admin user management and access control

####Auth:
- User registration and authorization
- uLogin service support
- Restore password capability

####Core
- Main module providing following essentials
    - Document - an HTML document object as basic view
    - Pager - widget for paginations
    - Breadcrumbs - content path widget
    - Grid - a basic grid for displaying paginated table data
- Css and Js assets minification and merging mechanisms

####Geo
- City, Region and Country structured data and models
- Internationalized in EN, RU and UA languages

####Image
- Image model implementing miscellaneous image transformations

####Sitemap
- Self-crawling sitemap builder

####Visitors
- Visitors actions logging

####Webform
- Forms with bootstrap-based layout that can be either built with form constructor
or templated in an individual template.
- Elements and fields with fully-controllable attributes and content
- Asyncronous csubmit capability, frontend and backend validation
- Customizable validator and rules
- Localizable validation errors
- Support following fields:
    - Captcha - a simple captcha for registration form or other
    - Checkbox - a checkbox form element
    - File - a single generic file input
    - FilePretty - prettified file input
    - Input - generic input
    - InputAppended - an input with an addon
    - InputHidden - hidden input
    - InputMasked - input with configurable mask for email/phone/credit-card data input
    - InputPrepended - input with addon in the beginning
    - LabeledCheckbox - a checkbox with label
    - LabeledRadio - a radio input with label
    - PasswordInput - a generic input for password
    - Radio - generic radio input
    - ReCaptcha - a ReCaptcha captcha input with validation
    - Select - generic select box
    - Textarea - generic textarea

Requirements
---------

- PHP 7+

Installation
---------

- Checkout code with git.
- `include 'core.php'`
- Use Magelight!

Installation with composer
------------------
- Create `composer.json` file in your project root or add Magelight to your existing `composer.json`
```
{
  "name": "yourname/project",
  "description": "Your project",
  "repositories": [
    {
      "type": "package",
      "package": {
        "name": "rganin/magelight",
        "version" : "0.0.2",
        "source": {
          "url": "https://github.com/rganin/magelight",
          "type": "git",
          "reference": "0.0.2"
        },
        "autoload": {
          "files": ["core.php"]
        }
      }
    }
  ],
  "require": {
    "php": ">=7",
    "rganin/magelight": "*"
  }
}
```
- require `vendor/autoload.php` in your project
- create modules directory in your project or copy sample application structure `vendor/rganin/magelight/app/*`
  to your project directory
- do not forget to add your own modules directory by `\Magelight::app()->addModulesDir('modules/directory')` in your project