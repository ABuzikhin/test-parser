# Test Parsers Project 

This project presents a parsing technic based on getting HTML via Chrome (in headless mode) with recognizing based on XPath/CSSpath for the parser.

The logic of creating the product based on the product URL is located in   \App\Service\ParsingService::parse

Implementation for the concrete parsers in App\Service\Parsers namespace.

In case a successfully parsed URL is, the application creates new App\Entity\Product and saves the URL, received HTML, and serialized parsed data in App\Entity\ParsedUrl. Otherwise, it saves the error and status in the ParsedUrl.

## Getting Started

1. To start the project run `make start`
2. Open php container shell `make enter`
3. Execute init script `sh ./init.sh`
4. Open `http://localhost` in your favorite web browser

## Stop Container

Run `make stop` to stop the Docker containers.


## Run tests

To run tests you can use command `make tests`
