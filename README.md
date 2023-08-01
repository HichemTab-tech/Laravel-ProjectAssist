# Laravel-ProjectAssist

**Laravel-ProjectAssist** is a comprehensive toolkit designed to streamline and enhance your Laravel project development process. This repository serves as a valuable resource for Laravel developers, offering a collection of utilities, helpers, and functionality that simplify common tasks and provide increased productivity.

## Features

### Enhanced Console Commands
Empower your command-line interface with a set of custom commands tailored to accelerate your Laravel project workflows. From scaffolding components to automating repetitive tasks, these commands boost your productivity and make development more efficient.

### Custom Service Providers
Discover a range of custom service providers that seamlessly integrate with your Laravel application. These providers extend the functionality of your project, offering additional features and integrations to simplify common development challenges.

### Utility Classes
Access a suite of utility classes that provide handy methods and functionalities for various aspects of Laravel development. From date handling and form processing to text manipulation and logging, these classes save you time and effort by encapsulating common functionalities into reusable components.
##
With "Laravel-ProjectAssist" at your disposal, you can expedite your Laravel project setup, reduce boilerplate code, and focus on building robust and scalable applications. Whether you are starting a new project or looking to optimize your existing Laravel application, "Laravel-ProjectAssist" provides the essential tools and resources to streamline your development workflow and improve overall productivity.

## Installation

```
composer require hichemtab-tech/laravel-project-assist
```

## Usage

### Repository maker

The Repository Maker is a feature of this library that facilitates the creation of repository classes using the command :
```
php artisan make:repository UsersRepository
```
This command generates a new class that extends the **\App\Repositories\Repository** base class.

Repository classes serve the purpose of organizing a group of methods under a specific theme or domain. For example, you can create a **UsersRepository** class that focuses on methods related to users' models and data.

By utilizing the **UsersRepository** class, you can conveniently centralize and manage all user-related functionality within a cohesive structure. This approach enhances code organization and maintainability.

Feel free to leverage this feature to create dedicated repository classes for various entities in your application, ensuring clear separation of concerns and efficient management of related methods and data.

### Trait Maker

The Trait Maker is another useful feature provided by this library. It simplifies the creation of traits that can be used to encapsulate reusable functionality across multiple classes.

To create a trait, you can utilize the command:
```
php artisan make:trait MyTrait
```
This command generates a new trait class that can be easily imported and used within your application.

Traits are a powerful way to extract common methods and behavior into separate reusable units. By using traits, you can avoid code duplication and enhance code organization. Traits can be applied to multiple classes, enabling them to inherit and use the functionality defined within the trait.

Make use of the Trait Maker to create traits for specific functionalities or behaviors that can be shared across various classes within your application. Enjoy the benefits of code reusability and maintainability that traits provide.

### DataClass Maker
The DataClass Maker is a convenient tool provided by this library to facilitate the creation of data classes. These data classes serve as containers for organizing and passing data between different components or parts of your application.

To create a data class, you can utilize the command:
```
php artisan make:data-class TeamMember
```
This command will prompt you to add fields to the data class. You have two options for adding fields:

#### Adding fields one by one
You can add fields individually by specifying their type and name using the following structure: `field type, field name`. For example, you can input `string,id` to add a field of type string with the name 'id'.

#### Adding fields in one line
Alternatively, you can add multiple fields at once by starting with a forward slash `/` followed by the field definitions. For example, you can input `/string id, bool good, int number` to add fields of types string, boolean, and integer with the respective names.

The DataClass Maker will generate a class with the provided fields, along with a builder and getter methods for easy access and manipulation of the data. This enables you to conveniently pass all the required project data in a single object, such as ProjectData, to be easily utilized within the component.

Leverage the power of data classes to organize and streamline your data management and transfer within your application. Simplify your code and improve maintainability by using the DataClass Maker to generate structured data classes with ease.

### Lang file Maker
This commande generates a lang file with the name you provide in the command, the lang file will be created with many copies for each lang directory (languages supported by your application).

```
php artisan make:lang validation
```
For example, if you have in your lang directory, the following files:
```
├── en
│   └── ...
├── fr
│   └── ...
└── ar
    └── ...
```
After creating the lang file with the command above, you will have the following structure:
```
├── en
│   └── ...
│   └── validation.php
├── fr
│   └── ...
│   └── validation.php
└── ar
│   └── ...
    └── validation.php
```
So this commande will help you to create a lang file in all languages supported by your application, without manually creating a copy of the lang file in each language directory.

### .env.example generator

When you're in development, you may change the `.env` file a lot,
and in each release you may need to create a `.env.example` to know how to deal with your new environment later,
but manually creating it everytime is hard and takes some time, but using this commande you will automatically generate the coresponding `.env.example` :

```bach
php artisan env:example
```

The commande will generate the `.env.example` file with all the keys and values of the `.env` file, hidding the fields that have sensitive data.
In .env you must mark the fields of sensitive data with the `#env_hide` suffix, for example:

```dotenv
APP_NAME="My App"
APP_ENV=local
APP_KEY=base64:HnA7smlksdfhqfjlskdjp6bQscsfsdfsgtqsd1oM=#env_hide
APP_DEBUG=true

DB_CONNECTION=mysql
DB_HOST=127.0.0.1#env_hide
DB_PORT=3306#env_hide
DB_DATABASE=laraval_project#env_hide
DB_USERNAME=root#env_hide
DB_PASSWORD=12345678#env_hide
```

The `.env.example` file will be generated as follows:

```dotenv
APP_NAME="My App"
APP_ENV=local
APP_KEY=*****#env_hide
APP_DEBUG=true

DB_CONNECTION=mysql
DB_HOST=*****#env_hide
DB_PORT=*****#env_hide
DB_DATABASE=*****#env_hide
DB_USERNAME=*****#env_hide
DB_PASSWORD=*****#env_hide
```

### Customization
If you want to customize those command files, you can publish them using the following command:
```Bash
php artisan vendor:publish --provider=HichemtabTech\LaravelProjectAssist\ProjectAssistServiceProvider
```

## License

[MIT](https://github.com/HichemTab-tech/Laravel-ProjectAssist/blob/master/LICENSE)
