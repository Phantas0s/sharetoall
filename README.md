# Sharetoall

This project is abandoned.

## Installation 

1. Clone the repository
2. Create a new config.env file at the root of the project - these are environment used by the file `app/config/parameters.yml`. 
Environment variables are docker friendly.

## Testing

The best is to use the commands in the `build.xml`

Unit test: `docker-compose exec php bin/phing generate-fixtures`
Acceptance test: `docker-compose exec -u 1000 php bin/phing test-acceptance`
