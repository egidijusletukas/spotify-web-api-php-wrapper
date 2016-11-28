#!/usr/bin/env bash
ROOT=$(git rev-parse --show-toplevel)

echo "php-cs-fixer pre commit hook start"

PHP_CS_FIXER=$ROOT"/vendor/bin/php-cs-fixer"

HAS_PHP_CS_FIXER=false

if [ -x $PHP_CS_FIXER ]; then
    HAS_PHP_CS_FIXER=true
fi

if $HAS_PHP_CS_FIXER; then
    $PHP_CS_FIXER fix $ROOT/src --level=psr2;
    $PHP_CS_FIXER fix $ROOT/tests --level=psr2;
else
    echo ""
    echo "Please install php-cs-fixer, e.g.:"
    echo ""
    echo "  composer require --dev friendsofphp/php-cs-fixer"
    echo ""
fi

echo "php-cs-fixer pre commit hook finish"
