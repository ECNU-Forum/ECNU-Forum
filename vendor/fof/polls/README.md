# Polls by FriendsOfFlarum

![License](https://img.shields.io/badge/license-MIT-blue.svg) [![Latest Stable Version](https://img.shields.io/packagist/v/fof/polls.svg)](https://packagist.org/packages/fof/polls) [![OpenCollective](https://img.shields.io/badge/opencollective-fof-blue.svg)](https://opencollective.com/fof/donate) [![Patreon](https://img.shields.io/badge/patreon-datitisev-f96854.svg?logo=patreon)](https://patreon.com/datitisev)

A [Flarum](http://flarum.org) extension. A Flarum extension that adds polls to your discussions.

### Installation

```sh
composer require fof/polls
```

#### Migrating from ReFlar Polls

Make sure you've updated to the latest `reflar/polls` version and run `php flarum migrate` BEFORE installing `fof/polls`.
You will not be able to install this extension if you have a version of ReFlar Polls older than v1.3.4 as well.

```sh
$ composer require reflar/polls
$ php flarum migrate
$ composer require fof/polls
```

### Updating

```sh
composer update fof/polls
```

### Metadata update

To improve performance, Polls calculates and stores the number of votes when it changes.

As long as the extension is active, Polls will automatically keep those numbers up to date and you don't need to do anything.

If you are updating from a version prior to 0.3.3, if you disabled the extension for a while or if you made manual changes to the database you should run the following command to refresh the numbers:

```sh
php flarum fof:polls:refresh
```

You can only run the command when the extension is enabled in the admin panel.

### Links

[<img src="https://opencollective.com/fof/donate/button@2x.png?color=blue" height="25" />](https://opencollective.com/fof/donate)
[<img src="https://c5.patreon.com/external/logo/become_a_patron_button.png" height="25" />](https://patreon.com/datitisev)

- [Packagist](https://packagist.org/packages/fof/polls)
- [GitHub](https://github.com/packages/FriendsOfFlarum/polls)

An extension by [FriendsOfFlarum](https://github.com/FriendsOfFlarum).