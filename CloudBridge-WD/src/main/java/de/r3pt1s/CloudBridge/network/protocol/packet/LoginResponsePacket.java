// This class was created by r3pt1s
package de.r3pt1s.CloudBridge.network.protocol.packet;

public class LoginResponsePacket extends Packet {

    public static final int SUCCESS = 0;
    public static final int DENIED = 1;

    public Object responseCode = 0;

    @Override
    public void encode() {
        super.encode();
        put(responseCode);
    }

    @Override
    public void decode() {
        super.decode();
        responseCode = get();
    }

    @Override
    public Integer getId() {
        return ID_LOGIN_RESPONSE;
    }

    public static LoginResponsePacket create(int responseCode) {
        LoginResponsePacket pk = new LoginResponsePacket();
        pk.responseCode = responseCode;
        return pk;
    }
}
