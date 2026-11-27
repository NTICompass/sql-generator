src_dir = src/
tester_bin = vendor/bin/tester
tests_dir = tests/
coverage_name = $(tests_dir)coverage.html
php_bin := $(shell if command -v php84 >/dev/null 2>&1; then echo php84; else echo php; fi)
phpstan_bin = vendor/bin/phpstan
php_cs_fixer_bin = vendor/bin/php-cs-fixer

.PHONY: test coverage clean phpstan
test:
		@$(tester_bin) -p $(php_bin) -C $(tests_dir)

coverage:
		@$(tester_bin) -p $(php_bin) -C -d zend_extension=xdebug --coverage $(coverage_name) --coverage-src $(src_dir) $(tests_dir)

clean:
		@rm -f $(coverage_name)

phpstan:
		@$(phpstan_bin) analyse --memory-limit=3G

check-cs-fixer:
		@$(php_bin) $(php_cs_fixer_bin) check --diff

cs-fixer:
		@$(php_bin) $(php_cs_fixer_bin) fix