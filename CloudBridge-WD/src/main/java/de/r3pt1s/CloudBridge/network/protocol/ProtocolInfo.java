// This class was created by r3pt1s
package de.r3pt1s.CloudBridge.network.protocol;

public class ProtocolInfo {

    public static final int ID_UNKNOWN = -1;
    public static final int ID_LOGIN_REQUEST = 0;
    public static final int ID_LOGIN_RESPONSE = 1;
    public static final int ID_DISCONNECT = 2;
    public static final int ID_CONNECT = 3;
    public static final int ID_DISPATCH_COMMAND = 4;
    public static final int ID_SAVE_SERVER = 5;
    public static final int ID_REGISTER_SERVER = 6; //Only Proxy
    public static final int ID_UNREGISTER_SERVER = 7; //Only Proxy
    public static final int ID_NOTIFY_STATUS_UPDATE = 8;
    public static final int ID_SEND_NOTIFY = 9;
    public static final int ID_PLAYER_JOIN = 10;
    public static final int ID_PLAYER_QUIT = 11;
    public static final int ID_PROXY_PLAYER_JOIN = 12; //Only Proxy
    public static final int ID_PROXY_PLAYER_QUIT = 13; //Only Proxy
    public static final int ID_LOG = 14;
    public static final int ID_TEXT = 15;
    public static final int ID_PLAYER_KICK = 16;
    public static final int ID_START_SERVER_REQUEST = 17;
    public static final int ID_START_SERVER_RESPONSE = 18;
    public static final int ID_STOP_SERVER_REQUEST = 19;
    public static final int ID_STOP_SERVER_RESPONSE = 20;
    public static final int ID_LIST_SERVERS_REQUEST = 21;
    public static final int ID_LIST_SERVERS_RESPONSE = 22;
    public static final int ID_SERVER_INFO_REQUEST = 23;
    public static final int ID_SERVER_INFO_RESPONSE = 24;
    public static final int ID_PLAYER_INFO_REQUEST = 25;
    public static final int ID_PLAYER_INFO_RESPONSE = 26;
}
