## Custom skeleton with user permissions control

### This is an example of custom framework with users and permissions control

---
### First launch
1. `make start` - will run `docker-compose up -d`
    - Launch container with PHP 7.4, Nginx, MySQL 5.7
2. `make setup` - will run (*on the first launch wait 30-120 sec. while database is created in `data/mysql`*):
    - Composer install
    - Migrations
    - Seeds
- If you want to run application without docker, run `composer install` in `src` directory, this will install composer dependencies, run migrations and seeds

### App configuration
- URL: `http:localhost:777/api/{endpoint}`
- DB inside docker container:
    - host: `permissions-db`
    - port: `3306`
    - user: `root`
    - pass: `root`
- DB outside docker container (locally):
    - host: `localhost` or `127.0.0.1`
    - port: `33067`
    - user: `root`
    - pass: `root`

### Other commands
- `make first-run` - runs both `start` and `setup` commands, but may fail on slow computers because migrations may run earlier that database is created
- `make stop` - exit application and stop docker container
- `make destroy` - destroy docker image
- `make rebuild` - destroy image, build image, run dependencies

**Tips:**
- Migrations and seeds are run on every `composer install` and `composer update`
- Migrations history is saved and already created/updated tables will not be effected twice
- Seed can be run only in local env., if any of dependant table has data seed will not be run
- Application can be tested with `postman_example` by importing postman collection and environment
    - Basic auth is used to authenticate application
    - Seeded users passwords are the same - `Str0nPassw0rd`
    - To test permissions endpoints with different users just change email in environment
        - sys_admin@example.com
        - admin@example.com
        - user1@example.com
        - user2@example.com

---
### What has been done

1. Basic framework functionality held in `core` folder (no third-party libraries used, composer is used only to ensure better OOP practices)
    - Boostrap
    - Basic ORM Query Builder `whereQuery()`, `select()`, `get()` for many, `first()` for one, `orderBy`, etc. methods
    - Automatic database migrations and seeds (*for local env. only*)
    - Simple Dependency Injection
    - Exceptions handler
    - Routes handling by simple array routing
2. Application `config`
    - Application variables (environment)
    - Database credentials
    - Middleware list
    - Seeds list
3. Database control functionality held in `database` folder
    - Migrations using SQL queries
    - Seeds for local (development) environment
    - Object based tables entities
    - Tables repositories
4. Business logic in `app` folder
    - Users control
      - Self view
      - Self update
    - Full users control:
      - List users
      - Fetch one user by uuid
      - Create user with roles and personalized permissions
      - Patch user
      - Delete user
    - Partial Role control
      - Create roles with assigned permissions
      - List roles
      - Fetch one role by uuid
      - Delete roles
    - Permissions can be added to any controller with `PermissionTrait`
      - Validate if user has needed permissions by calling method `hasPermission()` providing `Request` object and permission
      - Validate if user is not accessing/editing himself by calling method `isNotSelfModify()` to avoid user giving extra permissions to himself or self deletion
    - User permissions are handled in two ways
      - User can have as many roles as needed and have permissions granted to these roles
      - User can be granted extra permissions that do not belong to his roles
      - Permissions can also be denied to user personally even if one of his roles is granted with these permissions
    
---
### What has not been done or could be done better 
**Not done :-(**
- Role update/patch not created
- Role permissions and User permissions update / patch / delete has not been done
- Validation if permission granted when creating User/Role has not been implemented

**Could be done :-)**
- Validation if user who is granting permissions to another user/role has these permissions (*currently user can create new user with higher permissions and use it as a workaround*)
- Something more - there are no limits to perfection ;)

---
### CAUTION!

**This is not production ready application**

Some important functionality is missing:
- Data validation in most of the controllers methods
- ORM protection against SQL injection
- Abstract Enum class functionality (*all Enums are simple collections of constants*)
- Correct exceptions handling (*only a few Api exceptions directly extending \Exception are created*)
- Many other functionality needed for safe and high quality application