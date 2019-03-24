<?php

namespace App\Models;

use App\Models\Subscriptions\ActiveSubscription;
use App\Models\Traits\HasUuid;
use App\Models\Translations\Project;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens,
        Notifiable,
        HasUuid;

    const   SUPER_ADMIN_STAFF_MEMBER = 'super-admin';
    const   SENIOR_STAFF_MEMBER = 'senior-staff';
    const   JUNIOR_STAFF_MEMBER = 'junior-staff';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * @var array
     */
    protected $dates = [
        'created_at', 'updated_at', 'activated_at', 'last_login',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * @return HasOne
     */
    public function profile()
    {
        return $this->hasOne(UserProfile::class);
    }

    /**
     * @return HasOne
     */
    public function meta()
    {
        return $this->hasOne(UserMeta::class);
    }

    /**
     * @return BelongsToMany
     */
    public function organizations()
    {
        return $this->morphedByMany(Organization::class, 'entity', 'collaborators')
            ->withPivot([
                'is_owner',
                'is_valid',
                'role_id',
                'validation_token',
            ])
            ->withTimestamps();
    }

    /**
     * @return BelongsToMany
     */
    public function teams()
    {
        return $this->morphedByMany(Team::class, 'entity', 'collaborators')
            ->withPivot([
                'is_owner',
                'is_valid',
                'role_id',
                'validation_token',
            ])
            ->withTimestamps();
    }

    /**
     * @return BelongsToMany
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class)
            ->withTimestamps();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function projects()
    {
        return $this->morphedByMany(Project::class, 'entity', 'collaborators');
    }

    /**
     * @return HasOne
     */
    public function activeSubscription()
    {
        return $this->hasOne(ActiveSubscription::class);
    }

    /**
     * @return HasMany
     */
    public function memos()
    {
        return $this->hasMany(Memo::class, 'user_id', 'id');
    }

    /**
     * @param $id
     *
     * @return bool
     */
    public function isTeamMember($id)
    {
        $teams = array_column($this->teams->toArray(), 'id');

        return array_search($id, $teams) > -1;
    }

    /**
     * @param string $role
     *
     * @return bool
     */
    public function hasRole($role)
    {
        $roles = array_column($this->roles->toArray(), 'alias');

        return array_search($role, $roles) > -1;
    }

    /**
     * @param string $v
     */
    public function setPassword($v)
    {
        $this->attributes['password'] = Hash::make($v);
    }

    /**
     * @return string
     */
    public function getPasswordToken()
    {
        return md5($this->password);
    }

    /**
     * Checkes is a user has a given role type.
     *
     * @param $roleTypeAlias
     *
     * @return bool
     */
    public function checkUserHasRoleType($roleTypeAlias)
    {
        return in_array($roleTypeAlias, array_column($this->roles->toArray(), 'role_type'));
    }

    /**
     * @param $roleAlias
     *
     * @return bool
     */
    public function checkUserHasRole($roleAlias)
    {
        return in_array($roleAlias, array_column($this->roles->toArray(), 'alias'));
    }

    /**
     * @param $roleAlias
     *
     * @return bool
     *
     * @internal param User $user
     */
    public function checkSecurityClearance($roleAlias)
    {
        $role = Role::where('alias', $roleAlias)->firstOrFail();

        $userRoleClearances = array_column($this->roles->toArray(), 'security_clearance');
        foreach ($userRoleClearances as $item) {
            if ($item <= $role->security_clearance) {
                return true;
            }
        }

        return false;
    }

    /**
     * Checks if user is a staff member.
     *
     * @param \App\Models\User $user
     *
     * @return bool
     */
    public function isStaffMember()
    {
        return $this->checkUserHasRoleType('staff');
    }

    /**
     * @return string
     */
    public function getApiAccessToken()
    {
        return $this->apiTokens()->first()->getApiToken();
    }

    /**
     * @return string
     */
    public function getItemToken()
    {
        return $this->item_token;
    }

    public function createUserLoggedInEvent()
    {
        event(new UserLoggedIn($this));
    }

    /**
     * Sets user role.
     *
     * @param \App\Models\Role $role
     */
    public function setRole(Role $role)
    {
        $this->roles()->attach($role);
    }

    /**
     * Determines if the User is activated.
     *
     * @return bool
     */
    public function isActivated()
    {
        return (bool)$this->activated_at;
    }
}
