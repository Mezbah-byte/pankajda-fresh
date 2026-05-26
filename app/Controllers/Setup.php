<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use Config\Services;

class Setup extends Controller
{
    public function index($key = null)
    {
        // Change this secret key
        if ($key !== 'my-secret-key') {
            exit('Unauthorized');
        }

        try {

            /*
            |--------------------------------------------------------------------------
            | RUN MIGRATIONS
            |--------------------------------------------------------------------------
            */

            $migrate = Services::migrations();

            $migrate->latest();

            echo "Migrations completed.<br>";

            /*
            |--------------------------------------------------------------------------
            | RUN SEEDERS
            |--------------------------------------------------------------------------
            */

            $seeder = Services::seeder();

            // Change UserSeeder to your seeder class name
            $seeder->call('UserSeeder');

            echo "Seeder completed.<br>";

            echo "All done.";

        } catch (\Throwable $e) {

            echo "<pre>";
            echo $e->getMessage();
            echo "</pre>";
        }
    }
}