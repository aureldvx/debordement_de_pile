#!/bin/sh

CURRENT_DIR=$PWD
TOOLS_DIR="$CURRENT_DIR/tools"

set_phpcs_ruleset() {
	tools/phpcs/vendor/bin/phpcs --config-set show_warnings 0
	tools/phpcs/vendor/bin/phpcs --config-set installed_paths tools/phpcs/vendor/escapestudios/symfony2-coding-standard
}

launch_install() {
	cd "$TOOLS_DIR" || exit
	composer install
	cd "$TOOLS_DIR" || exit
	composer install
	cd "$TOOLS_DIR" || exit
	composer install
	cd "$TOOLS_DIR" || exit
	composer install
	cd "$TOOLS_DIR" || exit
	composer install
	cd "$CURRENT_DIR" || exit
	set_phpcs_ruleset
}

launch_install
