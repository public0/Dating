# Vanilla php Dating app

## Installation

RUN ``` git clone https://github.com/public0/Dating.git ```


cd into the project 
If composer is installed globally
RUN ``` composer install ```

RUN ``` php -S localhost:8000 ```

In case you don't have a redis server working

RUN ``` docker run --name dating-redis-server -p 6379:6379 -d redis ```

I'm assuming who is testing this has a mysql server running they should create the database ```date_test``` and run the migration (connection details are in bootstrap/app.php) no .env files or config filesused for this project

Using postman or any other similar HTTP clients access to run migrations
http://localhost:8000/public/migrate

I'd like to start by adding that I initially understood this was to be a no framework to be used project so that's how i did it. With that said I am using eloquent, I did implement my own container, router, bootstrap and i did go with the MVC pattern.

## Database Design and Implementation

### Database Schema 


The migration schemas are in app/database/ and the seeders in app/database/seeders

### users
- id (PK)
- username (VARCHAR)
- email (VARCHAR)
- password (VARCHAR)
- sex (CHAR)
- updated_at (TIMESTAMP)
- created_at (TIMESTAMP)

### conversations
- id (PK)
- user1_id (FK -> users.id)
- user2_id (FK -> users.id)
- updated_at (TIMESTAMP)
- created_at (TIMESTAMP)

### messages
- id (PK)
- conversation_id (FK -> conversations.id)
- body (TEXT)
- user_id (FK -> users.id)
- updated_at (TIMESTAMP)
- created_at (TIMESTAMP)

### Relationships
- conversations.user1_id -> users.id
- conversations.user2_id -> users.id
- messages.conversation_id -> conversations.id
- messages.user_id -> users.id

In a dating app only 2 people can talk so a conversation is mainly 2 ids while messages is the message sent and who sent the message (NOTE: messsages should probably be saved in a nosql instead of a RDBMS). The more risky decision I made that I assume some might not agree with is that I decided that for the conversations table user1_id is always smaller than user2_id in order to make it let's say more robust when querying.

## Endpoints

In bootstrap/app.php i added all the routes

#### The migration endpoint (suggest starting with this one) normally this would be ran with php artisan migrate in the case of laravel
http://localhost:8000/public/migrate

``` $this->router->get('/migrate', [UserController::class, 'migrate']); ```

#### POST Register
http://localhost:8000/public/register

``` $this->router->post('/register', [UserController::class, 'register']); ```

`POST data`

`required input: first_name`

`required input: last_name`

`required input: email`

`required input: password`

`required input: sex`

#### POST Login
http://localhost:8000/public/login

``` $this->router->post('/login', [UserController::class, 'login']); ```
`POST data`

`required input: email`

`required input: password`


#### GET Logged in user details
http://localhost:8000/public/user

``` $this->router->get('/user', [UserController::class, 'show']); ```

#### Search by first_name, last_name (this should problably be done in something like elasticsearch)
http://localhost:8000/public/search?term=Ju

``` $this->router->get('/search', [UserController::class, 'search']); ```

#### Post send message
http://localhost:8000/public/message

``` $this->router->post('/message', [UserController::class, 'sendMessage']); ```

`POST data`

`required input: to`

`optional input: message`

#### GET paginated messages 
http://localhost:8000/public/messages?id=4&page=1

``` $this->router->get('/messages', [UserController::class, 'messages']); ```

#### GET TOP profiles 
http://localhost:8000/public/profiles

``` $this->router->get('/profiles', [UserController::class, 'topProfiles']); ```


### Top 5 profiles
```
SELECT u.id AS user_id,
    u.first_name,
    u.last_name,
    COUNT(c.id) AS conversation_count
FROM users u
LEFT JOIN 
    conversations c
ON 
    u.id = c.user1_id OR u.id = c.user2_id
GROUP BY 
    u.id, u.first_name, u.last_name
ORDER BY 
    conversation_count DESC
LIMIT 5
```
Since I initially assumed this would be a more barebones approach (instead of using a framework ex. Laravel) I assumed you wanted to see the query more than just how i made relations between models so i decided to write the actual query instead.
As far as i can tell it's a pretty straightforward query unless i'm completely off, we just order desc by conversation_count and we limit the results to 5, I also cached the results for this in redis, this being the only endpoint i used redis on.

### Redis Caching
My caching approach as visible in UserRepositoy->topProfiles is pretty much i set a ttl of 600 seconds if it expires it returns the data from the db and if it's not expired it takes it from the redis cache. 
Note: the 600 seconds should be in a config file, as well as the `top_profiles` key some might say

## Notes
1. This being my own implementation I am aware many things are missing that laravel provides such as input validation (although illuminate/database does provide sanitization against sql injection), a more robust middleware and error handling, Authorization, better routing, DTO (in the form of API Resources), a better container implementation than my own rudimentary one and much more.

2. I am aware I am not using any .env files or config files in order to hide credentials and other sensitive data 
