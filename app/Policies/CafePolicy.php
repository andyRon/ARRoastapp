<?php

namespace App\Policies;

use App\Models\Cafe;
use App\Models\Company;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CafePolicy
{
    use HandlesAuthorization;
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * 管理员或超级管理员可以新增咖啡店
     * @param User $user
     * @param Company $company
     * @return bool
     */
    public function create(User $user, Company $company)
    {
        if ($user->permission == 2 || $user->permission == 3) {
            return true;
        } else if ($company != null && $user->companiesOwned->contains($company->id)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 管理员、超级管理员或者拥有该咖啡店所属公司则可以更新该咖啡店
     *
     * @param User $user
     * @param Cafe $cafe
     * @return boolean
     */
    public function update(User $user, Cafe $cafe)
    {
        if ($user->permission == 2 || $user->permission == 3) {
            return true;
        } else if ($user->companiesOwned->contains($cafe->company_id)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 管理员、超级管理员或拥有该咖啡店所属公司则可以删除该咖啡店
     *
     * @param User $user
     * @param Cafe $cafe
     * @return boolean
     */
    public function delete(User $user, Cafe $cafe)
    {
        if ($user->permission == 2 || $user->permission == 3) {
            return true;
        } else if ($user->companiesOwned->contains($cafe->company_id)) {
            return true;
        } else {
            return false;
        }
    }
}
