# CloudSystem

![PocketCloud](https://github.com/PocketCloudSystem/CloudSystem/blob/main/.github/images/logo.png)

A cloud system for pocketmine servers with proxy support.

## Requirements

- **[PHP 8.x](https://jenkins.pmmp.io/job/PHP-8.0-Aggregate/)**

## Features

- **Proxy**-Support
- Easy **Server** Management
- Easy **Template** Management
- Player **Implementation**
- **Choose** the start method between tmux, screen and a normal process
- **Send** ingame notify
- **WORKING** on windows

## Commands (Cloud)

- **help** - Get a list of all commands
- **exit** - Stop the cloud
- **create** [server|proxy] [name] - Create a template
- **remove** [name] - Remove a template
- **start** [template] [count: 1] - Start a server
- **stop** [server|template|all] - Stop a server/template
- **save** [server] - Save a server
- **dispatch** [server] [command] - Send a command to a server
- **list** [servers|templates|players] - Get a list of all servers, templates or players
- **kick** [player] [reason] - Kick a player
- **send** [player] [message|title|popup|tip|actionbar] [message] - Send a message, title, popup, tip or actionbar message to a player

## Commands (CloudBridge)

- /cloud **start** [template] [count: 1] - Start a server
- /cloud **stop** [server|template|all] - Stop a server/template
- /cloud **serverinfo** [server] - See information about a server
- /cloud **playerinfo** [player] - See information about a player
- /cloud **list** - Get a list of all running servers
- /cloud **notify** - Activate/Deactivate the notify mode for yourself 

## Installation

- **Put** the CloudSystem.phar in a directory and start the cloud
- **Download** the CloudBridge-PM and paste it in plugins/pmmp/
- **Download** the CloudBridge-WD and paste it in plugins/wdpe/
- **Now** you can start the cloud and create templates to start servers

## Downloads

- **[CloudBridge-PM (PHAR)](https://github.com/PocketCloudSystem/CloudBridge-PM/releases/tag/1.0.0)**
- **[CloudBridge-WD (JAR)](https://github.com/PocketCloudSystem/CloudBridge-WD/releases/tag/1.0.0)**
- **[JoinHandler (WD / JAR)](https://github.com/PocketCloudSystem/JoinHandler/releases/tag/1.0.0)**

## Issues / Ideas / Feedback 

- **[Discord Server](https://discord.gg/E2cyBGWGP2)**