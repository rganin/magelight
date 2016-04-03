<?php
/**
 * Magelight
 *
 * NOTICE OF LICENSE
 *
 * This file is open source and it`s distribution is based on
 * Open Software License (OSL 3.0). You can obtain license text at
 * http://opensource.org/licenses/osl-3.0.php
 *
 * For any non license implied issues please contact rganin@gmail.com
 *
 * DISCLAIMER
 *
 * This file is a part of a framework. Please, do not modify it unless you discard
 * further updates.
 *
 * @version 1.0
 * @author Roman Ganin
 * @copyright Copyright (c) 2013 rganin (rganin@gmail.com)
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

namespace Magelight\Auth\Models;

/**
 * @method static \Magelight\Auth\Models\User forge($data = [], $forceNew = false)
 */
class User extends \Magelight\Model
{
    /**
     * Table name
     *
     * @var string
     */
    protected static $tableName = 'users';

    /**
     * Add user contact
     *
     * @param string $type
     * @param string $content
     * @return int
     */
    public function addContact($type, $content)
    {
        return Contact::orm()->create([
            'type' => $type,
            'content' => $content,
            'user_id' => $this->id
        ], true)->save(true);
    }

    /**
     * Add user contact
     *
     * @param string $type
     * @param string $content
     * @return int
     */
    public function addContactIfNotExists($type, $content)
    {
        $contact = Contact::orm()->
                whereEq('user_id', $this->id)->
                whereEq('type', $type)->
                whereLike('content', $content)
                ->fetchRow();
        if ($contact) {
            return true;
        }
        return Contact::orm()->create([
            'type' => $type,
            'content' => $content,
            'user_id' => $this->id
        ], true)->save(true);
    }

    /**
     * @param $type
     * @return Contact[]
     */
    public function getContacts($type)
    {
        $contacts = Contact::orm()->whereEq('user_id', $this->id)
            ->whereEq('type', $type)
            ->fetchModels();
        return $contacts;
    }

    /**
     * @param $type
     * @return Contact
     */
    public function getContactFirst($type)
    {
        $contact = Contact::orm()->whereEq('user_id', $this->id)
            ->whereEq('type', $type)
            ->limit(1)
            ->fetchModel();
        return $contact;
    }

    /**
     * Authorize user via uLogin service
     *
     * @param string $userData
     * @return User
     */
    public function authorizeViaUlogin($userData)
    {
        return static::orm()
            ->whereEq('openid_uid', $userData['uid'])
            ->whereEq('openid_provider', $userData['network'])
            ->whereEq('is_registered', 1)
            ->fetchModel();
    }

    /**
     * Create user via uLogin service
     *
     * @param string $userData
     * @param string|null $defaultAvatar
     * @return User|null
     */
    public function createViaUlogin($userData, $defaultAvatar = null)
    {
        $user = \Magelight\ArrayWrapper::forge($userData);
        $name = isset($user->first_name)
                ? ($user->first_name . (isset($user->last_name) ? $user->first_name : ''))
                : (isset($user->nickname) ? $user->nickname : $user->email);

        $cityId = null;
        $countryId = \Magelight\Geo\Models\Country::forge()->getCountryIdByName($user->getData('country', ''));
        if (!empty($countryId)) {
            $cityId = \Magelight\Geo\Models\City::forge()->getCityIdByName($user->getData('city', ''));
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
            'city'            => isset($userData['city']) ? $userData['city'] : '',
            'country'         => isset($userData['country']) ? $userData['country'] : '',
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
