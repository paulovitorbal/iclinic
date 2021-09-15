##Instalation

###Considerations:
* You are running linux or macOs;
* You have docker properly installed;

### Environment configuration
The very first step is to configure your environment.

There is an example file where you can copy and fill with the desired values.

Or you can find a copy of the .env file on: [private bin](https://encryp.ch/note/?556e21d181a33e20#BofSHnC4mYvtH1FFkDDeTh1MLSnUdqYAq3fW15f92D77)

By using the the password: `mjk4KXY2tcx*puh9khj`

The file will cease to exist on 21/09/2021.

### Step-by-step guide
You can use the make commands available (more on that later) and install the packages using composer;

```
make composer install
```
Then run the migrations by using the following command: `docker compose run php-fpm php artisan migrate:fresh` and you should see something like:
```
$ docker compose run php-fpm php artisan migrate:fresh
Dropped all tables successfully.
Migration table created successfully.
Migrating: 2021_09_11_153254_create_prescriptions_table
Migrated:  2021_09_11_153254_create_prescriptions_table (28.77ms)
```


