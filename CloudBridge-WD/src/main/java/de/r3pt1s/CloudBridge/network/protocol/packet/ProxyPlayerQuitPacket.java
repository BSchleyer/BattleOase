// This class was created by r3pt1s
package de.r3pt1s.CloudBridge.network.protocol.packet;

public class ProxyPlayerQuitPacket extends Packet {

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
        return ID_PROXY_PLAYER_QUIT;
    }

    public static ProxyPlayerQuitPacket create(String name) {
        ProxyPlayerQuitPacket pk = new ProxyPlayerQuitPacket();
        pk.name = name;
        return pk;
    }
}
