[![PHP](https://img.shields.io/badge/PHP-8.2.2-blue)](https://www.php.net/)

[![Laravel](https://img.shields.io/badge/Laravel-10.1.13-red)](https://laravel.com/) 

![License](https://img.shields.io/badge/License-MIT-blue)

# University Data Importer

## Table of Contents

- [Introduction](#introduction)
- [Installation](#installation)
- [Usage](#usage)
- [Configuration](#configuration)
- [License](#license)

## Introduction

The project aims to handle large CSV file's data efficiently by dividing them into smaller chunks. It provides functionality to process each row of the CSV data and perform various operations on the data.

The main features of the project include:

* Uploading the CSV file
* Dividing a large CSV file into smaller chunk files
* Processing each row of the CSV data
* Performing custom operations on the data
* Storing the processed data into the database or generating output files
## Installation

Follow these steps to install and set up the project:

1. Clone the project repository to your local machine.

`git clone <repository_url>`

2. Navigate to the project directory.

`cd project-directory`

3. Install the project dependencies using Composer.
`composer install`

4. Set up the database configuration by creating a .env file. You can use the .env.example file as a template.

5. Generate the application key.

`php artisan key:generate`

6. Migrate the database to create the necessary tables.

`php artisan migrate`

7. Start the development server.

`php artisan serve`

The project is now installed and ready to use.


## Usage

To use the project, follow these steps:

1. Access the project in your web browser using the provided URL.

2. Use the provided interface to trigger the CSV processing functionality.

3. Upload the CSV file and submit it.

4. CSV file will be Placed in the specified directory. By default, it should be public/uploads.


5. Monitor the progress of the CSV processing and view the generated output.

## Configuration

The project provides configuration options that can be adjusted according to your needs. You can find the configuration file at `config/csv.php`.

The available configuration options include:

* `input_directory`: The directory where the input CSV file should be placed.
* `output_directory`: The directory where the chunk CSV files will be generated.
* `chunk_size`: The number of rows in each chunk file.
* `header_row`: The row number that contains the header in the input CSV file.
Adjust these configuration options as required for your project.

## License

This project is licensed under the MIT License.



