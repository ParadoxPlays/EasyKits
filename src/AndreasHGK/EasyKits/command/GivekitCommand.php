<?php
/**
 *    _____                         _  __  _   _         
 *   | ____|   __ _   ___   _   _  | |/ / (_) | |_   ___ 
 *   |  _|    / _` | / __| | | | | | ' /  | | | __| / __|
 *   | |___  | (_| | \__ \ | |_| | | . \  | | | |_  \__ \
 *   |_____|  \__,_| |___/  \__, | |_|\_\ |_|  \__| |___/
 *                           |___/                        
 *          by AndreasHGK and fernanACM 
 */
declare(strict_types=1);

namespace AndreasHGK\EasyKits\command;

use pocketmine\player\Player;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;

use AndreasHGK\EasyKits\manager\KitManager;
use AndreasHGK\EasyKits\ui\GivekitKitSelectForm;
use AndreasHGK\EasyKits\ui\GivekitPlayerSelectForm;
use AndreasHGK\EasyKits\utils\KitException;
use AndreasHGK\EasyKits\utils\LangUtils;
use AndreasHGK\EasyKits\utils\TryClaim;

use pocketmine\Server;

class GivekitCommand extends EKExecutor {

    public function __construct() {
        $this->setDataFromConfig("givekit");

    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args) : bool {
        if(!$sender instanceof Player) { //executable from console if all args are given
            if(!isset($args[0])) {
                $sender->sendMessage(LangUtils::getMessage("givekit-missing-argument-0"));
                return true;
            }
            if(!isset($args[1])) {
                $sender->sendMessage(LangUtils::getMessage("givekit-missing-argument-1"));
                return true;
            }
            $player = Server::getInstance()->getPlayerByPrefix($args[0]);
            if($player === null) {
                $sender->sendMessage(LangUtils::getMessage("givekit-player-not-found"));
                return true;
            }
            $kit = KitManager::get($args[1]);
            if($kit === null) {
                $sender->sendMessage(LangUtils::getMessage("givekit-kit-not-found"));
                return true;
            }
            try {
                TryClaim::ForceClaim($player, $kit);
                $sender->sendMessage(LangUtils::getMessage("givekit-success", true, ["{KIT}" => $kit->getName(), "{PLAYER}" => $player->getName()]));
            } catch(KitException $e) {
                switch($e->getCode()) {
                    case 3:
                        $sender->sendMessage(LangUtils::getMessage("givekit-insufficient-space"));
                        break;
                    default:
                        $sender->sendMessage(LangUtils::getMessage("unknown-exception"));
                        break;
                }
            }
            return true;
        }

        if(isset($args[0])) {
            $player = Server::getInstance()->getPlayerByPrefix($args[0]);
            if($player === null) {
                $sender->sendMessage(LangUtils::getMessage("givekit-player-not-found"));
                return true;
            }
            if(isset($args[1])) {
                $kit = KitManager::get($args[1]);
                if($kit === null) {
                    $sender->sendMessage(LangUtils::getMessage("givekit-kit-not-found"));
                    return true;
                }
                try {
                    TryClaim::ForceClaim($player, $kit);
                    $sender->sendMessage(LangUtils::getMessage("givekit-success", true, ["{KIT}" => $kit->getName(), "{PLAYER}" => $player->getName()]));
                } catch(KitException $e) {
                    switch($e->getCode()) {
                        case 3:
                            $sender->sendMessage(LangUtils::getMessage("givekit-insufficient-space"));
                            break;
                        default:
                            $sender->sendMessage(LangUtils::getMessage("unknown-exception"));
                            break;
                    }
                }
                return true;
            } else {
                GivekitKitSelectForm::sendTo($sender, $player);
                return true;
            }
        }
        GivekitPlayerSelectForm::sendTo($sender);
        return true;
    }
}
