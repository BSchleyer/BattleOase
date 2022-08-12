// This class was created by r3pt1s
package de.r3pt1s.CloudBridge.network.protocol.packet;

import de.r3pt1s.CloudBridge.network.protocol.ProtocolInfo;

import java.util.ArrayList;
import java.util.Arrays;
import java.util.Collection;
import java.util.Collections;

abstract public class Packet extends ProtocolInfo {

    private ArrayList<Object> packetContent = new ArrayList<>();

    public void encode() {
        put(getId());
    }

    public void decode() {
        Object id = get();
    }

    public void put(Object value) {
        packetContent.add(value);
    }

    public Object get() {
        if (packetContent.size() > 0) {
            Object get = packetContent.get(0);
            packetContent.remove(0);
            Collection<Object> oldContent = new ArrayList<>(packetContent);
            packetContent.clear();
            packetContent.addAll(oldContent);
            return get;
        }
        return null;
    }

    public ArrayList<Object> getPacketContent() {
        return packetContent;
    }

    public void setPacketContent(ArrayList<Object> packetContent) {
        this.packetContent = packetContent;
    }

    public String getName() {
        return getClass().getSimpleName();
    }

    abstract public Integer getId();
}
