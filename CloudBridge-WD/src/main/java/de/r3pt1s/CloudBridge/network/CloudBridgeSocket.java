// This class was created by r3pt1s
package de.r3pt1s.CloudBridge.network;

import com.google.gson.Gson;
import de.r3pt1s.CloudBridge.event.PacketReceiveEvent;
import de.r3pt1s.CloudBridge.network.protocol.PacketPool;
import de.r3pt1s.CloudBridge.network.protocol.packet.InvalidPacket;
import de.r3pt1s.CloudBridge.network.protocol.packet.Packet;
import de.r3pt1s.CloudBridge.network.udp.UDPClient;
import dev.waterdog.waterdogpe.ProxyServer;
import dev.waterdog.waterdogpe.logger.MainLogger;

import java.net.InetAddress;
import java.util.ArrayList;

public class CloudBridgeSocket implements Runnable {

    private static CloudBridgeSocket instance;
    private UDPClient udpClient;
    private PacketPool packetPool;

    public CloudBridgeSocket(InetAddress address, int port) {
        instance = this;
        udpClient = new UDPClient();
        packetPool = new PacketPool();

        MainLogger.getInstance().info("Connecting to §e" + address.getHostName() + ":" + port + "§r...");
        udpClient.connect(address, port);
        MainLogger.getInstance().info("§aSuccessfully §rconnected to §e" + address.getHostName() + ":" + port + "§r!");
    }
    
    @SuppressWarnings("InfiniteLoopStatement")
    @Override
    public void run() {
        do {
            try {
                if (udpClient.isConnected()) {
                    String buffer = udpClient.read();
                    if (buffer != null) {
                        Packet packet = packetPool.getPacket(buffer);
                        packet.decode();
                        if (!(packet instanceof InvalidPacket)) {
                        ProxyServer.getInstance().getEventManager().callEvent(new PacketReceiveEvent(packet));
                    }
                }
                //System.out.println(udpClient.isConnected());
            } catch (Exception e){
                //System.out.println("");
            }
        } while (true);
    }

    public void sendPacket(Packet packet) {
        packet.encode();
        String buffer = convertToJson(packet.getPacketContent());
        udpClient.write(buffer);
    }

    private String convertToJson(ArrayList<Object> list) {
        Gson gson = new Gson();
        return gson.toJson(list);
    }

    public PacketPool getPacketPool() {
        return packetPool;
    }

    public UDPClient getUdpClient() {
        return udpClient;
    }

    public static CloudBridgeSocket getInstance() {
        return instance;
    }
}
