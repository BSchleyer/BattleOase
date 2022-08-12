// This class was created by r3pt1s
package de.r3pt1s.JoinHandler;

import dev.waterdog.waterdogpe.plugin.Plugin;
import dev.waterdog.waterdogpe.utils.config.YamlConfig;

import java.io.File;

public class Main extends Plugin {

    public static String templateName;

    @Override
    public void onEnable() {
        saveResource("config.yml");

        File file = new File(this.getDataFolder().getAbsolutePath() + "/config.yml");
        YamlConfig config = new YamlConfig(file);

        templateName = (config.exists("template") ? config.getString("template") : "Lobby");

        getProxy().setJoinHandler(new JoinHandler());
    }
}
