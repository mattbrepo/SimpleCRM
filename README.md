# SimpleCRM
A simple CRM project to test Laravel and Vue.

**Language: PHP / Javascript**

**Start: 2023**

## Why
I wanted to test out the combo [Laravel](https://laravel.com/) + [Vue](https://vuejs.org/). I chose to build a simple [CRM](https://en.wikipedia.org/wiki/Customer_relationship_management) to manage Companies, Contacts, Orders and so on. It's not a finished project since my goal was to try these technologies and become familiar also with [npm](https://www.npmjs.com/) and modern PHP and Javascript development.

Login view (accessed on 127.0.0.1:8000):
![login](/images/login.jpg)

Companies view:
![companies](/images/companies.jpg)

## Useful command
Start server and watch javascript/scss changes:
```
php artisan serve
npm run watch
```

Basic Tinker:
```
php artisan tinker

>>> $User = new App\Models\User;
>>> $myuser = $User->find(1);
```

Prepare for production:
```
npm run production
APP_DEBUG=false in .env
```

## Command used to create the project
1. Create the site
```
composer create-project laravel/laravel simple-crm
```

2. Get in the project folder
```
cd simple-crm
```

3. Add Sanctum
```
composer require laravel/sanctum
```

4. Publish the Sanctum configuration and migration files
```
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
```

5. Create database _simple_crm_ in phpMyAdmin (I used XAMPP)

6. Run database migrations
```
php artisan migrate
```

7. Manual creation of the first user in the DB
```
user: a
email: a
password: $2y$10$EOty9ldgWCHHgocqOJw.MujyEx88.CmOJKmx/BLhQCYKFxdzwBswm (a)
```

8. Create user controller
```
php artisan make:controller UserController
```

9. Test token creation http://localhost:8000/api/login with following JSON
```
{
	"email": "a",
	"password": "a"
}
```

10. Add Laravel UI support
```
composer require laravel/ui
```

11. Add Vue.js boilerplate
```
php artisan ui vue
```

12. Compile Vue.js using a "javascript command line"
```
npm install
npm run dev
```

**use _npm run watch_ to keep updated your vue, css files**

13. Install Vue Router (using javascript)
```
npm install vue-router
```

14. Install Vuex
```
npm install vuex
```

15. Install vuex-persistedstate
```
npm install --save vuex-persistedstate
```

16. Make user group model, controller, migration
```
php artisan make:model UserGroup -mcr
```

18. Migration for new column 'user_group_id' in users
```
php artisan make:migration add_column_to_users
php artisan migrate
```

19. DB Seeder
```
php artisan db:seed
```

20. Install bootstrap-vue (js)
```
npm install bootstrap-vue
```

21. Make Country model, controller, migration
```
php artisan make:model Country -mcr
php artisan migrate
php artisan make:seeder CountrySeeder
php artisan db:seed
```

22. Migration to add column deleted_at in user_groups table (soft-deleting)
```
php artisan make:migration add_column_to_user_groups
php artisan migrate
```

23. Migration to add column deleted_at in users table (soft-deleting)
```
php artisan make:migration add_column_soft-deleting_to_users
php artisan migrate
```

24. Make Company model, controller, migration
```
php artisan make:model Company -mcr
php artisan migrate
php artisan make:seeder CompanySeeder
php artisan db:seed
php artisan make:migration create_companies_user_groups_table
```

25. Install javascript moment package
```
npm install moment
```

26. Make migration to add created_by to Company
```
php artisan make:migration add_column_created_by_to_companies
php artisan migrate
```

27. Make Contact model, controller, migration
```
php artisan make:model Contact -mcr
php artisan migrate
php artisan make:seeder ContactSeeder
php artisan db:seed
```

28. Make migration to create contacts_user_groups table
```
php artisan make:migration create_contacts_user_groups_table
php artisan migrate
```

29. Make Product model, controller, migration
```
php artisan make:model Product -mcr
php artisan migrate
php artisan make:seeder ProductSeeder
php artisan db:seed
```

30. Make migration to create products_user_groups table
```
php artisan make:migration create_products_user_groups_table
php artisan migrate
```

31. Make Order/OrderProduct/License model, controller, migration
```
php artisan make:model Order -mcr
php artisan make:migration create_orders_user_groups_table
php artisan make:model OrderProduct -mcr       (migration manually renamed to create_orders_products_table)
php artisan make:model License -mcr
php artisan migrate
php artisan make:seeder OrderSeeder
php artisan db:seed
```

