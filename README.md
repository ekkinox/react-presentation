# Reactive PHP presentation

### Related presentation slides

Presentation can be found [here](http://slides.com/ekkinox/deck-2#/).

### Installation

```
$ composer install
```

### Examples

#### Regular vs Parallel downloading

You can run and compare both downloading examples.

Regular:
```
$ php batch/regular_download.php
```
Parallel (React loop):
```
$ php batch/parallel_download.php
```

#### WebSocket chat (based on Ratchet)

Run server
```
$ php bin/server.php
```
Then open web/client.html in your browser.