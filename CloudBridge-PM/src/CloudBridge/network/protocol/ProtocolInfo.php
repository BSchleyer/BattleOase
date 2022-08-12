<?php

namespace CloudBridge\network\protocol;

class ProtocolInfo {

    const ID_UNKNOWN = -1;
    const ID_LOGIN_REQUEST = 0;
    const ID_LOGIN_RESPONSE = 1;
    const ID_DISCONNECT = 2;
    const ID_CONNECTION = 3;
    const ID_DISPATCH_COMMAND = 4;
    const ID_SAVE_SERVER = 5;
    const ID_REGISTER_SERVER = 6; //Only Proxy
    const ID_UNREGISTER_SERVER = 7; //Only Proxy
    const ID_NOTIFY_STATUS_UPDATE = 8;
    const ID_SEND_NOTIFY = 9;
    const ID_PLAYER_JOIN = 10;
    const ID_PLAYER_QUIT = 11;
    const ID_PROXY_PLAYER_JOIN = 12; //Only Proxy
    const ID_PROXY_PLAYER_QUIT = 13; //Only Proxy
    const ID_LOG = 14;
    const ID_TEXT = 15;
    const ID_PLAYER_KICK = 16;
    const ID_START_SERVER_REQUEST = 17;
    const ID_START_SERVER_RESPONSE = 18;
    const ID_STOP_SERVER_REQUEST = 19;
    const ID_STOP_SERVER_RESPONSE = 20;
    const ID_LIST_SERVERS_REQUEST = 21;
    const ID_LIST_SERVERS_RESPONSE = 22;
    const ID_SERVER_INFO_REQUEST = 23;
    const ID_SERVER_INFO_RESPONSE = 24;
    const ID_PLAYER_INFO_REQUEST = 25;
    const ID_PLAYER_INFO_RESPONSE = 26;
}