# Masquerade by FriendsOfFlarum

![License](https://img.shields.io/badge/license-MIT-blue.svg) [![Latest Stable Version](https://img.shields.io/packagist/v/fof/masquerade.svg)](https://packagist.org/packages/fof/masquerade)

The user profile generator. Includes:

- New tab on user profile to show masquerade profile with answers provided to configured fields.
- New tab on user profile for user to set up a masquerade profile.
- Add, update, delete and order profile fields in admin.
- Permission who can have a masquerade profile.
- Permission who can view a masquerade profile.
- Allowing you to configure forced redirection to make a (email verified) user complete the required fields.

## Installation

```bash
composer require fof/masquerade:*
```

## Update

```sh
composer require fof/masquerade:*
php flarum migrate
php flarum cache:clear
```

## Configuration

Enable the extension. Visit the masquerade tab in the admin to configure the fields. 

Be aware that the "Add new field" and "Edit field <foo>" toggle the field form when clicked.

Make sure you configure the masquerade permissions on the Admin Permissions tab to your needs.

## Updating from Flagrow

This extension replaces [Flagrow Masquerade](https://packagist.org/packages/flagrow/masquerade).

**Please backup your data before attempting the update!**

First make sure you installed the latest Flagrow release will all migrations applied:

```sh
composer require flagrow/masquerade
# Go to admin panel and check extension is enabled
php flarum migrate
```

(Instead of running the migrate command you can also disable and re-enable the extension in the admin panel)

Then upgrade from the old extension to the new one:

```sh
composer remove flagrow/masquerade
composer require fof/masquerade:*
```

When you enable the new extension, the permissions and the data from Flagrow Masquerade will be moved to FoF Masquerade.

## Links

- [Flarum Discuss post](https://discuss.flarum.org/d/5791)
- [Source code on GitHub](https://github.com/FriendsOfFlarum/masquerade)
- [Report an issue](https://github.com/FriendsOfFlarum/masquerade/issues)
- [Download via Packagist](https://packagist.org/packages/fof/masquerade)

An extension by [FriendsOfFlarum](https://github.com/FriendsOfFlarum)
