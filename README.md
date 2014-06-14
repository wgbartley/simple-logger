1. Clone the repo on your server.
2. Make sure the `logs` directory is writable by your web server.
3. Adapt `firmware/application.ino` to point to your server.
4. Install the [PHP stats extension](http://www.php.net/manual/en/book.stats.php) if you wish to get fancy stats in `/stats`.
5. If you're going to use `/stats`, you will probably need to edit the PHP/HTML with updates for the file names you are logging to.
