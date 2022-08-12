// This class was created by r3pt1s
package de.r3pt1s.CloudBridge.network.protocol.packet;

public class LoginRequestPacket extends Packet {

    public Object server = "";

    @Override
    public void encode() {
        super.encode();
        put(server);
    }

    @Override
    public void decode() {
        super.decode();
        server = get();
    }

    @Override
    public Integer getId() {
        return ID_LOGIN_REQUEST;
    }

    public static LoginRequestPacket create(String server) {
        LoginRequestPacket pk = new LoginRequestPacket();
        pk.server = server;
        return pk;
    }
}
