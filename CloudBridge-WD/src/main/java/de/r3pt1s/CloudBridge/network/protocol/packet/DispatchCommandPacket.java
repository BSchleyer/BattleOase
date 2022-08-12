// This class was created by r3pt1s
package de.r3pt1s.CloudBridge.network.protocol.packet;

public class DispatchCommandPacket extends Packet {

    public Object server = "";
    public Object commandLine = "";

    @Override
    public void encode() {
        super.encode();
        put(server);
        put(commandLine);
    }

    @Override
    public void decode() {
        super.decode();
        server = get();
        commandLine = get();
    }

    @Override
    public Integer getId() {
        return ID_DISPATCH_COMMAND;
    }

    public static DispatchCommandPacket create(String server, String commandLine) {
        DispatchCommandPacket pk = new DispatchCommandPacket();
        pk.server = server;
        pk.commandLine = commandLine;
        return pk;
    }
}
