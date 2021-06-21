<?php
/*
 * This file is a part of Mibew Mattermost Plugin.
 *
 * Copyright 2021 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace Mibew\Mibew\Plugin\Mattermost;

use Mibew\EventDispatcher\EventDispatcher;
use Mibew\EventDispatcher\Events;

class Plugin extends \Mibew\Plugin\AbstractPlugin implements \Mibew\Plugin\PluginInterface
{
    /**
     * List of the plugin configs.
     *
     * @var array
     */
    protected $config;

    /**
     * Indicates if the plugin was initialized correctly.
     *
     * @var boolean
     */
    protected $initialized = false;

    /**
     * Class constructor.
     *
     * @param array $config List of the plugin config. The following options are
     * supported:
     *   - "username": string, name of the Mattermost user
     *   - "channel": string, name of the Mattermost channel to post notification into
     *   - "mattermost_url": string, URL of the webhook from Setup Instructions in Mattermost
     *   - "custom_text": string, custom text to append after default notification.
     *     The default value is empty.
     */
    public function __construct($config)
    {
        $bad_config = !isset($config['username'])
                      || !isset($config['channel'])
                      || !isset($config['mattermost_url']);

        if ($bad_config) {
            // Config is invalid the plugin cannot be used. Nevertheless
            // the system should work well without the plugin.
            trigger_error(
                'Mattermost plugin is not properly configured',
                E_USER_WARNING
            );

            return;
        }

        $this->initialized = true;

        parent::__construct($config + array('custom_text' => ''));
    }

    /**
     * This creates the listener that listens for new
     * threads to send out mattermost notifications
     */
    public function run()
    {
        $dispatcher = EventDispatcher::getInstance();
        $dispatcher->attachListener(Events::THREAD_CREATE, $this, 'sendMattermostNotification');
    }

    /**
     * Sends notification to Mattermost.
     *
     * @return boolean
     */
    public function sendMattermostNotification(&$args)
    {
        // Convert to json
        $data = array(
          'username'    => $this->config['username'],
          'channel'     => strtolower($this->config['channel']),
          'text'        => getlocal('You have a new user {0} waiting for response.', array($args['thread']->userName))
                           . ( ($this->config['custom_text'] != '') ? ' ' . $this->config['custom_text'] : '' )
        );
        $json = json_encode($data);

        // Do the request
        $curl = curl_init($this->config['mattermost_url']);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $json);

        $json_response = curl_exec($curl);

        $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        if ( $status != 201 ) {
            trigger_error(
                'Unable to send notification to Mattermost. Request failed with status ' . $status,
                E_USER_WARNING
            );
            return false;
        }
        curl_close($curl);

        return true;
    }

    /**
     * Returns plugin's version.
     *
     * @return string
     */
    public static function getVersion()
    {
        return '1.1.0';
    }

    /**
     * Returns plugin's dependencies.
     *
     * @return type
     */
    public static function getDependencies()
    {
        return array();
    }

    /**
     * {@inheritdoc}
     */
    public static function install()
    {
        // Initialize localization constants
        $constants = array(
            'You have a new user {0} waiting for response.'
        );

        foreach ($constants as $constant) {
            getlocal($constant);
        }

        return true;
    }
}
