install:
	composer install
	sh ./tools/install_php_tools.sh

fix:
	./tools/phpcsfixer/vendor/bin/php-cs-fixer fix

phpstan:
	./tools/phpstan/vendor/bin/phpstan analyse -c phpstan.neon src

phpmd:
	./tools/phpmd/vendor/bin/phpmd src/ text .phpmd.xml

phpcs:
	./tools/phpcs/vendor/bin/phpcs -s --standard=phpcs.xml.dist

phpbeautify:
	./tools/phpcs/vendor/bin/phpcbf --standard=phpcs.xml.dist ./src ./tests

phpcpd:
	./tools/phpcpd/vendor/bin/phpcpd src/

lint:
	make phpmd
	make phpcpd
	make phpcs
	make phpstan
	make fix
	make phpbeautify
