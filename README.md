PHP Crater
==========

This is like [rust-lang/crater](https://github.com/rust-lang/crater), but for PHP. Well, a very, very cheap rip-off of crater really.

I'm using this to run PHPUnit tests for the top composer packages against a sanitizer-instrumented debug build of PHP, and looking for any crashes, assertion failures, sanitizer violations and leaks that show up.

Usage
-----

It's unlikely that this will work for anyone but me, but the basic usage is:

```sh
composer install

# Clone top 2k packages into repos/ (~20GB):
php download.php 0 2000

# Specify the list of repositories to test:
ls -d repos/*/* > repo_list

# Run using PHP build in ~/php/php-src-asan:
runner/run.sh ~/php/php-src-asan repo_list > log

# Look for interesting parts of the log:
php analyze.php log
```
