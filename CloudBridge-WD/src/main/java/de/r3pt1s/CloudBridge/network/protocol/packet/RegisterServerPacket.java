// This class was created by r3pt1s
package de.r3pt1s.CloudBridge.network.protocol.packet;

public class RegisterServerPacket extends Packet {

    public Object name = "";
    public Object port = 0;

    @Override
    public void encode() {
        super.encode();
        put(name);
        put(port);
    }

    @Override
    public void decode() {
        super.decode();
        name = get();
        port = get();
    }

    @Override
    public Integer getId() {
        return ID_REGISTER_SERVER;
    }

    public static RegisterServerPacket create(String name, int port) {
        RegisterServerPacket pk = new RegisterServerPacket();
        pk.name = name;
        pk.port = port;
        return pk;
    }
}
