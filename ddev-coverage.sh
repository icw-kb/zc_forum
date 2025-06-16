#!/bin/bash
# Run tests with code coverage in DDEV

# Default to console output
OUTPUT_TYPE="${1:-console}"

case "$OUTPUT_TYPE" in
    "console")
        echo "Running tests with console coverage output..."
        ddev exec 'XDEBUG_MODE=coverage php artisan test --coverage'
        ;;
    "html")
        echo "Running tests with HTML coverage output..."
        ddev exec 'XDEBUG_MODE=coverage php artisan test --coverage-html=coverage/html'
        echo "Coverage report generated at: coverage/html/index.html"
        ;;
    "min")
        echo "Running tests with minimum coverage threshold..."
        ddev exec 'XDEBUG_MODE=coverage php artisan test --coverage --min=80'
        ;;
    "all")
        echo "Running tests with all coverage outputs..."
        ddev exec 'XDEBUG_MODE=coverage php artisan test --coverage-html=coverage/html --coverage-clover=coverage/clover.xml --coverage-cobertura=coverage/cobertura.xml'
        echo "Coverage reports generated in coverage/ directory"
        ;;
    *)
        echo "Usage: $0 [console|html|min|all]"
        echo "  console - Show coverage in terminal (default)"
        echo "  html    - Generate HTML coverage report"
        echo "  min     - Run with 80% minimum coverage threshold"
        echo "  all     - Generate all coverage reports"
        exit 1
        ;;
esac