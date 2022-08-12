// This class was created by r3pt1s
package de.r3pt1s.CloudBridge.event;

import de.r3pt1s.CloudBridge.network.protocol.packet.InvalidPacket;
import de.r3pt1s.CloudBridge.network.protocol.packet.Packet;
import dev.waterdog.waterdogpe.event.Event;

public class PacketReceiveEvent extends Event {

    private Packet packet;
    private boolean invalid = false;

    public PacketReceiveEvent(Packet packet) {
        this.packet = packet;
        if (packet instanceof InvalidPacket) invalid = true;
    }

    public boolean isInvalid() {
        return invalid;
    }

    public Packet getPacket() {
        return packet;
    }
}
