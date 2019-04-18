<?php

namespace App\Models;

use App\Events\User\UserActivationTokenGenerated;
use App\Events\User\UserWasActivated;
use App\Events\User\UserWasDeactivated;
use App\Events\User\UserWasDeleted;
use App\Jobs\UpdateStripeCustomer;
use App\Models\Options\OptionUser;
use App\Models\Subscriptions\ActiveSubscription;
use App\Models\Traits\HasRegistration;
use App\Models\Traits\HasUuid;
use App\Models\Translations\Project;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens,
        Notifiable,
        HasRegistration,
        HasUuid,
        HasStripeId;

    const ACTIVATION_EXPIRES_AT      = 48;
    const ACTIVATION_TOKEN_LENGTH    = 32;
    const   SUPER_ADMIN_STAFF_MEMBER = 'super-admin';
    const   SENIOR_STAFF_MEMBER      = 'senior-staff';
    const   JUNIOR_STAFF_MEMBER      = 'junior-staff';

    /**
     * {@inheritdoc}
     */
    protected $dispatchesEvents = [
        'deleted'                    => UserWasDeleted::class,
        'activated'                  => UserWasActivated::class,
        'deactivated'                => UserWasDeactivated::class,
        'activation-token-generated' => UserActivationTokenGenerated::class,
    ];

    /**
     * {@inheritdoc}
     */
    public static function boot()
    {
        parent::boot();

        static::creating(function (self $model) {
            $model->activation_token = Str::random(self::ACTIVATION_TOKEN_LENGTH);
            $model->fireModelEvent('activation-token-generated');
        });
        static::updated(function (self $model) {
            if ($model->isDirty(['email', 'name'])) {
                UpdateStripeCustomer::dispatch($model);
            }
        });
        static::deleting(function (self $model) {
            $model->activeSubscription()->delete();
            $model->options()->delete();
        });
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'stripe_id',
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
            ->withTimestamps()
        ;
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
            ->withTimestamps()
        ;
    }

    /**
     * @return BelongsToMany
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class)
            ->withTimestamps()
        ;
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
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function options()
    {
        return $this->hasMany(OptionUser::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function cards()
    {
        return $this->hasMany(Card::class);
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
}
