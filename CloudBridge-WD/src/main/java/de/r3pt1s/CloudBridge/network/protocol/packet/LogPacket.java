// This class was created by r3pt1s
package de.r3pt1s.CloudBridge.network.protocol.packet;

public class LogPacket extends Packet {

    public Object message = "";

    @Override
    public void encode() {
        super.encode();
        put(message);
    }

    @Override
    public void decode() {
        super.decode();
        message = get();
    }

    @Override
    public Integer getId() {
        return ID_LOG;
    }

    public static LogPacket create(String message) {
        LogPacket pk = new LogPacket();
        pk.message = message;
        return pk;
    }
}
