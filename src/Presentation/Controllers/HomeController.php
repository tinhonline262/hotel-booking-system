<?php

namespace App\Presentation\Controllers;

/**
 * Home Controller
 */
class HomeController extends BaseController
{
    public function index(): void
    {
        $this->render('pages.home', [
            'title' => 'Welcome to Hotel Booking System'
        ]);
    }

    public function about(): void
    {
        $this->render('pages.about', [
            'title' => 'About Us'
        ]);
    }

    public function contact(): void
    {
        $this->render('pages.contact', [
            'title' => 'Contact Us'
        ]);
    }

    public function submitContact(): void
    {
        $errors = $this->validate($_POST, [
            'name' => 'required',
            'email' => 'required|email',
            'message' => 'required|min:10'
        ]);

        if (!empty($errors)) {
            $this->render('pages.contact', [
                'title' => 'Contact Us',
                'errors' => $errors,
                'old' => $_POST
            ]);
            return;
        }

        // Process contact form (send email, save to database, etc.)

        $_SESSION['success'] = 'Thank you for contacting us! We will get back to you soon.';
        $this->redirect('/contact');
    }
}

