# TinkerList Technical Test App

Welcome to the TinkerList Technical Test App!

This application serves as a demonstration of my coding skills for the TinkerList technical test.

## API Documentation

Please refer to the [API Documentation](https://documenter.getpostman.com/view/21608971/2s93m32Nkz) for detailed information on how to interact with the API endpoints.

## Requirements

To run this application, ensure that you have Docker set up and running on your machine. Follow the steps below to get started:

1. Clone the repository:

   ```bash
   git clone 
2. Change to the project directory:
   ```bash 
   cd tinkerlist
3. Create a copy of the .env.prod file and rename it to .env:
   ```bash 
   cp .env.prod .env
4. Create a copy of the .env file and rename it to .env.testing:
   ```bash 
   cp .env .env.testing
5. ```bash 
   docker-compose build
6. ```bash 
   docker-compose up -d
7. ```bash 
   Access the application at http://localhost:8070.
8. Run Unit Test
9. ```bash
   php artisan test tests/Unit/
