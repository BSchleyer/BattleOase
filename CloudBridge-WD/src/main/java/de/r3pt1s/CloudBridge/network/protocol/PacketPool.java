// This class was created by r3pt1s
package de.r3pt1s.CloudBridge.network.protocol;

import com.google.gson.JsonArray;
import com.google.gson.JsonParser;
import de.r3pt1s.CloudBridge.network.protocol.packet.*;
;
import java.util.ArrayList;
import java.util.HashMap;

public class PacketPool {

    private static PacketPool instance;

    private HashMap<Integer, Packet> packets = new HashMap<>();

    public PacketPool() {
        instance = this;
        registerPacket(new LoginRequestPacket());
        registerPacket(new LoginResponsePacket());
        registerPacket(new DisconnectPacket());
        registerPacket(new ConnectionPacket());
        registerPacket(new DispatchCommandPacket());
        registerPacket(new SaveServerPacket());
        registerPacket(new RegisterServerPacket());
        registerPacket(new UnregisterServerPacket());
        registerPacket(new ProxyPlayerJoinPacket());
        registerPacket(new ProxyPlayerQuitPacket());
        registerPacket(new LogPacket());
    }

    public void registerPacket(Packet packet) {
        packets.put(packet.getId(), packet);
    }

    public Packet getPacketById(int id) {
        return packets.getOrDefault(id, null);
    }

    public Packet getPacket(String buffer) {
        JsonArray contents = JsonParser.parseString(buffer).getAsJsonArray();
        if (isInteger("" + contents.get(0))) {
            int packetId = Integer.parseInt("" + contents.get(0));
            Packet packet = getPacketById(packetId);
            if (packet != null) {
                packet.setPacketContent(convert(contents));
                return packet;
            }
        }
        return new InvalidPacket();
    }

    private boolean isInteger(String s) {
        try {
            Integer.parseInt(s);
            return true;
        } catch (NumberFormatException e) {
            return false;
        }
    }

    public ArrayList<Object> convert(JsonArray jsonArray) {
        ArrayList<Object> list = new ArrayList<Object>();
        for (int i = 0, l = jsonArray.size(); i < l; i++){
            list.add(jsonArray.get(i).getAsString());
        }
        return list;
    }

    public static PacketPool getInstance() {
        return instance;
    }
}
