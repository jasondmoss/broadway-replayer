othillo/broadway-replayer
=========================

Yet another event replayer for [broadway/broadway](https://github.com/qandidate-labs/broadway)

[![Build Status](https://travis-ci.org/othillo/broadway-replayer.svg?branch=master)](https://travis-ci.org/othillo/broadway-replayer)

## Motivation
Thanks to the [EventStoreManagementInterface](https://github.com/qandidate-labs/broadway/blob/master/src/Broadway/EventStore/Management/EventStoreManagementInterface.php)
in Broadway replaying can be as easy as:

```php
$eventStore->visitEvents(new Criteria(), new CallableEventVisitor(function($domainMessage) use ($projector) {
    $projector->handle($domainMessage);
}));
```

This project provides a [EventBusPublishingVisitor](https://github.com/othillo/broadway-replayer/blob/master/src/EventStore/EventBusPublishingVisitor.php)
allowing you to register projectors to the event bus just as you would do for regular event handling in Broadway.

In addition it provides a [ReplayAwareInterface](https://github.com/othillo/broadway-replayer/blob/master/src/ReplayAwareInterface.php) which
provides you with hooks to prepare for and finalize replaying.

## Installation

```
$ composer require othillo/broadway-replayer
```

## Example

Check the [ReplayerTest](https://github.com/othillo/broadway-replayer/blob/master/test/Functional/ReplayerTest.php)
to see how this replayer works.

## License
This project is licensed under the MIT License - see the LICENSE file for details
