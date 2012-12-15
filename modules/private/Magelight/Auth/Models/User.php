<?php
/**
 * Created by JetBrains PhpStorm.
 * User: iddqd
 * Date: 02.12.12
 * Time: 2:01
 * To change this template use File | Settings | File Templates.
 */

namespace Magelight\Auth\Models;

/**
 * @method static \Magelight\Auth\Models\User forge($data = [], $forceNew = false)
 */
class User extends \Magelight\Model
{
    protected static $_tableName = 'users';

    public function addContact($type, $content)
    {
        return Contact::orm()->create([
            'type' => $type,
            'content' => $content,
            'user_id' => $this->id
        ], true)->save(true);
    }

    public function authorizeViaUlogin($userData)
    {
        return static::orm()
            ->whereEq('openid_uid', $userData['uid'])
            ->whereEq('openid_provider', $userData['network'])
            ->whereEq('is_registered', 1)
            ->fetchModel();
    }

    public function createViaUlogin($userData, $defaultAvatar = null)
    {
        $name = isset($userData['first_name'])
                ? ($userData['first_name'] . (isset($userData['last_name']) ? $userData['last_name'] : ''))
                : (isset($userData['nickname']) ? $userData['nickname'] : $userData['email']);

        $cityId = null;
        $countryId = \Magelight\Geo\Models\Country::forge()->getCountryIdByName($userData['country']);
        if (!empty($countryId)) {
            $cityId = \Magelight\Geo\Models\City::forge()->getCityIdByName($userData['city']);
        }
        $user = static::forge([
            'is_registered'   => 1,
            'date_register'   => time(),
            'openid_provider' => $userData['network'],
            'openid_identity' => $userData['identity'],
            'openid_uid'      => $userData['uid'],
            'name'            => $name,
            'email'           => $userData['email'],
            'email_verified'  => $userData['verified_email'] > 0 ? 1 : 0,
            'photo'           => isset($userData['photo']) ? $userData['photo'] : $defaultAvatar,
            'city'            => $userData['city'],
            'country'         => $userData['country'],
            'city_id'         => $cityId,
            'country_id'      => $countryId,
        ], true);
        if ($user->save(true)) {
            if (isset($userData['phone'])) {
                $user->addContact('phone', $userData['phone']);
            }
            return $user;
        }
        return null;
    }
}