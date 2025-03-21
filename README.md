# To-Do List Application

## Setup Instructions

### Using Docker

1. Clone the repository:

    ```bash
    git clone <repository-url>
    cd to_do_app
    ```

2. Copy the `.env.example` file to `.env`:

    ```bash
    cp .env.example .env
    ```

3. Build and start the containers:

    ```bash
    docker-compose up --build -d
    ```

4. Run the migrations:

    ```bash
    docker-compose exec app php artisan migrate
    ```

5. Seed the database:

    ```bash
    docker-compose exec app php artisan db:seed
    ```

6. The application will be running on `http://localhost:8000`.

### Without Docker

1. Clone the repository:

    ```bash
    git clone <repository-url>
    cd to_do_app
    ```

2. Install dependencies:

    ```bash
    composer install
    ```

3. Copy the `.env.example` file to `.env` and update the database configuration if necessary:

    ```bash
    cp .env.example .env
    ```

4. Set up MySQL database:

    - Create a database named `todo`.
    - Update the database configuration in `.env` if necessary.

5. Run the migrations:

    ```bash
    php artisan migrate
    ```

6. Seed the database:

    ```bash
    php artisan db:seed
    ```

7. Start the application:

    ```bash
    php artisan serve
    ```

8. The application will be running on `http://localhost:8000`.
