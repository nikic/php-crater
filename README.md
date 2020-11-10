PHP Crater
==========

This is like [rust-lang/crater](https://github.com/rust-lang/crater), but for PHP. Well, a very, very cheap rip-off of crater really.

I'm using this to run PHPUnit tests for the top composer packages against a sanitizer-instrumented debug build of PHP, and looking for any crashes, assertion failures, sanitizer violations and leaks that show up.

Usage
-----

It's unlikely that this will work for anyone but me, but the basic usage is:

```sh
# Clone top 2k packages into repos/ (~20GB):
php download.php 0 2000

# Run using PHP build in ~/php/php-src-asan:
runner/run.sh ~/php/php-src-asan repos/ > log

# Look for interesting parts of the log:
cat log | grep "AddressSanitizer\|Segmentation\|Assertion \`"
```
