# BMT DOCs
- Docker Compose
    - UP
        ``` bash
        docker-compose up -d 
        ```
    - Down
        ``` bash
        docker-compose down
        ```
    - Build
        ```bash
        docker-compose up -d --build
        ```
    - Update
        ```bash
        docker-compose run --rm composer update
        ```
    - npm run Dev
        ```bash
        docker-compose run --rm npm run dev
        ```
    - Migrate
        ```bash
        docker-compose run --rm artisan migrate
        ```
        ```
    - Test
        ```bash
        docker-compose run --rm artisan test
        ```

- Documentation Generate
    ```bash
    docker-compose run --rm artisan scribe:generate
    ```
- IDE Helper Generate
    ```bash
    docker-compose run --rm artisan ide-helper:generate && docker-compose run --rm artisan ide-helper:models --write --reset --write-mixin && docker-compose run --rm artisan ide-helper:meta
    ```
- MYSQL
    - Creating and Selecting a Database
    ```bash
    CREATE DATABASE DataBaseName;
    ```
    - Create a New User
    ```bash
    CREATE USER 'newuser'@'localhost' IDENTIFIED BY 'password';
    ```
    - Grant Permissions
    ```bash
    GRANT ALL PRIVILEGES ON * . * TO 'newuser'@'localhost';
    FLUSH PRIVILEGES;
    ```
