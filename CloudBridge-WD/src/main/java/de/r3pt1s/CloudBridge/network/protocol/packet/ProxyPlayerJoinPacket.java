// This class was created by r3pt1s
package de.r3pt1s.CloudBridge.network.protocol.packet;

public class ProxyPlayerJoinPacket extends Packet {

    public Object name = "";
    public Object uuid = "";
    public Object xuid = "";
    public Object address = "";
    public Object port = 0;
    public Object currentProxy = "";

    @Override
    public void encode() {
        super.encode();
        put(name);
        put(uuid);
        put(xuid);
        put(address);
        put(port);
        put(currentProxy);
    }

    @Override
    public void decode() {
        super.decode();
        name = get();
        uuid = get();
        xuid = get();
        address = get();
        port = get();
        currentProxy = get();
    }

    @Override
    public Integer getId() {
        return ID_PROXY_PLAYER_JOIN;
    }

    public static ProxyPlayerJoinPacket create(String name, String uuid, String xuid, String address, int port, String currentProxy) {
        ProxyPlayerJoinPacket pk = new ProxyPlayerJoinPacket();
        pk.name = name;
        pk.uuid = uuid;
        pk.xuid = xuid;
        pk.address = address;
        pk.port = port;
        pk.currentProxy = currentProxy;
        return pk;
    }
}
