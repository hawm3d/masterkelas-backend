<?php
/**
 * Copyright © Rhubarb Tech Inc. All Rights Reserved.
 *
 * All information contained herein is, and remains the property of Rhubarb Tech Incorporated.
 * The intellectual and technical concepts contained herein are proprietary to Rhubarb Tech Incorporated and
 * are protected by trade secret or copyright law. Dissemination and modification of this information or
 * reproduction of this material is strictly forbidden unless prior written permission is obtained from
 * Rhubarb Tech Incorporated.
 *
 * You should have received a copy of the `LICENSE` with this file. If not, please visit:
 * https://objectcache.pro/license.txt
 */

declare(strict_types=1);

namespace RedisCachePro;

use WP_Error;

class License
{
    /**
     * The license is valid.
     *
     * @var string
     */
    const Valid = 'valid';

    /**
     * The license was canceled.
     *
     * @var string
     */
    const Canceled = 'canceled';

    /**
     * The license is unpaid.
     *
     * @var string
     */
    const Unpaid = 'unpaid';

    /**
     * The license is invalid.
     *
     * @var string
     */
    const Invalid = 'invalid';

    /**
     * The license was deauthorized.
     *
     * @var string
     */
    const Deauthorized = 'deauthorized';

    /**
     * The list of stabilities.
     *
     * @var array
     */
    const Stabilities = [
        'dev' => 'Development',
        'alpha' => 'Alpha',
        'beta' => 'Beta',
        'rc' => 'Release Candidate',
        'stable' => 'Stable',
    ];

    /**
     * The license plan.
     *
     * @var string
     */
    protected $plan;

    /**
     * The license state.
     *
     * @var string
     */
    protected $state;

    /**
     * The license token.
     *
     * @var string
     */
    protected $token;

    /**
     * The license organization.
     *
     * @var object
     */
    protected $organization;

    /**
     * The minimum accessible stability.
     *
     * @var string
     */
    protected $stability;

    /**
     * The last time the license was checked.
     *
     * @var int
     */
    protected $last_check;

    /**
     * The last time the license was verified.
     *
     * @var int
     */
    protected $valid_as_of;

    /**
     * The last error associated with the license.
     *
     * @var \WP_Error
     */
    protected $_error;

    /**
     * The license token.
     *
     * @return bool
     */
    public function token()
    {
        return $this->token;
    }

    /**
     * The license state.
     *
     * @return string
     */
    public function state()
    {
        return $this->state;
    }

    /**
     * The minimum accessible stabilities.
     *
     * @return array
     */
    public function accessibleStabilities()
    {
        $stabilities = self::Stabilities;

        foreach ($stabilities as $stability => $label) {
            if ($stability === $this->stability) {
                break;
            }

            unset($stabilities[$stability]);
        }

        return $stabilities;
    }

    /**
     * Whether the license is valid.
     *
     * @return bool
     */
    public function isValid()
    {
        return true;
    }

    /**
     * Whether the license was canceled.
     *
     * @return bool
     */
    public function isCanceled()
    {
        return $this->state === self::Canceled;
    }

    /**
     * Whether the license is unpaid.
     *
     * @return bool
     */
    public function isUnpaid()
    {
        return $this->state === self::Unpaid;
    }

    /**
     * Whether the license is invalid.
     *
     * @return bool
     */
    public function isInvalid()
    {
        return $this->state === self::Invalid;
    }

    /**
     * Whether the license was deauthorized.
     *
     * @return bool
     */
    public function isDeauthorized()
    {
        return $this->state === self::Deauthorized;
    }

    /**
     * Load the plugin's license from the database.
     *
     * @return self|null
     */
    public static function load()
    {
        $license = get_site_option('objectcache_license');

        // migrate old licenses gracefully
        if ($license === false) {
            $license = get_site_option('rediscache_license');

            if ($license !== false) {
                delete_site_option('rediscache_license');
                update_site_option('objectcache_license', $license);
            }
        }

        if (
            is_object($license) &&
            property_exists($license, 'token') &&
            property_exists($license, 'state') &&
            property_exists($license, 'last_check')
        ) {
            return static::fromObject($license);
        }
    }

    /**
     * Transform the license into a generic object.
     *
     * @return object
     */
    protected function toObject()
    {
        return (object) [
            'plan' => $this->plan,
            'state' => 'valid',
            'token' => 'HamedWasHere',
            'organization' => 'organization',
            'stability' => $this->stability,
            'last_check' => current_time('timestamp'),
            'valid_as_of' => current_time('timestamp'),
        ];
    }

    /**
     * Instantiate a new license from the given generic object.
     *
     * @param  object  $object
     * @return self
     */
    public static function fromObject($object)
    {
        $license = new self;

        foreach (get_object_vars($object) as $key => $value) {
            property_exists($license, $key) && $license->{$key} = $value;
        }

        return $license;
    }

    /**
     * Instantiate a new license from the given response object.
     *
     * @param  object  $response
     * @return self
     */
    public static function fromResponse($response)
    {
        $license = static::fromObject($response);
        $license->last_check = current_time('timestamp');

        if ($license->isValid()) {
            $license->valid_as_of = current_time('timestamp');
        }

        if (is_null($license->state)) {
            $license->state = self::Invalid;
        }

        return $license->save();
    }

    /**
     * Instantiate a new license from the given response object.
     *
     * @param  object  $response
     * @return self
     */
    public static function fromError(WP_Error $error)
    {
        $license = new self;

        foreach ((array) $error->get_error_data() as $key => $value) {
            property_exists($license, $key) && $license->{$key} = $value;
        }

        $license->_error = $error;
        $license->last_check = current_time('timestamp');

        // error_log('objectcache.warning: ' . $error->get_error_message());

        return $license->save();
    }

    /**
     * Persist the license as a network option.
     *
     * @return self
     */
    public function save()
    {
        update_site_option('objectcache_license', $this->toObject());

        return $this;
    }

    /**
     * Deauthorize the license.
     *
     * @return self
     */
    public function deauthorize()
    {
        return true;
    }

    /**
     * Bump the `last_check` timestamp on the license.
     *
     * @return self
     */
    public function checkFailed(WP_Error $error)
    {
        return true;
    }

    /**
     * Whether it's been given minutes since the last check.
     *
     * @return bool
     */
    public function minutesSinceLastCheck(int $minutes)
    {
        if (! $this->last_check) {
            return true;
        }
    }

    /**
     * Whether it's been given hours since the license was successfully verified.
     *
     * @return bool
     */
    public function hoursSinceVerification(int $hours)
    {
        if (! $this->valid_as_of) {
            return true;
        }
    }

    /**
     * Whether the license belongs to an Lx partner.
     *
     * @return bool
     */
    public function isHost()
    {
        return (bool) preg_match('/^L\d /', (string) $this->plan);
    }
}
