// This class was created by r3pt1s
package de.r3pt1s.CloudBridge.handler;

import dev.waterdog.waterdogpe.ProxyServer;
import dev.waterdog.waterdogpe.network.serverinfo.ServerInfo;
import dev.waterdog.waterdogpe.player.ProxiedPlayer;
import dev.waterdog.waterdogpe.utils.types.IJoinHandler;

import java.util.Random;

public class JoinHandler implements IJoinHandler {

    @Override
    public ServerInfo determineServer(ProxiedPlayer player) {
        int wait = 0;
        int random = new Random().nextInt(ProxyServer.getInstance().getServers().size());
        for(ServerInfo i : ProxyServer.getInstance().getServers()){
            if (wait == random) {
                return i;
            }
            wait++;
        }
        return ProxyServer.getInstance().getServerInfo(ProxyServer.getInstance().getConfiguration().getPriorities().get(0));
    }
}
