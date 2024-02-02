## How to use for the first time
- ``` git clone ```
- ``` composer install ```
- create your database and add it in DB_DATABASE variable inside .env file, use this command if for copying .env.example became .env ``` cp .env.example .env ```
- ``` php artisan key:generate ```
- don't forget to change the APP_URL if ure using virtual env like laragon
- ``` php artisan migrate ```
- ## shield
    - ``` php artisan shield:install ```

## How to use shield plugin for resource
- create model and migration for resource, then do artisan migrate
- then create resource with make:filament-resource ModelNameResource --genereate
- php artisan shield:generate --resource=ModelNameResource -> this will create Policy for this model and add default permissions to database



## HOWTO
```bash
    #fetching and init project
    git fetch origin && git merge origin/main && composer2 install

    #migrate the database
    php artisan migrate

    #for creating permission and policy file 
    php artisan shield:generate --resource=UserResource,StudentResource,CompanyResource,ClassroomResource,SubjectResource,TopicSettingResource,AssessmentMethodSettingResource,AssessmentResource

    #ignore the changes via git
    git restore .

```


## TODO
- [ ] Make custom command to setup the project for the first time, such as creating default role, install composer
    - ``` composer install ```
    - ``` cp .env.example .env ```
    - ``` php artisan key:generate ```
    - don't forget to change the APP_URL if ure using virtual env like laragon
    - ``` php artisan migrate ```
    - ## shield
        - ``` php artisan shield:install ```
    - etc
- [ ] Add softdelete in company table