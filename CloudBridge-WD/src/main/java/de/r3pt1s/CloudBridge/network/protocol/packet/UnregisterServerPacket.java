// This class was created by r3pt1s
package de.r3pt1s.CloudBridge.network.protocol.packet;

public class UnregisterServerPacket extends Packet {

    public Object name = "";

    @Override
    public void encode() {
        super.encode();
        put(name);
    }

    @Override
    public void decode() {
        super.decode();
        name = get();
    }

    @Override
    public Integer getId() {
        return ID_UNREGISTER_SERVER;
    }

    public static UnregisterServerPacket create(String name) {
        UnregisterServerPacket pk = new UnregisterServerPacket();
        pk.name = name;
        return pk;
    }
}
