<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use Config\Services;

class Setup extends Controller
{
    private function authorize($key): void
    {
        // Change this secret key before deploying
        if ($key !== 'my-secret-key') {
            exit('Unauthorized');
        }
    }

    public function index($key = null)
    {
        $this->authorize($key);

        try {
            $migrate = Services::migrations();
            $migrate->latest();
            echo "Migrations completed.<br>";

            $seeder = Services::seeder();
            $seeder->call('DatabaseSeeder');
            echo "Seeder completed.<br>";

            echo "All done.";
        } catch (\Throwable $e) {
            echo "<pre>" . $e->getMessage() . "</pre>";
        }
    }

    public function migrate($key = null)
    {
        $this->authorize($key);

        try {
            $migrate = Services::migrations();
            $migrate->latest();
            echo "Migrations completed successfully.<br>";

            $all = $migrate->getHistory('default');
            echo "<br>Applied migrations:<br>";
            foreach ($all as $row) {
                echo "- " . $row->version . " " . $row->class . "<br>";
            }
        } catch (\Throwable $e) {
            echo "<pre>" . $e->getMessage() . "</pre>";
        }
    }
}