// This class was created by r3pt1s
package de.r3pt1s.JoinHandler;

import dev.waterdog.waterdogpe.ProxyServer;
import dev.waterdog.waterdogpe.network.serverinfo.ServerInfo;
import dev.waterdog.waterdogpe.player.ProxiedPlayer;
import dev.waterdog.waterdogpe.utils.types.IJoinHandler;

public class JoinHandler implements IJoinHandler {

    @Override
    public ServerInfo determineServer(ProxiedPlayer player) {
        for (ServerInfo i : ProxyServer.getInstance().getServers()) {
            if (i.getServerName().contains(Main.templateName)) return i;
        }
        return ProxyServer.getInstance().getServerInfo(ProxyServer.getInstance().getConfiguration().getPriorities().get(0));
    }
}
