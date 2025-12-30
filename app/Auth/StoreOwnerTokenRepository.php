<?php

namespace App\Auth;

use Illuminate\Auth\Passwords\DatabaseTokenRepository as BaseDatabaseTokenRepository;
use Illuminate\Contracts\Auth\CanResetPassword;

class StoreOwnerTokenRepository extends BaseDatabaseTokenRepository
{
    /**
     * Create a new token record.
     *
     * @param  \Illuminate\Contracts\Auth\CanResetPassword  $user
     * @return string
     */
    public function create(CanResetPassword $user)
    {
        $emailid = $user->getEmailForPasswordReset();

        $this->deleteExisting($user);

        // We will create a new, random token for the user so that we can e-mail them
        // a safe link to the password reset form. Then we will insert a record in
        // the database so that we can verify the token within the actual reset.
        $token = $this->createNewToken();

        $this->getTable()->insert($this->getPayload($emailid, $token));

        return $token;
    }

    /**
     * Delete all existing reset tokens from the database.
     *
     * @param  \Illuminate\Contracts\Auth\CanResetPassword  $user
     * @return int
     */
    protected function deleteExisting(CanResetPassword $user)
    {
        return $this->getTable()->where('emailid', $user->getEmailForPasswordReset())->delete();
    }

    /**
     * Build the record payload for the table.
     *
     * @param  string  $emailid
     * @param  string  $token
     * @return array
     */
    protected function getPayload($emailid, $token)
    {
        return ['emailid' => $emailid, 'token' => $this->hasher->make($token), 'created_at' => now()];
    }


    /**
     * Determine if a token record exists and is valid.
     *
     * @param  \Illuminate\Contracts\Auth\CanResetPassword  $user
     * @param  string  $token
     * @return bool
     */
    public function exists(CanResetPassword $user, $token)
    {
        $record = (array) $this->getTable()
            ->where('emailid', $user->getEmailForPasswordReset())
            ->first();

        return $record &&
               ! $this->tokenExpired($record['created_at']) &&
                 $this->hasher->check($token, $record['token']);
    }

    /**
     * Determine if the given user recently created a password reset token.
     *
     * @param  \Illuminate\Contracts\Auth\CanResetPassword  $user
     * @return bool
     */
    public function recentlyCreatedToken(CanResetPassword $user)
    {
        $record = (array) $this->getTable()
            ->where('emailid', $user->getEmailForPasswordReset())
            ->first();

        return $record && $this->tokenRecentlyCreated($record['created_at']);
    }

    /**
     * Delete a token record by user.
     *
     * @param  \Illuminate\Contracts\Auth\CanResetPassword  $user
     * @return void
     */
    public function delete(CanResetPassword $user)
    {
        $this->getTable()->where('emailid', $user->getEmailForPasswordReset())->delete();
    }

    /**
     * Delete expired tokens.
     *
     * @return void
     */
    public function deleteExpired()
    {
        $expiredAt = now()->subSeconds($this->expires * 60);

        $this->getTable()->where('created_at', '<', $expiredAt)->delete();
    }
}

