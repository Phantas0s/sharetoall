Sharetoall
===========================================================

## Installation 

1. Clone the repository
2. Create a new config.env file at the root of the project - these are environment used by the file `app/config/parameters.yml`. 
Environment variables are docker friendly.

## Deployment

See [here](https://laverna.cc/app/#/notes/f/notebook/q/7105aae8-072b-9745-285b-9b0914d6ed86/show/84cd301c-8fdb-669a-f4e5-9b60d83c3879)

## Testing

The best is to use the commands in the `build.xml`

Unit test: `docker-compose exec php bin/phing generate-fixtures`
Acceptance test: `docker-compose exec -u 1000 php bin/phing test-acceptance`
