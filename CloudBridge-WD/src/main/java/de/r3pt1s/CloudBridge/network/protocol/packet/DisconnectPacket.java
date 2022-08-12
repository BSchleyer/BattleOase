// This class was created by r3pt1s
package de.r3pt1s.CloudBridge.network.protocol.packet;

public class DisconnectPacket extends Packet {

    public static final int SERVER_SHUTDOWN = 0;
    public static final int CLOUD_SHUTDOWN = 1;

    public Object code = 0;

    @Override
    public void encode() {
        super.encode();
        put(code);
    }

    @Override
    public void decode() {
        super.decode();
        code = get();
    }

    @Override
    public Integer getId() {
        return ID_DISCONNECT;
    }

    public static DisconnectPacket create(int code) {
        DisconnectPacket pk = new DisconnectPacket();
        pk.code = code;
        return pk;
    }
}
