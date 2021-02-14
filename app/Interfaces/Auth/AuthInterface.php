<?php


namespace App\Interfaces\Auth;


interface AuthInterface
{
    public function register();

    public function login();

    public function loginAsGuest();

    public function logout();

    public function refresh();

    public function update();

    public function forgetPassword();

    public function resetPassword();

    public function requestEmailValidation();

    public function validateEmail();

}
