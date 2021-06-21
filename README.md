# Mibew Mattermost plugin

Provides Mattermost notifications when a chat is initiated.

## Installation

1. Get the archive with the plugin sources. You can download it from the [official site](https://mibew.org/plugins#mibew-mattermost) or build the plugin from sources.

2. Extract the downloaded plugin archive file.

3. Put files of the plugins to the `<Mibew root>/plugins` folder.

4. Obtain a webhook key from Mattermost:

    a. Go to you Mattermost instance, open "Integrations" in the settings, find and enter "Incoming WebHooks app".

    b. Click on "Add Incoming Webhook". Fill out the form and click on "Save".

    c. Copy the value of the "Webhook URL".

5. Add plugin's config to plugins structure like below.

    ```yaml
    plugins:
        "Mibew:Mattermost": # Plugin's configurations are described below
            username: "Mibew-Chat"
            channel: "main"
            mattermost_url: "http://example.com/hooks/xxx-generatedkey-xxx"
            custom_text: "Go to mibew/operator/users (send via Mattermost plugin)"
    ```

6. Navigate to "`<Mibew Base URL>`/operator/plugin" page and enable the plugin.

## Plugin's configurations
The plugin can be configured with values in "`<Mibew root>`/configs/config.yml" file.

### config.username

Type: `String`

Username of the Mattermost user.

### config.channel

Type: `String`

Channel to post notification into.

### config.mattermost_url

Type: `String`

Webhook URL from Setup Instructions in Mattermost.

### config.custom_text

Type: `String`

Default: `''`

Optional string to be appended after the default message.

## Build from sources

There are several actions one should do before use the latest version of the plugin from the repository:

1. Obtain a copy of the repository using `git clone`, download button, or another way.
2. Install [node.js](http://nodejs.org/) and [npm](https://www.npmjs.org/).
3. Install [Gulp](http://gulpjs.com/).
4. Install npm dependencies using `npm install`.
5. Run Gulp to build the sources using `gulp default`.

Finally `.tar.gz` and `.zip` archives of the ready-to-use Plugin will be available in `release` directory.

## License

[Apache License 2.0](http://www.apache.org/licenses/LICENSE-2.0.html)
