// This class was created by r3pt1s
package de.r3pt1s.CloudBridge.network.udp;

import org.apache.logging.log4j.core.appender.rolling.action.IfAll;

import java.io.IOException;
import java.net.DatagramPacket;
import java.net.DatagramSocket;
import java.net.InetAddress;
import java.net.SocketException;

public class UDPClient {

    private DatagramSocket socket;
    private InetAddress address;
    private int port;
    private boolean connected = false;

    public void connect(InetAddress address, int port)  {
        this.address = address;
        this.port = port;
        if (!isConnected()) {
            connected = true;
            try {
                socket = new DatagramSocket();
                socket.connect(address, port);
            } catch (SocketException e) {
                e.printStackTrace();
            }
        }
    }

    public void write(String buffer) {
        DatagramPacket packet = new DatagramPacket(buffer.getBytes(), buffer.getBytes().length, address, port);
        try {
            socket.send(packet);
        } catch (IOException e) {
            e.printStackTrace();
        }
    }

    public String read() {
        if (!this.isConnected()) return "";
        byte[] buffer = new byte[1024];
        DatagramPacket packet = new DatagramPacket(buffer, buffer.length);
        try {
            socket.receive(packet);
        } catch (IOException e) {
            e.printStackTrace();
        }
        String buf = new String(packet.getData()).trim();
        return buf;
    }

    public void close() {
        if (isConnected()) {
            connected = false;
            socket.close();
        }
    }

    public InetAddress getAddress() {
        return address;
    }

    public DatagramSocket getSocket() {
        return socket;
    }

    public boolean isConnected() {
        return (connected && socket.isConnected());
    }
}
