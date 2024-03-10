# Leaseweb rest API

The goal of this project is accomplish the requirements on the Backend Assessment.


## Running the application

- In the case of not having git in your local follow these steps
https://www.atlassian.com/git/tutorials/install-git
### First time Setup
```bash
git clone git@github.com:MarioGeovani/leaseweb.git
```

- Copy the `.env.example` to `.env` and edit accordingly

- Pull composer packages
- on the case of not having composer follow these steps
https://www.hostinger.com/tutorials/how-to-install-composer
```bash
./etc/bin/php composer install
```
  or
```bash
composer install
```
 - Note in case of Ubunto :
    - Go an issue with  ext-dg depedency
        - sudo apt-get update
        - php -v (check version to choose the correct php package version in my case 8.3)
        - sudo apt-get install php8.3-gd
    - Go an issue with ext-zip
         - sudo apt-get install php8.3-zip

    - And again
```bash
  ./etc/bin/php composer install
  ```
  or
   ```bash
  composer install
  ```

### First Time
```bash
sudo APP_PORT={the port you want the site to run}
./vendor/bin/sail up
```

- Run the migrations
```bash
./vendor/bin/sail artisan migrate
```

### Every New Instance
- Spin up the application
```bash
./vendor/bin/sail up
```

- if any issues on website local server plse check the local server port
and also add to your env

APP_PORT=89

- Go to webbrowser and in http:1270.0.1:89 (example) create the public key

-  If MySql is returning network issue doe same  port being used please
do :

```bash
docker-compose down --volumes
sail up --build
```

- And again

```bash
./vendor/bin/sail artisan migrate
```
 - Should be Up and running

 - to exit Sail just ctrl+c or
```bash
./vendor/bin/sail down
```

