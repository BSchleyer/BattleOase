// This class was created by r3pt1s
package de.r3pt1s.CloudBridge;

import de.r3pt1s.CloudBridge.event.PacketReceiveEvent;
import de.r3pt1s.CloudBridge.network.CloudBridgeSocket;
import de.r3pt1s.CloudBridge.network.protocol.packet.*;
import dev.waterdog.waterdogpe.ProxyServer;
import dev.waterdog.waterdogpe.command.ConsoleCommandSender;
import dev.waterdog.waterdogpe.event.defaults.PlayerDisconnectEvent;
import dev.waterdog.waterdogpe.event.defaults.PlayerPreLoginEvent;
import dev.waterdog.waterdogpe.logger.MainLogger;
import dev.waterdog.waterdogpe.network.serverinfo.BedrockServerInfo;
import dev.waterdog.waterdogpe.plugin.Plugin;
import dev.waterdog.waterdogpe.utils.config.YamlConfig;
import java.io.File;
import java.io.FileWriter;
import java.io.IOException;
import java.lang.management.ManagementFactory;
import java.net.InetAddress;
import java.net.InetSocketAddress;
import java.net.UnknownHostException;
import java.util.concurrent.ExecutorService;
import java.util.concurrent.Executors;

public class CloudBridge extends Plugin {

    private static CloudBridge instance;
    private CloudBridgeSocket socket;
    private ExecutorService threadPool = Executors.newCachedThreadPool();

    public CloudBridge() {
        instance = this;
    }

    @Override
    public void onEnable() {
        try {
            socket = new CloudBridgeSocket(InetAddress.getByName("127.0.0.1"), getCloudPort());
            threadPool.submit(socket);
        } catch (UnknownHostException e) {
            e.printStackTrace();
        }

        getProxy().getEventManager().subscribe(PacketReceiveEvent.class, this::onReceive);
        getProxy().getEventManager().subscribe(PlayerPreLoginEvent.class, this::onConnect);
        getProxy().getEventManager().subscribe(PlayerDisconnectEvent.class, this::onDisconnect);

        socket.sendPacket(LoginRequestPacket.create(getServerName()));

        if (socket.getUdpClient().isConnected()) {
            MainLogger.getInstance().info("§cWait for incoming packets...");
        }

        File pid = new File(getProxy().getDataPath().toString() + "/pid.txt");
        try {
            FileWriter writer = new FileWriter(pid);
            writer.append("" + ManagementFactory.getRuntimeMXBean().getName().split("@")[0]);
            writer.flush();
        } catch (IOException e) {
            e.printStackTrace();
        }
    }

    @Override
    public void onDisable() {
        socket.getUdpClient().close();
        threadPool.shutdown();
        System.exit(1);
    }

    public String getServerName() {
        return getProxyConfig().getString("server-name");
    }

    public String getTemplate() {
        return getProxyConfig().getString("template");
    }

    public int getCloudPort() {
        return getProxyConfig().getInt("cloud-port");
    }

    public String getCloudPath() {
        return getProxyConfig().getString("cloud-path");
    }

    public YamlConfig getProxyConfig() {
        File configFile = new File(getProxy().getDataPath().toString() + "/config.yml");
        return new YamlConfig(configFile);
    }

    public static CloudBridge getInstance() {
        return instance;
    }

    public void onConnect(PlayerPreLoginEvent e) {
        CloudBridgeSocket.getInstance().sendPacket(ProxyPlayerJoinPacket.create(
                e.getLoginData().getDisplayName(),
                e.getLoginData().getUuid().toString(),
                e.getLoginData().getXuid(),
                e.getLoginData().getAddress().getHostString(),
                e.getLoginData().getAddress().getPort(),
                getServerName()
        ));
    }

    public void onDisconnect(PlayerDisconnectEvent e) {
        CloudBridgeSocket.getInstance().sendPacket(ProxyPlayerQuitPacket.create(e.getPlayer().getName()));
    }

    public void onReceive(PacketReceiveEvent e) {
        Packet packet = e.getPacket();
        boolean isInvalid = e.isInvalid();

        if (!isInvalid) {
            if (packet instanceof LoginResponsePacket) {
                if (Integer.parseInt("" + ((LoginResponsePacket) packet).responseCode) == LoginResponsePacket.SUCCESS) {
                    MainLogger.getInstance().info("Server was §averified§r!");
                } else {
                    MainLogger.getInstance().error("Server cant be verified!");
                    ProxyServer.getInstance().shutdown();
                }
            } else if (packet instanceof ConnectionPacket) {
                socket.sendPacket(ConnectionPacket.create(getServerName()));
            } else if (packet instanceof DispatchCommandPacket) {
                if (((DispatchCommandPacket) packet).server.equals(getServerName())) {
                    ProxyServer.getInstance().dispatchCommand(new ConsoleCommandSender(getProxy()), "" + ((DispatchCommandPacket) packet).commandLine);
                }
            } else if (packet instanceof DisconnectPacket) {
                if (Integer.parseInt("" + ((DisconnectPacket) packet).code) == DisconnectPacket.SERVER_SHUTDOWN) ProxyServer.getInstance().shutdown();
                else if (Integer.parseInt("" + ((DisconnectPacket) packet).code) == DisconnectPacket.CLOUD_SHUTDOWN) {
                    MainLogger.getInstance().info("Cloud was stopped, shutdown the server...");
                    ProxyServer.getInstance().shutdown();
                }
            } else if (packet instanceof RegisterServerPacket) {
                ProxyServer.getInstance().registerServerInfo(new BedrockServerInfo(
                        "" + ((RegisterServerPacket) packet).name,
                        new InetSocketAddress("127.0.0.1", Integer.parseInt("" + ((RegisterServerPacket) packet).port)),
                        null
                ));
            } else if (packet instanceof UnregisterServerPacket) {
                ProxyServer.getInstance().removeServerInfo("" + ((UnregisterServerPacket) packet).name);
            } else if (packet instanceof LogPacket) {
                MainLogger.getInstance().info("§bCloud: §r" + ((LogPacket) packet).message);
            }
        }
    }
}
