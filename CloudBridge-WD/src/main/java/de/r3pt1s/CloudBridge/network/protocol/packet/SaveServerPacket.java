// This class was created by r3pt1s
package de.r3pt1s.CloudBridge.network.protocol.packet;

public class SaveServerPacket extends Packet {

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
        return ID_SAVE_SERVER;
    }

    public static SaveServerPacket create(String server) {
        SaveServerPacket pk = new SaveServerPacket();
        pk.server = server;
        return pk;
    }
}
