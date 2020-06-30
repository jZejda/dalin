<?php

// @formatter:off
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App{
/**
 * App\User
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string $avatar
 * @property string $color
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\Spatie\Permission\Models\Permission[] $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Post[] $posts
 * @property-read int|null $posts_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\Spatie\Permission\Models\Role[] $roles
 * @property-read int|null $roles_count
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User permission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User role($roles, $guard = null)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereAvatar($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereUpdatedAt($value)
 */
	class User extends \Eloquent {}
}

namespace App{
/**
 * App\Region
 *
 * @property int $id
 * @property string $short_name
 * @property string $long_name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Region newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Region newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Region query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Region whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Region whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Region whereLongName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Region whereShortName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Region whereUpdatedAt($value)
 */
	class Region extends \Eloquent {}
}

namespace App{
/**
 * App\Sport
 *
 * @property int $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Sport newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Sport newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Sport query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Sport whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Sport whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Sport whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Sport whereUpdatedAt($value)
 */
	class Sport extends \Eloquent {}
}

namespace App{
/**
 * App\Post
 *
 * @property int $id
 * @property int|null $user_id
 * @property string $title
 * @property string $editorial
 * @property string|null $img_url
 * @property string|null $content
 * @property int $content_mode
 * @property int|null $private
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Post newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Post newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Post query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Post whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Post whereContentMode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Post whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Post whereEditorial($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Post whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Post whereImgUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Post wherePrivate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Post whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Post whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Post whereUserId($value)
 */
	class Post extends \Eloquent {}
}

namespace App{
/**
 * App\Leg
 *
 * @property int $id
 * @property int|null $oevent_id
 * @property string $title
 * @property int|null $discipline_id
 * @property string|null $leg_datetime
 * @property float $lat
 * @property float $lon
 * @property mixed|null $forecast
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Oevent $oevent
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Oevent_leg newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Oevent_leg newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Oevent_leg query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Oevent_leg whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Oevent_leg whereDisciplineId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Oevent_leg whereForecast($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Oevent_leg whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Oevent_leg whereLat($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Oevent_leg whereLegDatetime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Oevent_leg whereLon($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Oevent_leg whereOeventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Oevent_leg whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Oevent_leg whereUpdatedAt($value)
 */
	class Oevent_leg extends \Eloquent {}
}

namespace App{
/**
 * App\Oevent
 *
 * @property int $id
 * @property int|null $sport_id
 * @property string $title
 * @property string $place
 * @property string|null $url
 * @property string|null $first_date
 * @property string|null $second_date
 * @property string|null $third_date
 * @property mixed|null $clubs
 * @property mixed|null $regions
 * @property mixed|null $event_category
 * @property string|null $description
 * @property int $content_format
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Oevent_leg[] $legs
 * @property-read int|null $legs_count
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Oevent newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Oevent newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Oevent query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Oevent whereClubs($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Oevent whereContentFormat($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Oevent whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Oevent whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Oevent whereEventCategory($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Oevent whereFirstDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Oevent whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Oevent wherePlace($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Oevent whereRegions($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Oevent whereSecondDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Oevent whereSportId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Oevent whereThirdDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Oevent whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Oevent whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Oevent whereUrl($value)
 */
	class Oevent extends \Eloquent {}
}

namespace App{
/**
 * App\Content_category
 *
 * @property int $id
 * @property string $title
 * @property string $description
 * @property string|null $slug
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Content_category findSimilarSlugs($attribute, $config, $slug)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Content_category newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Content_category newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Content_category query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Content_category whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Content_category whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Content_category whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Content_category whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Content_category whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Content_category whereUpdatedAt($value)
 */
	class Content_category extends \Eloquent {}
}

namespace App{
/**
 * App\Page
 *
 * @property int $id
 * @property int|null $user_id
 * @property int|null $content_category_id
 * @property string $title
 * @property string $content
 * @property string $status
 * @property int $weight
 * @property int $page_menu
 * @property int $content_format
 * @property string|null $slug
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Page findSimilarSlugs($attribute, $config, $slug)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Page newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Page newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Page query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Page whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Page whereContentCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Page whereContentFormat($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Page whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Page whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Page wherePageMenu($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Page whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Page whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Page whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Page whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Page whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Page whereWeight($value)
 */
	class Page extends \Eloquent {}
}

namespace App{
/**
 * App\Discipline
 *
 * @property int $id
 * @property string $short_name
 * @property string $long_name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Discipline newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Discipline newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Discipline query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Discipline whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Discipline whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Discipline whereLongName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Discipline whereShortName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Discipline whereUpdatedAt($value)
 */
	class Discipline extends \Eloquent {}
}

namespace App{
/**
 * App\Race_profile
 *
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Race_profile newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Race_profile newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Race_profile query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Race_profile whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Race_profile whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Race_profile whereUpdatedAt($value)
 */
	class Race_profile extends \Eloquent {}
}

