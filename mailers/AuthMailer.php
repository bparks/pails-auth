<?php

class AuthMailer extends \Pails\ActionMailer\Mailer
{
    protected function new_registration(User $user, $activation_url)
    {
        $model = ['user' => $user, 'activation_url' => $activation_url];
        return $this->mail($user->email, "Welcome", $model);
    }

    protected function lost_password(User $user, $new_password)
    {
        $model = ['user' => $user, 'new_password' => $new_password];
        return $this->mail($user->email, "Your new password", $model);
    }

    protected function lost_password_request(User $user, $confirm_url, $deny_url)
    {
        $model = ['user' => $user, 'confirm_url' => $confirm_url, 'deny_url' => $deny_url];
        return $this->mail($user->email, "Your Password Reset Request", $model);
    }

    protected function resend_activation(User $user, $activation_url)
    {
        $model = ['user' => $user, 'activation_url' => $activation_url];
        return $this->mail($user->email, "Activate your UserPie Account", $model);
    }
}
